@extends('layouts.app')

@section('title', 'Notifications Center — CropCircle')

@section('content')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1"><i class="bi bi-bell-fill text-warning me-2"></i> Notifications Center</h2>
            <p class="text-muted small mb-0">Stay updated on order status changes, farm messages, and community alerts</p>
        </div>

        @if(auth()->user()->unreadNotifications()->count() > 0)
            <form action="{{ route('notifications.readAll') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-outline-success btn-sm rounded-pill fw-bold px-3">
                    <i class="bi bi-check2-all me-1"></i> Mark All as Read
                </button>
            </form>
        @endif
    </div>

    <div class="card card-custom border-0 shadow-sm overflow-hidden p-3">
        @forelse($notifications as $notif)
            <div class="p-3 rounded-3 mb-2 d-flex justify-content-between align-items-center {{ !$notif->is_read ? 'bg-light border-start border-success border-4 fw-medium' : 'border-bottom' }}">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-success bg-opacity-10 text-success p-2 rounded-circle fs-5">
                        <i class="bi {{ $notif->type === 'order' ? 'bi-box-seam' : ($notif->type === 'message' ? 'bi-chat-dots' : 'bi-info-circle') }}"></i>
                    </div>
                    <div>
                        <div class="fw-bold text-dark">{{ $notif->title }}</div>
                        <div class="small text-secondary">{{ $notif->message }}</div>
                        <span class="small text-muted" style="font-size: 0.75rem;">{{ $notif->created_at->diffForHumans() }}</span>
                    </div>
                </div>

                <div>
                    @if(!$notif->is_read)
                        <a href="{{ route('notifications.read', $notif->id) }}" class="btn btn-sm btn-outline-success rounded-pill px-3">
                            View & Read
                        </a>
                    @elseif($notif->link)
                        <a href="{{ $notif->link }}" class="btn btn-sm btn-light border rounded-pill px-3">
                            Open
                        </a>
                    @endif
                </div>
            </div>
        @empty
            <div class="text-center py-5 text-muted">
                <i class="bi bi-bell-slash display-4 mb-2 d-block opacity-50"></i>
                No notifications found.
            </div>
        @endforelse
    </div>

    <div class="mt-4 d-flex justify-content-center">
        {{ $notifications->links('pagination::bootstrap-5') }}
    </div>
</div>
@endsection
