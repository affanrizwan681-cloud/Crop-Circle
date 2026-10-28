@extends('layouts.app')

@section('title', 'Join CropCircle — Create an Account')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-7">
            <div class="card card-custom p-4 p-md-5 border-0 shadow-sm">
                <div class="text-center mb-4">
                    <span class="badge bg-success bg-opacity-10 text-success fw-bold px-3 py-2 rounded-pill mb-2">Join the Community</span>
                    <h3 class="fw-bold">Create Your Account</h3>
                    <p class="text-muted small">Buy fresh local products or register as an approved market seller</p>
                </div>

                @if($errors->any())
                    <div class="alert alert-danger border-0 small py-2" data-cropcircle-flash>
                        <ul class="mb-0 ps-3">
                            @foreach($errors->all() as $err)
                                <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('register.submit') }}" method="POST" id="registerForm">
                    @csrf

                    <!-- Role Switcher -->
                    <div class="mb-4">
                        <label class="form-label small fw-bold d-block text-center text-muted">I want to join CropCircle as a:</label>
                        <div class="row g-3">
                            <div class="col-6">
                                <input type="radio" class="btn-check" name="role" id="roleCustomer" value="customer" {{ old('role', 'customer') === 'customer' ? 'checked' : '' }} onchange="toggleFarmerFields()">
                                <label class="btn btn-outline-success w-100 py-3 text-center rounded-3 d-flex flex-column align-items-center" for="roleCustomer">
                                    <i class="bi bi-person-heart fs-3 mb-1"></i>
                                    <span class="fw-bold">Customer</span>
                                    <span class="small opacity-75">Order local products</span>
                                </label>
                            </div>
                            <div class="col-6">
                                <input type="radio" class="btn-check" name="role" id="roleFarmer" value="farmer" {{ old('role') === 'farmer' ? 'checked' : '' }} onchange="toggleFarmerFields()">
                                <label class="btn btn-outline-success w-100 py-3 text-center rounded-3 d-flex flex-column align-items-center" for="roleFarmer">
                                    <i class="bi bi-shop fs-3 mb-1"></i>
                                    <span class="fw-bold">Farmer / Seller</span>
                                    <span class="small opacity-75">Sell products at stalls</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Common Fields -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Full Name</label>
                            <input type="text" name="name" class="form-control" required value="{{ old('name') }}" placeholder="John Doe">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Email Address</label>
                            <input type="email" name="email" class="form-control" required value="{{ old('email') }}" placeholder="john@example.com">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Phone Number</label>
                            <input type="text" name="phone" class="form-control" required value="{{ old('phone') }}" placeholder="+1 (555) 000-0000">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Pickup Contact Address / City</label>
                            <input type="text" name="address" class="form-control" required value="{{ old('address') }}" placeholder="123 Main St, Springfield">
                        </div>
                    </div>

                    <!-- Farmer Specific Fields -->
                    <div id="farmerFields" class="{{ old('role') === 'farmer' ? '' : 'd-none' }} p-3 bg-light rounded-3 mb-3 border">
                        <h6 class="fw-bold text-success mb-3"><i class="bi bi-tree me-1"></i> Farm Profile Details</h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Farm / Stall Business Name</label>
                                <input type="text" name="farm_name" class="form-control" value="{{ old('farm_name') }}" placeholder="Sunrise Organic Farm">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-bold">City</label>
                                <input type="text" name="city" class="form-control" value="{{ old('city') }}" placeholder="Springfield">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-bold">State</label>
                                <input type="text" name="state" class="form-control" value="{{ old('state') }}" placeholder="IL">
                            </div>
                            <div class="col-12">
                                <label class="form-label small fw-bold">Farm Description & Growing Practices</label>
                                <textarea name="farm_description" rows="3" class="form-control" placeholder="Tell customers about your heritage crops, organic methods, and products...">{{ old('farm_description') }}</textarea>
                            </div>
                            <div class="col-12">
                                <label class="form-label small fw-bold mb-1">Standard Market Operating Days</label>
                                <div class="d-flex flex-wrap gap-3">
                                    @foreach(['Wednesday', 'Friday', 'Saturday', 'Sunday'] as $day)
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="operating_days[]" value="{{ $day }}" id="day_{{ $day }}" checked>
                                            <label class="form-check-label small" for="day_{{ $day }}">{{ $day }}</label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Passwords -->
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Password (min 8 chars)</label>
                            <input type="password" name="password" class="form-control" required placeholder="••••••••">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Confirm Password</label>
                            <input type="password" name="password_confirmation" class="form-control" required placeholder="••••••••">
                        </div>
                    </div>

                    <button type="submit" class="btn btn-cropcircle w-100 py-2 fw-bold mb-3">
                        Create Account <i class="bi bi-arrow-right ms-1"></i>
                    </button>

                    <div class="text-center small text-muted">
                        Already have an account? <a href="{{ route('login') }}" class="text-success fw-bold text-decoration-none">Sign In</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function toggleFarmerFields() {
        const isFarmer = document.getElementById('roleFarmer').checked;
        const farmerDiv = document.getElementById('farmerFields');
        if (isFarmer) {
            farmerDiv.classList.remove('d-none');
        } else {
            farmerDiv.classList.add('d-none');
        }
    }
</script>
@endpush
@endsection
