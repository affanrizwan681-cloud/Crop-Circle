@extends('layouts.app')

@section('title', 'Your Pre-Order Basket — CropCircle')

@section('content')
<div class="container py-5">
    <h2 class="fw-bold mb-1"><i class="bi bi-basket2-fill text-success me-2"></i> Your Pre-Order Basket</h2>
    <p class="text-muted small mb-4">Review your items before selecting pickup slots and confirming your reservation</p>

    @if($cartItems->isEmpty())
        <div class="card card-custom p-5 text-center border-0 shadow-sm">
            <div class="py-4">
                <i class="bi bi-basket display-1 text-muted opacity-50 mb-3 d-block"></i>
                <h4 class="fw-bold text-dark">Your basket is empty</h4>
                <p class="text-muted small mb-4">Discover fresh local vegetables, fruits, honey, and dairy from neighborhood growers.</p>
                <a href="{{ route('products.index') }}" class="btn btn-cropcircle px-4 py-2 fw-bold rounded-pill">
                    <i class="bi bi-search me-1"></i> Browse Fresh Harvests
                </a>
            </div>
        </div>
    @else
        <div class="row g-4">
            <div class="col-lg-8">
                @foreach($groupedByFarmer as $farmerId => $items)
                    @php
                        $farmer = $items->first()->product->farmer;
                        $farmName = $farmer->farmerProfile->farm_name ?? $farmer->name;
                        $farmerSubtotal = $items->sum(fn($i) => $i->product ? $i->product->effectivePrice() * $i->quantity : 0);
                    @endphp

                    <div class="card card-custom border-0 shadow-sm p-4 mb-4">
                        <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-3">
                            <div class="d-flex align-items-center gap-2">
                                <span class="bg-success text-white p-2 rounded-2 fs-6"><i class="bi bi-shop"></i></span>
                                <div>
                                    <h6 class="fw-bold text-dark mb-0">{{ $farmName }}</h6>
                                    <span class="small text-muted">{{ $farmer->farmerProfile->city ?? 'Local' }} Stall</span>
                                </div>
                            </div>
                            <div class="text-end">
                                <span class="small text-muted">Stall Subtotal:</span>
                                <span class="fw-bold text-success fs-5 ms-1">${{ number_format($farmerSubtotal, 2) }}</span>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-borderless align-middle mb-0">
                                <thead class="small text-muted border-bottom">
                                    <tr>
                                        <th>Product</th>
                                        <th>Price</th>
                                        <th style="width: 140px;">Quantity</th>
                                        <th class="text-end">Subtotal</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($items as $cart)
                                        <tr class="border-bottom">
                                            <td class="position-relative"><div class="d-flex align-items-center gap-2 py-1">
                                                    <div class="bg-light rounded p-2 text-center" style="width: 45px; height: 45px;">
                                                        <i class="bi {{ $cart->product->category->icon ?? 'bi-bag' }} text-success fs-5"></i>
                                                    </div>
                                                    <div>
                                                        <a href="{{ route('products.show', $cart->product->slug) }}" class="fw-bold text-dark text-decoration-none small d-block stretched-link">
                                                            {{ $cart->product->name }}
                                                        </a>
                                                        <span class="small text-muted">Per {{ $cart->product->unit }}</span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="small fw-semibold">${{ number_format($cart->product->effectivePrice(), 2) }}</td>
                                            <td>
                                                <form action="{{ route('cart.update', $cart->id) }}" method="POST" class="d-flex align-items-center gap-1">
                                                    @csrf
                                                    @method('PUT')
                                                    <input type="number" name="quantity" class="form-control form-control-sm" value="{{ $cart->quantity }}" min="1" max="{{ $cart->product->stock_quantity }}" style="width: 70px;">
                                                    <button type="submit" class="btn btn-sm btn-outline-secondary" title="Update Quantity">
                                                        <i class="bi bi-arrow-repeat"></i>
                                                    </button>
                                                </form>
                                            </td>
                                            <td class="text-end fw-bold text-dark small">${{ number_format($cart->subtotal, 2) }}</td>
                                            <td class="text-end">
                                                <form action="{{ route('cart.remove', $cart->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger border-0" title="Remove Item">
                                                        <i class="bi bi-trash3"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endforeach

                <div class="d-flex justify-content-between align-items-center mt-3">
                    <form action="{{ route('cart.clear') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Clear entire cart?')">
                            <i class="bi bi-trash3 me-1"></i> Clear Basket
                        </button>
                    </form>
                    <a href="{{ route('products.index') }}" class="btn btn-sm btn-outline-success">
                        <i class="bi bi-plus-circle me-1"></i> Add More Products
                    </a>
                </div>
            </div>

            <!-- Order Summary Card -->
            <div class="col-lg-4">
                <div class="card card-custom p-4 border-0 shadow-sm sticky-top" style="top: 85px;">
                    <h5 class="fw-bold mb-3 border-bottom pb-2">Order Summary</h5>

                    <div class="d-flex justify-content-between mb-2 small">
                        <span class="text-muted">Total Product Items:</span>
                        <span class="fw-bold">{{ $cartItems->sum('quantity') }} items</span>
                    </div>

                    <div class="d-flex justify-content-between mb-2 small">
                        <span class="text-muted">Participating Farms:</span>
                        <span class="fw-bold">{{ $groupedByFarmer->count() }} Farms</span>
                    </div>

                    <div class="d-flex justify-content-between mb-3 small">
                        <span class="text-muted">Fulfillment:</span>
                        <span class="fw-bold text-success">Scheduled Market Pickup</span>
                    </div>

                    <div class="pt-3 border-top mb-4 d-flex justify-content-between align-items-center">
                        <span class="fs-5 fw-bold text-dark">Estimated Total:</span>
                        <span class="fs-4 fw-extrabold text-success">${{ number_format($total, 2) }}</span>
                    </div>

                    <a href="{{ route('checkout') }}" class="btn btn-cropcircle w-100 py-3 fw-bold rounded-3 text-center mb-3">
                        Proceed to Pickup Scheduling <i class="bi bi-arrow-right ms-1"></i>
                    </a>

                    <div class="p-3 bg-light rounded-3 small text-muted">
                        <i class="bi bi-shield-check text-success me-1"></i>
                        No upfront charge. Pay directly to the farmers upon pickup at their market stalls.
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
