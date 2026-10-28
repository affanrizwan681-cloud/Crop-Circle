@extends('layouts.app')

@section('title', 'Farmer Seller Portal — CropCircle')

@section('content')
<div class="bg-light py-4 border-bottom mb-4">
    <div class="container">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div>
                <span class="badge bg-success bg-opacity-10 text-success fw-bold px-3 py-1 rounded-pill mb-1">Producer Portal</span>
                <h1 class="h3 fw-bold mb-0">{{ $farmer->farmerProfile->farm_name ?? $farmer->name }}</h1>
                <span class="small text-muted"><i class="bi bi-geo-alt text-success me-1"></i> {{ $farmer->farmerProfile->city ?? 'Local' }}, {{ $farmer->farmerProfile->state ?? 'IL' }}</span>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('farmer.products.create') }}" class="btn btn-cropcircle btn-sm fw-bold">
                    <i class="bi bi-plus-lg me-1"></i> Add Harvest Item
                </a>
                <a href="{{ route('farmer.pickups.index') }}" class="btn btn-outline-success btn-sm fw-bold">
                    <i class="bi bi-calendar2-check me-1"></i> Pickup Calendar
                </a>
            </div>
        </div>
    </div>
</div>

<div class="container pb-5">
    <!-- Live Database Metric Cards -->
    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card card-custom p-3 border-0 shadow-sm">
                <span class="small text-muted text-uppercase fw-bold">Total Sales Revenue</span>
                <div class="fs-3 fw-extrabold text-success my-1 cc-count-up" data-count-target="{{ (float) $totalSales }}" data-count-prefix="$" data-count-decimals="2" aria-label="${{ number_format($totalSales, 2) }}">${{ number_format($totalSales, 2) }}</div>
                <span class="small text-muted">From {{ $completedOrders }} completed pickups</span>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card card-custom p-3 border-0 shadow-sm">
                <span class="small text-muted text-uppercase fw-bold">Pending Orders</span>
                <div class="fs-3 fw-extrabold text-warning my-1 cc-count-up" data-count-target="{{ (int) $pendingOrders }}" aria-label="{{ $pendingOrders }}">{{ $pendingOrders }}</div>
                <a href="{{ route('farmer.orders.index', ['status' => 'pending']) }}" class="small text-warning text-decoration-none fw-bold">Review & Accept &rarr;</a>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card card-custom p-3 border-0 shadow-sm">
                <span class="small text-muted text-uppercase fw-bold">Active Catalog</span>
                <div class="fs-3 fw-extrabold text-dark my-1"><span class="cc-count-up" data-count-target="{{ (int) $availableProducts }}" aria-label="{{ $availableProducts }}">{{ $availableProducts }}</span> <span class="fs-6 fw-normal text-muted">/ <span class="cc-count-up" data-count-target="{{ (int) $totalProducts }}" aria-label="{{ $totalProducts }}">{{ $totalProducts }}</span></span></div>
                <a href="{{ route('farmer.products.index') }}" class="small text-success text-decoration-none fw-bold">Manage Products &rarr;</a>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card card-custom p-3 border-0 shadow-sm">
                <span class="small text-muted text-uppercase fw-bold">Farmer Rating</span>
                <div class="fs-3 fw-extrabold text-warning my-1 cc-count-up" data-count-target="{{ (float) $averageRating }}" data-count-prefix="★ " data-count-decimals="1" aria-label="{{ number_format($averageRating, 1) }} out of 5">★ {{ number_format($averageRating, 1) }}</div>
                <a href="{{ route('farmer.reviews.index') }}" class="small text-secondary text-decoration-none">{{ $reviewCount }} Customer Reviews &rarr;</a>
            </div>
        </div>
    </div>

    <!-- Quick Shortcuts Toolbar -->
    <div class="card card-custom p-3 border-0 shadow-sm mb-4 bg-light">
        <div class="d-flex flex-wrap gap-2 align-items-center">
            <span class="small fw-bold text-muted me-2">Quick Navigation:</span>
            <a href="{{ route('farmer.products.index') }}" class="btn btn-sm btn-white border rounded-pill px-3">
                <i class="bi bi-boxes me-1 text-success"></i> My Products
            </a>
            <a href="{{ route('farmer.orders.index') }}" class="btn btn-sm btn-white border rounded-pill px-3">
                <i class="bi bi-receipt me-1 text-primary"></i> Order List
            </a>
            <a href="{{ route('farmer.pickupSlots.index') }}" class="btn btn-sm btn-white border rounded-pill px-3">
                <i class="bi bi-clock me-1 text-warning"></i> Pickup Time Windows
            </a>
            <a href="{{ route('farmer.markets.index') }}" class="btn btn-sm btn-white border rounded-pill px-3">
                <i class="bi bi-shop me-1 text-info"></i> Stalls & Markets
            </a>
            <a href="{{ route('farmer.reviews.index') }}" class="btn btn-sm btn-white border rounded-pill px-3">
                <i class="bi bi-star me-1 text-warning"></i> Customer Feedback
            </a>
            <a href="{{ route('chat.index') }}" class="btn btn-sm btn-white border rounded-pill px-3">
                <i class="bi bi-chat-dots me-1 text-success"></i> Customer Chat
            </a>
        </div>
    </div>

    <div class="row g-4">
        <!-- Recent Incoming Orders -->
        <div class="col-lg-8">
            <div class="card card-custom p-4 border-0 shadow-sm mb-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold mb-0"><i class="bi bi-receipt me-2 text-success"></i> Incoming Pre-Orders</h5>
                    <a href="{{ route('farmer.orders.index') }}" class="small text-success fw-bold text-decoration-none">View All Orders &rarr;</a>
                </div>

                @if($recentOrders->isEmpty())
                    <div class="text-center py-4 text-muted small">
                        No orders received yet.
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-borderless align-middle mb-0">
                            <thead class="small text-muted border-bottom">
                                <tr>
                                    <th>Order #</th>
                                    <th>Customer</th>
                                    <th>Pickup Date</th>
                                    <th>Status</th>
                                    <th>Total</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentOrders as $order)
                                    <tr class="border-bottom small">
                                        <td class="fw-bold">{{ $order->order_number }}</td>
                                        <td>
                                            <div class="fw-semibold">{{ $order->customer_name }}</div>
                                            <span class="text-muted" style="font-size: 0.75rem;">{{ $order->customer_phone }}</span>
                                        </td>
                                        <td>
                                            <div>{{ $order->pickup_date ? $order->pickup_date->format('M d, Y') : 'N/A' }}</div>
                                            <span class="text-muted" style="font-size: 0.72rem;">{{ $order->pickup_time }}</span>
                                        </td>
                                        <td>
                                            <span class="badge {{ $order->status_badge_class }}">{{ $order->status_label }}</span>
                                        </td>
                                        <td class="fw-bold text-success">${{ number_format($order->total_amount, 2) }}</td>
                                        <td class="text-end">
                                            <div class="btn-group btn-group-sm">
                                                <a href="{{ route('farmer.orders.show', $order->id) }}" class="btn btn-outline-secondary" title="View Details">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                @if($order->canBeAcceptedByFarmer())
                                                    <form action="{{ route('farmer.orders.status', $order->id) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <input type="hidden" name="status" value="accepted">
                                                        <button type="submit" class="btn btn-success" title="Accept Order">
                                                            <i class="bi bi-check-lg"></i> Accept
                                                        </button>
                                                    </form>
                                                @elseif($order->canBeMarkedReadyByFarmer())
                                                    <form action="{{ route('farmer.orders.status', $order->id) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <input type="hidden" name="status" value="ready_for_pickup">
                                                        <button type="submit" class="btn btn-primary" title="Mark Ready">
                                                            <i class="bi bi-box-seam"></i> Ready
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            <!-- Upcoming Pickups Schedule -->
            <div class="card card-custom p-4 border-0 shadow-sm">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold mb-0"><i class="bi bi-calendar-event me-2 text-primary"></i> Upcoming Stall Pickups</h5>
                    <a href="{{ route('farmer.pickups.index') }}" class="small text-success fw-bold text-decoration-none">Full Calendar &rarr;</a>
                </div>

                @forelse($upcomingPickups as $up)
                    <div class="p-3 bg-light rounded-3 mb-2 d-flex justify-content-between align-items-center small">
                        <div>
                            <span class="fw-bold text-dark">{{ $up->order_number }}</span> — {{ $up->customer_name }}
                            <div class="text-muted">{{ $up->items->count() }} product items (${{ number_format($up->total_amount, 2) }})</div>
                        </div>
                        <div class="text-end">
                            <span class="badge bg-success">{{ $up->pickup_date ? $up->pickup_date->format('l, M d') : '' }}</span>
                            <div class="text-muted small">{{ $up->pickup_time }}</div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-3 text-muted small">No upcoming pickups scheduled for the next days.</div>
                @endforelse
            </div>
        </div>

        <!-- Right Side: Low Stock Alerts & Stall Info -->
        <div class="col-lg-4">
            <!-- Low Stock Warnings -->
            <div class="card card-custom p-4 border-0 shadow-sm mb-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-bold mb-0 text-danger"><i class="bi bi-exclamation-triangle-fill me-1"></i> Low Stock Warnings</h6>
                    <span class="badge bg-danger">{{ $lowStockProducts }} items</span>
                </div>

                @forelse($lowStockItems as $lItem)
                    <div class="p-2 border rounded-3 mb-2 d-flex justify-content-between align-items-center small">
                        <div>
                            <div class="fw-bold text-dark">{{ $lItem->name }}</div>
                            <span class="text-muted">Stock: <strong class="text-danger">{{ $lItem->stock_quantity }} {{ $lItem->unit }}</strong> left</span>
                        </div>
                        <a href="{{ route('farmer.products.edit', $lItem->id) }}" class="btn btn-sm btn-outline-secondary">Update</a>
                    </div>
                @empty
                    <div class="text-center py-3 text-muted small">All active harvests have healthy stock levels.</div>
                @endforelse
            </div>

            <!-- Farm Stall & Market Info -->
            <div class="card card-custom p-4 border-0 shadow-sm">
                <h6 class="fw-bold mb-3 border-bottom pb-2"><i class="bi bi-shop me-1 text-success"></i> My Market Associations</h6>
                <ul class="list-unstyled mb-3 small d-flex flex-column gap-2">
                    @forelse($farmer->farmerProfile->markets as $m)
                        <li class="p-2 bg-light rounded-2 d-flex justify-content-between align-items-center">
                            <div>
                                <span class="fw-bold d-block">{{ $m->name }}</span>
                                <span class="text-muted">{{ $m->pivot->assigned_stall ?? 'Stall Assigned' }}</span>
                            </div>
                            <span class="badge bg-success bg-opacity-10 text-success">Active</span>
                        </li>
                    @empty
                        <li class="text-muted">No markets joined yet.</li>
                    @endforelse
                </ul>
                <a href="{{ route('farmer.markets.index') }}" class="btn btn-outline-success btn-sm w-100 fw-bold">
                    Join More Market Stalls &rarr;
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

