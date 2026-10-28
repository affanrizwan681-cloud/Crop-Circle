@extends('layouts.admin')

@section('title', 'Admin overview â€” CropCircle')

@section('admin-content')
<div class="page-header d-print-none mb-4">
    <div class="row align-items-center g-3">
        <div class="col">
            <div class="page-pretitle text-success">CropCircle marketplace</div>
            <h1 class="page-title">Admin overview</h1>
            <div class="text-secondary mt-1">Live farmer, customer, order and marketplace activity.</div>
        </div>
        <div class="col-auto">
            <div class="btn-list">
                <a class="btn btn-outline-secondary" href="{{ route('admin.products.index') }}"><i class="ti ti-package me-1"></i> Review products</a>
                <a class="btn btn-primary" href="{{ route('admin.farmers.index') }}"><i class="ti ti-user-check me-1"></i> Farmer approvals</a>
            </div>
        </div>
    </div>
</div>

@php
    $overviewStats = [
        ['label' => 'Customers', 'value' => number_format($totalCustomers), 'hint' => 'Registered buyers', 'icon' => 'ti ti-users', 'tone' => 'primary', 'href' => route('admin.users.index')],
        ['label' => 'Farmers', 'value' => number_format($totalFarmers), 'hint' => 'Grower accounts', 'icon' => 'ti ti-seeding', 'tone' => 'success', 'href' => route('admin.farmers.index')],
        ['label' => 'Awaiting approval', 'value' => number_format($pendingApprovals), 'hint' => 'Farmer applications', 'icon' => 'ti ti-user-check', 'tone' => 'warning', 'href' => route('admin.farmers.index')],
        ['label' => 'Products', 'value' => number_format($totalProducts), 'hint' => 'Marketplace listings', 'icon' => 'ti ti-package', 'tone' => 'azure', 'href' => route('admin.products.index')],
        ['label' => 'Markets', 'value' => number_format($totalMarkets), 'hint' => 'Community destinations', 'icon' => 'ti ti-building-store', 'tone' => 'purple', 'href' => route('admin.markets.index')],
        ['label' => 'Orders', 'value' => number_format($totalOrders), 'hint' => 'All pre-orders', 'icon' => 'ti ti-receipt', 'tone' => 'cyan', 'href' => route('admin.orders.index')],
        ['label' => 'Completed volume', 'value' => '$' . number_format((float) $totalVolume, 2), 'hint' => 'Completed pickups', 'icon' => 'ti ti-cash-banknote', 'tone' => 'green', 'href' => route('admin.orders.index')],
        ['label' => 'Open reports', 'value' => number_format($openReports), 'hint' => 'Need administrator review', 'icon' => 'ti ti-alert-triangle', 'tone' => 'orange', 'href' => route('admin.reports.index')],
    ];
@endphp

<div class="row row-cards mb-4">
    @foreach($overviewStats as $stat)
        <div class="col-sm-6 col-xl-3">
            <a href="{{ $stat['href'] }}" class="card card-sm card-link h-100 text-decoration-none">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <div class="text-secondary">{{ $stat['label'] }}</div>
                            <div class="h1 mb-1 mt-2 text-body cc-count-up" data-count-target="{{ preg_replace('/[^0-9.]/', '', $stat['value']) }}" data-count-prefix="{{ preg_replace('/[0-9,.]/', '', $stat['value']) }}" data-count-decimals="{{ str_contains($stat['value'], '.') ? 2 : 0 }}" aria-label="{{ $stat['value'] }}">{{ $stat['value'] }}</div>
                            <div class="small text-secondary">{{ $stat['hint'] }}</div>
                        </div>
                        <div class="col-auto"><span class="avatar bg-{{ $stat['tone'] }}-lt"><i class="{{ $stat['icon'] }} fs-2" aria-hidden="true"></i></span></div>
                    </div>
                </div>
            </a>
        </div>
    @endforeach
</div>

<div class="row row-cards">
    <div class="col-xl-5">
        <div class="card h-100">
            <div class="card-header">
                <div><div class="card-title">Farmer applications</div><div class="text-secondary small">Recent accounts awaiting review</div></div>
                <div class="card-actions"><a href="{{ route('admin.farmers.index') }}" class="btn btn-sm btn-outline-secondary">View all</a></div>
            </div>
            <div class="list-group list-group-flush">
                @forelse($pendingFarmersList as $profile)
                    <a href="{{ route('admin.farmers.index') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center gap-3">
                        <span class="d-flex align-items-center gap-3"><span class="avatar avatar-sm bg-success-lt"><i class="ti ti-seeding" aria-hidden="true"></i></span><span><span class="d-block fw-semibold">{{ $profile->farm_name ?: ($profile->user->name ?? 'New farm') }}</span><span class="small text-secondary">{{ $profile->user->email ?? 'Farmer account' }}</span></span></span>
                        <span class="badge bg-warning-lt">Review</span>
                    </a>
                @empty
                    <div class="empty py-4"><div class="empty-icon"><i class="ti ti-circle-check"></i></div><p class="empty-title">No pending applications</p><p class="empty-subtitle text-secondary">New farmer applications will appear here.</p></div>
                @endforelse
            </div>
        </div>
    </div>

    <div class="col-xl-7">
        <div class="card h-100">
            <div class="card-header"><div><div class="card-title">Recent orders</div><div class="text-secondary small">Latest marketplace pre-orders</div></div><div class="card-actions"><a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-outline-secondary">All orders</a></div></div>
            <div class="table-responsive">
                <table class="table table-vcenter card-table">
                    <thead><tr><th>Order</th><th>Customer</th><th>Farm</th><th>Status</th><th class="text-end">Amount</th><th></th></tr></thead>
                    <tbody>
                        @forelse($recentOrders as $order)
                            <tr>
                                <td class="fw-semibold">{{ $order->order_number }}</td>
                                <td>{{ $order->customer_name ?: ($order->customer->name ?? 'Customer') }}</td>
                                <td>{{ $order->farmer->farmerProfile->farm_name ?? $order->farmer->name ?? 'Farm' }}</td>
                                <td><span class="badge bg-secondary-lt">{{ ucfirst(str_replace('_', ' ', $order->status)) }}</span></td>
                                <td class="text-end fw-semibold">${{ number_format($order->total_amount, 2) }}</td>
                                <td class="text-end"><a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-sm btn-ghost-secondary" aria-label="View order {{ $order->order_number }}"><i class="ti ti-arrow-up-right" aria-hidden="true"></i></a></td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center text-secondary py-4">No orders have been placed yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-12">
        <div class="card">
            <div class="card-header"><div class="card-title">Recent registrations</div></div>
            <div class="table-responsive">
                <table class="table table-vcenter card-table">
                    <thead><tr><th>Name</th><th>Email</th><th>Role</th><th>Joined</th></tr></thead>
                    <tbody>
                        @forelse($recentRegistrations as $user)
                            <tr><td class="fw-semibold">{{ $user->name }}</td><td class="text-secondary">{{ $user->email }}</td><td><span class="badge bg-primary-lt">{{ ucfirst($user->role) }}</span></td><td class="text-secondary">{{ $user->created_at->format('M j, Y') }}</td></tr>
                        @empty
                            <tr><td colspan="4" class="text-center text-secondary py-4">No registrations yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
