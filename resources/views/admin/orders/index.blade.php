@extends('layouts.admin')

@section('admin-content')
<div class="container-fluid py-4">
  <h1 class="mb-4">Orders Overview</h1>
  <form method="GET" class="row g-3 mb-3">
    <div class="col-auto">
      <input type="text" name="q" class="form-control" placeholder="Search..." value="{{ request('q') }}">
    </div>
    <div class="col-auto">
      <select name="status" class="form-select">
        <option value="">All Statuses</option>
        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
        <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>Processing</option>
        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
      </select>
    </div>
    <div class="col-auto">
      <button class="btn btn-primary" type="submit">Filter</button>
    </div>
  </form>
  <div class="table-responsive">
    <table class="table table-hover align-middle">
      <thead class="table-light">
        <tr>
          <th>ID</th>
          <th>Customer</th>
          <th>Farmer</th>
          <th>Market</th>
          <th>Total</th>
          <th>Status</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($orders as $order)
        <tr>
          <td>#{{ $order->id }}</td>
          <td>{{ $order->customer->name ?? 'N/A' }}</td>
          <td>{{ $order->farmer->farmerProfile->farm_name ?? 'N/A' }}</td>
          <td>{{ $order->market->name ?? 'N/A' }}</td>
          <td>${{ number_format($order->total_amount, 2) }}</td>
          <td>
            <span class="badge bg-{{ $order->status == 'completed' ? 'success' : ($order->status == 'pending' ? 'warning' : 'secondary') }}">{{ ucfirst($order->status) }}</span>
          </td>
          <td>
            <a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-sm btn-outline-primary">View</a>
          </td>
        </tr>
        @empty
        <tr><td colspan="7" class="text-center">No orders found.</td></tr>
        @endforelse
      </tbody>
    </table>
    {{ $orders->links() }}
  </div>
</div>
@endsection
