@extends('layouts.app')

@section('title', 'Checkout & Pickup Scheduling — CropCircle')

@section('content')
<div class="container py-5">
    <div class="row g-4">
        <div class="col-lg-8">
            <h2 class="fw-bold mb-1"><i class="bi bi-calendar2-check text-success me-2"></i> Schedule Pickups & Checkout</h2>
            <p class="text-muted small mb-4">Select your pickup date and convenient time window for each participating farm</p>

<div class="alert alert-info border-0 shadow-sm mb-4"><i class="bi bi-info-circle-fill me-2"></i><strong>Order changes:</strong> After placing your order, you may request quantity or note changes until 24 hours before the scheduled pickup. The farmer must approve any proposed changes.</div>
            <form action="{{ route('order.place') }}" method="POST" id="checkoutForm">
                @csrf

                <!-- Contact Information -->
                <div class="card card-custom p-4 border-0 shadow-sm mb-4">
                    <h5 class="fw-bold mb-3 border-bottom pb-2"><i class="bi bi-person-lines-fill text-success me-1"></i> Contact Information</h5>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Full Name (for pickup confirmation)</label>
                            <input type="text" name="customer_name" class="form-control" required value="{{ old('customer_name', $user->name) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Contact Phone Number</label>
                            <input type="text" name="customer_phone" class="form-control" required value="{{ old('customer_phone', $user->phone) }}" placeholder="+1 (555) 000-0000">
                        </div>
                    </div>
                </div>

                <!-- Farm-Specific Pickup Scheduling -->
                @foreach($farmerDetails as $farmerId => $data)
                    @php
                        $farmer = $data['farmer'];
                        $items = $data['items'];
                        $subtotal = $data['subtotal'];
                        $slots = $data['pickupSlots'];
                        $farmName = $farmer->farmerProfile->farm_name ?? $farmer->name;
                    @endphp

                    <div class="card card-custom p-4 border-0 shadow-sm mb-4">
                        <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-3">
                            <div class="d-flex align-items-center gap-2">
                                <span class="bg-success text-white p-2 rounded-2 fs-6"><i class="bi bi-shop"></i></span>
                                <div>
                                    <h5 class="fw-bold mb-0">{{ $farmName }}</h5>
                                    <span class="small text-muted">{{ $farmer->farmerProfile->city ?? 'Local' }} Stall</span>
                                </div>
                            </div>
                            <span class="badge bg-success bg-opacity-10 text-success fw-bold px-3 py-2 fs-6">
                                Subtotal: ${{ number_format($subtotal, 2) }}
                            </span>
                        </div>

                        <!-- Items in this suborder -->
                        <div class="bg-light p-3 rounded-3 mb-3 small">
                            <span class="fw-bold d-block mb-1 text-muted">Reserved Items:</span>
                            <ul class="list-unstyled mb-0 d-flex flex-column gap-1">
                                @foreach($items as $item)
                                    <li class="d-flex justify-content-between">
                                        <span>{{ $item->quantity }}x {{ $item->product->name }} ({{ $item->product->unit }})</span>
                                        <span class="fw-bold">${{ number_format($item->subtotal, 2) }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>

                        <!-- Pickup Scheduling Inputs -->
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Select Pickup Date <span class="text-danger">*</span></label>
                                <input type="date" name="pickup_dates[{{ $farmerId }}]" class="form-control" required min="{{ date('Y-m-d') }}" value="{{ old('pickup_dates.' . $farmerId) }}">
                                <div class="form-text small text-muted">Select an upcoming operating day for this stall.</div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Select Available Pickup Window <span class="text-danger">*</span></label>
                                <select name="pickup_slots[{{ $farmerId }}]" class="form-select" required>
                                    <option value="">-- Choose Time Window --</option>
                                    @forelse($slots as $slot)
                                        <option value="{{ $slot->id }}" {{ old('pickup_slots.' . $farmerId) == $slot->id ? 'selected' : '' }}>
                                            {{ $slot->day_of_week }}: {{ $slot->formatted_window }}
                                            @if($slot->market) ({{ $slot->market->name }}) @endif
                                        </option>
                                    @empty
                                        <option value="" disabled>No active slots listed. Direct stall pickup.</option>
                                    @endforelse
                                </select>
                                <div class="form-text small text-muted">Ensure your chosen date matches the slot day.</div>
                            </div>

                            <div class="col-12">
                                <label class="form-label small fw-bold">Notes or Packing Requests for this Farmer</label>
                                <input type="text" name="notes[{{ $farmerId }}]" class="form-control form-control-sm" placeholder="e.g. Please select firmer avocados, or pack in recycled cardboard..." value="{{ old('notes.' . $farmerId) }}">
                            </div>
                        </div>
                    </div>
                @endforeach

                <!-- Payment Method Card -->
                <div class="card card-custom p-4 border-0 shadow-sm mb-4">
                    <h5 class="fw-bold mb-3 border-bottom pb-2"><i class="bi bi-wallet2 text-success me-1"></i> Payment Method</h5>
                    <div class="p-3 bg-light rounded-3 d-flex align-items-center gap-3">
                        <i class="bi bi-cash-coin fs-1 text-success"></i>
                        <div>
                            <div class="fw-bold text-dark">Pay Directly Upon Pickup</div>
                            <div class="small text-muted">
                                You will pay each farmer directly at their market stall using cash or their accepted card terminals. Zero upfront credit card fees.
                            </div>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-cropcircle w-100 py-3 fw-bold rounded-3 fs-5 shadow-sm">
                    <i class="bi bi-bag-check-fill me-2"></i> Confirm & Place Pre-Orders (${{ number_format($total, 2) }})
                </button>
            </form>
        </div>

        <!-- Sidebar Summary -->
        <div class="col-lg-4">
            <div class="card card-custom p-4 border-0 shadow-sm sticky-top" style="top: 85px;">
                <h5 class="fw-bold mb-3 border-bottom pb-2">Pre-Order Review</h5>

                <div class="d-flex justify-content-between mb-2 small">
                    <span class="text-muted">Total Subtotal:</span>
                    <span class="fw-bold">${{ number_format($total, 2) }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2 small">
                    <span class="text-muted">Platform Reservation Fee:</span>
                    <span class="fw-bold text-success">FREE ($0.00)</span>
                </div>
                <div class="d-flex justify-content-between mb-3 small">
                    <span class="text-muted">Participating Farms:</span>
                    <span class="fw-bold">{{ count($farmerDetails) }} Independent Stalls</span>
                </div>

                <div class="pt-3 border-top mb-4 d-flex justify-content-between align-items-center">
                    <span class="fs-5 fw-bold text-dark">Amount Due at Pickup:</span>
                    <span class="fs-4 fw-extrabold text-success">${{ number_format($total, 2) }}</span>
                </div>

                <div class="p-3 bg-light rounded-3 small text-muted">
                    <div class="fw-bold text-dark mb-1"><i class="bi bi-shield-check text-success me-1"></i> Freshness Guarantee</div>
                    Your order will be reserved from the harvest schedule. You can cancel with full release anytime while in <strong>Pending</strong> status.
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
