@extends('layouts.app')

@section('title', 'Contact Us — CropCircle Community Support')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card card-custom p-4 p-md-5 border-0 shadow-sm">
                <span class="badge bg-success bg-opacity-10 text-success fw-bold px-3 py-2 rounded-pill mb-3 align-self-start">Get In Touch</span>
                <h2 class="fw-bold mb-2">We'd Love to Hear From You</h2>
                <p class="text-muted mb-4">Have questions about setting up a farm stall, picking up orders, or partnering with a local market? Drop us a message.</p>

                <form action="{{ route('contact.submit') }}" method="POST">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Your Name</label>
                            <input type="text" name="name" class="form-control" required value="{{ old('name', auth()->user()?->name) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Your Email</label>
                            <input type="email" name="email" class="form-control" required value="{{ old('email', auth()->user()?->email) }}">
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold">Subject</label>
                            <input type="text" name="subject" class="form-control" required value="{{ old('subject') }}" placeholder="How can we help?">
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold">Message</label>
                            <textarea name="message" rows="5" class="form-control" required placeholder="Tell us how we can assist you...">{{ old('message') }}</textarea>
                        </div>
                        <div class="col-12 mt-4">
                            <button type="submit" class="btn btn-cropcircle w-100 py-2 fw-bold">
                                Send Message <i class="bi bi-send ms-1"></i>
                            </button>
                        </div>
                    </div>
                </form>

                <div class="row g-4 mt-4 pt-4 border-top">
                    <div class="col-md-4 text-center">
                        <i class="bi bi-envelope text-success fs-3 mb-2"></i>
                        <div class="fw-bold small">Email Support</div>
                        <div class="small text-muted">support@cropcircle.com</div>
                    </div>
                    <div class="col-md-4 text-center">
                        <i class="bi bi-telephone text-success fs-3 mb-2"></i>
                        <div class="fw-bold small">Farmer Hotline</div>
                        <div class="small text-muted">+1 (555) 010-FARM</div>
                    </div>
                    <div class="col-md-4 text-center">
                        <i class="bi bi-geo-alt text-success fs-3 mb-2"></i>
                        <div class="fw-bold small">Central Hub</div>
                        <div class="small text-muted">Springfield, IL</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
