@extends('layouts.admin')

@section('admin-content')
<div class="container-fluid py-4">
  <h1 class="mb-4">Farmer Approvals & Management</h1>
  <div class="table-responsive">
    <table class="table table-hover align-middle">
      <thead class="table-light">
        <tr>
          <th>ID</th>
          <th>Name</th>
          <th>Email</th>
          <th>Status</th>
          <th>Approved</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($farmers as $farmer)
        <tr>
          <td>{{ $farmer->id }}</td>
          <td>{{ $farmer->user->name }}</td>
          <td>{{ $farmer->user->email }}</td>
          <td>
            @if($farmer->user->status === 'active')
              <span class="badge bg-success">Active</span>
            @else
              <span class="badge bg-danger">{{ ucfirst($farmer->user->status) }}</span>
            @endif
          </td>
          <td>
            @if($farmer->is_approved)
              <span class="badge bg-success">Approved</span>
            @else
              <span class="badge bg-warning text-dark">Pending</span>
            @endif
          </td>
          <td>
            @if(!$farmer->is_approved)
              <form method="POST" action="{{ route('admin.farmers.approve', $farmer->id) }}" class="d-inline">
                @csrf
                <button class="btn btn-sm btn-success" onclick="return confirm('Approve this farmer?')">Approve</button>
              </form>
              <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $farmer->id }}">Reject</button>
            @endif
            <form method="POST" action="{{ route('admin.farmers.toggleStatus', $farmer->id) }}" class="d-inline">
              @csrf
              <button class="btn btn-sm btn-secondary" onclick="return confirm('Toggle status?')">
                {{ $farmer->user->status === 'active' ? 'Suspend' : 'Activate' }}
              </button>
            </form>
          </td>
        </tr>
        <!-- Reject Modal -->
        <div class="modal fade" id="rejectModal{{ $farmer->id }}" tabindex="-1" aria-labelledby="rejectModalLabel{{ $farmer->id }}" aria-hidden="true">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="rejectModalLabel{{ $farmer->id }}">Reject Farmer</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <form method="POST" action="{{ route('admin.farmers.reject', $farmer->id) }}">
                @csrf
                <div class="modal-body">
                  <div class="mb-3">
                    <label for="reason{{ $farmer->id }}" class="form-label">Rejection Reason</label>
                    <textarea class="form-control" id="reason{{ $farmer->id }}" name="rejection_reason" rows="3" required></textarea>
                  </div>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                  <button type="submit" class="btn btn-danger">Reject</button>
                </div>
              </form>
            </div>
          </div>
        </div>
        @empty
        <tr><td colspan="6" class="text-center">No farmers found.</td></tr>
        @endforelse
      </tbody>
    </table>
    {{ $farmers->links() }}
  </div>
</div>
@endsection
