<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\Notification;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ChatController extends Controller
{
    public function index(): View
    {
        $userId = Auth::id();
        $isFarmer = Auth::user()->isFarmer();

        $conversations = Conversation::where($isFarmer ? 'farmer_id' : 'customer_id', $userId)
            ->with(['customer', 'farmer.farmerProfile', 'product', 'latestMessage'])
            ->orderBy('last_message_at', 'desc')
            ->get();

        $activeConversation = $conversations->first();

        if ($activeConversation) {
            $activeConversation->markAsReadForUser($userId);
            $activeConversation->load(['messages.sender']);
        }

        return view('chat.index', compact('conversations', 'activeConversation'));
    }

    public function show(int $id): View
    {
        $userId = Auth::id();
        $isFarmer = Auth::user()->isFarmer();

        $conversation = Conversation::with(['customer', 'farmer.farmerProfile', 'product', 'order', 'messages.sender'])
            ->findOrFail($id);

        if ($conversation->customer_id !== $userId && $conversation->farmer_id !== $userId) {
            abort(403, 'Unauthorized conversation access.');
        }

        $conversation->markAsReadForUser($userId);

        $conversations = Conversation::where($isFarmer ? 'farmer_id' : 'customer_id', $userId)
            ->with(['customer', 'farmer.farmerProfile', 'product', 'latestMessage'])
            ->orderBy('last_message_at', 'desc')
            ->get();

        $activeConversation = $conversation;

        return view('chat.index', compact('conversations', 'activeConversation'));
    }

    public function start(Request $request, int $farmerId): RedirectResponse
    {
        $farmer = User::where('role', 'farmer')->findOrFail($farmerId);

        if ($farmer->id === Auth::id()) {
            return back()->with('warning', 'You cannot start a conversation with yourself.');
        }

        $productId = $request->query('product_id');
        $orderId = $request->query('order_id');

        $conversation = Conversation::firstOrCreate(
            [
                'customer_id' => Auth::id(),
                'farmer_id' => $farmer->id,
            ],
            [
                'product_id' => $productId,
                'order_id' => $orderId,
                'last_message_at' => now(),
            ]
        );

        if ($productId && !$conversation->product_id) {
            $conversation->update(['product_id' => $productId]);
        }
        if ($orderId && !$conversation->order_id) {
            $conversation->update(['order_id' => $orderId]);
        }

        return redirect()->route('chat.show', $conversation->id);
    }

    public function sendMessage(Request $request, int $id): JsonResponse|RedirectResponse
    {
        $request->validate([
            'message' => ['required', 'string', 'max:2000'],
        ]);

        $conversation = Conversation::findOrFail($id);
        $userId = Auth::id();

        if ($conversation->customer_id !== $userId && $conversation->farmer_id !== $userId) {
            abort(403, 'Unauthorized conversation action.');
        }

        $message = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $userId,
            'message' => trim($request->message),
            'is_read' => false,
        ]);

        $conversation->update(['last_message_at' => now()]);

        // Notify recipient
        $recipientId = $conversation->customer_id === $userId ? $conversation->farmer_id : $conversation->customer_id;
        Notification::send(
            $recipientId,
            'New Message from ' . Auth::user()->name,
            mb_substr($message->message, 0, 80) . '...',
            'message',
            "/chat/{$conversation->id}"
        );

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => [
                    'id' => $message->id,
                    'text' => $message->message,
                    'sender_name' => Auth::user()->name,
                    'is_me' => true,
                    'time' => $message->created_at->format('g:i A'),
                ],
            ]);
        }

        return back()->with('success', 'Message sent.');
    }

    public function pollMessages(Request $request, int $id): JsonResponse
    {
        $conversation = Conversation::findOrFail($id);
        $userId = Auth::id();

        if ($conversation->customer_id !== $userId && $conversation->farmer_id !== $userId) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $lastId = (int) $request->query('last_id', 0);

        $messages = $conversation->messages()
            ->where('id', '>', $lastId)
            ->with('sender')
            ->get();

        // Mark incoming messages as read
        $conversation->markAsReadForUser($userId);

        $formatted = $messages->map(function ($msg) use ($userId) {
            return [
                'id' => $msg->id,
                'text' => $msg->message,
                'sender_name' => $msg->sender->name,
                'is_me' => $msg->sender_id === $userId,
                'time' => $msg->created_at->format('g:i A'),
            ];
        });

        return response()->json([
            'messages' => $formatted,
            'count' => $formatted->count(),
        ]);
    }
}
