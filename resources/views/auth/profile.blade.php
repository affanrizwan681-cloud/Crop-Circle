@extends('layouts.app')

@section('title', 'Account Settings — CropCircle')

@section('content')
<div class="container py-5">
    <div class="row g-4 justify-content-center">
        <div class="col-lg-8">
            <div class="card card-custom p-4 border-0 shadow-sm mb-4">
                <div class="d-flex align-items-center gap-3 mb-4">
                    <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center fs-3 fw-bold" style="width: 55px; height: 55px;">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                    <div>
                        <h4 class="fw-bold mb-0">{{ $user->name }}</h4>
                        <span class="badge bg-success bg-opacity-10 text-success fw-bold text-uppercase">{{ $user->role }}</span>
                        <span class="small text-muted ms-2">Member since {{ $user->created_at->format('M Y') }}</span>
                    </div>
                </div>

                <h5 class="fw-bold border-bottom pb-2 mb-3">Profile Details</h5>

                <form action="{{ route('profile.update') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Full Name</label>
                            <input type="text" name="name" class="form-control" required value="{{ old('name', $user->name) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Email Address</label>
                            <input type="email" name="email" class="form-control" required value="{{ old('email', $user->email) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Phone Number</label>
                            <input type="text" name="phone" class="form-control" required value="{{ old('phone', $user->phone) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Primary Address / Pickup Contact</label>
                            <input type="text" name="address" class="form-control" required value="{{ old('address', $user->address) }}">
                        </div>

                        @if($user->isFarmer() && $user->farmerProfile)
                            <div class="col-12 mt-4">
                                <h6 class="fw-bold text-success border-bottom pb-2 mb-3"><i class="bi bi-shop me-1"></i> Farm Profile Settings</h6>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Farm / Business Name</label>
                                <input type="text" name="farm_name" class="form-control" required value="{{ old('farm_name', $user->farmerProfile->farm_name) }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-bold">City</label>
                                <input type="text" name="city" class="form-control" required value="{{ old('city', $user->farmerProfile->city) }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-bold">State</label>
                                <input type="text" name="state" class="form-control" required value="{{ old('state', $user->farmerProfile->state) }}">
                            </div>
                            <div class="col-12">
                                <label class="form-label small fw-bold">Farm Bio / Practices</label>
                                <textarea name="description" rows="3" class="form-control" required>{{ old('description', $user->farmerProfile->description) }}</textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Pickup Window Start</label>
                                <input type="time" name="pickup_start_time" class="form-control" required value="{{ old('pickup_start_time', $user->farmerProfile->pickup_start_time) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Pickup Window End</label>
                                <input type="time" name="pickup_end_time" class="form-control" required value="{{ old('pickup_end_time', $user->farmerProfile->pickup_end_time) }}">
                            </div>
                        @endif

                        <div class="col-12 mt-4 text-end">
                            <button type="submit" class="btn btn-cropcircle px-4 fw-bold">
                                Save Profile Changes
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Change Password Card -->
            <div class="card card-custom p-4 border-0 shadow-sm">
                <h5 class="fw-bold border-bottom pb-2 mb-3"><i class="bi bi-shield-lock me-1"></i> Update Password</h5>
                <form action="{{ route('profile.password') }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Current Password</label>
                            <input type="password" name="current_password" class="form-control" required placeholder="••••••••">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">New Password</label>
                            <input type="password" name="password" class="form-control" required placeholder="••••••••">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">Confirm New Password</label>
                            <input type="password" name="password_confirmation" class="form-control" required placeholder="••••••••">
                        </div>
                        <div class="col-12 text-end mt-3">
                            <button type="submit" class="btn btn-outline-secondary fw-bold">
                                Update Password
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
