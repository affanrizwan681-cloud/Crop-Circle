<?php

namespace App\Http\Controllers;

use App\Models\Market;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MarketController extends Controller
{
    public function index(): View
    {
        $markets = Market::active()
            ->withCount(['approvedFarmers' => function ($q) {
                $q->where('is_approved', true);
            }])
            ->get();

        return view('markets.index', compact('markets'));
    }

    public function show(int $id): View
    {
        $market = Market::with([
            'approvedFarmers.user',
            'pickupSlots.farmer',
        ])
        ->where('status', 'active')
        ->findOrFail($id);

        $farmerUserIds = $market->approvedFarmers->pluck('user_id');

        $products = Product::available()
            ->whereIn('farmer_id', $farmerUserIds)
            ->with(['farmer.farmerProfile', 'category'])
            ->paginate(8);

        return view('markets.show', compact('market', 'products'));
    }
}
