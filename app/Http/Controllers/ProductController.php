<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\FarmerProfile;
use App\Models\Market;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $query = Product::visible()
            ->with(['farmer.farmerProfile', 'category', 'images']);

        // Search
        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Category filter
        if ($request->filled('category')) {
            $catSlug = $request->category;
            $query->whereHas('category', function ($q) use ($catSlug) {
                $q->where('slug', $catSlug)->orWhere('id', $catSlug);
            });
        }

        // Farmer filter
        if ($request->filled('farmer_id')) {
            $query->where('farmer_id', $request->farmer_id);
        }

        // Market filter (products from farmers attending this market)
        if ($request->filled('market_id')) {
            $marketId = $request->market_id;
            $query->whereHas('farmer.farmerProfile.markets', function ($q) use ($marketId) {
                $q->where('markets.id', $marketId);
            });
        }

        // Availability filter
        if ($request->filled('availability')) {
            if ($request->availability === 'in_stock') {
                $query->where('stock_quantity', '>', 0)->where('status', 'available');
            }
        }

        // Day of week availability filter
        if ($request->filled('day')) {
            $day = $request->day;
            $query->whereHas('availabilities', function ($q) use ($day) {
                $q->where('day_of_week', $day)->where('is_available', true);
            });
        }

        // Price range
        if ($request->filled('min_price')) {
            $query->where('price', '>=', (float) $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('price', '<=', (float) $request->max_price);
        }

        // Sorting
        $sort = $request->input('sort', 'newest');
        switch ($sort) {
            case 'price_asc':
                $query->orderBy('price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('price', 'desc');
                break;
            case 'name_asc':
                $query->orderBy('name', 'asc');
                break;
            case 'popular':
                $query->withCount('orderItems')->orderBy('order_items_count', 'desc');
                break;
            default:
                $query->latest();
                break;
        }

        $products = $query->paginate(12)->withQueryString();

        $categories = Category::active()->withCount(['products' => function ($q) {
            $q->visible();
        }])->get();

        $markets = Market::active()->get();
        $farmers = FarmerProfile::where('is_approved', true)->with('user')->get();

        return view('products.index', compact('products', 'categories', 'markets', 'farmers', 'sort'));
    }

    public function show(string $slug): View
    {
        $product = Product::visible()->with([
            'farmer.farmerProfile.markets',
            'category',
            'images',
            'availabilities',
            'reviews.customer',
        ])
        ->where(function ($query) use ($slug) {
            $query->where('slug', $slug);
            if (is_numeric($slug)) {
                $query->orWhere('id', $slug);
            }
        })
        ->firstOrFail();

        $relatedProducts = Product::available()
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->take(4)
            ->get();

        $isFavorite = false;
        if (auth()->check()) {
            $isFavorite = auth()->user()->favorites()->where('product_id', $product->id)->exists();
        }

        $pickupSlots = $product->farmer->pickupSlots()->where('is_active', true)->with('market')->get();

        return view('products.show', compact('product', 'relatedProducts', 'isFavorite', 'pickupSlots'));
    }
}
