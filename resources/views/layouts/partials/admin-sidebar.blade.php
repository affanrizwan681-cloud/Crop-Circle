    <nav id="sidebarMenu" class="col-12 col-md-3 col-lg-2 portal-sidebar" aria-label="Admin navigation">
      <div class="portal-sidebar-brand">
        <span class="portal-sidebar-brand-icon"><i class="ti ti-seeding"></i></span>
        <span>CropCircle<br><small class="text-muted fw-normal">Admin workspace</small></span>
      </div>
      <div class="portal-sidebar-eyebrow">Manage marketplace</div>
      <div>
        <ul class="nav flex-column">
          <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
              <i class="ti ti-layout-dashboard"></i>
              Dashboard
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('admin.farmers.*') ? 'active' : '' }}" href="{{ route('admin.farmers.index') }}">
              <i class="ti ti-users"></i>
              Farmers
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}" href="{{ route('admin.users.index') }}">
              <i class="ti ti-user"></i>
              Users
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}" href="{{ route('admin.categories.index') }}">
              <i class="ti ti-tags"></i>
              Categories
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('admin.markets.*') || request()->routeIs('admin.marketApplications.*') ? 'active' : '' }}" href="{{ route('admin.markets.index') }}">
              <i class="ti ti-building-store"></i>
              Markets
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('admin.ads.*') ? 'active' : '' }}" href="{{ route('admin.ads.index') }}"><i class="ti ti-speakerphone"></i> Product ads</a>
          </li>
          <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('admin.products.*') ? 'active' : '' }}" href="{{ route('admin.products.index') }}">
              <i class="ti ti-package"></i>
              Products
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}" href="{{ route('admin.orders.index') }}">
              <i class="ti ti-receipt"></i>
              Orders
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('admin.reviews.*') ? 'active' : '' }}" href="{{ route('admin.reviews.index') }}">
              <i class="ti ti-message-star"></i>
              Reviews
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('admin.feedback.*') ? 'active' : '' }}" href="{{ route('admin.feedback.index') }}">
              <i class="ti ti-message-report"></i>
              Website Feedback
            </a>
          </li>          <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}" href="{{ route('admin.reports.index') }}">
              <i class="ti ti-alert-triangle"></i>
              Reports
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('admin.logs.*') ? 'active' : '' }}" href="{{ route('admin.logs.index') }}">
              <i class="ti ti-activity"></i>
              Activity Logs
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('admin.announcements.*') ? 'active' : '' }}" href="{{ route('admin.announcements.index') }}">
              <i class="ti ti-speakerphone"></i>
              Announcements
            </a>
          </li>
        </ul>
      </div>
    </nav>