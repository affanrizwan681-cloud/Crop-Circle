@extends('layouts.admin')

@section('admin-content')
<div class="container-fluid py-4">
  <h1 class="mb-4">Order Details</h1>
  <div class="card">
    <div class="card-body">
      <h5 class="card-title">Order #{{ $order->id }}</h5>
      <p><strong>Customer:</strong> {{ $order->customer->name ?? 'N/A' }} ({{ $order->customer->email ?? '' }})</p>
      <p><strong>Farmer:</strong> {{ $order->farmer->farmerProfile->farm_name ?? 'N/A' }}</p>
      <p><strong>Market:</strong> {{ $order->market->name ?? 'N/A' }}</p>
      <p><strong>Total Amount:</strong> ${{ number_format($order->total_amount, 2) }}</p>
      <p><strong>Status:</strong> <span class="badge bg-{{ $order->status == 'completed' ? 'success' : ($order->status == 'pending' ? 'warning' : 'secondary') }}">{{ ucfirst($order->status) }}</span></p>
      @if($order->pickupSlot)
        <p><strong>Pickup Slot:</strong> {{ $order->pickupSlot->day_of_week }} {{ $order->pickupSlot->start_time }} - {{ $order->pickupSlot->end_time }}</p>
      @endif
      <h5 class="mt-4">Items</h5>
      <ul class="list-group mb-3">
        @foreach($order->items as $item)
          <li class="list-group-item d-flex justify-content-between align-items-center">
            {{ $item->product->name ?? 'Product' }} x {{ $item->quantity }}
            <span>${{ number_format($item->price * $item->quantity, 2) }}</span>
          </li>
        @endforeach
      </ul>
      <h5>Reviews</h5>
      @if($order->reviews->count())
        <ul class="list-group">
          @foreach($order->reviews as $review)
            <li class="list-group-item">
              <strong>{{ $review->customer->name ?? 'Customer' }}:</strong> {{ Str::limit($review->content, 100) }}
            </li>
          @endforeach
        </ul>
      @else
        <p>No reviews for this order.</p>
      @endif
    </div>
  </div>
  <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary mt-3">Back to Orders</a>
</div>
@endsection
