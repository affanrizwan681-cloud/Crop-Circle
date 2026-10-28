@extends('layouts.app')

@section('title', $product->name . ' — CropCircle')

@section('content')
<div class="container py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-success text-decoration-none">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('products.index') }}" class="text-success text-decoration-none">Catalog</a></li>
            <li class="breadcrumb-item"><a href="{{ route('products.index', ['category' => $product->category->slug]) }}" class="text-success text-decoration-none">{{ $product->category->name }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $product->name }}</li>
        </ol>
    </nav>

    <div class="row g-5 mb-5">
        <!-- Product Image & Gallery -->
        <div class="col-lg-6">
            <div class="card card-custom p-3 border-0 shadow-sm overflow-hidden mb-3">
                <div class="bg-light rounded-3 d-flex align-items-center justify-content-center overflow-hidden" style="min-height: 380px; max-height: 440px;">
                    <x-produce-image id="mainProductImg" :image="$product->image" :category="$product->category" :alt="$product->name" class="w-100 h-100 object-fit-cover rounded-3" />
                </div>

                @if($product->images->isNotEmpty())
                    <div class="d-flex gap-2 mt-3 overflow-x-auto pb-1">
                        @if($product->image)
                            <img src="{{ (\Illuminate\Support\Str::startsWith($product->image, ['http://', 'https://']) ? $product->image : (\Illuminate\Support\Str::startsWith($product->image, ['http://', 'https://']) ? $product->image : asset('storage/' . $product->image))) }}" class="rounded border p-1" style="width: 70px; height: 70px; cursor: pointer; object-fit: cover;" onclick="document.getElementById('mainProductImg').src = this.src;">
                        @endif
                        @foreach($product->images as $gImg)
                            <img src="{{ (\Illuminate\Support\Str::startsWith($gImg->image_path, ['http://', 'https://']) ? $gImg->image_path : (\Illuminate\Support\Str::startsWith($gImg->image_path, ['http://', 'https://']) ? $gImg->image_path : asset('storage/' . $gImg->image_path))) }}" class="rounded border p-1" style="width: 70px; height: 70px; cursor: pointer; object-fit: cover;" onclick="document.getElementById('mainProductImg').src = this.src;">
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Weekly Availability Schedule -->
            <div class="card card-custom p-4 border-0 shadow-sm">
                <h6 class="fw-bold mb-3"><i class="bi bi-calendar3 me-1 text-success"></i> Weekly Harvest Availability</h6>
                <div class="table-responsive">
                    <table class="table table-sm table-borderless small mb-0">
                        <thead>
                            <tr class="text-muted border-bottom">
                                <th>Day</th>
                                <th>Status</th>
                                <th class="text-end">Batch Quota</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach(['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'] as $day)
                                @php
                                    $dayAvail = $product->availabilities->firstWhere('day_of_week', $day);
                                @endphp
                                <tr>
                                    <td class="fw-semibold">{{ $day }}</td>
                                    <td>
                                        @if($dayAvail && $dayAvail->is_available && $dayAvail->remainingQuantity() > 0)
                                            <span class="badge bg-success bg-opacity-10 text-success">Available for Pickup</span>
                                        @else
                                            <span class="badge bg-secondary bg-opacity-10 text-secondary">No Harvest</span>
                                        @endif
                                    </td>
                                    <td class="text-end text-muted">
                                        {{ $dayAvail ? $dayAvail->remainingQuantity() : 0 }} {{ $product->unit }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Product Information & Purchase -->
        <div class="col-lg-6">
            <span class="badge bg-success bg-opacity-10 text-success fw-bold px-3 py-1 rounded-pill mb-2">
                {{ $product->category->name }}
            </span>
            <h1 class="display-6 fw-bold text-dark mb-2">{{ $product->name }}</h1>

            <div class="d-flex align-items-center gap-3 mb-3">
                <div class="text-warning small">
                    @for($i = 1; $i <= 5; $i++)
                        <i class="bi {{ $i <= round($product->averageRating()) ? 'bi-star-fill' : 'bi-star' }}"></i>
                    @endfor
                    <span class="text-dark fw-bold ms-1">{{ number_format($product->averageRating(), 1) }}</span>
                </div>
                <span class="text-muted small">•</span>
                <a href="#reviewsSection" class="text-muted small text-decoration-none">
                    {{ $product->reviewCount() }} customer reviews
                </a>
            </div>

            <!-- Price & Stock Badge -->
            <div class="p-3 bg-light rounded-3 d-flex justify-content-between align-items-center mb-4 border">
                <div>
                    <span class="display-6 fw-extrabold text-success">${{ number_format($product->effectivePrice(), 2) }}</span>
                    <span class="text-muted">/ {{ $product->unit }}</span>
                </div>
                <div>
                    @if($product->stock_quantity > 0)
                        <span class="badge bg-success fs-6 fw-bold px-3 py-2">
                            <i class="bi bi-check-circle me-1"></i> {{ $product->stock_quantity }} {{ $product->unit }} In Stock
                        </span>
                    @else
                        <span class="badge bg-danger fs-6 fw-bold px-3 py-2">Sold Out</span>
                    @endif
                </div>
            </div>

            <!-- Description -->
            <div class="mb-4">
                <h6 class="fw-bold mb-2">About this Harvest</h6>
                <p class="text-secondary leading-relaxed">
                    {{ $product->description }}
                </p>
            </div>

            <!-- Add to Cart Form -->
            @if($product->stock_quantity > 0)
                <form action="{{ route('cart.add', $product->id) }}" method="POST" class="card card-custom p-4 border-0 shadow-sm mb-4">
                    @csrf
                    <div class="row g-3 align-items-center">
                        <div class="col-sm-4">
                            <label class="form-label small fw-bold">Quantity ({{ $product->unit }})</label>
                            <input type="number" name="quantity" class="form-control" value="1" min="1" max="{{ $product->stock_quantity }}" required>
                        </div>
                        <div class="col-sm-8 pt-sm-4">
                            <button type="submit" class="btn btn-cropcircle w-100 py-2 fw-bold">
                                <i class="bi bi-basket2-fill me-1"></i> Add to Pre-Order Cart
                            </button>
                        </div>
                    </div>
                </form>
            @endif

            <!-- Wishlist & Favorite Button -->
            <div class="d-flex gap-2 mb-4">
                @auth
                    <form action="{{ route('favorites.toggle') }}" method="POST" class="d-inline">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        <button type="submit" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                            <i class="bi {{ $isFavorite ? 'bi-heart-fill text-danger' : 'bi-heart' }} me-1"></i>
                            {{ $isFavorite ? 'Saved to Favorites' : 'Add to Favorites' }}
                        </button>
                    </form>
                @endauth
            </div>

            <!-- Farmer Card -->
            <div class="card card-custom p-4 border-0 shadow-sm bg-light">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center fw-bold fs-4" style="width: 50px; height: 50px;">
                            {{ strtoupper(substr($product->farmer->farmerProfile->farm_name ?? $product->farmer->name, 0, 1)) }}
                        </div>
                        <div>
                            <h6 class="fw-bold mb-0">
                                <a href="{{ route('farmers.show', $product->farmer_id) }}" class="text-dark text-decoration-none">
                                    {{ $product->farmer->farmerProfile->farm_name ?? $product->farmer->name }}
                                </a>
                            </h6>
                            <span class="small text-muted"><i class="bi bi-geo-alt me-1 text-success"></i>{{ $product->farmer->farmerProfile->city ?? 'Local' }}, {{ $product->farmer->farmerProfile->state ?? 'IL' }}</span>
                        </div>
                    </div>

                    @auth
                        @if(auth()->id() !== $product->farmer_id)
                            <a href="{{ route('chat.start', $product->farmer_id) }}?product_id={{ $product->id }}" class="btn btn-sm btn-outline-success rounded-pill px-3 fw-bold">
                                <i class="bi bi-chat-dots me-1"></i> Message Farmer
                            </a>
                        @endif
                    @endauth
                </div>

                <div class="small text-secondary mb-3">
                    {{ Str::limit($product->farmer->farmerProfile->description ?? 'Certified local farm committed to sustainable practices.', 140) }}
                </div>

                <div class="pt-2 border-top small text-muted d-flex justify-content-between">
                    <span><i class="bi bi-clock me-1 text-success"></i> Pickup Hours: {{ date('g:i A', strtotime($product->farmer->farmerProfile->pickup_start_time ?? '08:00')) }} - {{ date('g:i A', strtotime($product->farmer->farmerProfile->pickup_end_time ?? '17:00')) }}</span>
                    <a href="{{ route('farmers.show', $product->farmer_id) }}" class="text-success fw-bold text-decoration-none">View Stall & Profile &rarr;</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Customer Reviews Section -->
    <div class="card card-custom p-4 p-md-5 border-0 shadow-sm mb-5" id="reviewsSection">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-1">Customer Reviews</h4>
                <p class="text-muted small mb-0">Verified feedback from customers who purchased this harvest</p>
            </div>
            <div class="text-end">
                <div class="display-6 fw-bold text-warning">{{ number_format($product->averageRating(), 1) }}</div>
                <div class="small text-muted">out of 5.0 ({{ $product->reviews->count() }} reviews)</div>
            </div>
        </div>

        <div class="border-top pt-4">
            @forelse($product->reviews as $review)
                <div class="mb-4 pb-4 border-bottom">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div>
                            <span class="fw-bold text-dark">{{ $review->customer->name }}</span>
                            <span class="badge bg-success bg-opacity-10 text-success ms-2 small">
                                <i class="bi bi-patch-check-fill me-1"></i> Verified Purchase
                            </span>
                        </div>
                        <div class="small text-muted">{{ $review->created_at->format('M d, Y') }}</div>
                    </div>

                    <div class="text-warning small mb-2">
                        @for($i = 1; $i <= 5; $i++)
                            <i class="bi {{ $i <= $review->rating ? 'bi-star-fill' : 'bi-star' }}"></i>
                        @endfor
                    </div>

                    <p class="text-secondary small mb-2">
                        {{ $review->comment }}
                    </p>

                    @if($review->farmer_reply)
                        <div class="p-3 bg-light rounded-3 ms-4 border-start border-success border-3">
                            <div class="small fw-bold text-success mb-1">
                                <i class="bi bi-reply-fill me-1"></i> Farmer Response:
                            </div>
                            <div class="small text-muted">{{ $review->farmer_reply }}</div>
                        </div>
                    @endif
                </div>
            @empty
                <div class="text-center py-4 text-muted small">
                    <i class="bi bi-chat-square-heart display-6 d-block mb-2 text-muted opacity-50"></i>
                    No customer reviews yet. Be the first to try this harvest and leave feedback!
                </div>
            @endforelse
        </div>
    </div>

    <!-- Related Products -->
    @if($relatedProducts->isNotEmpty())
        <div class="mb-4">
            <h4 class="fw-bold mb-4">More Harvests in {{ $product->category->name }}</h4>
            <div class="row g-4">
                @foreach($relatedProducts as $rel)
                    <div class="col-lg-3 col-md-6">
                        <div class="card card-custom card-hover product-card-click position-relative h-100 border-0 shadow-sm p-3">
                            <div class="bg-light rounded-3 d-flex align-items-center justify-content-center mb-2" style="height: 140px;">
                                <i class="bi {{ $rel->category->icon ?? 'bi-bag' }} fs-1 text-success"></i>
                            </div>
                            <h6 class="fw-bold mb-1">
                                <a href="{{ route('products.show', $rel->slug) }}" class="text-dark text-decoration-none text-truncate d-block stretched-link">
                                    {{ $rel->name }}
                                </a>
                            </h6>
                            <div class="d-flex justify-content-between align-items-center mt-auto pt-2 border-top">
                                <span class="fw-bold text-success">${{ number_format($rel->price, 2) }} / {{ $rel->unit }}</span>
                                <a href="{{ route('products.show', $rel->slug) }}" class="btn btn-sm btn-outline-success rounded-pill px-3 position-relative z-2">View</a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection
