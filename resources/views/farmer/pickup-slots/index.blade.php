@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
  <h1 class="h3 mb-1">Pickup slots</h1>
  <p class="text-muted mb-4">Set the weekly pickup windows customers can choose at checkout.</p>

  @if ($errors->any())
    <div class="alert alert-danger"><ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
  @endif

  <div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
      <h2 class="h5 mb-3">Add a weekly window</h2>
      <form method="POST" action="{{ route('farmer.pickupSlots.store') }}" class="row g-3 align-items-end">
        @csrf
        <div class="col-md-3"><label class="form-label" for="day_of_week">Day</label><select class="form-select" id="day_of_week" name="day_of_week" required>@foreach (['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'] as $day)<option value="{{ $day }}" @selected(old('day_of_week') === $day)>{{ $day }}</option>@endforeach</select></div>
        <div class="col-md-2"><label class="form-label" for="start_time">Starts</label><input class="form-control" id="start_time" type="time" name="start_time" value="{{ old('start_time') }}" required></div>
        <div class="col-md-2"><label class="form-label" for="end_time">Ends</label><input class="form-control" id="end_time" type="time" name="end_time" value="{{ old('end_time') }}" required></div>
        <div class="col-md-2"><label class="form-label" for="max_orders">Order limit</label><input class="form-control" id="max_orders" type="number" name="max_orders" min="1" max="100" value="{{ old('max_orders', 15) }}" required></div>
        <div class="col-md-3"><label class="form-label" for="market_id">Market</label><select class="form-select" id="market_id" name="market_id"><option value="">Choose later / no market</option>@foreach ($markets as $market)<option value="{{ $market->id }}" @selected((string) old('market_id') === (string) $market->id)>{{ $market->name }}</option>@endforeach</select></div>
        <div class="col-12"><button class="btn btn-success">Add pickup window</button></div>
      </form>
    </div>
  </div>

  <div class="card border-0 shadow-sm"><div class="card-body">
    <h2 class="h5 mb-3">Your weekly windows</h2>
    <div class="table-responsive"><table class="table align-middle mb-0">
      <thead><tr><th>Day</th><th>Time</th><th>Market</th><th>Daily order limit</th><th>Status</th><th></th></tr></thead>
      <tbody>
        @forelse ($slots as $slot)
          <tr><td>{{ $slot->day_of_week }}</td><td>{{ $slot->formatted_window }}</td><td>{{ $slot->market?->name ?? 'Any location' }}</td><td>{{ $slot->max_orders }}</td><td><span class="badge {{ $slot->is_active ? 'text-bg-success' : 'text-bg-secondary' }}">{{ $slot->is_active ? 'Active' : 'Inactive' }}</span></td><td><form method="POST" action="{{ route('farmer.pickupSlots.destroy', $slot->id) }}" onsubmit="return confirm('Remove this pickup window?')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">Remove</button></form></td></tr>
        @empty
          <tr><td colspan="6" class="text-center text-muted py-4">No pickup windows yet. Add a weekly window above to offer pickup at checkout.</td></tr>
        @endforelse
      </tbody>
    </table></div>
  </div></div>
</div>
@endsection
