<nav class="col-12 col-md-3 col-lg-2 portal-sidebar" aria-label="Farmer navigation">
    <div class="portal-sidebar-brand">
        <span class="portal-sidebar-brand-icon"><i class="ti ti-seeding" aria-hidden="true"></i></span>
        <span>CropCircle<br><small class="text-muted fw-normal">Farmer workspace</small></span>
    </div>
    <div class="portal-sidebar-eyebrow">Manage your farm</div>
    <ul class="nav nav-pills flex-column">
        <li class="nav-item"><a class="nav-link {{ request()->routeIs('farmer.dashboard') ? 'active' : '' }}" href="{{ route('farmer.dashboard') }}"><i class="ti ti-layout-dashboard" aria-hidden="true"></i> Dashboard</a></li>
        <li class="nav-item"><a class="nav-link {{ request()->routeIs('farmer.products.*') ? 'active' : '' }}" href="{{ route('farmer.products.index') }}"><i class="ti ti-package" aria-hidden="true"></i> Products & stock</a></li><li class="nav-item"><a class="nav-link {{ request()->routeIs('farmer.ads.*') ? 'active' : '' }}" href="{{ route('farmer.ads.index') }}"><i class="ti ti-speakerphone"></i> Product ads</a></li>
        <li class="nav-item"><a class="nav-link {{ request()->routeIs('farmer.orders.*') ? 'active' : '' }}" href="{{ route('farmer.orders.index') }}"><i class="ti ti-receipt" aria-hidden="true"></i> Orders</a></li>
        <li class="nav-item"><a class="nav-link {{ request()->routeIs('farmer.pickupSlots.*') ? 'active' : '' }}" href="{{ route('farmer.pickupSlots.index') }}"><i class="ti ti-calendar-time" aria-hidden="true"></i> Pickup slots</a></li>
        <li class="nav-item"><a class="nav-link {{ request()->routeIs('farmer.pickups.*') ? 'active' : '' }}" href="{{ route('farmer.pickups.index') }}"><i class="ti ti-truck-delivery" aria-hidden="true"></i> Pickups</a></li>
        <li class="nav-item"><a class="nav-link {{ request()->routeIs('farmer.markets.*') ? 'active' : '' }}" href="{{ route('farmer.markets.index') }}"><i class="ti ti-building-store" aria-hidden="true"></i> Markets</a></li>
        <li class="nav-item"><a class="nav-link {{ request()->routeIs('farmer.reviews.*') ? 'active' : '' }}" href="{{ route('farmer.reviews.index') }}"><i class="ti ti-star" aria-hidden="true"></i> Reviews</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ route('chat.index') }}"><i class="ti ti-messages" aria-hidden="true"></i> Messages</a></li>
        <li class="nav-item"><a class="nav-link {{ request()->routeIs('feedback.*') ? 'active' : '' }}" href="{{ route('feedback.create') }}"><i class="ti ti-message-report" aria-hidden="true"></i> Website feedback</a></li>
    </ul>
</nav>