@extends('layouts.app')

@section('title', 'CropCircle — Connecting Farmers & Community Markets')

@section('content')
<!-- Home Hero -->
@push('styles')
<style>
    .cc-home-hero {
        --cc-hero-green: #082d1d;
        position: relative;
        isolation: isolate;
        overflow: hidden;
        min-height: clamp(600px, calc(100vh - 76px), 760px);
        color: #fff;
        background: radial-gradient(ellipse at 16% 42%, #124a2d 0, #082d1d 44%, #061f15 100%);
    }
    .cc-home-hero__media {
        position: absolute;
        z-index: -1;
        inset: 0 0 0 auto;
        width: min(64%, 1120px);
        overflow: hidden;
    }
    .cc-home-hero__media::after {
        position: absolute;
        inset: 0;
        content: '';
        background: linear-gradient(90deg, #082d1d 0%, rgba(8,45,29,.94) 8%, rgba(8,45,29,.58) 31%, rgba(8,45,29,.04) 72%), linear-gradient(0deg, rgba(3,20,13,.22), transparent 45%);
    }
    .cc-home-hero__media img {
        display: block;
        width: 100%;
        height: 100%;
        object-fit: cover;
        object-position: center right;
        animation: cc-hero-image-drift 20s ease-in-out infinite alternate;
        will-change: transform;
    }
    .cc-home-hero__inner {
        display: flex;
        align-items: center;
        min-height: inherit;
    }
    .cc-home-hero__content {
        width: min(64%, 760px);
        padding: clamp(64px, 9vh, 112px) 0 clamp(54px, 8vh, 92px);
    }
    .cc-home-hero__eyebrow {
        display: inline-flex;
        align-items: center;
        gap: .75rem;
        margin-bottom: 1.35rem;
        color: #a8d4b4;
        font-size: .8rem;
        font-weight: 700;
        letter-spacing: .2em;
        text-transform: uppercase;
        animation: cc-hero-rise .65s .05s both;
    }
    .cc-home-hero__eyebrow::before {
        width: 30px;
        height: 1px;
        content: '';
        background: #8fc8a0;
    }
    .cc-home-hero__title {
        max-width: 780px;
        margin: 0 0 1.35rem;
        color: #fff;
        font-size: clamp(3.3rem, 6.2vw, 6.25rem);
        font-weight: 800;
        letter-spacing: -.055em;
        line-height: .99;
        animation: cc-hero-rise .75s .12s both;
    }
    .cc-home-hero__title span { color: #a8e0b7; }
    .cc-home-hero__copy {
        max-width: 575px;
        margin: 0 0 2rem;
        color: rgba(238,248,240,.84);
        font-size: clamp(1.05rem, 1.6vw, 1.3rem);
        line-height: 1.65;
        animation: cc-hero-rise .75s .2s both;
    }
    .cc-home-hero__search {
        max-width: 650px;
        padding: .55rem;
        border: 1px solid rgba(255,255,255,.5);
        border-radius: 1rem;
        background: rgba(255,255,255,.97);
        box-shadow: 0 18px 48px rgba(0,0,0,.24);
        animation: cc-hero-rise .75s .28s both;
    }
    .cc-home-hero__search .input-group-text { color: #215c3a; }
    .cc-home-hero__search .form-control { min-width: 0; }
    .cc-home-hero__trust {
        display: flex;
        flex-wrap: wrap;
        gap: .8rem 1.5rem;
        margin-top: 1.35rem;
        color: rgba(240,248,242,.82);
        font-size: .88rem;
        animation: cc-hero-rise .75s .36s both;
    }
    .cc-home-hero__trust i { color: #a8e0b7; }
    @keyframes cc-hero-rise {
        from { opacity: 0; transform: translateY(18px); }
        to { opacity: 1; transform: translateY(0); }
    }
    @keyframes cc-hero-image-drift {
        from { transform: scale(1.015) translateX(0); }
        to { transform: scale(1.075) translateX(10px); }
    }
    @media (max-width: 991.98px) {
        .cc-home-hero__media { width: 72%; opacity: .78; }
        .cc-home-hero__content { width: min(75%, 700px); }
        .cc-home-hero__media::after { background: linear-gradient(90deg, #082d1d 0%, rgba(8,45,29,.92) 20%, rgba(8,45,29,.44) 67%, rgba(8,45,29,.1) 100%), linear-gradient(0deg, rgba(3,20,13,.25), transparent 55%); }
    }
    @media (max-width: 575.98px) {
        .cc-home-hero { min-height: 790px; }
        .cc-home-hero__media { inset: auto 0 0; width: 100%; height: 55%; opacity: .9; }
        .cc-home-hero__media::after { background: linear-gradient(180deg, #082d1d 0%, rgba(8,45,29,.56) 26%, rgba(8,45,29,.05) 78%), linear-gradient(90deg, rgba(8,45,29,.4), transparent 80%); }
        .cc-home-hero__content { width: 100%; padding: 48px 0 330px; }
        .cc-home-hero__eyebrow { gap: .55rem; font-size: .67rem; letter-spacing: .14em; }
        .cc-home-hero__eyebrow::before { width: 18px; }
        .cc-home-hero__title { font-size: clamp(3.1rem, 14vw, 4.25rem); }
        .cc-home-hero__copy { font-size: 1rem; line-height: 1.5; margin-bottom: 1.25rem; }
        .cc-home-hero__search { padding: .4rem; }
        .cc-home-hero__search .input-group { flex-wrap: nowrap; }
        .cc-home-hero__search .input-group-text { padding-inline: .6rem; }
        .cc-home-hero__search .form-control { font-size: .9rem; }
        .cc-home-hero__search .btn { padding-inline: .8rem !important; white-space: nowrap; }
        .cc-home-hero__trust { gap: .55rem 1rem; margin-top: 1rem; font-size: .78rem; }
    }
    @media (prefers-reduced-motion: reduce) {
        .cc-home-hero__eyebrow, .cc-home-hero__title, .cc-home-hero__copy, .cc-home-hero__search, .cc-home-hero__trust { animation: none; }
        .cc-home-hero__media img { animation: none; }
    }
</style>
@endpush

<section class="cc-home-hero">
    <div class="cc-home-hero__media" aria-hidden="true">
        <img src="{{ asset('images/cropcircle-hero-food.jpg') }}" alt="" fetchpriority="high">
    </div>
    <div class="container cc-home-hero__inner">
        <div class="cc-home-hero__content">
            <div class="cc-home-hero__eyebrow"><i class="bi bi-leaf"></i> Local farmers <span aria-hidden="true">â€¢</span> Fresh products <span aria-hidden="true">â€¢</span> Better communities</div>
            <h1 class="cc-home-hero__title">Fresh Food,<br><span>Better Tomorrow.</span></h1>
            <p class="cc-home-hero__copy">From local farms to your table â€” fresh, healthy, and sustainably grown.</p>

            <form action="{{ route('products.index') }}" method="GET" class="cc-home-hero__search">
                <div class="input-group">
                    <span class="input-group-text bg-transparent border-0 ps-3"><i class="bi bi-search fs-5"></i></span>
                    <input type="text" name="q" class="form-control border-0 shadow-none ps-2" placeholder="Search tomatoes, honey, apples..." value="{{ request('q') }}" aria-label="Search products">
                    <button class="btn btn-cropcircle px-4 rounded-3 fw-bold" type="submit">Search</button>
                </div>
            </form>

            <div class="cc-home-hero__trust">
                <span><i class="bi bi-geo-alt-fill me-1"></i> Local growers</span>
                <span><i class="bi bi-clock-fill me-1"></i> Convenient pickup</span>
                <span><i class="bi bi-cash-coin me-1"></i> Pay at pickup</span>
            </div>
        </div>
    </div>
</section>
@include('home.partials.testimonials')
<!-- Active Announcements Banner if any -->
@if(isset($announcements) && $announcements->isNotEmpty())
    <section class="container mt-4">
        @foreach($announcements as $ann)
            <div class="alert alert-warning border-0 shadow-sm d-flex align-items-center justify-content-between rounded-3 py-3 px-4 mb-3" role="alert">
                <div class="d-flex align-items-center gap-3">
                    <span class="bg-warning text-dark p-2 rounded-circle fs-5"><i class="bi bi-megaphone-fill"></i></span>
                    <div>
                        <div class="fw-bold text-dark">{{ $ann->title }}</div>
                        <div class="small text-muted">{{ $ann->message }}</div>
                    </div>
                </div>
                <span class="badge bg-warning bg-opacity-25 text-dark border border-warning small">Platform Notice</span>
            </div>
        @endforeach
    </section>
@endif

<!-- Categories Grid -->
<section class="py-5" id="categories">
    <div class="container">
        <div class="d-flex justify-content-between align-items-end mb-4">
            <div>
                <span class="text-success fw-bold small text-uppercase tracking-wider">Browse by Category</span>
                <h2 class="fw-bold tracking-tight mb-0">What Are You Craving?</h2>
            </div>
            <a href="{{ route('products.index') }}" class="text-success fw-bold text-decoration-none small">
                View All Categories <i class="bi bi-arrow-right ms-1"></i>
            </a>
        </div>

        <div class="row g-3">
            @foreach($categories as $cat)
                <div class="col-lg-3 col-md-4 col-6">
                    <a href="{{ route('products.index', ['category' => $cat->slug]) }}" class="card card-custom card-hover p-2 text-decoration-none h-100 d-flex flex-row align-items-center gap-3">
                        <x-produce-image :image="$cat->image" :category="$cat" :alt="$cat->name" class="rounded-3 flex-shrink-0" style="width: 76px; height: 76px; object-fit: cover;" />
                        <div>
                            <h6 class="fw-bold text-dark mb-0">{{ $cat->name }}</h6>
                            <span class="small text-muted">{{ $cat->products_count }} Products</span>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Featured product ads -->
@if(isset($productAds) && $productAds->isNotEmpty())
<section class="container py-5">
    <div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4">
        <div><span class="text-success fw-bold text-uppercase small">Farmer promotions</span><h2 class="fw-bold mb-1">Featured farm ads</h2><p class="text-muted mb-0">Special offers from approved local growers</p></div>
        <a href="{{ route('products.index') }}" class="btn btn-outline-success rounded-pill">Browse all products <i class="ti ti-arrow-right ms-1"></i></a>
    </div>
    <div class="row g-4">
        @foreach($productAds as $ad)
            @php($savingPercent = $ad->original_price > 0 ? round((($ad->original_price - $ad->discount_price) / $ad->original_price) * 100) : 0)
            <div class="col-sm-6 col-md-6">
                <article class="card product-card-click position-relative h-100 border-0 shadow-sm rounded-4 overflow-hidden">
                    <div class="position-relative">
                        <img src="{{ $ad->display_image }}" alt="{{ $ad->title }}" class="card-img-top" style="height:210px;object-fit:cover">
                        <span class="badge bg-warning text-dark position-absolute top-0 start-0 m-3 px-3 py-2 rounded-pill">Featured farm offer</span>
                        @if($savingPercent > 0)<span class="badge bg-success position-absolute top-0 end-0 m-3 px-3 py-2 rounded-pill">Save {{ $savingPercent }}%</span>@endif
                    </div>
                    <div class="card-body p-3 d-flex flex-column">
                        <div class="small text-success fw-semibold mb-1">{{ $ad->product->category->name ?? 'Fresh from the farm' }}</div>
                        <h3 class="h4 mb-2">{{ $ad->title }}</h3>
                        <p class="text-muted mb-3">{{ \Illuminate\Support\Str::limit($ad->description,140) }}</p>
                        <div class="d-flex align-items-baseline flex-wrap gap-2 mb-3"><span class="fs-4 fw-bold text-success">${{ number_format($ad->discount_price,2) }}</span><del class="text-muted">${{ number_format($ad->original_price,2) }}</del><span class="small text-muted">per {{ $ad->product->unit }}</span></div>
                        <div class="d-flex justify-content-between align-items-center gap-3 mt-auto pt-3 border-top"><span class="small text-muted"><i class="ti ti-seeding me-1"></i>{{ $ad->farmer->farmerProfile->farm_name ?? $ad->farmer->name }}</span><a href="{{ route('products.show',$ad->product->slug) }}" class="btn btn-success rounded-pill px-3 stretched-link">View offer <i class="ti ti-arrow-right ms-1"></i></a></div>
                    </div>
                </article>
            </div>
        @endforeach
    </div>
</section>
@endif
<!-- Featured Products Section -->
<!-- Featured Products Section -->
<!-- Featured Products Section -->
<section class="py-5 bg-light bg-opacity-50">
    <div class="container">
        <div class="d-flex justify-content-between align-items-end mb-4">
            <div>
                <span class="text-success fw-bold small text-uppercase tracking-wider">Top sellers and trending harvests</span>
                <h2 class="fw-bold tracking-tight mb-0">Most Selling Products</h2><p class="text-muted small mb-0">Ranked by completed purchases in the last 30 days, with lifetime sales as a tie-breaker.</p>
            </div>
            <a href="{{ route('products.index') }}" class="text-success fw-bold text-decoration-none small">
                See all products <i class="bi bi-arrow-right ms-1"></i>
            </a>
        </div>

        <div class="row g-4">
            @forelse($mostSellingProducts as $product)
                <div class="col-xl-3 col-lg-4 col-md-6">
                    <div class="card card-custom card-hover product-card-click position-relative h-100 d-flex flex-column overflow-hidden border-0 shadow-sm">
                        <!-- Product Card Top / Image Area -->
                        <div class="position-relative bg-light d-flex align-items-center justify-content-center" style="height: 180px;">
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

                        <!-- Product Content -->
<div class="card-body p-3 d-flex flex-column">
                        <div class="d-flex flex-wrap gap-2 mb-2">
                            @if(($product->recent_units_sold ?? 0) > 0)
                                <span class="badge bg-warning-lt text-dark"><i class="ti ti-trending-up me-1"></i>{{ $product->recent_units_sold }} sold in the last 30 days</span>
                            @elseif(($product->lifetime_units_sold ?? 0) > 0)
                                <span class="badge bg-light text-muted">{{ $product->lifetime_units_sold }} completed sales</span>
                            @else
                                <span class="badge bg-light text-muted">New harvest</span>
                            @endif
                        </div>
                            <div class="small text-muted mb-1 d-flex align-items-center gap-1">
                                <i class="bi bi-shop text-success"></i>
                                <span class="text-truncate">{{ $product->farmer->farmerProfile->farm_name ?? $product->farmer->name }}</span>
                            </div>
                            <h6 class="card-title fw-bold text-dark mb-1">
                                <a href="{{ route('products.show', $product->slug) }}" class="text-dark text-decoration-none text-truncate d-block stretched-link">
                                    {{ $product->name }}
                                </a>
                            </h6>
                            <p class="small text-secondary text-truncate-2 mb-3" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; min-height: 38px;">
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
                <div class="col-12 text-center py-5">
                    <p class="text-muted">No products available at this time.</p>
                </div>
            @endforelse
        </div>
    </div>
</section>

<!-- Local Markets Showcase -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="d-flex justify-content-between align-items-end mb-4">
            <div>
                <span class="text-success fw-bold small text-uppercase tracking-wider">Community Hubs</span>
                <h2 class="fw-bold tracking-tight mb-0">Explore Local Farmers Markets</h2>
            </div>
            <a href="{{ route('markets.index') }}" class="text-success fw-bold text-decoration-none small">
                Interactive Market Map <i class="bi bi-arrow-right ms-1"></i>
            </a>
        </div>

        <div class="row g-4">
            @foreach($markets as $market)
                <div class="col-lg-4 col-md-6">
                    <div class="card card-custom card-hover h-100 border-0 shadow-sm p-4 d-flex flex-column">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <span class="badge bg-success bg-opacity-10 text-success fw-bold px-2 py-1">
                                <i class="bi bi-shop me-1"></i> {{ $market->approved_farmers_count }} Stalls
                            </span>
                            <span class="badge bg-light text-secondary border">Active Market</span>
                        </div>
                        <h5 class="fw-bold text-dark mb-1">{{ $market->name }}</h5>
                        <div class="small text-muted mb-3"><i class="bi bi-geo-alt text-danger me-1"></i> {{ $market->address }}</div>
                        <p class="small text-secondary mb-4 flex-grow-1">
                            {{ Str::limit($market->description, 110) }}
                        </p>

                        <div class="pt-3 border-top mt-auto d-flex justify-content-between align-items-center">
                            <div class="small text-muted">
                                <i class="bi bi-clock me-1 text-success"></i> {{ date('g:i A', strtotime($market->open_time)) }} - {{ date('g:i A', strtotime($market->close_time)) }}
                            </div>
                            <a href="{{ route('markets.show', $market->id) }}" class="btn btn-sm btn-outline-success fw-bold rounded-pill px-3">
                                View Stalls <i class="bi bi-chevron-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Top farmers spotlight -->
<section class="py-5">
    <div class="container py-3">
        <div class="d-flex justify-content-between align-items-end mb-4">
            <div>
                <span class="text-success fw-bold small text-uppercase tracking-wider">Top-rated and trending growers</span>
                <h2 class="fw-bold tracking-tight mb-0">Best Farmers on CropCircle</h2>
            </div>
            <a href="{{ route('farmers.index') }}" class="text-success fw-bold text-decoration-none small">
                Browse all farmers <i class="bi bi-arrow-right ms-1"></i>
            </a>
        </div>

        <div class="row g-4">
            @foreach($bestFarmers as $fp)
                <div class="col-lg-3 col-md-6">
                    <div class="card card-custom card-hover position-relative h-100 text-center p-4 border-0 shadow-sm d-flex flex-column align-items-center">
                        <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center mb-3 shadow-sm fs-2 fw-bold" style="width: 75px; height: 75px;">
                            {{ strtoupper(substr($fp->farm_name, 0, 1)) }}
                        </div>
                        <h6 class="fw-bold text-dark mb-1"><a class="text-dark text-decoration-none stretched-link" href="{{ route('farmers.show', $fp->user_id) }}">{{ $fp->farm_name }}</a></h6>
                        <div class="small text-muted mb-2"><i class="bi bi-geo-alt me-1 text-success"></i> {{ $fp->city }}, {{ $fp->state }}</div>
                        <div class="d-flex flex-wrap justify-content-center gap-2 mb-2">
                            @if($fp->review_count > 0)
                                <span class="badge bg-warning-lt text-dark"><i class="bi bi-star-fill text-warning me-1"></i>{{ number_format($fp->average_rating, 1) }} Â· {{ $fp->review_count }} reviews</span>
                            @else
                                <span class="badge bg-light text-muted">New Â· no reviews yet</span>
                            @endif
                            @if($fp->recent_order_count > 0)
                                <span class="badge bg-success-lt text-success"><i class="ti ti-trending-up me-1"></i>{{ $fp->recent_order_count }} completed orders in the last 30 days</span>
                            @endif
                        </div>                        <p class="small text-secondary mb-3 text-truncate-2" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; min-height: 38px;">
                            {{ $fp->description }}
                        </p>
                        <div class="mt-auto w-100 pt-2 border-top">
                            <a href="{{ route('farmers.show', $fp->user_id) }}" class="btn btn-sm btn-outline-cropcircle w-100 rounded-pill position-relative z-2">
                                View Farm & Products
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endsection
