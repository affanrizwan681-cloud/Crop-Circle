@extends('layouts.app')

@section('title', 'Sign In — CropCircle')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card card-custom p-4 p-md-5 border-0 shadow-sm">
                <div class="text-center mb-4">
                    <div class="bg-success text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-2" style="width: 50px; height: 50px;">
                        <i class="bi bi-box-arrow-in-right fs-4"></i>
                    </div>
                    <h3 class="fw-bold">Welcome Back</h3>
                    <p class="text-muted small">Sign in to your CropCircle account</p>
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

                <form action="{{ route('login.submit') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Email Address</label>
                        <input type="email" name="email" id="loginEmail" class="form-control" required value="{{ old('email') }}" placeholder="name@example.com">
                    </div>

                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label class="form-label small fw-bold mb-0">Password</label>
                        </div>
                        <input type="password" name="password" id="loginPassword" class="form-control" required placeholder="••••••••">
                    </div>

                    <div class="mb-3 form-check">
                        <input type="checkbox" name="remember" class="form-check-input" id="rememberMe">
                        <label class="form-check-label small text-muted" for="rememberMe">Remember me on this device</label>
                    </div>

                    <button type="submit" class="btn btn-cropcircle w-100 py-2 fw-bold mb-3">
                        Sign In <i class="bi bi-arrow-right ms-1"></i>
                    </button>

                    <div class="text-center small text-muted">
                        Don't have an account? <a href="{{ route('register') }}" class="text-success fw-bold text-decoration-none">Join CropCircle</a>
                    </div>
                </form>
            </div>

            <!-- Demo Account Credentials Helper -->
            <div class="card card-custom border-0 shadow-sm mt-4 p-3 bg-light">
                <h6 class="fw-bold small text-dark mb-2"><i class="bi bi-key-fill text-warning me-1"></i> Quick Test Accounts (Password: <code>password123</code>)</h6>
                <div class="d-flex flex-wrap gap-2">
                    <button type="button" class="btn btn-sm btn-outline-secondary small py-1" onclick="fillLogin('customer@marketlink.com')">
                        <i class="bi bi-person me-1"></i> Customer
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-success small py-1" onclick="fillLogin('farmer@marketlink.com')">
                        <i class="bi bi-shop me-1"></i> Farmer (Thomas)
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-danger small py-1" onclick="fillLogin('admin@marketlink.com')">
                        <i class="bi bi-shield-lock me-1"></i> Admin
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function fillLogin(email) {
        document.getElementById('loginEmail').value = email;
        document.getElementById('loginPassword').value = 'password123';
    }
</script>
@endpush
@endsection
