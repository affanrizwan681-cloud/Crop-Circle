@extends('layouts.app')

@section('title', 'Order ' . $order->order_number . ' — CropCircle')

@section('content')
<div class="container py-5">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
        <div>
            <a href="{{ route('customer.orders.index') }}" class="text-success text-decoration-none small fw-bold mb-1 d-inline-block">
                &larr; Back to All Orders
            </a>
            <h2 class="fw-bold mb-0">Order #{{ $order->order_number }}</h2>
            <span class="text-muted small">Placed on {{ $order->created_at->format('F d, Y \a\t g:i A') }}</span>
        </div>
        <div>
            <span class="badge {{ $order->status_badge_class }} fs-6 px-3 py-2">
                {{ $order->status_label }}
            </span>
        </div>
    </div>

    <!-- Status Tracker Bar -->
    <div class="card card-custom p-4 border-0 shadow-sm mb-4">
        <h6 class="fw-bold text-muted small text-uppercase mb-3">Order Progress Lifecycle</h6>
        <div class="row text-center g-2">
            <div class="col">
                <div class="p-2 rounded-3 {{ in_array($order->status, ['pending', 'accepted', 'ready_for_pickup', 'completed']) ? 'bg-success text-white' : 'bg-light text-muted' }} small fw-bold mb-1">
                    1. Pending Review
                </div>
            </div>
            <div class="col">
                <div class="p-2 rounded-3 {{ in_array($order->status, ['accepted', 'ready_for_pickup', 'completed']) ? 'bg-success text-white' : 'bg-light text-muted' }} small fw-bold mb-1">
                    2. Accepted / Packing
                </div>
            </div>
            <div class="col">
                <div class="p-2 rounded-3 {{ in_array($order->status, ['ready_for_pickup', 'completed']) ? 'bg-success text-white' : 'bg-light text-muted' }} small fw-bold mb-1">
                    3. Ready at Stall
                </div>
            </div>
            <div class="col">
                <div class="p-2 rounded-3 {{ $order->status === 'completed' ? 'bg-success text-white' : 'bg-light text-muted' }} small fw-bold mb-1">
                    4. Completed
                </div>
            </div>
        </div>

        @if($order->status === 'cancelled')
            <div class="alert alert-secondary border-0 mt-3 mb-0 small">
                <i class="bi bi-x-circle me-1 text-danger"></i> This order was cancelled and reserved inventory has been returned to the producer.
            </div>
        @elseif($order->status === 'rejected')
            <div class="alert alert-danger border-0 mt-3 mb-0 small">
                <i class="bi bi-x-circle me-1"></i> Order Declined by Producer: {{ $order->decline_reason ?? 'Unavailable for this pickup window.' }}
            </div>
        @endif
    </div>

    @include('orders.partials.edit-request-panel', ['order' => $order])

    <div class="row g-4">
        <div class="col-lg-8">
            <!-- Order Items Table -->
            <div class="card card-custom p-4 border-0 shadow-sm mb-4">
                <h5 class="fw-bold mb-3 border-bottom pb-2">Reserved Product Items</h5>
                <div class="table-responsive">
                    <table class="table table-borderless align-middle mb-0">
                        <thead class="small text-muted border-bottom">
                            <tr>
                                <th>Item</th>
                                <th>Unit Price</th>
                                <th>Quantity</th>
                                <th class="text-end">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->items as $item)
                                <tr class="border-bottom">
                                    <td>
                                        <div class="fw-bold text-dark">{{ $item->product_name }}</div>
                                        <span class="small text-muted">Per {{ $item->unit }}</span>
                                    </td>
                                    <td class="small">${{ number_format($item->unit_price, 2) }}</td>
                                    <td class="small">{{ $item->quantity }}</td>
                                    <td class="text-end fw-bold text-dark small">${{ number_format($item->subtotal, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3" class="text-end fw-bold pt-3 fs-5">Total:</td>
                                <td class="text-end fw-extrabold text-success pt-3 fs-4">${{ number_format($order->total_amount, 2) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                @if($order->notes)
                    <div class="mt-3 p-3 bg-light rounded-3 small">
                        <span class="fw-bold text-muted d-block">Your Special Requests:</span>
                        {{ $order->notes }}
                    </div>
                @endif
            </div>

            <!-- Customer Review Section if Completed -->
            @if($order->status === 'completed')
                <div class="card card-custom p-4 border-0 shadow-sm mb-4">
                    <h5 class="fw-bold mb-3"><i class="bi bi-star-fill text-warning me-1"></i> Verified Purchase Review</h5>

                    @php
                        $existingReview = $order->reviews()->where('customer_id', auth()->id())->first();
                    @endphp

                    @if($existingReview)
                        <div class="p-3 bg-light rounded-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="badge bg-success bg-opacity-10 text-success small">
                                    <i class="bi bi-check-circle-fill me-1"></i> Your Review Submitted
                                </span>
                                <div class="text-warning">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="bi {{ $i <= $existingReview->rating ? 'bi-star-fill' : 'bi-star' }}"></i>
                                    @endfor
                                </div>
                            </div>
                            <p class="small text-secondary mb-2">{{ $existingReview->comment }}</p>

                            @if($existingReview->farmer_reply)
                                <div class="p-2 bg-white rounded border-start border-success border-3 ms-3 small">
                                    <span class="fw-bold text-success d-block">Farmer Reply:</span>
                                    <span class="text-muted">{{ $existingReview->farmer_reply }}</span>
                                </div>
                            @endif
                        </div>
                    @else
                        <form action="{{ route('reviews.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="order_id" value="{{ $order->id }}">

                            <div class="mb-3">
                                <label class="form-label small fw-bold">Select Specific Product (Optional)</label>
                                <select name="product_id" class="form-select form-select-sm">
                                    <option value="">Overall Farm & Order Review</option>
                                    @foreach($order->items as $item)
                                        @if($item->product_id)
                                            <option value="{{ $item->product_id }}">{{ $item->product_name }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label small fw-bold">Rating (1 to 5 Stars)</label>
                                <select name="rating" class="form-select form-select-sm" required style="max-width: 180px;">
                                    <option value="5">★★★★★ (5 Stars - Outstanding)</option>
                                    <option value="4">★★★★☆ (4 Stars - Very Good)</option>
                                    <option value="3">★★★☆☆ (3 Stars - Good)</option>
                                    <option value="2">★★☆☆☆ (2 Stars - Mediocre)</option>
                                    <option value="1">★☆☆☆☆ (1 Star - Poor)</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label small fw-bold">Your Review Feedback</label>
                                <textarea name="comment" rows="3" class="form-control" placeholder="Share your thoughts on harvest freshness, taste, and stall pickup experience..." required></textarea>
                            </div>

                            <button type="submit" class="btn btn-sm btn-cropcircle px-4 fw-bold">
                                Post Verified Review
                            </button>
                        </form>
                    @endif
                </div>
            @endif
        </div>

        <div class="col-lg-4">
            <!-- Pickup Information Card -->
            <div class="card card-custom p-4 border-0 shadow-sm mb-4">
                <h6 class="fw-bold mb-3 border-bottom pb-2"><i class="bi bi-geo-alt-fill text-success me-1"></i> Pickup Stall Details</h6>

                <div class="mb-3">
                    <span class="small text-muted d-block">Producer Farm:</span>
                    <span class="fw-bold text-dark fs-6">{{ $order->farmer->farmerProfile->farm_name ?? $order->farmer->name }}</span>
                </div>

                @if($order->market)
                    <div class="mb-3">
                        <span class="small text-muted d-block">Farmers Market:</span>
                        <span class="fw-bold text-dark">{{ $order->market->name }}</span>
                        <div class="small text-muted">{{ $order->market->address }}</div>
                    </div>
                @endif

                <div class="mb-3">
                    <span class="small text-muted d-block">Scheduled Pickup Date:</span>
                    <span class="fw-bold text-dark fs-6">{{ $order->pickup_date ? $order->pickup_date->format('l, F d, Y') : 'N/A' }}</span>
                </div>

                <div class="mb-4">
                    <span class="small text-muted d-block">Time Window:</span>
                    <span class="fw-bold text-success fs-6">{{ $order->pickup_time }}</span>
                </div>

                <div class="p-3 bg-light rounded-3 mb-3 small">
                    <div class="fw-bold text-dark mb-1"><i class="bi bi-wallet2 me-1 text-success"></i> Payment Due at Pickup</div>
                    <div>${{ number_format($order->total_amount, 2) }} (Status: <span class="badge {{ $order->payment_status === 'paid' ? 'bg-success' : 'bg-warning text-dark' }}">{{ ucfirst($order->payment_status) }}</span>)</div>
                </div>

                <div class="d-flex flex-column gap-2">
                    <a href="{{ route('chat.start', $order->farmer_id) }}?order_id={{ $order->id }}" class="btn btn-outline-success btn-sm w-100 fw-bold">
                        <i class="bi bi-chat-dots-fill me-1"></i> Message This Farmer
                    </a>

                    @if($order->canBeCancelledByCustomer())
                        <form action="{{ route('customer.orders.cancel', $order->id) }}" method="POST" onsubmit="return confirm('Cancel this reservation? Stock will be released.')">
                            @csrf
                            <button type="submit" class="btn btn-outline-danger btn-sm w-100">
                                <i class="bi bi-x-circle me-1"></i> Cancel Pre-Order
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
