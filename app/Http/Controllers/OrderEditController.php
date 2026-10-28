<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\Order;
use App\Models\OrderEditRequest;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductAvailability;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use RuntimeException;

class OrderEditController extends Controller
{
    public function store(Request $request, int $id): RedirectResponse
    {
        $order = Order::where('customer_id', Auth::id())->with(['items', 'pickupSlot'])->findOrFail($id);
        if (!$order->canBeEditedByCustomer()) {
            $deadline = $order->customerEditDeadline()->format('M d, Y g:i A');
            Notification::send(Auth::id(), 'Order edit window closed', "Order #{$order->order_number} can only be changed until {$deadline}, at least 24 hours before pickup.", 'warning', "/customer/orders/{$order->id}");
            return $this->keepOrderAlert(back()->with('warning', 'Order changes close 24 hours before pickup. Your edit window ended at '.$deadline.'. We sent this notice to your notifications.'));
        }

        if (!in_array($order->status, [Order::STATUS_PENDING, Order::STATUS_ACCEPTED, Order::STATUS_READY], true)) {
            return $this->keepOrderAlert(back()->with('warning', 'This order is no longer active and cannot be changed.'));
        }

        $activeRequest = $order->editRequests()->whereIn('status', [OrderEditRequest::STATUS_PENDING_FARMER, OrderEditRequest::STATUS_FARMER_DENIED])->exists();
        if ($activeRequest) return back()->with('warning', 'Resolve the current edit request before sending another.');

        $data = $request->validate([
            'items' => ['required', 'array', 'min:1'],
            'items.*' => ['required', 'integer', 'min:0', 'max:10000'],
            'proposed_notes' => ['nullable', 'string', 'max:2000'],
        ]);
        $submittedIds = array_map('strval', array_keys($data['items']));
        $orderItemIds = $order->items->pluck('id')->map(fn ($value) => (string) $value)->all();
        sort($submittedIds); sort($orderItemIds);
        if ($submittedIds !== $orderItemIds) return back()->withInput()->with('danger', 'The proposed items do not match this order. Refresh the page and try again.');
        if (collect($data['items'])->every(fn ($quantity) => (int) $quantity === 0)) return back()->withInput()->with('danger', 'Keep at least one product in the order.');

        $changed = (string) ($data['proposed_notes'] ?? '') !== (string) ($order->notes ?? '');
        foreach ($order->items as $item) if ((int) $data['items'][$item->id] !== (int) $item->quantity) $changed = true;
        if (!$changed) return back()->withInput()->with('info', 'No changes were made to this order.');

        $edit = OrderEditRequest::create([
            'order_id' => $order->id,
            'customer_id' => Auth::id(),
            'farmer_id' => $order->farmer_id,
            'proposed_items' => collect($data['items'])->map(fn ($quantity) => (int) $quantity)->all(),
            'proposed_notes' => $data['proposed_notes'] ?? null,
            'status' => OrderEditRequest::STATUS_PENDING_FARMER,
        ]);
        Notification::send($order->farmer_id, 'Customer requested order changes', "{$order->customer_name} requested changes to order #{$order->order_number}. Review the proposed quantities and notes.", 'order', "/farmer/orders/{$order->id}");

        return $this->keepOrderAlert(redirect()->route('customer.orders.show', $order->id)->with('success', 'Your proposed changes were sent to the farmer. The order stays unchanged until they respond.'));
    }

