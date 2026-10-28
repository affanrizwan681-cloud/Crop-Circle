@extends('layouts.app')

@section('title', ($farmer->farmerProfile->farm_name ?? $farmer->name) . ' — CropCircle')

@section('content')
<div class="bg-light py-5 border-bottom mb-4">
    <div class="container">
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb small">
                <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-success text-decoration-none">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('farmers.index') }}" class="text-success text-decoration-none">Growers</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $farmer->farmerProfile->farm_name ?? $farmer->name }}</li>
            </ol>
        </nav>

        <div class="row align-items-center g-4">
            <div class="col-lg-8">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center fw-bold fs-2 shadow-sm" style="width: 75px; height: 75px;">
                        {{ strtoupper(substr($farmer->farmerProfile->farm_name ?? $farmer->name, 0, 1)) }}
                    </div>
                    <div>
                        <span class="badge bg-success bg-opacity-10 text-success fw-bold px-3 py-1 rounded-pill mb-1">
                            <i class="bi bi-shield-check me-1"></i> Verified Local Grower
                        </span>
                        <h1 class="display-6 fw-bold mb-0">{{ $farmer->farmerProfile->farm_name ?? $farmer->name }}</h1>
                        <div class="small text-muted"><i class="bi bi-person me-1"></i> {{ $farmer->farmerProfile->contact_person ?? $farmer->name }} • <i class="bi bi-geo-alt text-success me-1"></i> {{ $farmer->farmerProfile->city ?? 'Springfield' }}, {{ $farmer->farmerProfile->state ?? 'IL' }}</div>
                    </div>
                </div>

                <p class="text-secondary leading-relaxed mb-3">
                    {{ $farmer->farmerProfile->description }}
                </p>

                <!-- Actions: Chat & Favorite -->
                <div class="d-flex flex-wrap gap-2 pt-2">
                    @auth
                        @if(auth()->id() !== $farmer->id)
                            <a href="{{ route('chat.start', $farmer->id) }}" class="btn btn-cropcircle rounded-pill px-4 fw-bold">
                                <i class="bi bi-chat-dots-fill me-1"></i> Message Farmer
                            </a>

                            <form action="{{ route('favorites.toggle') }}" method="POST" class="d-inline">
                                @csrf
                                <input type="hidden" name="farmer_id" value="{{ $farmer->id }}">
                                <button type="submit" class="btn btn-outline-secondary rounded-pill px-3">
                                    <i class="bi {{ $isFavorite ? 'bi-heart-fill text-danger' : 'bi-heart' }} me-1"></i>
                                    {{ $isFavorite ? 'Saved to Favorites' : 'Follow Farm' }}
                                </button>
                            </form>
                        @endif
                    @else
                        <a href="{{ route('login') }}" class="btn btn-outline-success rounded-pill px-3 fw-bold">
                            <i class="bi bi-chat-dots me-1"></i> Sign in to Message Farmer
                        </a>
                    @endauth
                </div>
            </div>

            <!-- Operating & Pickup Info Card -->
            <div class="col-lg-4">
                <div class="card card-custom p-4 border-0 shadow-sm bg-white">
                    <h6 class="fw-bold mb-3 border-bottom pb-2"><i class="bi bi-clock-history text-success me-1"></i> Stall Pickup Schedule</h6>
                    <div class="small mb-2 d-flex justify-content-between">
                        <span class="text-muted">Standard Pickup Hours:</span>
                        <span class="fw-bold">{{ date('g:i A', strtotime($farmer->farmerProfile->pickup_start_time ?? '08:00')) }} - {{ date('g:i A', strtotime($farmer->farmerProfile->pickup_end_time ?? '17:00')) }}</span>
                    </div>
                    <div class="small mb-3 d-flex justify-content-between">
                        <span class="text-muted">Rating:</span>
                        <span class="fw-bold text-warning">
                            ★ {{ number_format($farmer->averageRating(), 1) }} ({{ $farmer->reviewCount() }} reviews)
                        </span>
                    </div>

                    <h6 class="fw-bold mb-2 small text-uppercase text-muted border-top pt-3">Attending Markets:</h6>
                    <ul class="list-unstyled small mb-0 d-flex flex-column gap-2">
                        @forelse($farmer->farmerProfile->markets as $m)
                            <li class="p-2 bg-light rounded-2 d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="fw-bold d-block">{{ $m->name }}</span>
                                    <span class="text-muted" style="font-size: 0.75rem;">{{ $m->pivot->assigned_stall ?? 'Stall Assigned' }}</span>
                                </div>
                                <a href="{{ route('markets.show', $m->id) }}" class="text-success fw-bold text-decoration-none">Stall &rarr;</a>
                            </li>
                        @empty
                            <li class="text-muted">Direct farm pickup only.</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container pb-5">
    <!-- Available Harvests from this Farm -->
    <div class="mb-5">
        <h4 class="fw-bold mb-4"><i class="bi bi-boxes me-2 text-success"></i> Available Harvests & Products</h4>
        <div class="row g-4">
            @forelse($products as $prod)
                <div class="col-lg-3 col-md-6">
                    <div class="card card-custom card-hover product-card-click position-relative h-100 border-0 shadow-sm p-3 d-flex flex-column">
                        <div class="bg-light rounded-3 d-flex align-items-center justify-content-center overflow-hidden mb-2" style="height: 160px;">
                            <x-produce-image :image="$prod->image" :category="$prod->category" :alt="$prod->name" class="w-100 h-100 object-fit-cover rounded-3" />
                        </div>
                        <span class="badge bg-success bg-opacity-10 text-success align-self-start mb-1 small">{{ $prod->category->name }}</span>
                        <h6 class="fw-bold mb-2">
                            <a href="{{ route('products.show', $prod->slug) }}" class="text-dark text-decoration-none text-truncate d-block stretched-link">
                                {{ $prod->name }}
                            </a>
                        </h6>
                        <div class="mt-auto d-flex justify-content-between align-items-center pt-2 border-top">
                            <div>
                                <span class="fw-bold text-success fs-5">${{ number_format($prod->price, 2) }}</span>
                                <span class="small text-muted">/ {{ $prod->unit }}</span>
                            </div>
                            <form action="{{ route('cart.add', $prod->id) }}" method="POST" class="position-relative z-2">
                                @csrf
                                <input type="hidden" name="quantity" value="1">
                                <button type="submit" class="btn btn-sm btn-outline-success rounded-pill px-3 fw-bold">
                                    <i class="bi bi-cart-plus me-1"></i> Add
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-muted text-center py-4">No products listed by this grower right now.</div>
            @endforelse
        </div>

        <div class="mt-4">
            {{ $products->links('pagination::bootstrap-5') }}
        </div>
    </div>

    @if($productAds->isNotEmpty())
    <section class="mb-5" aria-labelledby="farmer-offers-title">
        <div class="d-flex flex-wrap justify-content-between align-items-end gap-2 mb-4">
            <div>
                <span class="text-success fw-bold text-uppercase small">Limited-time farm offers</span>
                <h2 class="h3 fw-bold mb-1" id="farmer-offers-title">Featured Offers</h2>
                <p class="text-muted mb-0">Special prices from {{ $farmer->farmerProfile->farm_name ?? $farmer->name }}</p>
            </div>
        </div>
        <div class="row g-4">
            @foreach($productAds as $ad)
                @php($savingPercent = $ad->original_price > 0 ? round((($ad->original_price - $ad->discount_price) / $ad->original_price) * 100) : 0)
                <div class="col-md-6 col-xl-4">
                    <article class="card product-card-click position-relative h-100 overflow-hidden">
                        <div class="position-relative">
                            <img src="{{ $ad->display_image }}" alt="{{ $ad->title }}" class="card-img-top" style="height: 205px; object-fit: cover;">
                            <span class="badge bg-warning text-dark position-absolute top-0 start-0 m-3 rounded-pill">Farm offer</span>
                            @if($savingPercent > 0)
                                <span class="badge bg-success position-absolute top-0 end-0 m-3 rounded-pill">Save {{ $savingPercent }}%</span>
                            @endif
                        </div>
                        <div class="card-body d-flex flex-column">
                            <div class="small text-success fw-semibold mb-1">{{ $ad->product->category->name ?? 'Fresh from the farm' }}</div>
                            <h3 class="h5 fw-bold mb-2"><a href="{{ route('products.show', $ad->product->slug) }}" class="stretched-link text-dark text-decoration-none">{{ $ad->title }}</a></h3>
                            <p class="small text-muted mb-3">{{ \Illuminate\Support\Str::limit($ad->description, 120) }}</p>
                            <div class="d-flex align-items-baseline flex-wrap gap-2 mt-auto pt-3 border-top">
                                <span class="fs-4 fw-bold text-success">${{ number_format($ad->discount_price, 2) }}</span>
                                <del class="small text-muted">${{ number_format($ad->original_price, 2) }}</del>
                                <span class="small text-muted">per {{ $ad->product->unit }}</span>
                            </div>
                        </div>
                    </article>
                </div>
            @endforeach
        </div>
    </section>
    @endif
    <!-- Customer Reviews for this Farm -->
    <div class="card card-custom p-4 border-0 shadow-sm">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold mb-0">Customer Reviews ({{ $farmer->reviewCount() }})</h4>
            <div class="text-warning fw-bold">
                ★ {{ number_format($farmer->averageRating(), 1) }} / 5.0
            </div>
        </div>

        <div class="border-top pt-3">
            @forelse($reviews as $rev)
                <div class="mb-4 pb-3 border-bottom">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <div>
                            <span class="fw-bold">{{ $rev->customer->name }}</span>
                            @if($rev->product)
                                <span class="text-muted small ms-2">(Reviewed: <em>{{ $rev->product->name }}</em>)</span>
                            @endif
                        </div>
                        <span class="small text-muted">{{ $rev->created_at->format('M d, Y') }}</span>
                    </div>

                    <div class="text-warning small mb-2">
                        @for($i = 1; $i <= 5; $i++)
                            <i class="bi {{ $i <= $rev->rating ? 'bi-star-fill' : 'bi-star' }}"></i>
                        @endfor
                    </div>

                    <p class="small text-secondary mb-2">{{ $rev->comment }}</p>

                    @if($rev->farmer_reply)
                        <div class="p-3 bg-light rounded-3 ms-4 border-start border-success border-3">
                            <div class="small fw-bold text-success mb-1">Response from {{ $farmer->farmerProfile->farm_name ?? $farmer->name }}:</div>
                            <div class="small text-muted">{{ $rev->farmer_reply }}</div>
                        </div>
                    @endif
                </div>
            @empty
                <div class="text-center py-4 text-muted small">No reviews submitted yet for this farm.</div>
            @endforelse
        </div>

        <div class="mt-3">
            {{ $reviews->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>
@endsection
