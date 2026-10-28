<?php

namespace App\Http\Controllers;

use App\Models\FarmerProfile;
use App\Models\Product;
use App\Models\ProductAd;
use App\Models\Review;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FarmerController extends Controller
{
    public function index(Request $request): View
    {
        $query = FarmerProfile::where('is_approved', true)
            ->with(['user.products' => function ($q) {
                $q->where('status', 'available')->where('is_moderated', true);
            }, 'markets']);

        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function ($q) use ($search) {
                $q->where('farm_name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('city', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('market_id')) {
            $marketId = $request->market_id;
            $query->whereHas('markets', function ($q) use ($marketId) {
                $q->where('markets.id', $marketId);
            });
        }

        $farmers = $query->paginate(9)->withQueryString();

        return view('farmers.index', compact('farmers'));
    }

    public function show(int $id): View
    {
        $farmer = User::where('role', 'farmer')
            ->whereHas('farmerProfile', function ($q) {
                $q->where('is_approved', true);
            })
            ->with(['farmerProfile.markets', 'pickupSlots.market'])
            ->findOrFail($id);

        $products = Product::available()
            ->where('farmer_id', $farmer->id)
            ->with('category')
            ->paginate(8);

        $productAds = ProductAd::where('farmer_id', $farmer->id)
            ->where('status', 'approved')
            ->whereHas('product', fn ($query) => $query->available())
            ->with(['product.category'])
            ->orderBy('sort_order')
            ->latest()
            ->get();
        $reviews = Review::where('farmer_id', $farmer->id)
            ->where('is_moderated', true)
            ->with(['customer', 'product'])
            ->latest()
            ->paginate(5, ['*'], 'reviews_page');

        $isFavorite = false;
        if (auth()->check()) {
            $isFavorite = auth()->user()->favorites()->where('farmer_id', $farmer->id)->exists();
        }

        return view('farmers.show', compact('farmer', 'products', 'productAds', 'reviews', 'isFavorite'));
    }
}
