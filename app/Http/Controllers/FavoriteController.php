<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class FavoriteController extends Controller
{
    public function index(): View
    {
        $favoriteProducts = Favorite::where('customer_id', Auth::id())
            ->whereNotNull('product_id')
            ->with(['product.farmer.farmerProfile', 'product.category'])
            ->get()
            ->filter(fn($fav) => $fav->product !== null);

        $favoriteFarmers = Favorite::where('customer_id', Auth::id())
            ->whereNotNull('farmer_id')
            ->with(['farmer.farmerProfile.markets'])
            ->get()
            ->filter(fn($fav) => $fav->farmer !== null);

        return view('customer.favorites', compact('favoriteProducts', 'favoriteFarmers'));
    }

    public function toggle(Request $request): JsonResponse|RedirectResponse
    {
        $productId = $request->input('product_id');
        $farmerId = $request->input('farmer_id');

        if (!$productId && !$farmerId) {
            if ($request->wantsJson()) {
                return response()->json(['error' => 'Invalid parameters'], 400);
            }
            return back()->with('danger', 'Invalid favorite target.');
        }

        $query = Favorite::where('customer_id', Auth::id());
        if ($productId) {
            $query->where('product_id', $productId);
        } else {
            $query->where('farmer_id', $farmerId);
        }

        $existing = $query->first();

        if ($existing) {
            $existing->delete();
            $status = 'removed';
            $message = 'Removed from your favorites.';
        } else {
            Favorite::create([
                'customer_id' => Auth::id(),
                'product_id' => $productId,
                'farmer_id' => $farmerId,
            ]);
            $status = 'added';
            $message = 'Saved to your favorites!';
        }

        if ($request->wantsJson()) {
            return response()->json(['status' => $status, 'message' => $message]);
        }

        return back()->with('success', $message);
    }
}
