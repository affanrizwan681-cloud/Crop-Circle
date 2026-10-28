<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\Category;
use App\Models\FarmerProfile;
use App\Models\Market;
use App\Models\Order;
use App\Models\Product;
use App\Models\Review;
use App\Models\WebsiteFeedback;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $categories = Category::active()->withCount(['products' => function ($q) {
            $q->visible()->where('status', 'available');
        }])->get();

        $trendSince = now()->subDays(30);
        $mostSellingProducts = Product::visible()
            ->whereIn('status', ['available', 'sold_out'])
            ->with(['farmer.farmerProfile', 'category'])
            ->withSum(['orderItems as recent_units_sold' => function ($query) use ($trendSince) {
                $query->whereHas('order', function ($orders) use ($trendSince) {
                    $orders->where('status', Order::STATUS_COMPLETED)
                        ->where('updated_at', '>=', $trendSince);
                });
            }], 'quantity')
            ->withSum(['orderItems as lifetime_units_sold' => function ($query) {
                $query->whereHas('order', fn ($orders) => $orders->where('status', Order::STATUS_COMPLETED));
            }], 'quantity')
            ->orderByDesc('recent_units_sold')
            ->orderByDesc('lifetime_units_sold')
            ->orderByDesc('is_featured')
            ->latest()
            ->take(8)
            ->get();

        $markets = Market::active()->withCount('approvedFarmers')->take(3)->get();

        // Only consented, reviewed, admin-featured customer and farmer website feedback is public.
        $featuredTestimonials = WebsiteFeedback::featuredForHomepage()
            ->with('user:id,name,role')
            ->latest()
            ->take(8)
            ->get();

        $reviewStats = Review::query()
            ->select('farmer_id')
            ->selectRaw('AVG(rating) as average_rating, COUNT(*) as review_count')
            ->where('is_moderated', true)
            ->groupBy('farmer_id');

        $recentFarmerOrders = Order::query()
            ->select('farmer_id')
            ->selectRaw('COUNT(*) as recent_order_count')
            ->where('status', Order::STATUS_COMPLETED)
            ->where('updated_at', '>=', $trendSince)
            ->groupBy('farmer_id');

        $bestFarmers = FarmerProfile::query()
            ->select('farmer_profiles.*')
            ->leftJoinSub($reviewStats, 'review_stats', fn ($join) => $join->on('farmer_profiles.user_id', '=', 'review_stats.farmer_id'))
            ->leftJoinSub($recentFarmerOrders, 'recent_orders', fn ($join) => $join->on('farmer_profiles.user_id', '=', 'recent_orders.farmer_id'))
            ->addSelect(DB::raw('COALESCE(review_stats.average_rating, 0) as average_rating'))
            ->addSelect(DB::raw('COALESCE(review_stats.review_count, 0) as review_count'))
            ->addSelect(DB::raw('COALESCE(recent_orders.recent_order_count, 0) as recent_order_count'))
            ->where('farmer_profiles.is_approved', true)
            ->whereHas('user', fn ($query) => $query->where('status', 'active'))
            ->with('user')
            ->orderByDesc('recent_order_count')
            ->orderByDesc('average_rating')
            ->orderByDesc('review_count')
            ->take(4)
            ->get();
        $announcements = Announcement::active()->forRole(auth()->user()?->role)->latest()->take(2)->get();
        $productAds = \App\Models\ProductAd::where('status','approved')->whereHas('product',fn($q)=>$q->available())->with(['product.farmer.farmerProfile','product.category','farmer.farmerProfile'])->orderBy('sort_order')->latest()->take(6)->get();

        return view('home', compact(
            'categories',
            'mostSellingProducts',
            'markets',
            'bestFarmers',
            'announcements',
            'productAds',
            'featuredTestimonials'
        ));
    }

    public function about(): View
    {
        return view('pages.about');
    }

    public function contact(): View
    {
        return view('pages.contact');
    }

    public function submitContact(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'subject' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:2000'],
        ]);

        Mail::raw(
            "Name: {$validated['name']}\nEmail: {$validated['email']}\n\n{$validated['message']}",
            function ($mail) use ($validated) {
                $mail->to(config('app.contact_email'))
                    ->replyTo($validated['email'], $validated['name'])
                    ->subject('[CropCircle contact] ' . $validated['subject']);
            }
        );

        return back()->with('success', 'Thank you for reaching out! Our team will respond to your inquiry shortly.');
    }

    public function faq(): View
    {
        return view('pages.faq');
    }

    public function terms(): View
    {
        return view('pages.terms');
    }

    public function privacy(): View
    {
        return view('pages.privacy');
    }
}
