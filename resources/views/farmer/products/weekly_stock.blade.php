@extends('layouts.app')

@section('title', 'Weekly Stock Schedule for ' . $product->name . ' — Farmer Portal')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-9">
            <div class="card card-custom p-4 p-md-5 border-0 shadow-sm">
                <a href="{{ route('farmer.products.index') }}" class="text-success text-decoration-none small fw-bold mb-2 d-inline-block">
                    &larr; Back to Products List
                </a>
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h3 class="fw-bold mb-1">Weekly Harvest Schedule</h3>
                        <p class="text-muted small mb-0">Product: <strong>{{ $product->name }}</strong> (Total Stock: {{ $product->stock_quantity }} {{ $product->unit }})</p>
                    </div>
                    <span class="badge bg-success bg-opacity-10 text-success fw-bold px-3 py-2 fs-6">
                        ${{ number_format($product->price, 2) }} / {{ $product->unit }}
                    </span>
                </div>

                <p class="small text-secondary mb-4">
                    Define which market days this crop is freshly harvested and available for customer stall pickup. Set the available batch quota for each day to prevent overselling.
                </p>

                <form action="{{ route('farmer.products.weeklyStock.update', $product->id) }}" method="POST">
                    @csrf

                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-4">
                            <thead class="small text-muted bg-light">
                                <tr>
                                    <th style="width: 160px;">Day of Week</th>
                                    <th style="width: 140px;">Availability</th>
                                    <th>Available Harvest Quota ({{ $product->unit }})</th>
                                    <th class="text-end">Reserved by Orders</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($days as $day)
                                    @php
                                        $record = $availabilities[$day];
                                    @endphp
                                    <tr>
                                        <td class="fw-bold">{{ $day }}</td>
                                        <td>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" name="days[{{ $day }}][is_available]" value="1" id="switch_{{ $day }}" {{ $record->is_available ? 'checked' : '' }}>
                                                <label class="form-check-label small" for="switch_{{ $day }}">
                                                    {{ $record->is_available ? 'Available' : 'Closed' }}
                                                </label>
                                            </div>
                                        </td>
                                        <td>
                                            <input type="number" name="days[{{ $day }}][available_quantity]" class="form-control form-control-sm" value="{{ $record->available_quantity }}" min="0" style="max-width: 160px;">
                                        </td>
                                        <td class="text-end fw-semibold text-muted">
                                            {{ $record->reserved_quantity }} {{ $product->unit }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-between align-items-center">
                        <span class="small text-muted">
                            <i class="bi bi-info-circle text-success me-1"></i>
                            Reserved quantities update automatically as customers place and pick up orders.
                        </span>
                        <button type="submit" class="btn btn-cropcircle px-4 fw-bold">
                            Save Weekly Schedule
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
