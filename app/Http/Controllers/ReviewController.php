<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\Order;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'order_id' => ['required', 'exists:orders,id'],
            'product_id' => ['nullable', 'exists:products,id'],
            'rating' => ['required', 'integer', 'between:1,5'],
            'comment' => ['required', 'string', 'min:5', 'max:1000'],
        ]);

        $order = Order::where('customer_id', Auth::id())
            ->where('status', Order::STATUS_COMPLETED)
            ->findOrFail($request->order_id);

        $productId = $request->product_id ?: null;

        // Verify product was actually part of this completed order if specified
        if ($productId) {
            $itemExists = $order->items()->where('product_id', $productId)->exists();
            if (!$itemExists) {
                return back()->with('danger', 'You can only review products included in your completed order.');
            }
        }

        // Prevent duplicate review
        $alreadyReviewed = Review::where('customer_id', Auth::id())
            ->where('order_id', $order->id)
            ->where('product_id', $productId)
            ->exists();

        if ($alreadyReviewed) {
            return back()->with('warning', 'You have already submitted a review for this purchase.');
        }

        $review = Review::create([
            'customer_id' => Auth::id(),
            'farmer_id' => $order->farmer_id,
            'product_id' => $productId,
            'order_id' => $order->id,
            'rating' => (int) $request->rating,
            'comment' => $request->comment,
            'is_moderated' => true,
        ]);

        Notification::send(
            $order->farmer_id,
            'New Customer Review',
            Auth::user()->name . ' left a ' . $review->rating . '-star review for order #' . $order->order_number . '.',
            'review',
            "/farmer/reviews"
        );

        return back()->with('success', 'Thank you! Your verified purchase review has been published.');
    }

    public function reply(Request $request, int $reviewId): RedirectResponse
    {
        $request->validate([
            'farmer_reply' => ['required', 'string', 'max:1000'],
        ]);

        $review = Review::where('farmer_id', Auth::id())->findOrFail($reviewId);

        $review->update([
            'farmer_reply' => $request->farmer_reply,
            'replied_at' => now(),
        ]);

        Notification::send(
            $review->customer_id,
            'Farmer Replied to Your Review',
            Auth::user()->name . ' responded to your review.',
            'review',
            "/customer/orders/{$review->order_id}"
        );

        return back()->with('success', 'Your reply has been posted.');
    }
}
