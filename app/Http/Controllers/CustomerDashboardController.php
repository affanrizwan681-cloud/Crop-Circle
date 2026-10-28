<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Favorite;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CustomerDashboardController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();

        $recentOrders = Order::where('customer_id', $user->id)
            ->with(['farmer.farmerProfile', 'items', 'market', 'pickupSlot'])
            ->latest()
            ->take(5)
            ->get();

        $totalOrders = Order::where('customer_id', $user->id)->count();
        $activeOrders = Order::where('customer_id', $user->id)
            ->whereIn('status', [Order::STATUS_PENDING, Order::STATUS_ACCEPTED, Order::STATUS_READY])
            ->count();

        $completedOrders = Order::where('customer_id', $user->id)
            ->where('status', Order::STATUS_COMPLETED)
            ->count();

        $favoritesCount = Favorite::where('customer_id', $user->id)->count();

        $recentConversations = Conversation::where('customer_id', $user->id)
            ->with(['farmer.farmerProfile', 'latestMessage'])
            ->orderBy('last_message_at', 'desc')
            ->take(3)
            ->get();

        $unreadNotifications = $user->unreadNotifications()->take(5)->get();

        return view('customer.dashboard', compact(
            'user',
            'recentOrders',
            'totalOrders',
            'activeOrders',
            'completedOrders',
            'favoritesCount',
            'recentConversations',
            'unreadNotifications'
        ));
    }
}
