@extends('layouts.admin')

@section('admin-content')
<div class="container-fluid py-4">
  <h1 class="mb-4">Products Management</h1>
  <div class="table-responsive">
    <table class="table table-hover align-middle">
      <thead class="table-light">
        <tr>
          <th>ID</th>
          <th>Image</th>
          <th>Name</th>
          <th>Farmer</th>
          <th>Category</th>
          <th>Price</th>
          <th>Moderated</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($products as $product)
        <tr>
          <td>{{ $product->id }}</td>
          <td><x-produce-image :image="$product->image" :category="$product->category" :alt="$product->name" class="rounded" style="width: 54px; height: 54px; object-fit: cover;" /></td>
          <td>{{ $product->name }}</td>
          <td>{{ $product->farmer->name ?? 'N/A' }}</td>
          <td>{{ $product->category->name ?? 'N/A' }}</td>
          <td>${{ number_format($product->price, 2) }}</td>
          <td>
            @if($product->is_moderated)
              <span class="badge bg-success">Approved and visible</span>
            @else
              <span class="badge bg-warning text-dark">Awaiting review / hidden</span>
            @endif
          </td>
          <td>
            <form method="POST" action="{{ route('admin.products.toggleModeration', $product->id) }}" class="d-inline">
              @csrf
              <button class="btn btn-sm {{ $product->is_moderated ? 'btn-outline-danger' : 'btn-primary' }}" onclick="return confirm('{{ $product->is_moderated ? 'Hide' : 'Approve' }} {{ $product->name }}?')">
                {{ $product->is_moderated ? 'Hide listing' : 'Approve listing' }}
              </button>
            </form>
          </td>
        </tr>
        @empty
        <tr><td colspan="8" class="text-center">No products found.</td></tr>
        @endforelse
      </tbody>
    </table>
    {{ $products->links() }}
  </div>
</div>
@endsection
