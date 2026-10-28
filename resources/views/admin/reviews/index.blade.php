@extends('layouts.admin')

@section('admin-content')
<div class="container-fluid py-4">
  <div class="mb-4">
    <span class="text-success text-uppercase small fw-bold">Purchase review moderation</span>
    <h1 class="h2 fw-bold mb-1">Customer Reviews</h1>
    <p class="text-muted mb-0">These are product and order reviews. Website testimonials are managed separately under Website Feedback.</p>
  </div>
  <div class="card border-0 shadow-sm"><div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
      <thead class="table-light"><tr><th>Review</th><th>Reviewer</th><th>Farmer</th><th>Rating</th><th>Approval</th><th>Actions</th></tr></thead>
      <tbody>
      @forelse($reviews as $review)
        <tr>
          <td style="min-width:280px"><div class="fw-semibold">{{ $review->product?->name ?? 'Order review' }}</div><div class="small text-muted" style="white-space:pre-line">{{ $review->comment }}</div><div class="small text-muted mt-2">{{ $review->created_at->format('M d, Y') }}</div></td>
          <td>{{ $review->customer?->name ?? 'Deleted account' }}<div><span class="badge bg-primary-lt">{{ ucfirst($review->customer?->role ?? 'unknown') }}</span></div></td>
          <td>{{ $review->farmer?->farmerProfile?->farm_name ?? $review->farmer?->name ?? 'Farmer' }}</td>
          <td><span class="text-warning">@for($star=1;$star<=5;$star++)<i class="bi {{ $star <= $review->rating ? 'bi-star-fill' : 'bi-star' }}"></i>@endfor</span> {{ $review->rating }}/5</td>
          <td>@if($review->is_moderated)<span class="badge bg-success-lt text-success">Approved</span>@else<span class="badge bg-warning-lt text-dark">Pending</span>@endif</td>
          <td><div class="d-flex flex-column gap-2">
            <form method="POST" action="{{ route('admin.reviews.toggleModeration', $review->id) }}">@csrf<button class="btn btn-sm {{ $review->is_moderated ? 'btn-outline-warning' : 'btn-outline-success' }}">{{ $review->is_moderated ? 'Revoke approval' : 'Approve review' }}</button></form>
            <form method="POST" action="{{ route('admin.reviews.destroy', $review->id) }}" onsubmit="return confirm('Delete this review permanently?')">@csrf @method('DELETE')<button class="btn btn-sm btn-link text-danger">Delete review</button></form>
          </div></td>
        </tr>
      @empty
        <tr><td colspan="6" class="text-center text-muted py-5">No purchase reviews have been submitted yet.</td></tr>
      @endforelse
      </tbody>
    </table>
  </div><div class="card-footer bg-white">{{ $reviews->links() }}</div></div>
</div>
@endsection