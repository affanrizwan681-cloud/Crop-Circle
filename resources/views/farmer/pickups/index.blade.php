@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h1 class="h2 fw-bold mb-1">Your Upcoming Pickups</h1>
            <p class="text-muted mb-0">Orders scheduled for collection at your market.</p>
        </div>
        <a href="{{ route('farmer.orders.index') }}" class="btn btn-outline-success">View all orders</a>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Order</th>
                        <th>Customer</th>
                        <th>Market</th>
                        <th>Pickup date</th>
                        <th>Pickup time</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($upcomingOrders as $order)
                        <tr>
                            <td class="fw-semibold">#{{ $order->order_number ?? $order->id }}</td>
                            <td>{{ $order->customer->name ?? $order->customer_name ?? 'Customer' }}</td>
                            <td>{{ $order->market->name ?? 'Market not set' }}</td>
                            <td>{{ $order->pickup_date?->format('M d, Y') ?? 'Not set' }}</td>
                            <td>
                                @if($order->pickupSlot)
                                    {{ \Illuminate\Support\Carbon::parse($order->pickupSlot->start_time)->format('g:i A') }} â€“ {{ \Illuminate\Support\Carbon::parse($order->pickupSlot->end_time)->format('g:i A') }}
                                @else
                                    {{ $order->pickup_time ?? 'Not set' }}
                                @endif
                            </td>
                            <td><span class="badge bg-{{ $order->status === 'ready_for_pickup' ? 'success' : 'primary' }}-lt">{{ str_replace('_', ' ', ucfirst($order->status)) }}</span></td>
                            <td class="text-end"><a href="{{ route('farmer.orders.show', $order->id) }}" class="btn btn-sm btn-outline-success">Order details</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center text-muted py-5">No upcoming pickups are scheduled.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($upcomingOrders->hasPages())
            <div class="card-footer bg-white">{{ $upcomingOrders->links() }}</div>
        @endif
    </div>
</div>
@endsection