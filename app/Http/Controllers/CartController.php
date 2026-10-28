<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CartController extends Controller
{
    public function index(): View
    {
        $cartItems = Cart::where('customer_id', Auth::id())
            ->with(['product.farmer.farmerProfile', 'product.category'])
            ->get();

        // Group items by farmer
        $groupedByFarmer = $cartItems->groupBy(function ($item) {
            return $item->product->farmer_id;
        });

        $total = $cartItems->sum(function ($item) {
            return $item->product ? $item->product->effectivePrice() * $item->quantity : 0;
        });

        return view('cart.index', compact('cartItems', 'groupedByFarmer', 'total'));
    }

    public function add(Request $request, int $productId): RedirectResponse
    {
        $request->validate([
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        $product = Product::available()->findOrFail($productId);
        $quantity = (int) $request->quantity;

        if ($quantity > $product->stock_quantity) {
            return back()->with('warning', 'Only ' . $product->stock_quantity . ' ' . $product->unit . ' available in stock.');
        }

        $cart = Cart::firstOrNew([
            'customer_id' => Auth::id(),
            'product_id' => $product->id,
        ]);

        $newQty = ($cart->exists ? $cart->quantity : 0) + $quantity;

        if ($newQty > $product->stock_quantity) {
            return back()->with('warning', 'Adding this exceeds the available stock of ' . $product->stock_quantity . ' ' . $product->unit . '.');
        }

        $cart->quantity = $newQty;
        $cart->save();

        return redirect()->route('cart.index')->with('success', $product->name . ' added to your pre-order cart!');
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $request->validate([
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        $cart = Cart::where('customer_id', Auth::id())->with('product')->findOrFail($id);

        if ($request->quantity > $cart->product->stock_quantity) {
            return back()->with('warning', 'Only ' . $cart->product->stock_quantity . ' ' . $cart->product->unit . ' available in stock.');
        }

        $cart->update(['quantity' => (int) $request->quantity]);

        return back()->with('success', 'Cart updated successfully.');
    }

    public function remove(int $id): RedirectResponse
    {
        $cart = Cart::where('customer_id', Auth::id())->findOrFail($id);
        $cart->delete();

        return back()->with('success', 'Item removed from cart.');
    }

    public function clear(): RedirectResponse
    {
        Cart::where('customer_id', Auth::id())->delete();
        return back()->with('info', 'Your cart has been cleared.');
    }
}
