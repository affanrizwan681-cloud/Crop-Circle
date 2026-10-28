@extends('layouts.app')

@section('title', 'Manage Products & Stock — Farmer Portal')

@section('content')
<div class="bg-light py-4 border-bottom mb-4">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <a href="{{ route('farmer.dashboard') }}" class="text-success text-decoration-none small fw-bold">&larr; Back to Dashboard</a>
                <h1 class="h3 fw-bold mb-0">My Harvest Catalog & Inventory</h1>
            </div>
            <a href="{{ route('farmer.products.create') }}" class="btn btn-cropcircle btn-sm fw-bold">
                <i class="bi bi-plus-lg me-1"></i> Add New Product
            </a>
        </div>
    </div>
</div>

<div class="container pb-5">
    <div class="card card-custom border-0 shadow-sm overflow-hidden p-3">
        @if($products->isEmpty())
            <div class="text-center py-5 text-muted">
                <i class="bi bi-boxes display-4 mb-3 d-block opacity-50"></i>
                <h5 class="fw-bold">No products in your catalog</h5>
                <p class="small text-muted mb-3">Add your first agricultural product to start accepting customer reservations.</p>
                <a href="{{ route('farmer.products.create') }}" class="btn btn-cropcircle btn-sm">Add First Harvest</a>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="small text-muted border-bottom bg-light">
                        <tr>
                            <th>Product</th>
                            <th>Category</th>
                            <th>Price</th>
                            <th>Stock Level</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($products as $p)
                            <tr>
                                <td class="position-relative"><div class="d-flex align-items-center gap-3 py-1">
                                        <x-produce-image :image="$p->image" :category="$p->category" :alt="$p->name" class="rounded" style="width: 48px; height: 48px; object-fit: cover;" />
                                        <div>
                                            @if($p->is_moderated)
                                                <a href="{{ route('products.show', $p->slug) }}" class="fw-bold text-dark text-decoration-none stretched-link">{{ $p->name }}</a>
                                            @else
                                                <span class="fw-bold text-dark">{{ $p->name }}</span>
                                            @endif
                                            <span class="small text-muted d-block">Unit: {{ $p->unit }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td><span class="badge bg-light text-dark border">{{ $p->category->name }}</span></td>
                                <td class="fw-bold text-success">${{ number_format($p->price, 2) }}</td>
                                <td>
                                    <span class="fw-bold {{ $p->stock_quantity <= 5 ? 'text-danger' : 'text-dark' }}">
                                        {{ $p->stock_quantity }} {{ $p->unit }}
                                    </span>
                                </td>
                                <td>
                                    @if(!$p->is_moderated)
                                        <span class="badge bg-warning text-dark">Pending review</span>
                                    @elseif($p->status === 'available')
                                        <span class="badge bg-success">Available</span>
                                    @elseif($p->status === 'sold_out')
                                        <span class="badge bg-danger">Sold Out</span>
                                    @else
                                        <span class="badge bg-secondary">Unavailable</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('farmer.products.weeklyStock', $p->id) }}" class="btn btn-outline-info" title="Weekly Availability">
                                            <i class="bi bi-calendar-week"></i> Schedule
                                        </a>
                                        <a href="{{ route('farmer.products.edit', $p->id) }}" class="btn btn-outline-secondary" title="Edit Product">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="{{ route('farmer.products.destroy', $p->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this product permanently?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger" title="Delete">
                                                <i class="bi bi-trash3"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4 d-flex justify-content-center">
                {{ $products->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>
</div>
@endsection
