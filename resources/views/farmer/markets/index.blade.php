@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
  <h1 class="mb-4">Markets</h1>
  <h2 class="h5 mb-3">My market applications</h2>
  <div class="table-responsive mb-5">
    <table class="table table-hover align-middle">
      <thead class="table-light"><tr><th>Market</th><th>Location</th><th>Stall</th><th>Application status</th></tr></thead>
      <tbody>
        @forelse($joinedMarkets as $market)
        <tr>
          <td>{{ $market->name }}</td>
          <td>{{ $market->location }}</td>
          <td>{{ $market->pivot->assigned_stall ?: 'Not assigned' }}</td>
          <td><span class="badge {{ $market->pivot->status === 'approved' ? 'bg-success' : ($market->pivot->status === 'pending' ? 'bg-warning text-dark' : 'bg-secondary') }}">{{ ucfirst($market->pivot->status) }}</span></td>
        </tr>
        @empty
        <tr><td colspan="4" class="text-center text-muted">You have not joined any markets yet.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <h2 class="h5 mb-3">Available markets</h2>
  <div class="table-responsive">
    <table class="table table-hover align-middle">
      <thead class="table-light">
        <tr>
          <th>ID</th>
          <th>Name</th>
          <th>Location</th>
          <th>Status</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        @forelse($availableMarkets as $market)
        <tr>
          <td>{{ $market->id }}</td>
          <td>{{ $market->name }}</td>
          <td>{{ $market->location }}</td>
          <td>{{ ucfirst($market->status) }}</td>
          <td>
            <form method="POST" action="{{ route('farmer.markets.apply') }}" class="d-inline">
              @csrf
              <input type="hidden" name="market_id" value="{{ $market->id }}" />
              <button class="btn btn-sm btn-primary" onclick="return confirm('Send an application to this market?')">Apply to join</button>
            </form>
          </td>
        </tr>
        @empty
        <tr><td colspan="5" class="text-center">No markets available.</td></tr>
        @endforelse
      </tbody>
    </table>
    {{ $availableMarkets->links() }}
  </div>
</div>
@endsection
