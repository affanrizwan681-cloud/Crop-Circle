@extends('layouts.app')

@section('title', 'Browse Fresh Products & Goods — CropCircle')

@section('content')
<div class="bg-light py-4 border-bottom mb-4">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h1 class="h3 fw-bold mb-1">Seasonal Harvest Catalog</h1>
                <p class="text-muted small mb-0">Browse fresh agricultural products from local certified growers</p>
            </div>
            <div class="col-md-6 text-md-end mt-3 mt-md-0">
                <span class="badge bg-success bg-opacity-10 text-success fw-bold px-3 py-2 rounded-pill">
                    {{ $products->total() }} Products Available
                </span>
            </div>
        </div>
    </div>
</div>

<div class="container pb-5">
    <div class="row g-4">
        <!-- Sidebar Filters -->
        <div class="col-lg-3">
            <div class="card card-custom p-4 border-0 shadow-sm sticky-top" style="top: 85px;">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-bold mb-0"><i class="bi bi-funnel me-1 text-success"></i> Filter Harvests</h6>
                    <a href="{{ route('products.index') }}" class="small text-danger text-decoration-none">Reset</a>
                </div>

                <form action="{{ route('products.index') }}" method="GET">
                    <!-- Preserve search & sort -->
                    @if(request('q'))
                        <input type="hidden" name="q" value="{{ request('q') }}">
                    @endif
                    @if(request('sort'))
                        <input type="hidden" name="sort" value="{{ request('sort') }}">
                    @endif

                    <!-- Category Filter -->
                    <div class="mb-4">
                        <label class="form-label small fw-bold text-uppercase text-muted">Category</label>
                        <select name="category" class="form-select form-select-sm" onchange="this.form.submit()">
                            <option value="">All Categories</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->slug }}" {{ request('category') === $cat->slug ? 'selected' : '' }}>
                                    {{ $cat->name }} ({{ $cat->products_count }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Market Filter -->
                    <div class="mb-4">
                        <label class="form-label small fw-bold text-uppercase text-muted">Farmers Market</label>
                        <select name="market_id" class="form-select form-select-sm" onchange="this.form.submit()">
                            <option value="">All Markets</option>
                            @foreach($markets as $m)
                                <option value="{{ $m->id }}" {{ request('market_id') == $m->id ? 'selected' : '' }}>
                                    {{ $m->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Farmer Filter -->
                    <div class="mb-4">
                        <label class="form-label small fw-bold text-uppercase text-muted">Specific Producer</label>
                        <select name="farmer_id" class="form-select form-select-sm" onchange="this.form.submit()">
                            <option value="">All Farmers</option>
                            @foreach($farmers as $f)
                                <option value="{{ $f->user_id }}" {{ request('farmer_id') == $f->user_id ? 'selected' : '' }}>
                                    {{ $f->farm_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Pickup Day Filter -->
                    <div class="mb-4">
                        <label class="form-label small fw-bold text-uppercase text-muted">Pickup Day</label>
                        <select name="day" class="form-select form-select-sm" onchange="this.form.submit()">
                            <option value="">Any Day</option>
                            @foreach(['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'] as $day)
                                <option value="{{ $day }}" {{ request('day') === $day ? 'selected' : '' }}>
                                    {{ $day }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- In Stock Checkbox -->
                    <div class="mb-4 form-check">
                        <input class="form-check-input" type="checkbox" name="availability" value="in_stock" id="checkInStock" {{ request('availability') === 'in_stock' ? 'checked' : '' }} onchange="this.form.submit()">
                        <label class="form-check-label small fw-semibold" for="checkInStock">
                            In Stock Only
                        </label>
                    </div>

                    <!-- Price Range -->
                    <div class="mb-4">
                        <label class="form-label small fw-bold text-uppercase text-muted">Price Range ($)</label>
                        <div class="d-flex gap-2 align-items-center">
                            <input type="number" step="0.5" name="min_price" class="form-control form-control-sm" placeholder="Min" value="{{ request('min_price') }}">
                            <span class="text-muted">-</span>
                            <input type="number" step="0.5" name="max_price" class="form-control form-control-sm" placeholder="Max" value="{{ request('max_price') }}">
                        </div>
                    </div>

                    <button type="submit" class="btn btn-sm btn-cropcircle w-100">Apply Filters</button>
                </form>
            </div>
        </div>

        <!-- Catalog Grid -->
        <div class="col-lg-9">
            <!-- Search & Sort Topbar -->
            <div class="card card-custom p-3 border-0 shadow-sm mb-4">
                <form action="{{ route('products.index') }}" method="GET" class="row g-2 align-items-center">
                    <!-- Preserve existing filters -->
                    @if(request('category')) <input type="hidden" name="category" value="{{ request('category') }}"> @endif
                    @if(request('market_id')) <input type="hidden" name="market_id" value="{{ request('market_id') }}"> @endif
                    @if(request('farmer_id')) <input type="hidden" name="farmer_id" value="{{ request('farmer_id') }}"> @endif
                    @if(request('day')) <input type="hidden" name="day" value="{{ request('day') }}"> @endif
                    @if(request('availability')) <input type="hidden" name="availability" value="{{ request('availability') }}"> @endif

                    <div class="col-md-7">
                        <div class="input-group">
                            <span class="input-group-text bg-transparent border-end-0 text-muted"><i class="bi bi-search text-success"></i></span>
                            <input type="text" name="q" class="form-control border-start-0" placeholder="Search by harvest name or description..." value="{{ request('q') }}">
                            <button class="btn btn-outline-success" type="submit">Search</button>
                        </div>
                    </div>
                    <div class="col-md-5 d-flex align-items-center gap-2 justify-content-md-end">
                        <label class="small text-muted text-nowrap">Sort by:</label>
                        <select name="sort" class="form-select form-select-sm" onchange="this.form.submit()" style="max-width: 180px;">
                            <option value="newest" {{ request('sort') === 'newest' ? 'selected' : '' }}>Newest</option>
                            <option value="price_asc" {{ request('sort') === 'price_asc' ? 'selected' : '' }}>Price: Low to High</option>
                            <option value="price_desc" {{ request('sort') === 'price_desc' ? 'selected' : '' }}>Price: High to Low</option>
                            <option value="popular" {{ request('sort') === 'popular' ? 'selected' : '' }}>Most Popular</option>
                            <option value="name_asc" {{ request('sort') === 'name_asc' ? 'selected' : '' }}>Name: A-Z</option>
                        </select>
                    </div>
                </form>
            </div>

            <!-- Products Cards -->
            <div class="row g-4">
                @forelse($products as $product)
                    <div class="col-xl-4 col-md-6">
                        <div class="card card-custom card-hover product-card-click position-relative h-100 d-flex flex-column border-0 shadow-sm overflow-hidden">
                            <div class="position-relative bg-light d-flex align-items-center justify-content-center" style="height: 190px;">
                                <x-produce-image :image="$product->image" :category="$product->category" :alt="$product->name" class="w-100 h-100 object-fit-cover" />
                                <span class="position-absolute top-0 start-0 m-2 badge bg-success fw-bold">
                                    {{ $product->category->name }}
                                </span>
                                @if($product->stock_quantity <= 0)
                                    <span class="position-absolute top-0 end-0 m-2 badge bg-danger">Sold Out</span>
                                @else
                                    <span class="position-absolute top-0 end-0 m-2 badge bg-light text-dark border">
                                        {{ $product->stock_quantity }} {{ $product->unit }} left
                                    </span>
                                @endif
                            </div>

                            <div class="card-body p-3 d-flex flex-column">
                                <div class="small text-muted mb-1 d-flex align-items-center gap-1">
                                    <i class="bi bi-shop text-success"></i>
                                    <a href="{{ route('farmers.show', $product->farmer_id) }}" class="text-muted text-decoration-none text-truncate position-relative z-2">
                                        {{ $product->farmer->farmerProfile->farm_name ?? $product->farmer->name }}
                                    </a>
                                </div>
                                <h6 class="card-title fw-bold text-dark mb-1">
                                    <a href="{{ route('products.show', $product->slug) }}" class="text-dark text-decoration-none text-truncate d-block stretched-link">
                                        {{ $product->name }}
                                    </a>
                                </h6>
                                <p class="small text-secondary mb-3 text-truncate-2" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; min-height: 38px;">
                                    {{ $product->description }}
                                </p>

                                <div class="mt-auto d-flex justify-content-between align-items-center pt-2 border-top">
                                    <div>
                                        <span class="fs-5 fw-extrabold text-success">${{ number_format($product->effectivePrice(), 2) }}</span>
                                        <span class="small text-muted">/ {{ $product->unit }}</span>
                                    </div>

                                    @if($product->stock_quantity > 0)
                                        <form action="{{ route('cart.add', $product->id) }}" method="POST" class="position-relative z-2">
                                            @csrf
                                            <input type="hidden" name="quantity" value="1">
                                            <button type="submit" class="btn btn-sm btn-outline-success fw-bold rounded-pill px-3">
                                                <i class="bi bi-cart-plus me-1"></i> Add
                                            </button>
                                        </form>
                                    @else
                                        <button class="btn btn-sm btn-secondary disabled rounded-pill px-3">Sold Out</button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 py-5 text-center">
                        <i class="bi bi-basket text-muted display-4 mb-3 d-block"></i>
                        <h5 class="fw-bold text-muted">No products found matching your criteria</h5>
                        <p class="small text-muted">Try clearing some filters or searching for another term.</p>
                        <a href="{{ route('products.index') }}" class="btn btn-sm btn-cropcircle">Reset All Filters</a>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            <div class="mt-5 d-flex justify-content-center">
                {{ $products->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>
@endsection
