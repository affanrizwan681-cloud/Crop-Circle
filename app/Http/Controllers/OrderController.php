<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Cart;
use App\Models\Notification;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PickupSlot;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function checkout(): View|RedirectResponse
    {
        $cartItems = Cart::where('customer_id', Auth::id())
            ->with(['product.farmer.farmerProfile.markets', 'product.category'])
            ->get();

        if ($cartItems->isEmpty()) {
            return $this->keepOrderAlert(redirect()->route('cart.index')->with('warning', 'Your cart is empty. Add products before checkout.'));
        }

        // Group cart items by farmer
        $groupedByFarmer = $cartItems->groupBy(function ($item) {
            return $item->product->farmer_id;
        });

        $farmerDetails = [];
        foreach ($groupedByFarmer as $farmerId => $items) {
            $farmer = User::with(['farmerProfile.markets', 'pickupSlots' => function ($q) {
                $q->where('is_active', true)->with('market');
            }])->findOrFail($farmerId);

            if ($farmer->pickupSlots->isEmpty()) {
                return $this->keepOrderAlert(redirect()->route('cart.index')->with(
                    'warning',
                    ($farmer->farmerProfile->farm_name ?? $farmer->name) . ' has not set any active pickup windows yet. Contact the farmer or remove these items before checkout.'
                ));
            }

            $subtotal = $items->sum(function ($item) {
                return $item->product ? $item->product->effectivePrice() * $item->quantity : 0;
            });

            $farmerDetails[$farmerId] = [
                'farmer' => $farmer,
                'items' => $items,
                'subtotal' => $subtotal,
                'pickupSlots' => $farmer->pickupSlots,
            ];
        }

        $total = $cartItems->sum(function ($item) {
            return $item->product ? $item->product->effectivePrice() * $item->quantity : 0;
        });

        $user = Auth::user();

        return view('orders.checkout', compact('farmerDetails', 'total', 'user'));
    }

    public function placeOrder(Request $request): RedirectResponse
    {
        $request->validate([
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_phone' => ['required', 'string', 'max:20'],
            'pickup_dates' => ['required', 'array'],
            'pickup_dates.*' => ['required', 'date', 'after_or_equal:today'],
            'pickup_slots' => ['required', 'array'],
            'pickup_slots.*' => ['required', 'exists:pickup_slots,id'],
            'notes' => ['nullable', 'array'],
        ]);

        $cartItems = Cart::where('customer_id', Auth::id())
            ->with('product')
            ->get();

        if ($cartItems->isEmpty()) {
            return $this->keepOrderAlert(redirect()->route('cart.index')->with('warning', 'Your cart is empty.'));
        }

        $groupedByFarmer = $cartItems->groupBy(function ($item) {
            return $item->product->farmer_id;
        });

        $createdOrders = [];

        try {
            DB::transaction(function () use ($request, $groupedByFarmer, &$createdOrders) {
                foreach ($groupedByFarmer as $farmerId => $items) {
                    $pickupDateStr = $request->pickup_dates[$farmerId] ?? null;
                    $slotId = $request->pickup_slots[$farmerId] ?? null;

                    if (!$pickupDateStr || !$slotId) {
                        throw new \Exception('Please select a valid pickup date and time window for all farm orders.');
                    }

                    $slot = PickupSlot::where('farmer_id', $farmerId)
                        ->where('is_active', true)
                        ->lockForUpdate()
                        ->findOrFail($slotId);

                    // Validate slot day matches date
                    $pickupDayName = date('l', strtotime($pickupDateStr));
                    if ($slot->day_of_week !== $pickupDayName) {
                        throw new \Exception("The chosen pickup window for {$slot->farmer->name} is only available on {$slot->day_of_week}s, but {$pickupDateStr} is a {$pickupDayName}.");
                    }

                    // Check slot capacity
                    if (!$slot->hasCapacityForDate($pickupDateStr)) {
                        throw new \Exception("The pickup slot {$slot->formatted_window} on {$pickupDateStr} has reached full capacity. Please choose another slot.");
                    }

                    // Recalculate subtotal on server
                    $farmerTotal = 0;
                    $lockedProducts = [];
                    foreach ($items as $item) {
                        $product = Product::available()
                            ->where('farmer_id', $farmerId)
                            ->lockForUpdate()
                            ->find($item->product_id);
                        if (!$product || $product->status !== 'available' || $product->stock_quantity < $item->quantity) {
                            throw new \Exception("Product '" . ($item->product->name ?? 'Unknown') . "' is no longer available in the requested quantity.");
                        }

                        $availability = $product->availabilities()
                            ->where('day_of_week', $pickupDayName)
                            ->lockForUpdate()
                            ->first();

                        if ($availability && (!$availability->is_available || $availability->remainingQuantity() < $item->quantity)) {
                            throw new \Exception("Product '{$product->name}' does not have enough stock allocated for {$pickupDayName}.");
                        }

                        $farmerTotal += $product->effectivePrice() * $item->quantity;
                        $lockedProducts[$item->id] = $product;
                    }

                    // Create Order
                    $order = Order::create([
                        'customer_id' => Auth::id(),
                        'farmer_id' => $farmerId,
                        'market_id' => $slot->market_id,
                        'pickup_slot_id' => $slot->id,
                        'pickup_date' => $pickupDateStr,
                        'pickup_time' => $slot->formatted_window,
                        'total_amount' => $farmerTotal,
                        'status' => Order::STATUS_PENDING,
                        'payment_method' => 'cash_on_pickup',
                        'payment_status' => 'unpaid',
                        'customer_name' => $request->customer_name,
                        'customer_phone' => $request->customer_phone,
                        'notes' => $request->notes[$farmerId] ?? null,
                    ]);

                    // Create Order Items & Deduct Stock
                    foreach ($items as $item) {
                        $product = $lockedProducts[$item->id];

                        OrderItem::create([
                            'order_id' => $order->id,
                            'product_id' => $product->id,
                            'product_name' => $product->name,
                            'unit' => $product->unit,
                            'unit_price' => $product->effectivePrice(),
                            'quantity' => $item->quantity,
                            'subtotal' => $product->effectivePrice() * $item->quantity,
                        ]);

                        if (!$product->deductStock($item->quantity, $pickupDayName)) {
                            throw new \Exception("Product '{$product->name}' could not be reserved. Please try again.");
                        }
                    }

                    // Send notification to Farmer
                    Notification::send(
                        $farmerId,
                        'New Pre-Order Received',
                        "New order #{$order->order_number} for \${$order->total_amount} placed by {$order->customer_name}.",
                        'order',
                        "/farmer/orders/{$order->id}"
                    );

                    $createdOrders[] = $order;
                }

                // Clear customer's cart
                Cart::where('customer_id', Auth::id())->delete();

                ActivityLog::record(Auth::id(), 'order_placed', 'Customer placed ' . count($createdOrders) . ' farm order(s).');
            });
        } catch (\Exception $e) {
            return $this->keepOrderAlert(back()->withInput()->with('danger', 'Order placement failed: ' . $e->getMessage()));
        }

        return $this->keepOrderAlert(redirect()->route('customer.orders.index')->with('success', 'Your pre-order has been placed successfully! Each farmer will review your reservation.'));
    }

    public function index(Request $request): View
    {
        $query = Order::where('customer_id', Auth::id())
            ->with(['farmer.farmerProfile', 'items', 'market', 'pickupSlot'])
            ->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $orders = $query->paginate(10)->withQueryString();

        return view('orders.index', compact('orders'));
    }

    public function show(int $id): View
    {
        $order = Order::where('customer_id', Auth::id())
            ->with([
                'farmer.farmerProfile',
                'items.product',
                'market',
                'pickupSlot',
                'reviews',
            ])
            ->findOrFail($id);

        return view('orders.show', compact('order'));
    }

    public function cancel(int $id): RedirectResponse
    {
        $order = Order::where('customer_id', Auth::id())->findOrFail($id);

        if (!$order->canBeCancelledByCustomer()) {
            return $this->keepOrderAlert(back()->with('danger', 'This order cannot be cancelled as it is already being processed or completed.'));
        }

        DB::transaction(function () use ($order) {
            $order->update(['status' => Order::STATUS_CANCELLED]);
            $order->restoreInventory();

            Notification::send(
                $order->farmer_id,
                'Order Cancelled',
                "Order #{$order->order_number} was cancelled by the customer. Reserved inventory has been released.",
                'order',
                "/farmer/orders/{$order->id}"
            );

            ActivityLog::record(Auth::id(), 'order_cancelled', "Customer cancelled order #{$order->order_number}.");
        });

        return $this->keepOrderAlert(back()->with('info', 'Your order # ' . $order->order_number . ' has been cancelled and stock released.'));
    }
}
