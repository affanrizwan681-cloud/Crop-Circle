@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
  <h1 class="mb-4">Your Reviews</h1>
  <div class="table-responsive">
    <table class="table table-hover align-middle">
      <thead class="table-light">
        <tr>
          <th>ID</th>
          <th>Product</th>
          <th>Rating</th>
          <th>Comment</th>
          <th>Reply</th>
          <th>Created At</th>
        </tr>
      </thead>
      <tbody>
        @forelse($reviews as $review)
        <tr>
          <td>{{ $review->id }}</td>
          <td>{{ $review->product->name ?? 'N/A' }}</td>
          <td>{{ $review->rating }} / 5</td>
          <td>{{ $review->comment }}</td>
          <td>
            @if($review->reply)
              {{ $review->reply }}
            @else
              <form method="POST" action="{{ route('farmer.reviews.reply', $review->id) }}" class="d-flex">
                @csrf
                <input type="text" name="reply" class="form-control me-2" placeholder="Write a reply..." required>
                <button class="btn btn-sm btn-primary">Send</button>
              </form>
            @endif
          </td>
          <td>{{ $review->created_at->format('Y-m-d H:i') }}</td>
        </tr>
        @empty
        <tr><td colspan="6" class="text-center">No reviews found.</td></tr>
        @endforelse
      </tbody>
    </table>
    {{ $reviews->links() }}
  </div>
</div>
@endsection