    public function accept(int $id, int $editRequest): RedirectResponse
    {
        $proposal = OrderEditRequest::where('farmer_id', Auth::id())->where('order_id', $id)->findOrFail($editRequest);
        try {
            DB::transaction(function () use ($proposal, $id) {
                $edit = OrderEditRequest::whereKey($proposal->id)->lockForUpdate()->firstOrFail();
                $order = Order::where('farmer_id', Auth::id())->with('pickupSlot')->lockForUpdate()->findOrFail($id);
                if ($edit->status !== OrderEditRequest::STATUS_PENDING_FARMER) throw new RuntimeException('This edit request has already been answered.');
                if (!in_array($order->status, [Order::STATUS_PENDING, Order::STATUS_ACCEPTED, Order::STATUS_READY], true)) throw new RuntimeException('This order is no longer active.');

                $items = OrderItem::where('order_id', $order->id)->with('product')->lockForUpdate()->get()->keyBy(fn ($item) => (string) $item->id);
                $weekday = $order->pickup_date->format('l');
                $newTotal = 0;
                foreach ($edit->proposed_items as $itemId => $newQuantity) {
                    $item = $items->get((string) $itemId);
                    if (!$item) throw new RuntimeException('An ordered product is no longer available to edit.');
                    $newQuantity = (int) $newQuantity;
                    $oldQuantity = (int) $item->quantity;
                    $delta = $newQuantity - $oldQuantity;
                    $product = $item->product ? Product::whereKey($item->product_id)->lockForUpdate()->first() : null;

                    if ($delta > 0) {
                        if (!$product || $product->status !== 'available' || $product->stock_quantity < $delta) throw new RuntimeException("There is not enough stock to increase {$item->product_name}.");
                        $availability = ProductAvailability::where('product_id', $product->id)->where('day_of_week', $weekday)->lockForUpdate()->first();
                        if ($availability && (!$availability->is_available || $availability->remainingQuantity() < $delta)) throw new RuntimeException("There is not enough pickup-day stock to increase {$item->product_name}.");
                        if (!$product->deductStock($delta, $weekday)) throw new RuntimeException("Could not reserve the additional {$item->product_name} stock.");
                    } elseif ($delta < 0 && $product) {
                        $product->restoreStock(abs($delta), $weekday);
                    }

                    if ($newQuantity === 0) {
                        $item->delete();
                        continue;
                    }
                    $item->update(['quantity' => $newQuantity, 'subtotal' => (float) $item->unit_price * $newQuantity]);
                    $newTotal += (float) $item->unit_price * $newQuantity;
                }
                if ($newTotal <= 0) throw new RuntimeException('An order must contain at least one product.');
                $order->update(['total_amount' => $newTotal, 'notes' => $edit->proposed_notes]);
                $edit->update(['status' => OrderEditRequest::STATUS_FARMER_ACCEPTED, 'responded_at' => now()]);
                Notification::send($edit->customer_id, 'Order changes accepted', "The farmer accepted your changes to order #{$order->order_number}. The updated order is confirmed.", 'success', "/customer/orders/{$order->id}");
            });
        } catch (RuntimeException $exception) {
            return $this->keepOrderAlert(back()->with('danger', $exception->getMessage()));
        }
        return $this->keepOrderAlert(back()->with('success', 'You accepted the proposed order changes. The order and reserved stock have been updated.'));
    }

    public function deny(Request $request, int $id, int $editRequest): RedirectResponse
    {
        $data = $request->validate(['farmer_response' => ['nullable', 'string', 'max:1000']]);
        $edit = OrderEditRequest::where('farmer_id', Auth::id())->where('order_id', $id)->where('status', OrderEditRequest::STATUS_PENDING_FARMER)->findOrFail($editRequest);
        $order = Order::findOrFail($id);
        $edit->update(['status' => OrderEditRequest::STATUS_FARMER_DENIED, 'farmer_response' => $data['farmer_response'] ?? null, 'responded_at' => now()]);
        Notification::send($edit->customer_id, 'Farmer declined the order changes', "Your proposed changes to order #{$order->order_number} were declined. Choose whether to continue with the original order or cancel it.", 'warning', "/customer/orders/{$order->id}");
        return $this->keepOrderAlert(back()->with('info', 'You declined the proposed changes. The customer can proceed with the original order or cancel it.'));
    }

    public function customerDecision(Request $request, int $id, int $editRequest): RedirectResponse
    {
        $data = $request->validate(['decision' => ['required', 'in:proceed,cancel']]);
        $edit = OrderEditRequest::where('customer_id', Auth::id())->where('order_id', $id)->where('status', OrderEditRequest::STATUS_FARMER_DENIED)->findOrFail($editRequest);
        $order = Order::where('customer_id', Auth::id())->with('items.product')->findOrFail($id);
        if ($data['decision'] === 'proceed') {
            $edit->update(['status' => OrderEditRequest::STATUS_CUSTOMER_PROCEEDED, 'customer_decided_at' => now()]);
            Notification::send($order->farmer_id, 'Customer kept the original order', "The customer chose to proceed with the original order #{$order->order_number} after declining your proposed edit.", 'order', "/farmer/orders/{$order->id}");
            return $this->keepOrderAlert(back()->with('success', 'You chose to continue with the original order. The farmer has been notified.'));
        }

        if (!in_array($order->status, [Order::STATUS_PENDING, Order::STATUS_ACCEPTED, Order::STATUS_READY], true)) return back()->with('warning', 'This order can no longer be cancelled.');
        DB::transaction(function () use ($order, $edit) {
            $lockedOrder = Order::whereKey($order->id)->with('items.product')->lockForUpdate()->firstOrFail();
            $lockedOrder->update(['status' => Order::STATUS_CANCELLED]);
            $lockedOrder->restoreInventory();
            $edit->update(['status' => OrderEditRequest::STATUS_CUSTOMER_CANCELLED, 'customer_decided_at' => now()]);
            Notification::send($lockedOrder->farmer_id, 'Customer cancelled after edit was declined', "The customer cancelled order #{$lockedOrder->order_number} after you declined their proposed changes. Reserved stock was released.", 'order', "/farmer/orders/{$lockedOrder->id}");
        });
        return $this->keepOrderAlert(redirect()->route('customer.orders.index')->with('info', 'You cancelled the order and the farmer has been notified.'));
    }
}