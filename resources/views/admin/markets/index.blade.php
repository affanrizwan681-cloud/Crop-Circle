@extends('layouts.admin')

@section('admin-content')
<div class="container-fluid py-4">
  <h1 class="mb-4">Markets Management</h1>
  <h2 class="h5 mb-3">Pending farmer applications</h2>
  <div class="table-responsive mb-5">
    <table class="table table-hover align-middle">
      <thead class="table-light"><tr><th>Farm</th><th>Farmer</th><th>Market</th><th>Applied</th><th>Actions</th></tr></thead>
      <tbody>
        @php($applicationCount = 0)
        @foreach($pendingMarketApplications as $profile)
          @foreach($profile->markets as $market)
            @php($applicationCount++)
            <tr>
              <td>{{ $profile->farm_name }}</td>
              <td>{{ $profile->user->name }}</td>
              <td>{{ $market->name }}</td>
              <td>{{ optional($market->pivot->created_at)->format('M j, Y') }}</td>
              <td class="d-flex gap-2">
                <form method="POST" action="{{ route('admin.marketApplications.approve', [$profile->id, $market->id]) }}" class="d-flex gap-1">@csrf<input class="form-control form-control-sm" name="assigned_stall" maxlength="100" placeholder="Optional stall" aria-label="Assigned stall"><button class="btn btn-sm btn-success">Approve</button></form>
                <form method="POST" action="{{ route('admin.marketApplications.reject', [$profile->id, $market->id]) }}">@csrf<button class="btn btn-sm btn-outline-danger">Decline</button></form>
              </td>
            </tr>
          @endforeach
        @endforeach
        @if($applicationCount === 0)
          <tr><td colspan="5" class="text-center text-muted">There are no market applications waiting for review.</td></tr>
        @endif
      </tbody>
    </table>
  </div>

  <a href="{{ route('admin.markets.index') }}" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#createMarketModal">Create New Market</a>
  <div class="table-responsive">
    <table class="table table-hover align-middle">
      <thead class="table-light">
        <tr>
          <th>ID</th>
          <th>Name</th>
          <th>Location</th>
          <th>Status</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($markets as $market)
        <tr>
          <td>{{ $market->id }}</td>
          <td>{{ $market->name }}</td>
          <td>{{ $market->location }}</td>
          <td>{{ ucfirst($market->status) }}</td>
          <td>
            <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editMarketModal{{ $market->id }}">Edit</button>
          </td>
        </tr>
        <!-- Edit Modal -->
        <div class="modal fade" id="editMarketModal{{ $market->id }}" tabindex="-1" aria-labelledby="editMarketLabel{{ $market->id }}" aria-hidden="true">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="editMarketLabel{{ $market->id }}">Edit Market</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <form method="POST" action="{{ route('admin.markets.update', $market->id) }}">
                @csrf
                @method('PUT')
                <div class="modal-body">
                  <div class="mb-3">
                    <label class="form-label">Name</label>
                    <input type="text" name="name" class="form-control" value="{{ $market->name }}" required>
                  </div>
                  <div class="mb-3">
                    <label class="form-label">Location</label>
                    <input type="text" name="location" class="form-control" value="{{ $market->location }}" required>
                  </div>
                  <div class="mb-3">
                    <label class="form-label">Address</label>
                    <input type="text" name="address" class="form-control" value="{{ $market->address }}" required>
                  </div>
                  <div class="mb-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                      <option value="active" {{ $market->status == 'active' ? 'selected' : '' }}>Active</option>
                      <option value="inactive" {{ $market->status == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                  </div>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                  <button type="submit" class="btn btn-primary">Save changes</button>
                </div>
              </form>
            </div>
          </div>
        </div>
        @empty
        <tr><td colspan="5" class="text-center">No markets found.</td></tr>
        @endforelse
      </tbody>
    </table>
    {{ $markets->links() }}
  </div>

  <!-- Create Modal -->
  <div class="modal fade" id="createMarketModal" tabindex="-1" aria-labelledby="createMarketLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="createMarketLabel">Create Market</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form method="POST" action="{{ route('admin.markets.store') }}">
          @csrf
          <div class="modal-body">
            <div class="mb-3"><label class="form-label">Name</label><input type="text" name="name" class="form-control" required></div>
            <div class="mb-3"><label class="form-label">Location</label><input type="text" name="location" class="form-control" required></div>
            <div class="mb-3"><label class="form-label">Address</label><input type="text" name="address" class="form-control" required></div>
            <div class="mb-3"><label class="form-label">Description</label><textarea name="description" class="form-control"></textarea></div>
            <div class="mb-3"><label class="form-label">Open Time</label><input type="time" name="open_time" class="form-control" required></div>
            <div class="mb-3"><label class="form-label">Close Time</label><input type="time" name="close_time" class="form-control" required></div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary">Create</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
