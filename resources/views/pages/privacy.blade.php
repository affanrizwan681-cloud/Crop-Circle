@extends('layouts.app')

@section('title', 'Privacy Policy — CropCircle')

@section('content')
<div class="container py-5">
    <div class="card card-custom p-4 p-md-5 border-0 shadow-sm mx-auto" style="max-width: 900px;">
        <h2 class="fw-bold mb-4">Privacy Policy</h2>
        <div class="text-secondary small d-flex flex-column gap-3 leading-relaxed">
            <p><strong>1. Information Collection:</strong> CropCircle collects account information (name, email, phone number, general location) solely to facilitate farmer-customer order fulfillment and private messaging.</p>
            <p><strong>2. No Payment Card Storage:</strong> CropCircle supports offline payment upon pickup and does not collect, process, or store sensitive credit card credentials.</p>
            <p><strong>3. Communications:</strong> Private chat messages between consumers and producers are restricted solely to the authorized participants and platform administrators for moderation purposes.</p>
            <p><strong>4. Data Security:</strong> Passwords are cryptographically hashed and all database records are protected with industry-standard server authorization.</p>
        </div>
    </div>
</div>
@endsection
