@extends('layouts.app')

@section('title', 'Direct Farmer Messages — CropCircle')

@section('content')
<div class="container py-4">
    <div class="card card-custom border-0 shadow-sm overflow-hidden" style="min-height: 600px;">
        <div class="row g-0" style="min-height: 600px;">
            <!-- Conversations Sidebar -->
            <div class="col-lg-4 border-end bg-light d-flex flex-column" style="max-height: 700px;">
                <div class="p-3 border-bottom bg-white d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0"><i class="bi bi-chat-dots-fill text-success me-2"></i> Messages</h5>
                    <span class="badge bg-success bg-opacity-10 text-success fw-bold">{{ $conversations->count() }} Threads</span>
                </div>

                <div class="overflow-y-auto flex-grow-1 p-2" id="conversationList">
                    @forelse($conversations as $c)
                        @php
                            $otherUser = $c->getOtherUser(auth()->user());
                            $unread = $c->unreadCountForUser(auth()->id());
                            $isActive = $activeConversation && $activeConversation->id === $c->id;
                        @endphp

                        <a href="{{ route('chat.show', $c->id) }}" class="p-3 rounded-3 d-block text-decoration-none text-dark mb-2 border transition {{ $isActive ? 'bg-white shadow-sm border-success border-2' : 'bg-white' }}">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center fw-bold small" style="width: 32px; height: 32px;">
                                        {{ strtoupper(substr($otherUser->name, 0, 1)) }}
                                    </div>
                                    <span class="fw-bold text-truncate" style="max-width: 140px;">
                                        {{ $otherUser->farmerProfile->farm_name ?? $otherUser->name }}
                                    </span>
                                </div>
                                <span class="small text-muted" style="font-size: 0.75rem;">
                                    {{ $c->last_message_at ? $c->last_message_at->diffForHumans() : '' }}
                                </span>
                            </div>

                            <div class="d-flex justify-content-between align-items-center">
                                <span class="small text-secondary text-truncate" style="max-width: 200px;">
                                    {{ $c->latestMessage ? $c->latestMessage->message : 'No messages yet' }}
                                </span>
                                @if($unread > 0)
                                    <span class="badge bg-danger rounded-pill">{{ $unread }}</span>
                                @endif
                            </div>

                            @if($c->product)
                                <div class="mt-1">
                                    <span class="badge bg-light text-muted border" style="font-size: 0.68rem;">
                                        <i class="bi bi-tag me-1"></i> {{ $c->product->name }}
                                    </span>
                                </div>
                            @endif
                        </a>
                    @empty
                        <div class="p-4 text-center text-muted small">
                            <i class="bi bi-chat-square-text display-6 d-block mb-2 opacity-50"></i>
                            No conversations yet. Start a chat directly from any farmer or products page!
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Active Chat Thread -->
            <div class="col-lg-8 d-flex flex-column bg-white" style="max-height: 700px;">
                @if($activeConversation)
                    @php
                        $otherUser = $activeConversation->getOtherUser(auth()->user());
                    @endphp

                    <!-- Chat Header -->
                    <div class="p-3 border-bottom d-flex justify-content-between align-items-center bg-white shadow-xs">
                        <div class="d-flex align-items-center gap-2">
                            <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center fw-bold fs-5" style="width: 42px; height: 42px;">
                                {{ strtoupper(substr($otherUser->name, 0, 1)) }}
                            </div>
                            <div>
                                <h6 class="fw-bold mb-0">{{ $otherUser->farmerProfile->farm_name ?? $otherUser->name }}</h6>
                                <span class="small text-muted">
                                    <i class="bi bi-patch-check-fill text-success me-1"></i>
                                    {{ ucfirst($otherUser->role) }} • {{ $otherUser->farmerProfile->city ?? 'Local' }}
                                </span>
                            </div>
                        </div>

                        <div>
                            @if($activeConversation->product)
                                <a href="{{ route('products.show', $activeConversation->product->slug) }}" class="btn btn-sm btn-outline-success">
                                    <i class="bi bi-box me-1"></i> {{ $activeConversation->product->name }}
                                </a>
                            @endif
                            @if($activeConversation->farmer_id && auth()->user()->isCustomer())
                                <a href="{{ route('farmers.show', $activeConversation->farmer_id) }}" class="btn btn-sm btn-light border ms-1">
                                    View Stall
                                </a>
                            @endif
                        </div>
                    </div>

                    <!-- Messages History Container -->
                    <div class="flex-grow-1 p-3 overflow-y-auto" id="messagesContainer" style="background-color: #f8fafc;">
                        @foreach($activeConversation->messages as $msg)
                            @php
                                $isMe = $msg->sender_id === auth()->id();
                            @endphp
                            <div class="d-flex {{ $isMe ? 'justify-content-end' : 'justify-content-start' }} mb-3">
                                <div class="p-3 rounded-4 shadow-xs" style="max-width: 75%; background-color: {{ $isMe ? '#2d6a4f' : '#ffffff' }}; color: {{ $isMe ? '#ffffff' : '#1e293b' }}; border: {{ $isMe ? 'none' : '1px solid #e2e8f0' }};">
                                    <div class="small fw-bold mb-1 opacity-75" style="font-size: 0.72rem;">
                                        {{ $isMe ? 'You' : $msg->sender->name }}
                                    </div>
                                    <div class="message-text mb-1" style="word-break: break-word;">{{ $msg->message }}</div>
                                    <div class="text-end small opacity-50" style="font-size: 0.65rem;">
                                        {{ $msg->created_at->format('g:i A') }}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Message Send Form -->
                    <div class="p-3 border-top bg-white">
                        <form id="chatForm" action="{{ route('chat.message', $activeConversation->id) }}" method="POST">
                            @csrf
                            <div class="input-group">
                                <input type="text" name="message" id="messageInput" class="form-control" placeholder="Type your message to {{ $otherUser->name }}..." required autocomplete="off">
                                <button type="submit" class="btn btn-cropcircle px-4 fw-bold">
                                    Send <i class="bi bi-send-fill ms-1"></i>
                                </button>
                            </div>
                        </form>
                    </div>

                @else
                    <div class="h-100 d-flex flex-column align-items-center justify-content-center text-center p-5 text-muted">
                        <i class="bi bi-chat-heart display-1 opacity-50 mb-3 text-success"></i>
                        <h5 class="fw-bold text-dark">No Conversation Selected</h5>
                        <p class="small text-muted" style="max-width: 320px;">
                            Choose an existing thread from the left, or message a farmer directly from their profile page.
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@if($activeConversation)
@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const container = document.getElementById('messagesContainer');
        const form = document.getElementById('chatForm');
        const input = document.getElementById('messageInput');
        const conversationId = {{ $activeConversation->id }};

        // Scroll to bottom
        function scrollToBottom() {
            container.scrollTop = container.scrollHeight;
        }
        scrollToBottom();

        // AJAX Send Message
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            const text = input.value.trim();
            if (!text) return;

            fetch(form.action, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ message: text })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    input.value = '';
                    appendMessage(data.message.text, 'You', true, data.message.time);
                    scrollToBottom();
                }
            })
            .catch(err => form.submit()); // Fallback to traditional POST if AJAX fails
        });

        // Polling for incoming messages every 4 seconds
        let lastMessageId = {{ $activeConversation->messages->last()?->id ?? 0 }};

        setInterval(function () {
            fetch(`/chat/${conversationId}/poll?last_id=${lastMessageId}`, {
                headers: { 'Accept': 'application/json' }
            })
            .then(res => res.json())
            .then(data => {
                if (data.messages && data.messages.length > 0) {
                    data.messages.forEach(msg => {
                        if (!msg.is_me) {
                            appendMessage(msg.text, msg.sender_name, false, msg.time);
                        }
                        lastMessageId = Math.max(lastMessageId, msg.id);
                    });
                    scrollToBottom();
                }
            })
            .catch(e => console.log('Poll waiting...'));
        }, 4000);

        function appendMessage(text, sender, isMe, time) {
            const wrapper = document.createElement('div');
            wrapper.className = `d-flex ${isMe ? 'justify-content-end' : 'justify-content-start'} mb-3`;
            wrapper.innerHTML = `
                <div class="p-3 rounded-4 shadow-xs" style="max-width: 75%; background-color: ${isMe ? '#2d6a4f' : '#ffffff'}; color: ${isMe ? '#ffffff' : '#1e293b'}; border: ${isMe ? 'none' : '1px solid #e2e8f0'};">
                    <div class="small fw-bold mb-1 opacity-75" style="font-size: 0.72rem;">${sender}</div>
                    <div class="message-text mb-1" style="word-break: break-word;">${text}</div>
                    <div class="text-end small opacity-50" style="font-size: 0.65rem;">${time}</div>
                </div>
            `;
            container.appendChild(wrapper);
        }
    });
</script>
@endpush
@endif
@endsection
