@extends('layouts.admin')

@section('admin-content')
<div class="container-fluid py-4">
  <h1 class="mb-4">Activity Logs</h1>
  <div class="table-responsive">
    <table class="table table-hover align-middle">
      <thead class="table-light">
        <tr>
          <th>ID</th>
          <th>User</th>
          <th>Action</th>
          <th>Description</th>
          <th>Timestamp</th>
        </tr>
      </thead>
      <tbody>
        @forelse($logs as $log)
        <tr>
          <td>{{ $log->id }}</td>
          <td>{{ $log->user->name ?? 'System' }}</td>
          <td>{{ $log->action }}</td>
          <td>{{ $log->description }}</td>
          <td>{{ $log->created_at->format('Y-m-d H:i') }}</td>
        </tr>
        @empty
        <tr><td colspan="5" class="text-center">No activity logs found.</td></tr>
        @endforelse
      </tbody>
    </table>
    {{ $logs->links() }}
  </div>
</div>
@endsection
