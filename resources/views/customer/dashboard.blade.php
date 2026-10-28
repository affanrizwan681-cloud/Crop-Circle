@extends('layouts.app')

@section('title', 'Customer Dashboard — CropCircle')

@section('content')
<div class="bg-light py-4 border-bottom mb-4">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <span class="badge bg-primary bg-opacity-10 text-primary fw-bold px-3 py-1 rounded-pill mb-1">Customer Portal</span>
                <h1 class="h3 fw-bold mb-0">Welcome, {{ $user->name }}!</h1>
            </div>
            <a href="{{ route('products.index') }}" class="btn btn-cropcircle btn-sm fw-bold px-3">
                <i class="bi bi-search me-1"></i> Browse Fresh Harvests
            </a>
        </div>
    </div>
</div>

<div class="container pb-5">
    <!-- Quick Stats Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-3 col-6">
            <div class="card card-custom p-3 border-0 shadow-sm text-center">
                <span class="small text-muted text-uppercase fw-bold">Total Orders</span>
                <div class="display-6 fw-extrabold text-dark my-1 cc-count-up" data-count-target="{{ (int) $totalOrders }}" aria-label="{{ $totalOrders }}">{{ $totalOrders }}</div>
                <a href="{{ route('customer.orders.index') }}" class="small text-success text-decoration-none fw-bold">View History &rarr;</a>
            </div>
        </div>

        <div class="col-md-3 col-6">
            <div class="card card-custom p-3 border-0 shadow-sm text-center">
                <span class="small text-muted text-uppercase fw-bold">Active Pickups</span>
                <div class="display-6 fw-extrabold text-warning my-1 cc-count-up" data-count-target="{{ (int) $activeOrders }}" aria-label="{{ $activeOrders }}">{{ $activeOrders }}</div>
                <a href="{{ route('customer.orders.index', ['status' => 'pending']) }}" class="small text-warning text-decoration-none fw-bold">Track Pickups &rarr;</a>
            </div>
        </div>

        <div class="col-md-3 col-6">
            <div class="card card-custom p-3 border-0 shadow-sm text-center">
                <span class="small text-muted text-uppercase fw-bold">Completed</span>
                <div class="display-6 fw-extrabold text-success my-1 cc-count-up" data-count-target="{{ (int) $completedOrders }}" aria-label="{{ $completedOrders }}">{{ $completedOrders }}</div>
                <span class="small text-muted">Direct to Soil</span>
            </div>
        </div>

        <div class="col-md-3 col-6">
            <div class="card card-custom p-3 border-0 shadow-sm text-center">
                <span class="small text-muted text-uppercase fw-bold">Saved Favorites</span>
                <div class="display-6 fw-extrabold text-danger my-1 cc-count-up" data-count-target="{{ (int) $favoritesCount }}" aria-label="{{ $favoritesCount }}">{{ $favoritesCount }}</div>
                <a href="{{ route('customer.favorites.index') }}" class="small text-danger text-decoration-none fw-bold">My Wishlist &rarr;</a>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Recent Orders Table -->
        <div class="col-lg-8">
            <div class="card card-custom p-4 border-0 shadow-sm mb-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold mb-0"><i class="bi bi-clock-history me-2 text-success"></i> Recent Pre-Orders</h5>
                    <a href="{{ route('customer.orders.index') }}" class="small text-success fw-bold text-decoration-none">View All &rarr;</a>
                </div>

                @if($recentOrders->isEmpty())
                    <div class="text-center py-4 text-muted small">
                        You have not placed any pre-orders yet.
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-borderless align-middle mb-0">
                            <thead class="small text-muted border-bottom">
                                <tr>
                                    <th>Order #</th>
                                    <th>Farmer Stall</th>
                                    <th>Pickup Date</th>
                                    <th>Status</th>
                                    <th class="text-end">Amount</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentOrders as $ord)
                                    <tr class="border-bottom small">
                                        <td class="fw-bold">{{ $ord->order_number }}</td>
                                        <td>{{ $ord->farmer->farmerProfile->farm_name ?? $ord->farmer->name }}</td>
                                        <td>{{ $ord->pickup_date ? $ord->pickup_date->format('M d, Y') : 'N/A' }}</td>
                                        <td><span class="badge {{ $ord->status_badge_class }}">{{ $ord->status_label }}</span></td>
                                        <td class="text-end fw-bold text-success">${{ number_format($ord->total_amount, 2) }}</td>
                                        <td class="text-end">
                                            <a href="{{ route('customer.orders.show', $ord->id) }}" class="btn btn-sm btn-outline-success">View</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>

        <!-- Right Side: Recent Chats & Notifications -->
        <div class="col-lg-4">
            <!-- Active Conversations -->
            <div class="card card-custom p-4 border-0 shadow-sm mb-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-bold mb-0"><i class="bi bi-chat-dots-fill text-primary me-2"></i> Farmer Messages</h6>
                    <a href="{{ route('chat.index') }}" class="small text-primary fw-bold text-decoration-none">Open Inbox</a>
                </div>

                @forelse($recentConversations as $convo)
                    <a href="{{ route('chat.show', $convo->id) }}" class="p-2 rounded-3 bg-light text-decoration-none text-dark d-block mb-2 card-hover border">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="fw-bold small">{{ $convo->farmer->farmerProfile->farm_name ?? $convo->farmer->name }}</span>
                            <span class="small text-muted" style="font-size: 0.72rem;">{{ $convo->last_message_at ? $convo->last_message_at->diffForHumans() : '' }}</span>
                        </div>
                        <div class="small text-secondary text-truncate">
                            {{ $convo->latestMessage ? $convo->latestMessage->message : 'No messages yet' }}
                        </div>
                    </a>
                @empty
                    <div class="text-center py-3 text-muted small">No active conversations.</div>
                @endforelse
            </div>

            <!-- Recent Notifications -->
            <div class="card card-custom p-4 border-0 shadow-sm">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-bold mb-0"><i class="bi bi-bell-fill text-warning me-2"></i> Recent Updates</h6>
                    <a href="{{ route('notifications.index') }}" class="small text-warning text-dark fw-bold text-decoration-none">View All</a>
                </div>

                @forelse($unreadNotifications as $notif)
                    <div class="p-2 rounded bg-light mb-2 small border-start border-warning border-3">
                        <div class="fw-bold">{{ $notif->title }}</div>
                        <div class="text-muted text-truncate">{{ $notif->message }}</div>
                    </div>
                @empty
                    <div class="text-center py-3 text-muted small">All caught up! No unread notifications.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection

