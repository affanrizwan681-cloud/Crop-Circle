@extends('layouts.app')

@section('title', 'Website Feedback â€” CropCircle')

@section('content')
<div class="container py-5" style="max-width: 920px;">
    <div class="mb-4">
        <span class="badge bg-success-lt text-success mb-2">Your voice helps us improve</span>
        <h1 class="h2 fw-bold mb-2">Share website feedback</h1>
        <p class="text-muted mb-0">Tell us about your experience, report a problem, or suggest an improvement.</p>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4 p-md-5">
                    <form method="POST" action="{{ route('feedback.store') }}">
                        @csrf
                        <div class="mb-3">
                            <label for="feedback-type" class="form-label fw-semibold">What would you like to share?</label>
                            <select id="feedback-type" name="type" class="form-select @error('type') is-invalid @enderror" required>
                                <option value="">Choose a topic</option>
                                <option value="review" @selected(old('type') === 'review')>Website review</option>
                                <option value="issue" @selected(old('type') === 'issue')>Issue or problem</option>
                                <option value="suggestion" @selected(old('type') === 'suggestion')>Suggestion</option>
                            </select>
                            @error('type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label for="feedback-subject" class="form-label fw-semibold">Subject</label>
                            <input id="feedback-subject" name="subject" value="{{ old('subject') }}" maxlength="180" class="form-control @error('subject') is-invalid @enderror" placeholder="A short title for your feedback" required>
                            @error('subject')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label for="feedback-message" class="form-label fw-semibold">Details</label>
                            <textarea id="feedback-message" name="message" rows="6" maxlength="4000" class="form-control @error('message') is-invalid @enderror" placeholder="Please describe your experience or the problem you ran into..." required>{{ old('message') }}</textarea>
                            @error('message')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-4">
                            <label for="feedback-rating" class="form-label fw-semibold">Your website rating <span class="text-muted fw-normal">(optional)</span></label>
                            <select id="feedback-rating" name="rating" class="form-select @error('rating') is-invalid @enderror">
                                <option value="">No rating</option>
                                @foreach(range(5, 1) as $rating)
                                    <option value="{{ $rating }}" @selected((string) old('rating') === (string) $rating)>{{ $rating }} {{ $rating === 1 ? 'star' : 'stars' }}</option>
                                @endforeach
                            </select>
                            @error('rating')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="form-check mb-4 p-3 rounded border bg-light" id="public-review-consent" hidden>
                            <input class="form-check-input" type="checkbox" value="1" id="feedback-public-consent" name="public_consent" @checked(old('public_consent'))>
                            <label class="form-check-label" for="feedback-public-consent">I agree that CropCircle may display this website review, my name, my customer/farmer role, and rating publicly if an administrator selects it for the homepage.</label>
                            <div class="small text-muted mt-1">This is optional. Without consent, your feedback stays private to admins.</div>
                            @error('public_consent')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                        </div>
                        <button type="submit" class="btn btn-cropcircle px-4"><i class="bi bi-send me-2"></i>Send to admin</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card border-0 bg-success-lt h-100">
                <div class="card-body p-4">
                    <div class="avatar bg-success text-white mb-3"><i class="bi bi-shield-lock"></i></div>
                    <h2 class="h5 fw-bold">Private to admins</h2>
                    <p class="text-muted small mb-0">Issues and suggestions remain private to admins. Website reviews are also private unless you opt in above and an administrator features them on the homepage. Please do not include passwords or payment details.</p>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var type = document.getElementById('feedback-type');
    var consent = document.getElementById('public-review-consent');
    var checkbox = document.getElementById('feedback-public-consent');
    function updateConsent() {
        var isReview = type && type.value === 'review';
        consent.hidden = !isReview;
        if (!isReview) checkbox.checked = false;
    }
    type.addEventListener('change', updateConsent);
    updateConsent();
});
</script>
@endsection