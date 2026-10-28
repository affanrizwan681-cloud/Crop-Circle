@extends('layouts.app')

@section('title', 'Farm Verification Pending — CropCircle')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-7">
            <div class="card card-custom p-5 border-0 shadow-sm text-center">
                @if($profile && $profile->rejection_reason)
                    <div class="bg-danger text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 70px; height: 70px;">
                        <i class="bi bi-exclamation-triangle fs-1"></i>
                    </div>
                    <h3 class="fw-bold text-dark">Application Requires Changes</h3>
                    <p class="text-secondary mb-3">Our moderation team reviewed your farm profile and requested additional information:</p>
                    <div class="alert alert-danger text-start small mb-4">
                        <i class="bi bi-info-circle me-1"></i> <strong>Reason:</strong> {{ $profile->rejection_reason }}
                    </div>
                    <a href="{{ route('profile') }}" class="btn btn-cropcircle px-4 fw-bold">
                        Update Farm Details & Resubmit &rarr;
                    </a>
                @else
                    <div class="bg-warning text-dark rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 70px; height: 70px;">
                        <i class="bi bi-hourglass-split fs-1"></i>
                    </div>
                    <h3 class="fw-bold text-dark">Farm Profile Under Review</h3>
                    <p class="text-secondary mb-4">
                        Thank you for applying to sell with CropCircle, <strong>{{ $user->name }}</strong>! Platform administrators are verifying your farm credentials and stall locations.
                    </p>

                    <div class="p-3 bg-light rounded-3 text-start small mb-4 border">
                        <div class="fw-bold text-success mb-2"><i class="bi bi-shop me-1"></i> Submitted Farm Details:</div>
                        <div><strong>Farm Name:</strong> {{ $profile->farm_name ?? 'N/A' }}</div>
                        <div><strong>Location:</strong> {{ $profile->city ?? 'N/A' }}, {{ $profile->state ?? 'N/A' }}</div>
                        <div><strong>Contact Phone:</strong> {{ $user->phone }}</div>
                        <div class="text-truncate"><strong>Description:</strong> {{ $profile->description ?? 'N/A' }}</div>
                    </div>

                    <p class="small text-muted mb-4">
                        Once approved, you will gain full access to publish products, manage inventory, schedule market pickups, and accept online customer reservations.
                    </p>

                    <div class="d-flex justify-content-center gap-2">
                        <a href="{{ route('profile') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                            <i class="bi bi-pencil me-1"></i> Edit Profile
                        </a>
                        <a href="{{ route('home') }}" class="btn btn-outline-success btn-sm rounded-pill px-3">
                            Return to Homepage
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
