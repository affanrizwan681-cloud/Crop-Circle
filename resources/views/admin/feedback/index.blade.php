@extends('layouts.admin')

@section('title', 'Website Feedback â€” CropCircle Admin')

@section('admin-content')
<div class="container-fluid py-4">
    <div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4">
        <div>
            <div class="text-success text-uppercase small fw-bold">Private admin inbox</div>
            <h1 class="h2 fw-bold mb-1">Website Reviews & Issues</h1>
            <p class="text-muted mb-0">Read feedback sent by customers and farmers.</p>
        </div>
        <span class="badge bg-warning-lt text-dark fs-6">{{ $newFeedbackCount }} new</span>
    </div>

    <form method="GET" action="{{ route('admin.feedback.index') }}" class="card card-body border-0 shadow-sm mb-4">
        <div class="row g-2 align-items-end">
            <div class="col-sm-5 col-md-4">
                <label class="form-label small fw-semibold" for="feedback-filter-type">Type</label>
                <select class="form-select" id="feedback-filter-type" name="type">
                    <option value="">All types</option>
                    @foreach(['review' => 'Website review', 'issue' => 'Issue or problem', 'suggestion' => 'Suggestion'] as $value => $label)
                        <option value="{{ $value }}" @selected(request('type') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-sm-5 col-md-4">
                <label class="form-label small fw-semibold" for="feedback-filter-status">Status</label>
                <select class="form-select" id="feedback-filter-status" name="status">
                    <option value="">All statuses</option>
                    @foreach(['new' => 'New', 'reviewed' => 'Reviewed', 'resolved' => 'Resolved'] as $value => $label)
                        <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-sm-2 col-md-4 d-flex gap-2">
                <button class="btn btn-success" type="submit">Filter</button>
                <a class="btn btn-outline-secondary" href="{{ route('admin.feedback.index') }}">Clear</a>
            </div>
        </div>
    </form>

    <div class="d-grid gap-3">
        @forelse($feedbackItems as $entry)
            <article class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-3">
                        <div>
                            <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                                <span class="badge bg-{{ $entry->type === 'issue' ? 'danger' : ($entry->type === 'review' ? 'primary' : 'success') }}-lt">{{ ucfirst($entry->type === 'issue' ? 'Issue / problem' : $entry->type) }}</span>
                                <span class="badge bg-{{ $entry->status === 'resolved' ? 'success' : ($entry->status === 'reviewed' ? 'azure' : 'warning') }}-lt">{{ ucfirst($entry->status) }}</span>
                                @if($entry->is_featured)<span class="badge bg-success-lt text-success">Featured on homepage</span>@endif
                                @if($entry->type === 'review' && !$entry->public_consent)<span class="badge bg-secondary-lt text-secondary">Private: no public consent</span>@endif
                            </div>
                            <h2 class="h4 fw-bold mb-1">{{ $entry->subject }}</h2>
                            <div class="small text-muted">
                                Submitted by <strong class="text-dark">{{ $entry->user?->name ?? 'Deleted account' }}</strong>
                                <span class="badge bg-secondary-lt text-secondary ms-1">{{ ucfirst($entry->submitter_role) }}</span>
                                @if($entry->user?->email)<span class="ms-2">{{ $entry->user->email }}</span>@endif
                                <span class="ms-2">{{ $entry->created_at->format('M d, Y g:i A') }}</span>
                            </div>
                        </div>
                        @if($entry->rating)
                            <div class="text-warning fw-bold" aria-label="{{ $entry->rating }} out of 5 stars">
                                @for($star = 1; $star <= 5; $star++)<i class="ti ti-star{{ $star <= $entry->rating ? '-filled' : '' }}"></i>@endfor
                                <span class="text-dark small ms-1">{{ $entry->rating }}/5</span>
                            </div>
                        @endif
                    </div>
                    <p class="mb-4" style="white-space: pre-line;">{{ $entry->message }}</p>
                    <form method="POST" action="{{ route('admin.feedback.updateStatus', $entry->id) }}" class="d-flex flex-wrap align-items-end gap-2 border-top pt-3">
                        @csrf
                        @method('PUT')
                        <div>
                            <label for="feedback-status-{{ $entry->id }}" class="form-label small mb-1">Update status</label>
                            <select class="form-select form-select-sm" id="feedback-status-{{ $entry->id }}" name="status">
                                @foreach(['new' => 'New', 'reviewed' => 'Reviewed', 'resolved' => 'Resolved'] as $value => $label)
                                    <option value="{{ $value }}" @selected($entry->status === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button class="btn btn-sm btn-outline-success">Save status</button>
                    </form>
                    @if($entry->type === 'review')
                        <form method="POST" action="{{ route('admin.feedback.feature', $entry->id) }}" class="mt-3">
                            @csrf
                            @method('PATCH')
                            <button class="btn btn-sm {{ $entry->is_featured ? 'btn-outline-danger' : 'btn-success' }}" {{ !$entry->is_featured && (!$entry->public_consent || $entry->status !== 'reviewed') ? 'disabled' : '' }}>
                                {{ $entry->is_featured ? 'Remove from homepage' : 'Feature this website review' }}
                            </button>
                            @if(!$entry->is_featured && (!$entry->public_consent || $entry->status !== 'reviewed'))
                                <span class="small text-muted ms-2">Requires public consent and Reviewed status.</span>
                            @endif
                        </form>
                    @endif
                </div>
            </article>
        @empty
            <div class="card border-0 shadow-sm"><div class="card-body text-center py-5 text-muted">No website feedback matches these filters.</div></div>
        @endforelse
    </div>

    <div class="mt-4">{{ $feedbackItems->links() }}</div>
</div>
@endsection