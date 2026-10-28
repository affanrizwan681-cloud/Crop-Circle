@extends('layouts.app')

@section('title', $market->name . ' — CropCircle')

@section('content')
<div class="bg-light py-5 border-bottom mb-4">
    <div class="container">
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb small">
                <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-success text-decoration-none">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('markets.index') }}" class="text-success text-decoration-none">Markets</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $market->name }}</li>
            </ol>
        </nav>

        <div class="row align-items-center g-4">
            <div class="col-lg-8">
                <span class="badge bg-success bg-opacity-10 text-success fw-bold px-3 py-1 rounded-pill mb-2">Verified Market Pavilion</span>
                <h1 class="display-6 fw-bold mb-2">{{ $market->name }}</h1>
                <p class="text-muted mb-3"><i class="bi bi-geo-alt-fill text-danger me-1"></i> {{ $market->address }}</p>
                <p class="text-secondary mb-0 leading-relaxed">{{ $market->description }}</p>
            </div>
            <div class="col-lg-4">
                <div class="card card-custom p-4 border-0 shadow-sm bg-white">
                    <h6 class="fw-bold mb-3"><i class="bi bi-info-circle text-success me-1"></i> Operating Hours</h6>
                    <div class="d-flex justify-content-between mb-2 small">
                        <span class="text-muted">Market Days:</span>
                        <span class="fw-bold">{{ is_array($market->operating_days) ? implode(', ', $market->operating_days) : 'Weekends' }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2 small">
                        <span class="text-muted">Hours:</span>
                        <span class="fw-bold">{{ date('g:i A', strtotime($market->open_time)) }} - {{ date('g:i A', strtotime($market->close_time)) }}</span>
                    </div>
                    <div class="d-flex justify-content-between small">
                        <span class="text-muted">Active Stalls:</span>
                        <span class="fw-bold text-success">{{ $market->approvedFarmers->count() }} Producers</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container pb-5">
    <!-- Attending Farmers & Stalls -->
    <div class="mb-5">
        <h4 class="fw-bold mb-4"><i class="bi bi-shop me-2 text-success"></i> Attending Farmers & Stall Locations</h4>
        <div class="row g-4">
            @forelse($market->approvedFarmers as $farmer)
                <div class="col-md-4">
                    <div class="card card-custom p-4 border-0 shadow-sm h-100 d-flex flex-column">
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center fw-bold fs-4" style="width: 50px; height: 50px;">
                                {{ strtoupper(substr($farmer->farm_name, 0, 1)) }}
                            </div>
                            <div>
                                <h6 class="fw-bold mb-0">
                                    <a href="{{ route('farmers.show', $farmer->user_id) }}" class="text-dark text-decoration-none">
                                        {{ $farmer->farm_name }}
                                    </a>
                                </h6>
                                <span class="badge bg-warning text-dark small mt-1">
                                    <i class="bi bi-pin-map me-1"></i> {{ $farmer->pivot->assigned_stall ?? 'Stall Assigned' }}
                                </span>
                            </div>
                        </div>

                        <p class="small text-secondary mb-4 flex-grow-1">
                            {{ Str::limit($farmer->description, 110) }}
                        </p>

                        <div class="mt-auto pt-2 border-top">
                            <a href="{{ route('farmers.show', $farmer->user_id) }}" class="btn btn-sm btn-outline-success w-100 rounded-pill">
                                View Farmer Stall & Products
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-muted text-center py-4">No stalls currently assigned.</div>
            @endforelse
        </div>
    </div>

    <!-- Products Available from this Market's Farmers -->
    <div>
        <h4 class="fw-bold mb-4"><i class="bi bi-basket2 me-2 text-success"></i> Products Available from This Market's Producers</h4>
        <div class="row g-4">
            @forelse($products as $prod)
                <div class="col-lg-3 col-md-6">
                    <div class="card card-custom card-hover product-card-click position-relative h-100 border-0 shadow-sm p-3 d-flex flex-column">
                        <div class="bg-light rounded-3 d-flex align-items-center justify-content-center mb-2" style="height: 150px;">
                            <i class="bi {{ $prod->category->icon ?? 'bi-bag' }} fs-1 text-success"></i>
                        </div>
                        <div class="small text-muted mb-1">{{ $prod->farmer->farmerProfile->farm_name ?? $prod->farmer->name }}</div>
                        <h6 class="fw-bold mb-2">
                            <a href="{{ route('products.show', $prod->slug) }}" class="text-dark text-decoration-none text-truncate d-block stretched-link">
                                {{ $prod->name }}
                            </a>
                        </h6>
                        <div class="mt-auto d-flex justify-content-between align-items-center pt-2 border-top">
                            <span class="fw-bold text-success">${{ number_format($prod->price, 2) }} / {{ $prod->unit }}</span>
                            <a href="{{ route('products.show', $prod->slug) }}" class="btn btn-sm btn-outline-success rounded-pill px-3 position-relative z-2">View</a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-muted text-center py-4">No products listed by these growers yet.</div>
            @endforelse
        </div>

        <div class="mt-4">
            {{ $products->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>
@endsection
