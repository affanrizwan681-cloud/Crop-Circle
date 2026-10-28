@extends('layouts.app')

@section('title', 'Incoming Customer Orders — Farmer Portal')

@section('content')
<div class="bg-light py-4 border-bottom mb-4">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <a href="{{ route('farmer.dashboard') }}" class="text-success text-decoration-none small fw-bold">&larr; Back to Dashboard</a>
                <h1 class="h3 fw-bold mb-0">Incoming Customer Pre-Orders</h1>
            </div>
            <a href="{{ route('farmer.pickups.index') }}" class="btn btn-outline-success btn-sm fw-bold">
                <i class="bi bi-calendar-event me-1"></i> View Pickup Calendar
            </a>
        </div>
    </div>
</div>

<div class="container pb-5">
    <!-- Status Filter Buttons -->
    <div class="mb-4 d-flex gap-2 overflow-x-auto pb-1">
        <a href="{{ route('farmer.orders.index') }}" class="btn btn-sm {{ !request('status') ? 'btn-success' : 'btn-light border text-muted' }} rounded-pill px-3">
            All ({{ auth()->user()->farmerOrders()->count() }})
        </a>
        <a href="{{ route('farmer.orders.index', ['status' => 'pending']) }}" class="btn btn-sm {{ request('status') === 'pending' ? 'btn-warning text-dark' : 'btn-light border text-muted' }} rounded-pill px-3">
            Pending Review
        </a>
        <a href="{{ route('farmer.orders.index', ['status' => 'accepted']) }}" class="btn btn-sm {{ request('status') === 'accepted' ? 'btn-info text-dark' : 'btn-light border text-muted' }} rounded-pill px-3">
            Accepted / Packing
        </a>
        <a href="{{ route('farmer.orders.index', ['status' => 'ready_for_pickup']) }}" class="btn btn-sm {{ request('status') === 'ready_for_pickup' ? 'btn-primary' : 'btn-light border text-muted' }} rounded-pill px-3">
            Ready for Pickup
        </a>
        <a href="{{ route('farmer.orders.index', ['status' => 'completed']) }}" class="btn btn-sm {{ request('status') === 'completed' ? 'btn-success' : 'btn-light border text-muted' }} rounded-pill px-3">
            Completed
        </a>
        <a href="{{ route('farmer.orders.index', ['status' => 'rejected']) }}" class="btn btn-sm {{ request('status') === 'rejected' ? 'btn-danger' : 'btn-light border text-muted' }} rounded-pill px-3">
            Declined
        </a>
    </div>

    <div class="card card-custom border-0 shadow-sm overflow-hidden p-3">
        @if($orders->isEmpty())
            <div class="text-center py-5 text-muted">
                <i class="bi bi-receipt display-4 mb-2 d-block opacity-50"></i>
                No customer orders matching this status.
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="small text-muted border-bottom bg-light">
                        <tr>
                            <th>Order #</th>
                            <th>Customer</th>
                            <th>Pickup Scheduled</th>
                            <th>Stall / Market</th>
                            <th>Status</th>
                            <th>Total</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($orders as $ord)
                            <tr>
                                <td>
                                    <a href="{{ route('farmer.orders.show', $ord->id) }}" class="fw-bold text-dark text-decoration-none">
                                        {{ $ord->order_number }}
                                    </a>
                                    <span class="small text-muted d-block">{{ $ord->items->count() }} items</span>
                                </td>
                                <td>
                                    <div class="fw-semibold">{{ $ord->customer_name }}</div>
                                    <span class="small text-muted"><i class="bi bi-telephone me-1"></i>{{ $ord->customer_phone }}</span>
                                </td>
                                <td>
                                    <div class="fw-semibold">{{ $ord->pickup_date ? $ord->pickup_date->format('M d, Y') : 'N/A' }}</div>
                                    <span class="small text-muted">{{ $ord->pickup_time }}</span>
                                </td>
                                <td>
                                    <span class="small text-muted">{{ $ord->market->name ?? 'Farm Stall Pickup' }}</span>
                                </td>
                                <td>
                                    <span class="badge {{ $ord->status_badge_class }}">{{ $ord->status_label }}</span>
                                </td>
                                <td class="fw-bold text-success">${{ number_format($ord->total_amount, 2) }}</td>
                                <td class="text-end">
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('farmer.orders.show', $ord->id) }}" class="btn btn-outline-secondary" title="View Full Order">
                                            <i class="bi bi-eye"></i> Details
                                        </a>

                                        @if($ord->canBeAcceptedByFarmer())
                                            <form action="{{ route('farmer.orders.status', $ord->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <input type="hidden" name="status" value="accepted">
                                                <button type="submit" class="btn btn-success" title="Accept Order">
                                                    <i class="bi bi-check-lg"></i> Accept
                                                </button>
                                            </form>
                                        @elseif($ord->canBeMarkedReadyByFarmer())
                                            <form action="{{ route('farmer.orders.status', $ord->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <input type="hidden" name="status" value="ready_for_pickup">
                                                <button type="submit" class="btn btn-primary" title="Mark Ready">
                                                    <i class="bi bi-box-seam"></i> Ready
                                                </button>
                                            </form>
                                        @elseif($ord->canBeCompleted())
                                            <form action="{{ route('farmer.orders.status', $ord->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <input type="hidden" name="status" value="completed">
                                                <button type="submit" class="btn btn-success" title="Complete Order">
                                                    <i class="bi bi-check2-circle"></i> Complete
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

            <div class="mt-4 d-flex justify-content-center">
                {{ $orders->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>
</div>
@endsection
