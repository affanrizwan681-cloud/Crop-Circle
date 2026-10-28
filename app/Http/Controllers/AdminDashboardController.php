<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Announcement;
use App\Models\Category;
use App\Models\FarmerProfile;
use App\Models\Market;
use App\Models\Notification;
use App\Models\Order;
use App\Models\Product;
use App\Models\Report;
use App\Models\Review;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    public function dashboard(): View
    {
        $totalCustomers = User::where('role', 'customer')->count();
        $totalFarmers = User::where('role', 'farmer')->count();
        $pendingApprovals = FarmerProfile::where('is_approved', false)->count();

        $totalProducts = Product::count();
        $totalMarkets = Market::count();
        $totalOrders = Order::count();

        $totalVolume = Order::where('status', Order::STATUS_COMPLETED)->sum('total_amount');
        $openReports = Report::where('status', 'pending')->count();

        $recentRegistrations = User::latest()->take(5)->get();
        $pendingFarmersList = FarmerProfile::where('is_approved', false)->with('user')->take(5)->get();
        $recentOrders = Order::with(['customer', 'farmer.farmerProfile'])->latest()->take(5)->get();
        $recentLogs = ActivityLog::with('user')->latest()->take(6)->get();

        return view('admin.dashboard', compact(
            'totalCustomers',
            'totalFarmers',
            'pendingApprovals',
            'totalProducts',
            'totalMarkets',
            'totalOrders',
            'totalVolume',
            'openReports',
            'recentRegistrations',
            'pendingFarmersList',
            'recentOrders',
            'recentLogs'
        ));
    }

    // --- FARMER APPROVALS & ACCOUNTS ---
    public function farmersIndex(): View
    {
        $farmers = FarmerProfile::with(['user', 'markets'])
            ->latest()
            ->paginate(12);

        return view('admin.farmers.index', compact('farmers'));
    }

    public function approveFarmer(int $id): RedirectResponse
    {
        $profile = FarmerProfile::findOrFail($id);
        $profile->update([
            'is_approved' => true,
            'rejection_reason' => null,
        ]);

        $profile->user->update(['status' => 'active']);

        Notification::send(
            $profile->user_id,
            'Farmer Account Approved!',
            'Congratulations! Your farm profile has been verified. You can now publish products and accept orders.',
            'farmer_approval',
            '/farmer/dashboard'
        );

        ActivityLog::record(Auth::id(), 'farmer_approved', 'Approved farmer profile: ' . $profile->farm_name);

        return back()->with('success', 'Farmer "' . $profile->farm_name . '" has been approved and activated.');
    }

    public function rejectFarmer(Request $request, int $id): RedirectResponse
    {
        $request->validate([
            'rejection_reason' => ['required', 'string', 'max:500'],
        ]);

        $profile = FarmerProfile::findOrFail($id);
        $profile->update([
            'is_approved' => false,
            'rejection_reason' => $request->rejection_reason,
        ]);

        Notification::send(
            $profile->user_id,
            'Farmer Application Update',
            'Your farmer application requires changes: ' . $request->rejection_reason,
            'farmer_approval',
            '/farmer/pending'
        );

        ActivityLog::record(Auth::id(), 'farmer_rejected', 'Rejected farmer application: ' . $profile->farm_name);

        return back()->with('warning', 'Farmer application declined with notice provided.');
    }

    public function toggleFarmerStatus(int $id): RedirectResponse
    {
        $user = User::where('role', 'farmer')->findOrFail($id);
        $newStatus = $user->status === 'active' ? 'suspended' : 'active';
        $user->update(['status' => $newStatus]);

        ActivityLog::record(Auth::id(), 'user_status_changed', "Admin toggled farmer {$user->name} status to {$newStatus}.");

        return back()->with('info', "Farmer account status updated to {$newStatus}.");
    }

    // --- USER MANAGEMENT ---
    public function usersIndex(Request $request): View
    {
        $query = User::latest();

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }
        if ($request->filled('q')) {
            $s = $request->q;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")->orWhere('email', 'like', "%{$s}%");
            });
        }

        $users = $query->paginate(15)->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    public function toggleUserStatus(int $id): RedirectResponse
    {
        $user = User::findOrFail($id);

        if ($user->isAdmin() && $user->id === Auth::id()) {
            return back()->with('danger', 'You cannot suspend your own administrator account.');
        }

        $newStatus = $user->status === 'active' ? 'suspended' : 'active';
        $user->update(['status' => $newStatus]);

        ActivityLog::record(Auth::id(), 'user_status_changed', "Admin toggled user {$user->name} status to {$newStatus}.");

        return back()->with('info', "User {$user->name} status changed to {$newStatus}.");
    }

    // --- CATEGORIES MANAGEMENT ---
    public function categoriesIndex(): View
    {
        $categories = Category::withCount('products')->get();
        return view('admin.categories.index', compact('categories'));
    }

    public function storeCategory(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:categories'],
            'description' => ['nullable', 'string'],
            'icon' => ['nullable', 'string', 'max:50'],
        ]);

        $category = Category::create([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'description' => $validated['description'] ?? null,
            'icon' => $validated['icon'] ?? 'bi-tag',
            'is_active' => true,
        ]);

        ActivityLog::record(Auth::id(), 'category_created', 'Created category: ' . $category->name);

        return back()->with('success', 'Category "' . $category->name . '" created.');
    }

    public function updateCategory(Request $request, int $id): RedirectResponse
    {
        $category = Category::findOrFail($id);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:categories,name,' . $category->id],
            'description' => ['nullable', 'string'],
            'icon' => ['nullable', 'string', 'max:50'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $category->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'icon' => $validated['icon'] ?? $category->icon,
            'is_active' => $request->boolean('is_active'),
        ]);

        ActivityLog::record(Auth::id(), 'category_updated', 'Updated category: ' . $category->name);

        return back()->with('success', 'Category updated successfully.');
    }

    public function deleteCategory(int $id): RedirectResponse
    {
        $category = Category::withCount('products')->findOrFail($id);

        if ($category->products_count > 0) {
            return back()->with('danger', 'Cannot delete category with associated products. Reassign products first.');
        }

        $name = $category->name;
        $category->delete();

        ActivityLog::record(Auth::id(), 'category_deleted', 'Deleted category: ' . $name);

        return back()->with('info', 'Category "' . $name . '" deleted.');
    }

    // --- MARKETS MANAGEMENT ---
    public function marketsIndex(): View
    {
        $markets = Market::withCount('approvedFarmers')->paginate(10)->withQueryString();
        $pendingMarketApplications = FarmerProfile::whereHas('markets', function ($query) {
            $query->where('market_farmer.status', 'pending');
        })->with(['user', 'markets' => function ($query) {
            $query->where('market_farmer.status', 'pending');
        }])->get();

        return view('admin.markets.index', compact('markets', 'pendingMarketApplications'));
    }

    public function approveMarketApplication(Request $request, int $farmerProfileId, int $marketId): RedirectResponse
    {
        $validated = $request->validate([
            'assigned_stall' => ['nullable', 'string', 'max:100'],
        ]);
        $profile = FarmerProfile::with('user')->findOrFail($farmerProfileId);
        $market = Market::active()->findOrFail($marketId);
        $profile->markets()->where('markets.id', $marketId)->wherePivot('status', 'pending')->firstOrFail();
        $profile->markets()->updateExistingPivot($marketId, [
            'status' => 'approved',
            'assigned_stall' => $validated['assigned_stall'] ?? null,
        ]);

        Notification::send(
            $profile->user_id,
            'Market Application Approved',
            'Your application to join ' . $market->name . ' has been approved.',
            'market_application',
            '/farmer/markets'
        );
        ActivityLog::record(Auth::id(), 'market_application_approved', 'Approved market application for ' . $profile->farm_name);

        return back()->with('success', 'Market application approved.');
    }

    public function rejectMarketApplication(int $farmerProfileId, int $marketId): RedirectResponse
    {
        $profile = FarmerProfile::with('user')->findOrFail($farmerProfileId);
        $market = Market::findOrFail($marketId);
        $profile->markets()->where('markets.id', $marketId)->wherePivot('status', 'pending')->firstOrFail();
        $profile->markets()->updateExistingPivot($marketId, ['status' => 'rejected']);

        Notification::send(
            $profile->user_id,
            'Market Application Update',
            'Your application to join ' . $market->name . ' was not approved. You may apply again later.',
            'market_application',
            '/farmer/markets'
        );
        ActivityLog::record(Auth::id(), 'market_application_rejected', 'Rejected market application for ' . $profile->farm_name);

        return back()->with('info', 'Market application declined.');
    }

    public function storeMarket(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'location' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string'],
            'description' => ['nullable', 'string'],
            'latitude' => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],
            'operating_days' => ['nullable', 'array'],
            'open_time' => ['required'],
            'close_time' => ['required'],
        ]);

        $market = Market::create([
            'name' => $validated['name'],
            'location' => $validated['location'],
            'address' => $validated['address'],
            'description' => $validated['description'] ?? null,
            'latitude' => $validated['latitude'] ?? null,
            'longitude' => $validated['longitude'] ?? null,
            'operating_days' => $validated['operating_days'] ?? ['Saturday'],
            'open_time' => $validated['open_time'],
            'close_time' => $validated['close_time'],
            'status' => 'active',
        ]);

        ActivityLog::record(Auth::id(), 'market_created', 'Created market: ' . $market->name);

        return back()->with('success', 'Market "' . $market->name . '" created.');
    }

    public function updateMarket(Request $request, int $id): RedirectResponse
    {
        $market = Market::findOrFail($id);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'location' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string'],
            'description' => ['nullable', 'string'],
            'latitude' => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],
            'operating_days' => ['nullable', 'array'],
            'open_time' => ['required'],
            'close_time' => ['required'],
            'status' => ['required', 'in:active,inactive'],
        ]);

        $market->update($validated);

        ActivityLog::record(Auth::id(), 'market_updated', 'Updated market: ' . $market->name);

        return back()->with('success', 'Market updated successfully.');
    }

    // --- MODERATION ---
    public function productsIndex(Request $request): View
    {
        $query = Product::with(['farmer.farmerProfile', 'category'])->latest();

        if ($request->filled('q')) {
            $s = $request->q;
            $query->where('name', 'like', "%{$s}%");
        }

        $products = $query->paginate(15)->withQueryString();

        return view('admin.products.index', compact('products'));
    }

    public function toggleProductModeration(int $id): RedirectResponse
    {
        $product = Product::findOrFail($id);
        $product->update(['is_moderated' => !$product->is_moderated]);

        $statusStr = $product->is_moderated ? 'visible' : 'hidden';
        ActivityLog::record(Auth::id(), 'product_moderation', "Toggled product {$product->name} to {$statusStr}.");
        Notification::send(
            $product->farmer_id,
            $product->is_moderated ? 'Product Approved' : 'Product Hidden',
            $product->is_moderated
                ? "Your product {$product->name} has been approved and is now visible to customers."
                : "Your product {$product->name} has been hidden by an administrator.",
            'product_moderation',
            '/farmer/products'
        );

        return back()->with('info', "Product visibility set to {$statusStr}.");
    }

    public function reviewsIndex(): View
    {
        $reviews = Review::with(['customer', 'farmer.farmerProfile', 'product'])->latest()->paginate(15);
        return view('admin.reviews.index', compact('reviews'));
    }

    public function toggleReviewModeration(int $id): RedirectResponse
    {
        $review = Review::findOrFail($id);
        $review->update(['is_moderated' => !$review->is_moderated]);

        return back()->with('info', 'Review moderation status updated.');
    }
    public function deleteReview(int $id): RedirectResponse
    {
        $review = Review::findOrFail($id);
        $review->delete();

        ActivityLog::record(Auth::id(), 'review_deleted', 'Admin deleted inappropriate review #' . $id);

        return back()->with('info', 'Review deleted.');
    }

    public function reportsIndex(): View
    {
        $reports = Report::with('reporter')->latest()->paginate(15);
        return view('admin.reports.index', compact('reports'));
    }

    public function resolveReport(Request $request, int $id): RedirectResponse
    {
        $validated = $request->validate([
            'admin_notes' => ['required', 'string', 'max:2000'],
        ]);
        $report = Report::findOrFail($id);
        $report->update([
            'status' => 'reviewed',
            'admin_notes' => $validated['admin_notes'],
        ]);

        ActivityLog::record(Auth::id(), 'report_resolved', 'Admin updated report #' . $id);

        return back()->with('success', 'Report marked as resolved.');
    }

    // --- ORDERS OVERSIGHT ---
    public function ordersIndex(Request $request): View
    {
        $query = Order::with(['customer', 'farmer.farmerProfile', 'market', 'pickupSlot'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $orders = $query->paginate(15)->withQueryString();

        return view('admin.orders.index', compact('orders'));
    }

    public function orderShow(int $id): View
    {
        $order = Order::with(['customer', 'farmer.farmerProfile', 'items.product', 'market', 'pickupSlot', 'reviews'])
            ->findOrFail($id);

        return view('admin.orders.show', compact('order'));
    }

    // --- ANNOUNCEMENTS ---
    public function announcementsIndex(): View
    {
        $announcements = Announcement::with('admin')->latest()->paginate(10);
        return view('admin.announcements.index', compact('announcements'));
    }

    public function storeAnnouncement(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string'],
            'target_role' => ['required', 'in:all,farmer,customer'],
            'expires_at' => ['nullable', 'date', 'after:today'],
        ]);

        Announcement::create([
            'admin_id' => Auth::id(),
            'title' => $validated['title'],
            'message' => $validated['message'],
            'target_role' => $validated['target_role'],
            'expires_at' => $validated['expires_at'] ?? null,
            'is_active' => true,
        ]);

        ActivityLog::record(Auth::id(), 'announcement_created', 'Created platform announcement: ' . $validated['title']);

        return back()->with('success', 'Announcement published.');
    }

    public function deleteAnnouncement(int $id): RedirectResponse
    {
        $announcement = Announcement::findOrFail($id);
        $announcement->delete();

        return back()->with('info', 'Announcement deleted.');
    }

    // --- AUDIT ACTIVITY LOGS ---
    public function activityLogsIndex(): View
    {
        $logs = ActivityLog::with('user')->latest()->paginate(25);
        return view('admin.logs.index', compact('logs'));
    }
}
