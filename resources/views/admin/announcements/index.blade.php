@extends('layouts.admin')

@section('admin-content')
<div class="container-fluid py-4">
  <h1 class="mb-4">Announcements</h1>
  <a href="{{ route('admin.announcements.index') }}" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#createAnnouncementModal">Create Announcement</a>
  <div class="table-responsive">
    <table class="table table-hover align-middle">
      <thead class="table-light">
        <tr>
          <th>ID</th>
          <th>Title</th>
          <th>Target</th>
          <th>Expires</th>
          <th>Status</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($announcements as $announcement)
        <tr>
          <td>{{ $announcement->id }}</td>
          <td>{{ $announcement->title }}</td>
          <td>{{ $announcement->target_role }}</td>
          <td>{{ $announcement->expires_at ? $announcement->expires_at->format('Y-m-d') : 'Never' }}</td>
          <td>
            @if($announcement->is_active)
              <span class="badge bg-success">Active</span>
            @else
              <span class="badge bg-secondary">Inactive</span>
            @endif
          </td>
          <td>
            <form method="POST" action="{{ route('admin.announcements.destroy', $announcement->id) }}" class="d-inline" onsubmit="return confirm('Delete this announcement?')">
              @csrf
              @method('DELETE')
              <button class="btn btn-sm btn-outline-danger">Delete</button>
            </form>
          </td>
        </tr>
        @empty
        <tr><td colspan="6" class="text-center">No announcements found.</td></tr>
        @endforelse
      </tbody>
    </table>
    {{ $announcements->links() }}
  </div>

  <!-- Create Modal -->
  <div class="modal fade" id="createAnnouncementModal" tabindex="-1" aria-labelledby="createAnnouncementLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="createAnnouncementLabel">Create Announcement</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form method="POST" action="{{ route('admin.announcements.store') }}">
          @csrf
          <div class="modal-body">
            <div class="mb-3">
              <label class="form-label">Title</label>
              <input type="text" name="title" class="form-control" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Message</label>
              <textarea name="message" class="form-control" rows="4" required></textarea>
            </div>
            <div class="mb-3">
              <label class="form-label">Target Role</label>
              <select name="target_role" class="form-select" required>
                <option value="all">All Users</option>
                <option value="farmer">Farmers</option>
                <option value="customer">Customers</option>
              </select>
            </div>
            <div class="mb-3">
              <label class="form-label">Expires At (optional)</label>
              <input type="date" name="expires_at" class="form-control">
            </div>
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
