@extends('layouts.app')

@section('title', 'Community Farmers Markets & Pickups — CropCircle')

@section('content')
<div class="bg-light py-4 border-bottom mb-4">
    <div class="container">
        <h1 class="h3 fw-bold mb-1">Local Farmers Markets</h1>
        <p class="text-muted small mb-0">Discover community market pavilions and pre-order for scheduled stall pickup</p>
    </div>
</div>

<div class="container pb-5">
    <!-- Leaflet Map Container -->
    <div class="card card-custom border-0 shadow-sm overflow-hidden mb-5 p-2">
        <div id="marketsMap" style="height: 380px; width: 100%; border-radius: 8px;"></div>
    </div>

    <!-- Markets Cards Grid -->
    <div class="row g-4">
        @foreach($markets as $market)
            <div class="col-lg-4 col-md-6">
                <div class="card card-custom card-hover h-100 border-0 shadow-sm p-4 d-flex flex-column">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <span class="badge bg-success bg-opacity-10 text-success fw-bold px-3 py-2 rounded-pill">
                            <i class="bi bi-shop me-1"></i> {{ $market->approved_farmers_count }} Stalls
                        </span>
                        <span class="badge bg-light text-muted border">Active Hub</span>
                    </div>

                    <h4 class="fw-bold text-dark mb-1">{{ $market->name }}</h4>
                    <div class="small text-muted mb-3"><i class="bi bi-geo-alt-fill text-danger me-1"></i> {{ $market->address }}</div>

                    <p class="small text-secondary mb-4 flex-grow-1">
                        {{ $market->description }}
                    </p>

                    <div class="p-3 bg-light rounded-3 mb-3 small">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted"><i class="bi bi-calendar-check text-success me-1"></i> Operating Days:</span>
                            <span class="fw-bold">{{ is_array($market->operating_days) ? implode(', ', $market->operating_days) : 'Weekends' }}</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted"><i class="bi bi-clock text-success me-1"></i> Market Hours:</span>
                            <span class="fw-bold">{{ date('g:i A', strtotime($market->open_time)) }} - {{ date('g:i A', strtotime($market->close_time)) }}</span>
                        </div>
                    </div>

                    <div class="mt-auto">
                        <a href="{{ route('markets.show', $market->id) }}" class="btn btn-cropcircle w-100 fw-bold">
                            View Attending Farmers & Stalls <i class="bi bi-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function () {
        // Initialize Leaflet Map
        const map = L.map('marketsMap').setView([39.7900, -89.6450], 12);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 18,
            attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        const markets = @json($markets);

        const markers = [];
        markets.forEach(m => {
            if (m.latitude && m.longitude) {
                const marker = L.marker([m.latitude, m.longitude]).addTo(map);
                marker.bindPopup(`
                    <div style="min-width: 180px;">
                        <h6 style="font-weight: bold; margin-bottom: 4px;">${m.name}</h6>
                        <p style="font-size: 12px; color: #64748b; margin-bottom: 6px;">${m.address}</p>
                        <a href="/markets/${m.id}" style="color: #2d6a4f; font-weight: bold; font-size: 12px; text-decoration: none;">View Stalls &rarr;</a>
                    </div>
                `);
                markers.push(marker);
            }
        });

        if (markers.length > 0) {
            const group = new L.featureGroup(markers);
            map.fitBounds(group.getBounds().pad(0.2));
        }
    });
</script>
@endpush
@endsection
