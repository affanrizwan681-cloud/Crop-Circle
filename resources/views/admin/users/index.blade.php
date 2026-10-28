@extends('layouts.admin')

@section('admin-content')
<div class="container-fluid py-4">
  <h1 class="mb-4">User Management</h1>
  <form method="GET" class="row g-3 mb-3">
    <div class="col-auto">
      <input type="text" name="q" class="form-control" placeholder="Search..." value="{{ request('q') }}">
    </div>
    <div class="col-auto">
      <select name="role" class="form-select">
        <option value="">All Roles</option>
        <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
        <option value="farmer" {{ request('role') == 'farmer' ? 'selected' : '' }}>Farmer</option>
        <option value="customer" {{ request('role') == 'customer' ? 'selected' : '' }}>Customer</option>
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
          <th>Name</th>
          <th>Email</th>
          <th>Role</th>
          <th>Status</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($users as $user)
        <tr>
          <td>{{ $user->id }}</td>
          <td>{{ $user->name }}</td>
          <td>{{ $user->email }}</td>
          <td><span class="badge bg-info">{{ ucfirst($user->role) }}</span></td>
          <td>
            @if($user->status === 'active')
              <span class="badge bg-success">Active</span>
            @else
              <span class="badge bg-danger">{{ ucfirst($user->status) }}</span>
            @endif
          </td>
          <td>
            <form method="POST" action="{{ route('admin.users.toggleStatus', $user->id) }}" class="d-inline">
              @csrf
              <button class="btn btn-sm btn-secondary" onclick="return confirm('Toggle status for {{ $user->name }}?')">
                {{ $user->status === 'active' ? 'Suspend' : 'Activate' }}
              </button>
            </form>
          </td>
        </tr>
        @empty
        <tr><td colspan="6" class="text-center">No users found.</td></tr>
        @endforelse
      </tbody>
    </table>
    {{ $users->links() }}
  </div>
</div>
@endsection
