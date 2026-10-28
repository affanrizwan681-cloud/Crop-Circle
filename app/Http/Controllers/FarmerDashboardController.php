<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Category;
use App\Models\Market;
use App\Models\Notification;
use App\Models\Order;
use App\Models\PickupSlot;
use App\Models\Product;
use App\Models\ProductAvailability;
use App\Models\ProductImage;
use App\Models\Review;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class FarmerDashboardController extends Controller
{
    public function dashboard(): View
    {
        $farmer = Auth::user();

        $totalProducts = Product::where('farmer_id', $farmer->id)->count();
        $availableProducts = Product::where('farmer_id', $farmer->id)->where('status', 'available')->count();
        $lowStockProducts = Product::where('farmer_id', $farmer->id)->where('stock_quantity', '<=', 5)->count();

        $pendingOrders = Order::where('farmer_id', $farmer->id)->where('status', Order::STATUS_PENDING)->count();
        $readyOrders = Order::where('farmer_id', $farmer->id)->where('status', Order::STATUS_READY)->count();
        $completedOrders = Order::where('farmer_id', $farmer->id)->where('status', Order::STATUS_COMPLETED)->count();

        $totalSales = Order::where('farmer_id', $farmer->id)
            ->where('status', Order::STATUS_COMPLETED)
            ->sum('total_amount');

        $averageRating = $farmer->averageRating();
        $reviewCount = $farmer->reviewCount();

        $recentOrders = Order::where('farmer_id', $farmer->id)
            ->with(['customer', 'items', 'market', 'pickupSlot'])
            ->latest()
            ->take(5)
            ->get();

        $upcomingPickups = Order::where('farmer_id', $farmer->id)
            ->whereIn('status', [Order::STATUS_ACCEPTED, Order::STATUS_READY])
            ->where('pickup_date', '>=', now()->toDateString())
            ->orderBy('pickup_date', 'asc')
            ->take(5)
            ->get();

        $lowStockItems = Product::where('farmer_id', $farmer->id)
            ->where('stock_quantity', '<=', 5)
            ->take(4)
            ->get();

        return view('farmer.dashboard', compact(
            'farmer',
            'totalProducts',
            'availableProducts',
            'lowStockProducts',
            'pendingOrders',
            'readyOrders',
            'completedOrders',
            'totalSales',
            'averageRating',
            'reviewCount',
            'recentOrders',
            'upcomingPickups',
            'lowStockItems'
        ));
    }

    public function pendingApproval(): View
    {
        $user = Auth::user();
        $profile = $user->farmerProfile;
        return view('farmer.pending', compact('user', 'profile'));
    }

    // --- PRODUCTS MANAGEMENT ---
    public function productsIndex(): View
    {
        $products = Product::where('farmer_id', Auth::id())
            ->with(['category', 'availabilities'])
            ->latest()
            ->paginate(12);

        return view('farmer.products.index', compact('products'));
    }

    public function createProduct(): View
    {
        $categories = Category::active()->get();
        return view('farmer.products.create', compact('categories'));
    }

    public function storeProduct(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'exists:categories,id'],
            'description' => ['required', 'string'],
            'price' => ['required', 'numeric', 'min:0.01'],
            'unit' => ['required', 'string', 'max:50'],
            'stock_quantity' => ['required', 'integer', 'min:0'],
            'weekly_quota' => ['nullable', 'integer', 'min:0'],
            'image' => ['nullable', 'image', 'max:2048'],
            'image_url' => ['nullable', 'url:http,https', 'max:2048'],
            'gallery_images.*' => ['nullable', 'image', 'max:2048'],
            'gallery_image_url' => ['nullable', 'url:http,https', 'max:2048'],
            'is_featured' => ['nullable', 'boolean'],
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
        } elseif ($request->filled('image_url')) {
            $imagePath = $validated['image_url'];
        }

        $galleryImageUrl = $validated['gallery_image_url'] ?? null;

        $product = Product::create([
            'farmer_id' => Auth::id(),
            'category_id' => $validated['category_id'],
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']) . '-' . Str::random(5),
            'description' => $validated['description'],
            'price' => $validated['price'],
            'unit' => $validated['unit'],
            'stock_quantity' => $validated['stock_quantity'],
            'weekly_quota' => $validated['weekly_quota'] ?? $validated['stock_quantity'],
            'image' => $imagePath,
            'status' => $validated['stock_quantity'] > 0 ? 'available' : 'sold_out',
            'is_moderated' => false,
            'is_featured' => $request->boolean('is_featured'),
        ]);

        // Upload gallery images
        if ($request->hasFile('gallery_images')) {
            foreach ($request->file('gallery_images') as $galleryFile) {
                $gPath = $galleryFile->store('products/gallery', 'public');
                ProductImage::create([
                    'product_id' => $product->id,
                    'image_path' => $gPath,
                ]);
            }
        }
        if ($request->filled('gallery_image_url')) {
            ProductImage::create(['product_id' => $product->id, 'image_path' => $galleryImageUrl]);
        }

        // Initialize weekly stock availabilities for each day of the week
        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
        $dailyQty = (int) floor($product->stock_quantity / 3);
        foreach ($days as $day) {
            ProductAvailability::create([
                'product_id' => $product->id,
                'day_of_week' => $day,
                'available_quantity' => in_array($day, ['Friday', 'Saturday', 'Sunday']) ? $dailyQty : 0,
                'reserved_quantity' => 0,
                'is_available' => in_array($day, ['Friday', 'Saturday', 'Sunday']),
            ]);
        }

        ActivityLog::record(Auth::id(), 'product_created', 'Created product: ' . $product->name);

        foreach (User::where('role', 'admin')->get() as $admin) {
            Notification::send(
                $admin->id,
                'Product Review Required',
                $product->name . ' was submitted by ' . Auth::user()->name . ' and is waiting for approval.',
                'product_moderation',
                '/admin/products'
            );
        }

        return redirect()->route('farmer.products.index')->with('success', 'Product submitted successfully and is waiting for administrator review.');
    }

    public function editProduct(int $id): View
    {
        $product = Product::where('farmer_id', Auth::id())->with('images')->findOrFail($id);
        $categories = Category::active()->get();

        return view('farmer.products.edit', compact('product', 'categories'));
    }

    public function updateProduct(Request $request, int $id): RedirectResponse
    {
        $product = Product::where('farmer_id', Auth::id())->findOrFail($id);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'exists:categories,id'],
            'description' => ['required', 'string'],
            'price' => ['required', 'numeric', 'min:0.01'],
            'unit' => ['required', 'string', 'max:50'],
            'stock_quantity' => ['required', 'integer', 'min:0'],
            'weekly_quota' => ['nullable', 'integer', 'min:0'],
            'status' => ['required', 'in:available,sold_out,unavailable'],
            'image' => ['nullable', 'image', 'max:2048'],
            'image_url' => ['nullable', 'url:http,https', 'max:2048'],
            'gallery_images.*' => ['nullable', 'image', 'max:2048'],
            'gallery_image_url' => ['nullable', 'url:http,https', 'max:2048'],
            'is_featured' => ['nullable', 'boolean'],
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('products', 'public');
        } elseif ($request->filled('image_url')) {
            $validated['image'] = $validated['image_url'];
        }
        $galleryImageUrl = $validated['gallery_image_url'] ?? null;
        unset($validated['image_url'], $validated['gallery_image_url']);

        $validated['is_featured'] = $request->boolean('is_featured');
        $validated['is_moderated'] = false;
        $product->update($validated);

        if ($request->hasFile('gallery_images')) {
            foreach ($request->file('gallery_images') as $galleryFile) {
                $gPath = $galleryFile->store('products/gallery', 'public');
                ProductImage::create([
                    'product_id' => $product->id,
                    'image_path' => $gPath,
                ]);
            }
        }
        if ($request->filled('gallery_image_url')) {
            ProductImage::create(['product_id' => $product->id, 'image_path' => $galleryImageUrl]);
        }

        ActivityLog::record(Auth::id(), 'product_updated', 'Updated product: ' . $product->name);

        foreach (User::where('role', 'admin')->get() as $admin) {
            Notification::send(
                $admin->id,
                'Product Review Required',
                $product->name . ' was updated by ' . Auth::user()->name . ' and is waiting for approval.',
                'product_moderation',
                '/admin/products'
            );
        }

        return redirect()->route('farmer.products.index')->with('success', 'Product changes submitted for administrator review.');
    }

    public function deleteProduct(int $id): RedirectResponse
    {
        $product = Product::where('farmer_id', Auth::id())->findOrFail($id);
        $name = $product->name;
        $product->delete();

        ActivityLog::record(Auth::id(), 'product_deleted', 'Deleted product: ' . $name);

        return redirect()->route('farmer.products.index')->with('info', 'Product "' . $name . '" has been deleted.');
    }

    // --- WEEKLY STOCK MANAGEMENT ---
    public function manageWeeklyStock(int $productId): View
    {
        $product = Product::where('farmer_id', Auth::id())
            ->with('availabilities')
            ->findOrFail($productId);

        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
        $availabilities = [];

        foreach ($days as $day) {
            $record = $product->availabilities->firstWhere('day_of_week', $day);
            $availabilities[$day] = $record ?: new ProductAvailability([
                'product_id' => $product->id,
                'day_of_week' => $day,
                'available_quantity' => 0,
                'reserved_quantity' => 0,
                'is_available' => false,
            ]);
        }

        return view('farmer.products.weekly_stock', compact('product', 'availabilities', 'days'));
    }

    public function updateWeeklyStock(Request $request, int $productId): RedirectResponse
    {
        $product = Product::where('farmer_id', Auth::id())->findOrFail($productId);

        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];

        $reservedByDay = $product->availabilities()->pluck('reserved_quantity', 'day_of_week');
        $rules = [
            'days' => ['required', 'array:' . implode(',', $days)],
        ];

        foreach ($days as $day) {
            $reserved = (int) ($reservedByDay[$day] ?? 0);
            $rules["days.{$day}.available_quantity"] = [
                'required',
                'integer',
                'min:' . $reserved,
                'max:' . max($product->stock_quantity, $reserved),
            ];
            $rules["days.{$day}.is_available"] = ['nullable', 'boolean'];
        }

        $request->validate($rules);

        foreach ($days as $day) {
            $isAvail = $request->has("days.{$day}.is_available");
            $qty = (int) $request->input("days.{$day}.available_quantity", 0);

            ProductAvailability::updateOrCreate(
                ['product_id' => $product->id, 'day_of_week' => $day],
                [
                    'available_quantity' => $qty,
                    'is_available' => $isAvail,
                ]
            );
        }

        ActivityLog::record(Auth::id(), 'weekly_stock_updated', 'Updated weekly stock for product: ' . $product->name);

        return redirect()->route('farmer.products.index')->with('success', 'Weekly availability schedule for ' . $product->name . ' updated successfully.');
    }

    // --- ORDERS MANAGEMENT ---
    public function ordersIndex(Request $request): View
    {
        $query = Order::where('farmer_id', Auth::id())
            ->with(['customer', 'items', 'market', 'pickupSlot'])
            ->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $orders = $query->paginate(10)->withQueryString();

        return view('farmer.orders.index', compact('orders'));
    }

    public function orderShow(int $id): View
    {
        $order = Order::where('farmer_id', Auth::id())
            ->with(['customer', 'items.product', 'market', 'pickupSlot', 'reviews', 'editRequests'])
            ->findOrFail($id);

        return view('farmer.orders.show', compact('order'));
    }

    public function updateOrderStatus(Request $request, int $id): RedirectResponse
    {
        $order = Order::where('farmer_id', Auth::id())->with('items.product')->findOrFail($id);

        $request->validate([
            'status' => ['required', 'in:accepted,ready_for_pickup,completed,rejected'],
            'decline_reason' => ['nullable', 'string', 'max:500'],
        ]);

        $newStatus = $request->status;

        // Transition rule checks
        if ($newStatus === Order::STATUS_ACCEPTED && !$order->canBeAcceptedByFarmer()) {
            return $this->keepOrderAlert(back()->with('danger', 'Cannot accept an order that is already ' . $order->status));
        }
        if ($newStatus === Order::STATUS_READY && !$order->canBeMarkedReadyByFarmer()) {
            return $this->keepOrderAlert(back()->with('danger', 'Order must be accepted before marking ready for pickup.'));
        }
        if ($newStatus === Order::STATUS_COMPLETED && !$order->canBeCompleted()) {
            return $this->keepOrderAlert(back()->with('danger', 'Cannot mark this order as completed.'));
        }
        if ($newStatus === Order::STATUS_REJECTED && !$order->canBeRejected()) {
            return $this->keepOrderAlert(back()->with('danger', 'Cannot reject an order in status ' . $order->status));
        }

        DB::transaction(function () use ($order, $newStatus, $request) {
            $order->status = $newStatus;
            if ($newStatus === Order::STATUS_COMPLETED) {
                $order->payment_status = 'paid';
                $order->releaseReservations();
            }
            if ($newStatus === Order::STATUS_REJECTED) {
                $order->decline_reason = $request->decline_reason ?: 'Declined by seller.';
                // Restore reserved inventory!
                $order->restoreInventory();
            }
            $order->save();

            // Notify Customer
            $statusLabels = [
                Order::STATUS_ACCEPTED => 'Your order has been accepted and is being prepared!',
                Order::STATUS_READY => 'Your order is ready for pickup! Please bring your order number.',
                Order::STATUS_COMPLETED => 'Your order has been completed. Thank you for supporting local farming!',
                Order::STATUS_REJECTED => 'Your order was declined: ' . ($request->decline_reason ?: 'Unavailable at this time.'),
            ];

            Notification::send(
                $order->customer_id,
                'Order Status: ' . $order->status_label,
                $statusLabels[$newStatus] ?? 'Order status updated.',
                'order_status',
                "/customer/orders/{$order->id}"
            );

            ActivityLog::record(Auth::id(), 'order_status_updated', "Farmer updated order #{$order->order_number} to {$newStatus}.");
        });

        return $this->keepOrderAlert(back()->with('success', 'Order #' . $order->order_number . ' updated to ' . $order->status_label . '.'));
    }

    // --- PICKUP CALENDAR & SLOTS ---
    public function pickupsIndex(): View
    {
        $farmer = Auth::user();

        $upcomingOrders = Order::where('farmer_id', $farmer->id)
            ->whereIn('status', [Order::STATUS_ACCEPTED, Order::STATUS_READY])
            ->where('pickup_date', '>=', now()->toDateString())
            ->with(['customer', 'items', 'market', 'pickupSlot'])
            ->orderBy('pickup_date', 'asc')
            ->paginate(15);

        return view('farmer.pickups.index', compact('upcomingOrders'));
    }

    public function pickupSlotsIndex(): View
    {
        $farmer = Auth::user();
        $slots = PickupSlot::where('farmer_id', $farmer->id)->with('market')->get();
        $markets = $farmer->farmerProfile->approvedMarkets;

        return view('farmer.pickup-slots.index', compact('slots', 'markets'));
    }

    public function storePickupSlot(Request $request): RedirectResponse
    {
        $request->validate([
            'day_of_week' => ['required', 'in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
            'max_orders' => ['required', 'integer', 'min:1', 'max:100'],
            'market_id' => [
                'nullable',
                Rule::exists('market_farmer', 'market_id')->where(fn ($query) => $query
                    ->where('farmer_profile_id', Auth::user()->farmerProfile->id)
                    ->where('status', 'approved')),
            ],
        ]);

        PickupSlot::create([
            'farmer_id' => Auth::id(),
            'market_id' => $request->market_id ?: null,
            'day_of_week' => $request->day_of_week,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'max_orders' => (int) $request->max_orders,
            'is_active' => true,
        ]);

        return back()->with('success', 'Pickup time window added successfully.');
    }

    public function deletePickupSlot(int $id): RedirectResponse
    {
        $slot = PickupSlot::where('farmer_id', Auth::id())->findOrFail($id);
        $slot->delete();

        return back()->with('info', 'Pickup slot removed.');
    }

    // --- REVIEWS MANAGEMENT ---
    public function reviewsIndex(): View
    {
        $reviews = Review::where('farmer_id', Auth::id())
            ->with(['customer', 'product', 'order'])
            ->latest()
            ->paginate(10);

        return view('farmer.reviews.index', compact('reviews'));
    }

    // --- MARKETS ASSOCIATION ---
    public function marketsIndex(): View
    {
        $profile = Auth::user()->farmerProfile;
        $joinedMarkets = $profile->markets()->get();
        $joinedMarketIds = $joinedMarkets
            ->filter(fn ($market) => in_array($market->pivot->status, ['approved', 'pending'], true))
            ->pluck('id')
            ->toArray();
        $availableMarkets = Market::active()->whereNotIn('id', $joinedMarketIds)->paginate(10);

        return view('farmer.markets.index', compact('profile', 'joinedMarkets', 'availableMarkets'));
    }

    public function applyToMarket(Request $request): RedirectResponse
    {
        $request->validate([
            'market_id' => ['required', 'exists:markets,id,status,active'],
        ]);

        $profile = Auth::user()->farmerProfile;
        $profile->markets()->syncWithoutDetaching([
            $request->market_id => [
                'assigned_stall' => null,
                'status' => 'pending',
            ],
        ]);

        ActivityLog::record(Auth::id(), 'market_application_submitted', 'Applied to join market: ' . $request->market_id);

        foreach (User::where('role', 'admin')->get() as $admin) {
            Notification::send(
                $admin->id,
                'New Market Application',
                ($profile->farm_name ?: Auth::user()->name) . ' applied to join a farmers market.',
                'market_application',
                '/admin/markets'
            );
        }

        return back()->with('success', 'Your application has been sent to the market administrator for review.');
    }
}
