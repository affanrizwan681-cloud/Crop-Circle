@extends('layouts.app')

@section('title', 'Meet Our Verified Local Growers — CropCircle')

@section('content')
<div class="bg-light py-4 border-bottom mb-4">
    <div class="container">
        <h1 class="h3 fw-bold mb-1">Our Local Producers & Farmers</h1>
        <p class="text-muted small mb-0">Discover certified local farms, family homesteads, and artisan food producers</p>
    </div>
</div>

<div class="container pb-5">
    <div class="card card-custom p-3 border-0 shadow-sm mb-4">
        <form action="{{ route('farmers.index') }}" method="GET" class="row g-2">
            <div class="col-md-8">
                <div class="input-group">
                    <span class="input-group-text bg-transparent border-end-0 text-muted"><i class="bi bi-search text-success"></i></span>
                    <input type="text" name="q" class="form-control border-start-0" placeholder="Search by farm name, grower, city, or bio..." value="{{ request('q') }}">
                </div>
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-cropcircle w-100 fw-bold">Search Farmers</button>
            </div>
        </form>
    </div>

    <div class="row g-4">
        @forelse($farmers as $fp)
            <div class="col-lg-4 col-md-6">
                <div class="card card-custom card-hover h-100 p-4 border-0 shadow-sm d-flex flex-column">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center fw-bold fs-3" style="width: 60px; height: 60px;">
                            {{ strtoupper(substr($fp->farm_name, 0, 1)) }}
                        </div>
                        <div>
                            <h5 class="fw-bold mb-0">
                                <a href="{{ route('farmers.show', $fp->user_id) }}" class="text-dark text-decoration-none">
                                    {{ $fp->farm_name }}
                                </a>
                            </h5>
                            <div class="small text-muted"><i class="bi bi-person me-1"></i> {{ $fp->contact_person }}</div>
                            <div class="small text-muted"><i class="bi bi-geo-alt text-success me-1"></i> {{ $fp->city }}, {{ $fp->state }}</div>
                        </div>
                    </div>

                    <p class="small text-secondary mb-4 flex-grow-1">
                        {{ Str::limit($fp->description, 130) }}
                    </p>

                    <div class="p-3 bg-light rounded-3 mb-3 small">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted"><i class="bi bi-shop text-success me-1"></i> Attending Markets:</span>
                            <span class="fw-bold">{{ $fp->markets->count() }} Markets</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted"><i class="bi bi-clock text-success me-1"></i> Pickup Window:</span>
                            <span class="fw-bold">{{ date('g:i A', strtotime($fp->pickup_start_time)) }} - {{ date('g:i A', strtotime($fp->pickup_end_time)) }}</span>
                        </div>
                    </div>

                    <div class="mt-auto">
                        <a href="{{ route('farmers.show', $fp->user_id) }}" class="btn btn-cropcircle w-100 fw-bold">
                            View Products & Stalls <i class="bi bi-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5 text-muted">
                No farmers found matching your search.
            </div>
        @endforelse
    </div>

    <div class="mt-5 d-flex justify-content-center">
        {{ $farmers->links('pagination::bootstrap-5') }}
    </div>
</div>
@endsection
