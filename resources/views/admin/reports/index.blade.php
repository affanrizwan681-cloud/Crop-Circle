@extends('layouts.admin')

@section('admin-content')
<div class="container-fluid py-4">
  <h1 class="mb-4">Reports Management</h1>
  <div class="table-responsive">
    <table class="table table-hover align-middle">
      <thead class="table-light">
        <tr>
          <th>ID</th>
          <th>Reporter</th>
          <th>Target</th>
          <th>Reason</th>
          <th>Status</th>
          <th>Created At</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($reports as $report)
        <tr>
          <td>{{ $report->id }}</td>
          <td>{{ $report->reporter->name ?? 'System' }}</td>
          <td>{{ class_basename($report->reportable_type) }} #{{ $report->reportable_id }}</td>
          <td>{{ $report->reason }}<div class="small text-muted">{{ \Illuminate\Support\Str::limit($report->details, 100) }}</div></td>
          <td>
            @if($report->status == 'pending')
              <span class="badge bg-warning text-dark">Pending</span>
            @else
              <span class="badge bg-success">{{ ucfirst($report->status) }}</span>
            @endif
          </td>
          <td>{{ $report->created_at->format('Y-m-d') }}</td>
          <td>
            @if($report->status == 'pending')
              <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#resolveModal{{ $report->id }}">Resolve</button>
            @endif
          </td>
        </tr>
        <!-- Resolve Modal -->
        <div class="modal fade" id="resolveModal{{ $report->id }}" tabindex="-1" aria-labelledby="resolveLabel{{ $report->id }}" aria-hidden="true">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="resolveLabel{{ $report->id }}">Resolve Report #{{ $report->id }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <form method="POST" action="{{ route('admin.reports.resolve', $report->id) }}">
                @csrf
                <div class="modal-body">
                  <div class="mb-3">
                    <label class="form-label">Admin Notes</label>
                    <textarea name="admin_notes" class="form-control" rows="4" required></textarea>
                  </div>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                  <button type="submit" class="btn btn-primary">Mark as Resolved</button>
                </div>
              </form>
            </div>
          </div>
        </div>
        @empty
        <tr><td colspan="7" class="text-center">No reports found.</td></tr>
        @endforelse
      </tbody>
    </table>
    {{ $reports->links() }}
  </div>
</div>
@endsection
