@extends('layouts.admin')

@section('admin-content')
<div class="container-fluid py-4">
  <h1 class="mb-4">Categories</h1>
  <a href="{{ route('admin.categories.index') }}" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#createCategoryModal">Create New Category</a>
  <div class="table-responsive">
    <table class="table table-hover align-middle">
      <thead class="table-light">
        <tr>
          <th>ID</th>
          <th>Image</th>
          <th>Name</th>
          <th>Products</th>
          <th>Status</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($categories as $category)
        <tr>
          <td>{{ $category->id }}</td>
          <td><x-produce-image :image="$category->image" :category="$category" :alt="$category->name" class="rounded" style="width: 54px; height: 54px; object-fit: cover;" /></td>
          <td>{{ $category->name }}</td>
          <td>{{ $category->products_count }}</td>
          <td>
            @if($category->is_active)
              <span class="badge bg-success">Active</span>
            @else
              <span class="badge bg-secondary">Inactive</span>
            @endif
          </td>
          <td>
            <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editCategoryModal{{ $category->id }}">Edit</button>
            <form method="POST" action="{{ route('admin.categories.destroy', $category->id) }}" class="d-inline" onsubmit="return confirm('Delete this category?')">
              @csrf
              @method('DELETE')
              <button class="btn btn-sm btn-outline-danger">Delete</button>
            </form>
          </td>
        </tr>
        <!-- Edit Modal -->
        <div class="modal fade" id="editCategoryModal{{ $category->id }}" tabindex="-1" aria-labelledby="editCategoryLabel{{ $category->id }}" aria-hidden="true">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="editCategoryLabel{{ $category->id }}">Edit Category</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <form method="POST" action="{{ route('admin.categories.update', $category->id) }}">
                @csrf
                @method('PUT')
                <div class="modal-body">
                  <div class="mb-3">
                    <label class="form-label">Name</label>
                    <input type="text" name="name" class="form-control" value="{{ $category->name }}" required>
                  </div>
                  <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control">{{ $category->description }}</textarea>
                  </div>
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="is_active" value="1" {{ $category->is_active ? 'checked' : '' }}>
                    <label class="form-check-label">Active</label>
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
        <tr><td colspan="6" class="text-center">No categories found.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <!-- Create Modal -->
  <div class="modal fade" id="createCategoryModal" tabindex="-1" aria-labelledby="createCategoryLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="createCategoryLabel">Create Category</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form method="POST" action="{{ route('admin.categories.store') }}">
          @csrf
          <div class="modal-body">
            <div class="mb-3">
              <label class="form-label">Name</label>
              <input type="text" name="name" class="form-control" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Description</label>
              <textarea name="description" class="form-control"></textarea>
            </div>
            <div class="mb-3">
              <label class="form-label">Icon (Bootstrap Icons class)</label>
              <input type="text" name="icon" class="form-control" placeholder="bi-tag" value="bi-tag">
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
