@extends('layouts.app')

@section('title', 'My Saved Favorites — CropCircle')

@section('content')
<div class="container py-5">
    <h2 class="fw-bold mb-1"><i class="bi bi-heart-fill text-danger me-2"></i> My Saved Favorites</h2>
    <p class="text-muted small mb-4">Quickly re-order your favorite harvests and follow your trusted local growers</p>

    <!-- Nav Tabs -->
    <ul class="nav nav-pills mb-4" id="favTabs" role="tablist">
        <li class="nav-item">
            <button class="nav-link active fw-bold" id="products-tab" data-bs-toggle="pill" data-bs-target="#favProducts" type="button">
                Favorite Products ({{ $favoriteProducts->count() }})
            </button>
        </li>
        <li class="nav-item ms-2">
            <button class="nav-link fw-bold" id="farmers-tab" data-bs-toggle="pill" data-bs-target="#favFarmers" type="button">
                Followed Farms ({{ $favoriteFarmers->count() }})
            </button>
        </li>
    </ul>

    <div class="tab-content" id="favTabContent">
        <!-- Favorite Products Tab -->
        <div class="tab-pane fade show active" id="favProducts" role="tabpanel">
            <div class="row g-4">
                @forelse($favoriteProducts as $fav)
                    @php $product = $fav->product; @endphp
                    @if($product)
                        <div class="col-lg-3 col-md-6">
                            <div class="card card-custom card-hover product-card-click position-relative h-100 border-0 shadow-sm p-3 d-flex flex-column">
                                <div class="bg-light rounded-3 d-flex align-items-center justify-content-center mb-2" style="height: 150px;">
                                    <i class="bi {{ $product->category->icon ?? 'bi-bag' }} fs-1 text-success"></i>
                                </div>
                                <div class="small text-muted mb-1">{{ $product->farmer->farmerProfile->farm_name ?? $product->farmer->name }}</div>
                                <h6 class="fw-bold mb-2">
                                    <a href="{{ route('products.show', $product->slug) }}" class="text-dark text-decoration-none text-truncate d-block stretched-link">
                                        {{ $product->name }}
                                    </a>
                                </h6>
                                <div class="mt-auto d-flex justify-content-between align-items-center pt-2 border-top">
                                    <span class="fw-bold text-success">${{ number_format($product->price, 2) }} / {{ $product->unit }}</span>
                                    <div class="d-flex gap-1 position-relative z-2">
                                        <form action="{{ route('cart.add', $product->id) }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="quantity" value="1">
                                            <button type="submit" class="btn btn-sm btn-outline-success rounded-pill px-2" title="Add to Cart">
                                                <i class="bi bi-cart-plus"></i>
                                            </button>
                                        </form>
                                        <form action="{{ route('favorites.toggle') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                                            <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-2" title="Remove Favorite">
                                                <i class="bi bi-trash3"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                @empty
                    <div class="col-12 py-5 text-center text-muted">
                        <i class="bi bi-heart display-4 text-muted opacity-50 mb-2 d-block"></i>
                        No saved products yet. Click the heart icon on any product to save it here!
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Followed Farmers Tab -->
        <div class="tab-pane fade" id="favFarmers" role="tabpanel">
            <div class="row g-4">
                @forelse($favoriteFarmers as $fav)
                    @php $f = $fav->farmer; @endphp
                    @if($f && $f->farmerProfile)
                        <div class="col-lg-4 col-md-6">
                            <div class="card card-custom card-hover p-4 border-0 shadow-sm h-100 d-flex flex-column">
                                <div class="d-flex align-items-center gap-3 mb-3">
                                    <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center fw-bold fs-3" style="width: 55px; height: 55px;">
                                        {{ strtoupper(substr($f->farmerProfile->farm_name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <h5 class="fw-bold mb-0">
                                            <a href="{{ route('farmers.show', $f->id) }}" class="text-dark text-decoration-none">
                                                {{ $f->farmerProfile->farm_name }}
                                            </a>
                                        </h5>
                                        <span class="small text-muted"><i class="bi bi-geo-alt text-success me-1"></i>{{ $f->farmerProfile->city }}</span>
                                    </div>
                                </div>

                                <p class="small text-secondary mb-4 flex-grow-1">
                                    {{ Str::limit($f->farmerProfile->description, 110) }}
                                </p>

                                <div class="mt-auto d-flex gap-2">
                                    <a href="{{ route('farmers.show', $f->id) }}" class="btn btn-sm btn-cropcircle flex-grow-1">
                                        View Stall Products
                                    </a>
                                    <form action="{{ route('favorites.toggle') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="farmer_id" value="{{ $f->id }}">
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Unfollow Farm">
                                            <i class="bi bi-heartbreak"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endif
                @empty
                    <div class="col-12 py-5 text-center text-muted">
                        <i class="bi bi-shop display-4 text-muted opacity-50 mb-2 d-block"></i>
                        No followed growers yet. Browse our farmers directory and follow your favorites!
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
