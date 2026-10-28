@extends('layouts.app')

@section('content')
<div class="container-fluid portal-shell">
  <div class="row">
    <nav id="farmerSidebar" class="col-12 col-md-3 col-lg-2 portal-sidebar" aria-label="Farmer navigation">
      <div class="portal-sidebar-brand">
        <span class="portal-sidebar-brand-icon"><i class="bi bi-tree-fill"></i></span>
        <span>CropCircle<br><small class="text-muted fw-normal">Farmer workspace</small></span>
      </div>
      <div class="portal-sidebar-eyebrow">Manage your farm</div>
      <div>
        <ul class="nav flex-column">
          <li class="nav-item"><a class="nav-link {{ request()->routeIs('farmer.dashboard') ? 'active' : '' }}" href="{{ route('farmer.dashboard') }}"><i class="bi bi-speedometer2"></i> Dashboard</a></li>
          <li class="nav-item"><a class="nav-link {{ request()->routeIs('farmer.orders.*') ? 'active' : '' }}" href="{{ route('farmer.orders.index') }}"><i class="bi bi-receipt"></i> Orders</a></li>
          <li class="nav-item"><a class="nav-link {{ request()->routeIs('farmer.products.*') ? 'active' : '' }}" href="{{ route('farmer.products.index') }}"><i class="bi bi-box-seam"></i> Products</a></li>
          <li class="nav-item"><a class="nav-link {{ request()->routeIs('farmer.pickupSlots.*') ? 'active' : '' }}" href="{{ route('farmer.pickupSlots.index') }}"><i class="bi bi-calendar-event"></i> Pickup Slots</a></li>
          <li class="nav-item"><a class="nav-link {{ request()->routeIs('farmer.pickups.*') ? 'active' : '' }}" href="{{ route('farmer.pickups.index') }}"><i class="bi bi-truck"></i> Pickups</a></li>
          <li class="nav-item"><a class="nav-link {{ request()->routeIs('farmer.reviews.*') ? 'active' : '' }}" href="{{ route('farmer.reviews.index') }}"><i class="bi bi-chat-left-text"></i> Reviews</a></li>
          <li class="nav-item"><a class="nav-link {{ request()->routeIs('farmer.markets.*') ? 'active' : '' }}" href="{{ route('farmer.markets.index') }}"><i class="bi bi-shop"></i> Markets</a></li>
        </ul>
      </div>
    </nav>
    <div class="col-12 col-md-9 col-lg-10 portal-main px-md-4">
      @yield('content')
    </div>
  </div>
</div>
@endsection
