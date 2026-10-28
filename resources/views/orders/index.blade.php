@extends('layouts.app')

@section('title', 'My Pre-Orders — CropCircle')

@section('content')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1"><i class="bi bi-bag-check text-success me-2"></i> My Pre-Orders</h2>
            <p class="text-muted small mb-0">Track incoming pickups, order confirmations, and past completed orders</p>
        </div>
        <a href="{{ route('products.index') }}" class="btn btn-outline-success btn-sm rounded-pill px-3 fw-bold">
            <i class="bi bi-plus-lg me-1"></i> Order More Products
        </a>
    </div>

    <!-- Orders Filter Tabs -->
    <div class="mb-4 d-flex gap-2 overflow-x-auto pb-1">
        <a href="{{ route('customer.orders.index') }}" class="btn btn-sm {{ !request('status') ? 'btn-success' : 'btn-light border text-muted' }} rounded-pill px-3">
            All ({{ auth()->user()->customerOrders()->count() }})
        </a>
        <a href="{{ route('customer.orders.index', ['status' => 'pending']) }}" class="btn btn-sm {{ request('status') === 'pending' ? 'btn-warning text-dark' : 'btn-light border text-muted' }} rounded-pill px-3">
            Pending
        </a>
        <a href="{{ route('customer.orders.index', ['status' => 'accepted']) }}" class="btn btn-sm {{ request('status') === 'accepted' ? 'btn-info text-dark' : 'btn-light border text-muted' }} rounded-pill px-3">
            Accepted
        </a>
        <a href="{{ route('customer.orders.index', ['status' => 'ready_for_pickup']) }}" class="btn btn-sm {{ request('status') === 'ready_for_pickup' ? 'btn-primary' : 'btn-light border text-muted' }} rounded-pill px-3">
            Ready for Pickup
        </a>
        <a href="{{ route('customer.orders.index', ['status' => 'completed']) }}" class="btn btn-sm {{ request('status') === 'completed' ? 'btn-success' : 'btn-light border text-muted' }} rounded-pill px-3">
            Completed
        </a>
    </div>

    @if($orders->isEmpty())
        <div class="card card-custom p-5 text-center border-0 shadow-sm">
            <i class="bi bi-receipt display-3 text-muted opacity-50 mb-3 d-block"></i>
            <h5 class="fw-bold text-dark">No orders found</h5>
            <p class="text-muted small mb-3">You don't have any pre-orders matching this status.</p>
            <a href="{{ route('products.index') }}" class="btn btn-cropcircle btn-sm px-4">Browse Products</a>
        </div>
    @else
        <div class="row g-3">
            @foreach($orders as $order)
                <div class="col-12">
                    <div class="card card-custom card-hover p-4 border-0 shadow-sm">
                        <div class="row align-items-center g-3">
                            <div class="col-md-3">
                                <span class="badge {{ $order->status_badge_class }} mb-1">
                                    {{ $order->status_label }}
                                </span>
                                <h6 class="fw-bold mb-0 text-dark">{{ $order->order_number }}</h6>
                                <span class="small text-muted">Placed {{ $order->created_at->format('M d, Y') }}</span>
                            </div>

                            <div class="col-md-3">
                                <div class="small text-muted">Producer Stall:</div>
                                <div class="fw-bold text-dark">{{ $order->farmer->farmerProfile->farm_name ?? $order->farmer->name }}</div>
                                @if($order->market)
                                    <div class="small text-muted"><i class="bi bi-geo-alt text-success me-1"></i>{{ $order->market->name }}</div>
                                @endif
                            </div>

                            <div class="col-md-3">
                                <div class="small text-muted"><i class="bi bi-calendar-event me-1"></i> Scheduled Pickup:</div>
                                <div class="fw-bold text-dark">{{ $order->pickup_date ? $order->pickup_date->format('l, M d, Y') : 'Date not set' }}</div>
                                <div class="small text-muted">{{ $order->pickup_time }}</div>
                            </div>

                            <div class="col-md-3 text-md-end">
                                <div class="fs-5 fw-extrabold text-success mb-2">${{ number_format($order->total_amount, 2) }}</div>
                                <div class="d-flex justify-content-md-end gap-2">
                                    <a href="{{ route('customer.orders.show', $order->id) }}" class="btn btn-sm btn-outline-success fw-bold">
                                        View Details
                                    </a>
                                    @if($order->canBeCancelledByCustomer())
                                        <form action="{{ route('customer.orders.cancel', $order->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to cancel this reservation?')">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-danger">Cancel</button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-4 d-flex justify-content-center">
            {{ $orders->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>
@endsection
