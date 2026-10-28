@extends('layouts.app')

@section('title', 'About CropCircle — Soil to Stall Community Commerce')

@section('content')
<div class="container py-5">
    <div class="row align-items-center g-5 mb-5">
        <div class="col-lg-6">
            <span class="badge bg-success bg-opacity-10 text-success fw-bold px-3 py-2 rounded-pill mb-3">Our Mission</span>
            <h1 class="display-5 fw-bold text-dark mb-4">Empowering Regional Agriculture Through Direct Community Pre-Orders.</h1>
            <p class="lead text-secondary mb-3">
                CropCircle was built from the ground up to solve the single largest challenge facing small family farms: unpredictable market demand and perishable harvest waste.
            </p>
            <p class="text-secondary">
                By allowing consumers to browse seasonal crops and reserve them online prior to weekly market days, farmers harvest with exact precision. Customers secure the highest quality products, free-range dairy, raw honey, and artisan sourdough, ready for pickup at their neighborhood market stall.
            </p>
        </div>
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm p-4 rounded-4 bg-light">
                <div class="row g-3 text-center">
                    <div class="col-6">
                        <div class="p-3 bg-white rounded-3 shadow-xs cc-glass-mini">
                            <h2 class="fw-bold text-success mb-1 cc-count-up" data-count-target="100" data-count-suffix="%" aria-label="100%">0%</h2>
                            <span class="small text-muted">Direct to Producer</span>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-3 bg-white rounded-3 shadow-xs cc-glass-mini">
                            <h2 class="fw-bold text-success mb-1 cc-count-up" data-count-target="0" data-count-suffix="%" aria-label="0%">0%</h2>
                            <span class="small text-muted">Middlemen Markups</span>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-3 bg-white rounded-3 shadow-xs cc-glass-mini">
                            <h2 class="fw-bold text-success mb-1 cc-count-up" data-count-target="24" data-count-suffix="hr" aria-label="24hr">0hr</h2>
                            <span class="small text-muted">Harvest to Basket</span>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-3 bg-white rounded-3 shadow-xs cc-glass-mini">
                            <h2 class="fw-bold text-success mb-1 cc-count-up" data-count-target="15" data-count-suffix="+" aria-label="15+">0+</h2>
                            <span class="small text-muted">Market Stalls</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mt-2">
        <div class="col-md-4">
            <div class="card card-custom p-4 h-100 border-0 shadow-sm">
                <i class="bi bi-shield-check text-success fs-1 mb-3"></i>
                <h5 class="fw-bold">Verified Organic & Natural</h5>
                <p class="small text-muted mb-0">Every producer is inspected and verified by platform administrators before their products are available for pre-order.</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-custom p-4 h-100 border-0 shadow-sm">
                <i class="bi bi-chat-heart text-success fs-1 mb-3"></i>
                <h5 class="fw-bold">Direct Farmer Chat</h5>
                <p class="small text-muted mb-0">Ask questions about farming practices, growing methods, or special batch requests directly with the farmer in our chat system.</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-custom p-4 h-100 border-0 shadow-sm">
                <i class="bi bi-geo-alt text-success fs-1 mb-3"></i>
                <h5 class="fw-bold">Hyper-Local Resilience</h5>
                <p class="small text-muted mb-0">Every dollar spent circulates within your immediate community, preserving local farmlands and green spaces.</p>
            </div>
        </div>
    </div>
</div>
@endsection
