<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'CropCircle') — Fresh Local Farm Marketplace</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Tabler Core 1.7.1 includes the Bootstrap CSS foundation -->
    <link href="https://cdn.jsdelivr.net/npm/@tabler/core@1.7.1/dist/css/tabler.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@3.49.0/dist/tabler-icons.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="{{ asset('css/cropcircle-tabler.css') }}">
    <link rel="stylesheet" href="{{ asset('css/cropcircle-refresh.css') }}">
    <!-- Leaflet CSS for Maps -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    
    <style>
        :root,
        [data-bs-theme="light"] {
            --tblr-primary: #215c3a;
            --tblr-primary-rgb: 33, 92, 58;
            --tblr-success: #215c3a;
            --tblr-success-rgb: 33, 92, 58;
            --tblr-warning: #DDAA38;
            --tblr-warning-rgb: 221, 170, 56;
            --tblr-warning-fg: #342a15;
            --tblr-body-bg: #F5F8F2;
            --tblr-body-bg-rgb: 245, 248, 242;
            --tblr-body-color: #1e293b;
            --tblr-border-color: #e2e8f0;
            --tblr-link-color: #215c3a;
            --tblr-link-hover-color: #174629;
            --ml-primary: #215c3a;
            --ml-primary-dark: #174629;
            --ml-primary-light: #397c54;
            --ml-primary-subtle: #edf4e9;
            --ml-accent: #DDAA38;
            --ml-sand: #F5F8F2;
            --ml-text-dark: #1e293b;
            --ml-border: #e2e8f0;
        }

        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            color: var(--ml-text-dark);
            background-color: #F5F8F2;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        main {
            flex: 1 0 auto;
        }

        /* Navbar & Branding */
        .navbar-cropcircle {
            background: #ffffff;
            border-bottom: 1px solid var(--ml-border);
            padding: 0.85rem 0;
            box-shadow: 0 1px 3px rgba(0,0,0,0.02);
        }

        .navbar-brand {
            font-weight: 800;
            font-size: 1.45rem;
            color: var(--ml-primary-dark);
            letter-spacing: -0.5px;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .navbar-brand .brand-badge {
            background: linear-gradient(135deg, var(--ml-primary), var(--ml-primary-light));
            color: white;
            padding: 0.35rem 0.55rem;
            border-radius: 8px;
            font-size: 1.1rem;
        }

        .nav-link {
            font-weight: 500;
            color: #475569;
            padding: 0.5rem 0.9rem !important;
            transition: color 0.15s ease-in-out;
        }

        .nav-link:hover, .nav-link.active {
            color: var(--ml-primary);
        }

        /* Buttons & Badges */
        .btn-cropcircle {
            background-color: var(--ml-primary);
            border-color: var(--ml-primary);
            color: #ffffff;
            font-weight: 600;
            border-radius: 8px;
            padding: 0.55rem 1.25rem;
            transition: all 0.2s ease;
        }

        .btn-cropcircle:hover, .btn-cropcircle:focus {
            background-color: var(--ml-primary-dark);
            border-color: var(--ml-primary-dark);
            color: #ffffff;
            box-shadow: 0 4px 12px rgba(45, 106, 79, 0.25);
        }

        .btn-outline-cropcircle {
            border-color: var(--ml-primary);
            color: var(--ml-primary);
            font-weight: 600;
            border-radius: 8px;
            padding: 0.55rem 1.25rem;
        }

        .btn-outline-cropcircle:hover {
            background-color: var(--ml-primary);
            color: #ffffff;
        }

        .badge-role {
            font-size: 0.72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 0.35em 0.65em;
            border-radius: 6px;
        }

        /* Cards */
        .card-custom {
            background: #ffffff;
            border: 1px solid var(--ml-border);
            border-radius: 12px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.02);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .card-hover:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.05);
            border-color: #cbd5e1;
        }

        /* Banner & Alert */
        .alert-dismissible .btn-close {
            padding: 1.1rem;
        }

        /* Footer */
        .footer-cropcircle {
            background: #0f241a;
            color: #cbd5e1;
            padding-top: 4rem;
            padding-bottom: 2rem;
            margin-top: 4rem;
            border-top: 3px solid var(--ml-primary);
        }

        .footer-cropcircle a {
            color: #94a3b8;
            text-decoration: none;
            transition: color 0.15s ease;
        }

        .footer-cropcircle a:hover {
            color: #ffffff;
        }

        .footer-heading {
            color: #ffffff;
            font-weight: 700;
            font-size: 1rem;
            margin-bottom: 1.25rem;
            letter-spacing: -0.2px;
        }

        .portal-sidebar {
            position: sticky;
            top: 88px;
            align-self: flex-start;
            height: calc(100vh - 88px);
            min-height: 540px;
            overflow-y: auto;
            padding: 22px 14px;
            border-right: 1px solid #e5ece7;
            border-radius: 0 22px 22px 0;
            background: linear-gradient(180deg, #fff 0%, #f5faf6 100%);
            box-shadow: 8px 0 28px rgba(26, 67, 50, .05);
        }

        .portal-sidebar-brand { display: flex; align-items: center; gap: 11px; padding: 4px 8px 18px; margin-bottom: 16px; border-bottom: 1px solid #e7eee9; color: var(--ml-primary-dark); font-weight: 800; }
        .portal-sidebar-brand-icon { width: 42px; height: 42px; display: grid; place-items: center; border-radius: 14px; color: #fff; font-size: 1.15rem; background: linear-gradient(135deg, #215c3a, #74a878); }
        .portal-sidebar-eyebrow { padding: 0 10px 7px; color: #90a198; font-size: .66rem; font-weight: 800; letter-spacing: .13em; text-transform: uppercase; }
        .portal-sidebar .nav-link { display: flex; align-items: center; gap: .75rem; margin: 3px 0; padding: .72rem .82rem !important; border-radius: 13px; color: #52615a; font-size: .9rem; font-weight: 650; transition: background .16s ease, color .16s ease, transform .16s ease; }
        .portal-sidebar .nav-link i { width: 20px; text-align: center; color: #688271; font-size: 1.05rem; }
        .portal-sidebar .nav-link:hover, .portal-sidebar .nav-link.active { color: var(--ml-primary-dark); background: #e8f4eb; transform: translateX(2px); }
        .portal-sidebar .nav-link.active i { color: var(--ml-primary); }
        .portal-main { min-width: 0; padding-top: 1rem; padding-bottom: 2rem; }

        .password-toggle-group .form-control { min-width: 0; }
        .password-toggle-btn { min-width: 46px; color: #52615a; border-color: #ced8d1; background: #fff; }
        .password-toggle-btn:hover, .password-toggle-btn:focus { color: var(--ml-primary-dark); border-color: var(--ml-primary); background: #edf6ef; }

        @media (max-width: 767.98px) {
            .portal-sidebar { position: relative; top: auto; height: auto; min-height: 0; padding: 12px; border-right: 0; border-bottom: 1px solid #e5ece7; border-radius: 0; }
            .portal-sidebar-brand { padding-bottom: 10px; margin-bottom: 10px; }
            .portal-sidebar .nav { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 2px 6px; }
            .portal-sidebar .nav-link { margin: 0; padding: .6rem .65rem !important; font-size: .82rem; }
            .portal-main { padding-top: 1rem; }
        }

    </style>
    @stack('styles')
</head>
<body class="theme-cropcircle">

    <!-- Main Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-cropcircle sticky-top">
        <div class="container">
            <a class="navbar-brand" href="{{ route('home') }}">
                <span class="brand-badge"><i class="bi bi-tree-fill"></i></span>
                <span>CropCircle</span>
            </a>

            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain" aria-controls="navbarMain" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarMain">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-lg-3">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}" href="{{ route('products.index') }}">Browse Products</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('markets.*') ? 'active' : '' }}" href="{{ route('markets.index') }}">Local Markets</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('farmers.*') ? 'active' : '' }}" href="{{ route('farmers.index') }}">Our Farmers</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('about') ? 'active' : '' }}" href="{{ route('about') }}">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('contact') ? 'active' : '' }}" href="{{ route('contact') }}">Contact</a>
                    </li>
                </ul>

                <div class="d-flex align-items-center gap-2">
                    @auth
                        <!-- Customer Cart Icon -->
                        @if(auth()->user()->isCustomer())
                            <a href="{{ route('cart.index') }}" class="btn btn-outline-light text-dark position-relative border p-2 px-3 rounded-3" title="Shopping Cart">
                                <i class="bi bi-basket2-fill text-success fs-5"></i>
                                @php
                                    $cartCount = auth()->user()->cartItems()->count();
                                @endphp
                                @if($cartCount > 0)
                                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                        {{ $cartCount }}
                                    </span>
                                @endif
                            </a>

                            <a href="{{ route('customer.favorites.index') }}" class="btn btn-outline-light text-dark border p-2 px-3 rounded-3" title="Wishlist">
                                <i class="bi bi-heart-fill text-danger fs-5"></i>
                            </a>
                        @endif

                        <!-- Chat Icon -->
                        <a href="{{ route('chat.index') }}" class="btn btn-outline-light text-dark border p-2 px-3 rounded-3" title="Private Messages">
                            <i class="bi bi-chat-dots-fill text-primary fs-5"></i>
                        </a>

                        <!-- Notifications Dropdown -->
                        @php
                            $unreadCount = auth()->user()->unreadNotifications()->count();
                        @endphp
                        <div class="dropdown">
                            <button class="btn btn-outline-light text-dark position-relative border p-2 px-3 rounded-3" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="Notifications">
                                <i class="bi bi-bell-fill text-warning fs-5"></i>
                                @if($unreadCount > 0)
                                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                        {{ $unreadCount }}
                                    </span>
                                @endif
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow border-0 p-2" style="width: 320px; max-height: 400px; overflow-y: auto;">
                                <li class="d-flex justify-content-between align-items-center px-2 py-1 mb-2 border-bottom">
                                    <h6 class="mb-0 fw-bold">Notifications</h6>
                                    @if($unreadCount > 0)
                                        <form action="{{ route('notifications.readAll') }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-link btn-sm text-decoration-none p-0 text-success">Mark all read</button>
                                        </form>
                                    @endif
                                </li>
                                @forelse(auth()->user()->notifications()->take(5)->get() as $notif)
                                    <li>
                                        <a href="{{ route('notifications.read', $notif->id) }}" class="dropdown-item rounded p-2 mb-1 {{ !$notif->is_read ? 'bg-light fw-bold' : '' }}">
                                            <div class="small text-muted">{{ $notif->created_at->diffForHumans() }}</div>
                                            <div class="text-truncate">{{ $notif->title }}</div>
                                            <div class="small text-secondary text-truncate">{{ $notif->message }}</div>
                                        </a>
                                    </li>
                                @empty
                                    <li class="text-center py-3 text-muted small">No notifications yet</li>
                                @endforelse
                                <li class="border-top pt-2 text-center">
                                    <a href="{{ route('notifications.index') }}" class="small text-decoration-none text-success fw-bold">View all notifications</a>
                                </li>
                            </ul>
                        </div>

                        <!-- User Profile Dropdown -->
                        <div class="dropdown ms-1">
                            <button class="btn btn-light border d-flex align-items-center gap-2 rounded-3 px-3 py-2" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 28px; height: 28px; font-size: 0.85rem;">
                                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                </div>
                                <span class="d-none d-md-inline fw-semibold small">{{ auth()->user()->name }}</span>
                                <i class="bi bi-chevron-down small text-muted"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow border-0 p-2" style="min-width: 220px;">
                                <li class="px-3 py-2 border-bottom mb-1">
                                    <div class="fw-bold text-truncate">{{ auth()->user()->name }}</div>
                                    <div class="small text-muted text-truncate">{{ auth()->user()->email }}</div>
                                    <span class="badge badge-role mt-1 
                                        {{ auth()->user()->isAdmin() ? 'bg-danger' : (auth()->user()->isFarmer() ? 'bg-success' : 'bg-primary') }}">
                                        {{ ucfirst(auth()->user()->role) }}
                                    </span>
                                </li>

                                <!-- Role Portal Links -->
                                @if(auth()->user()->isAdmin())
                                    <li><a class="dropdown-item py-2 fw-semibold text-danger" href="{{ route('admin.dashboard') }}"><i class="bi bi-speedometer2 me-2"></i>Admin Dashboard</a></li>
                                    <li><a class="dropdown-item py-2" href="{{ route('admin.farmers.index') }}"><i class="bi bi-check-circle me-2"></i>Farmer Approvals</a></li>
                                    <li><a class="dropdown-item py-2" href="{{ route('admin.orders.index') }}"><i class="bi bi-box-seam me-2"></i>All Orders</a></li>
                                @elseif(auth()->user()->isFarmer())
                                    <li><a class="dropdown-item py-2 fw-semibold text-success" href="{{ route('farmer.dashboard') }}"><i class="bi bi-speedometer2 me-2"></i>Farmer Dashboard</a></li>
                                    <li><a class="dropdown-item py-2" href="{{ route('farmer.products.index') }}"><i class="bi bi-boxes me-2"></i>My Products</a></li>
                                    <li><a class="dropdown-item py-2" href="{{ route('farmer.orders.index') }}"><i class="bi bi-receipt me-2"></i>Customer Orders</a></li>
                                    <li><a class="dropdown-item py-2" href="{{ route('farmer.pickupSlots.index') }}"><i class="bi bi-clock me-2"></i>Pickup Slots</a></li>
                                @else
                                    <li><a class="dropdown-item py-2 fw-semibold text-primary" href="{{ route('customer.dashboard') }}"><i class="bi bi-speedometer2 me-2"></i>Customer Dashboard</a></li>
                                    <li><a class="dropdown-item py-2" href="{{ route('customer.orders.index') }}"><i class="bi bi-bag-check me-2"></i>My Orders</a></li>
                                    <li><a class="dropdown-item py-2" href="{{ route('customer.favorites.index') }}"><i class="bi bi-heart me-2"></i>My Favorites</a></li>
                                @endif

                                @if(auth()->user()->isCustomer() || auth()->user()->isFarmer())
                                    <li><a class="dropdown-item py-2" href="{{ route('feedback.create') }}"><i class="bi bi-chat-square-text me-2"></i>Share Website Feedback</a></li>
                                @endif
                                <li><a class="dropdown-item py-2" href="{{ route('profile') }}"><i class="bi bi-gear me-2"></i>Account Settings</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="dropdown-item py-2 text-danger"><i class="bi bi-box-arrow-right me-2"></i>Sign Out</button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    @else
                        <!-- Guest Sign In / Register -->
                        <a href="{{ route('login') }}" class="btn btn-outline-cropcircle me-2">Sign In</a>
                        <a href="{{ route('register') }}" class="btn btn-cropcircle">Join CropCircle</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Global Flash Alerts -->
    <div class="container mt-3">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert" data-cropcircle-flash @if(session('order_flash')) data-cropcircle-order-flash @endif>
                <i class="bi bi-check-circle-fill me-2 fs-5 align-middle"></i>
                <span class="align-middle">{{ session('success') }}</span>
                @if(session('order_flash'))
                    <button type="button" class="btn btn-sm btn-outline-secondary ms-3" data-bs-dismiss="alert" aria-label="OK">OK</button>
                @else
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                @endif
            </div>
        @endif

        @if(session('danger'))
            <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert" data-cropcircle-flash @if(session('order_flash')) data-cropcircle-order-flash @endif>
                <i class="bi bi-exclamation-triangle-fill me-2 fs-5 align-middle"></i>
                <span class="align-middle">{{ session('danger') }}</span>
                @if(session('order_flash'))
                    <button type="button" class="btn btn-sm btn-outline-secondary ms-3" data-bs-dismiss="alert" aria-label="OK">OK</button>
                @else
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                @endif
            </div>
        @endif

        @if(session('warning'))
            <div class="alert alert-warning alert-dismissible fade show border-0 shadow-sm" role="alert" data-cropcircle-flash @if(session('order_flash')) data-cropcircle-order-flash @endif>
                <i class="bi bi-exclamation-circle-fill me-2 fs-5 align-middle"></i>
                <span class="align-middle">{{ session('warning') }}</span>
                @if(session('order_flash'))
                    <button type="button" class="btn btn-sm btn-outline-secondary ms-3" data-bs-dismiss="alert" aria-label="OK">OK</button>
                @else
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                @endif
            </div>
        @endif

        @if(session('info'))
            <div class="alert alert-info alert-dismissible fade show border-0 shadow-sm" role="alert" data-cropcircle-flash @if(session('order_flash')) data-cropcircle-order-flash @endif>
                <i class="bi bi-info-circle-fill me-2 fs-5 align-middle"></i>
                <span class="align-middle">{{ session('info') }}</span>
                @if(session('order_flash'))
                    <button type="button" class="btn btn-sm btn-outline-secondary ms-3" data-bs-dismiss="alert" aria-label="OK">OK</button>
                @else
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                @endif
            </div>
        @endif
    </div>

    <!-- Keep role navigation available while farmers and admins browse the public marketplace. -->
    <main class="page-wrapper flex-fill">
        @if(auth()->check() && auth()->user()->isFarmer())
            <div class="container-fluid portal-shell"><div class="row g-0">
                @include('layouts.partials.farmer-sidebar')
                <div class="col-12 col-md-9 col-lg-10 portal-main px-md-4">@yield('content')</div>
            </div></div>
        @elseif(auth()->check() && auth()->user()->isAdmin() && !request()->routeIs('admin.*'))
            <div class="container-fluid portal-shell"><div class="row g-0">
                @include('layouts.partials.admin-sidebar')
                <div class="col-12 col-md-9 col-lg-10 portal-main px-md-4">@yield('content')</div>
            </div></div>
        @else
            @yield('content')
        @endif
    </main>

    <!-- Professional Footer -->
    <footer class="footer-cropcircle">
        <div class="container">
            <div class="row g-4 mb-5">
                <div class="col-lg-4 col-md-6">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <span class="bg-success text-white p-2 rounded-2 fs-5"><i class="bi bi-tree-fill"></i></span>
                        <span class="fs-4 fw-bold text-white">CropCircle</span>
                    </div>
                    <p class="text-secondary small pe-lg-4">
                        Connecting conscientious consumers with passionate local farmers. Direct from soil to stall, order seasonal harvests for scheduled community market pickup.
                    </p>
                    <div class="d-flex gap-3 text-secondary fs-5 mt-3">
                        <a href="#" class="text-secondary"><i class="bi bi-facebook"></i></a>
                        <a href="#" class="text-secondary"><i class="bi bi-instagram"></i></a>
                        <a href="#" class="text-secondary"><i class="bi bi-twitter-x"></i></a>
                    </div>
                </div>

                <div class="col-lg-2 col-md-3 col-6">
                    <h6 class="footer-heading">Discover</h6>
                    <ul class="list-unstyled small d-flex flex-column gap-2">
                        <li><a href="{{ route('products.index') }}">Seasonal Products</a></li>
                        <li><a href="{{ route('markets.index') }}">Farmers Markets</a></li>
                        <li><a href="{{ route('farmers.index') }}">Meet the Growers</a></li>
                        <li><a href="{{ route('about') }}">How Pre-Orders Work</a></li>
                    </ul>
                </div>

                <div class="col-lg-2 col-md-3 col-6">
                    <h6 class="footer-heading">Account & Help</h6>
                    <ul class="list-unstyled small d-flex flex-column gap-2">
                        <li><a href="{{ route('login') }}">Sign In to Account</a></li>
                        <li><a href="{{ route('contact') }}">Help & Support</a></li>
                        <li><a href="{{ route('faq') }}">Frequently Asked Questions</a></li>
                        <li><a href="{{ route('terms') }}">Terms of Service</a></li>
                        <li><a href="{{ route('privacy') }}">Privacy Policy</a></li>
                    </ul>
                </div>

                <div class="col-lg-4 col-md-6">
                    <div class="p-4 rounded-3 border border-secondary border-opacity-25 bg-black bg-opacity-25">
                        <h6 class="text-white fw-bold mb-2"><i class="bi bi-shop me-2 text-success"></i>Are you a local producer?</h6>
                        <p class="text-secondary small mb-3">
                            Sell your vegetables, fruits, dairy, or honey directly to your local neighborhood with zero upfront listing fees.
                        </p>
                        <a href="{{ route('register') }}" class="btn btn-sm btn-success fw-bold px-3 py-2">
                            Register Your Farm Today <i class="bi bi-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>

            <div class="pt-4 border-top border-secondary border-opacity-25 text-center text-secondary small d-flex flex-column flex-md-row justify-content-between align-items-center gap-2">
                <div>&copy; {{ date('Y') }} CropCircle Agricultural Marketplace. All rights reserved.</div>
                <div>Fresh Products. Transparent Origins. Local Impact.</div>
            </div>
        </div>
    </footer>

    <!-- Tabler Core 1.7.1 includes Bootstrap JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/@tabler/core@1.7.1/dist/js/tabler.min.js"></script>
    <!-- Leaflet JS for Maps -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('[data-cropcircle-flash]:not([data-cropcircle-order-flash])').forEach(function (alert) {
                window.setTimeout(function () {
                    if (window.bootstrap && bootstrap.Alert) {
                        bootstrap.Alert.getOrCreateInstance(alert).close();
                    } else {
                        alert.remove();
                    }
                }, 2000);
            });

            document.querySelectorAll('input[type="password"]').forEach(function (input, index) {
                if (input.dataset.passwordToggleReady === 'true') return;
                input.dataset.passwordToggleReady = 'true';
                if (!input.id) input.id = 'cropcircle-password-' + index;

                var group = document.createElement('div');
                group.className = 'input-group password-toggle-group';
                input.parentNode.insertBefore(group, input);
                group.appendChild(input);

                var toggle = document.createElement('button');
                toggle.type = 'button';
                toggle.className = 'btn btn-outline-secondary password-toggle-btn';
                toggle.setAttribute('aria-label', 'Show password');
                toggle.setAttribute('aria-pressed', 'false');
                toggle.setAttribute('aria-controls', input.id);
                toggle.innerHTML = '<i class="bi bi-eye" aria-hidden="true"></i>';
                toggle.addEventListener('click', function () {
                    var showPassword = input.type === 'password';
                    input.type = showPassword ? 'text' : 'password';
                    toggle.setAttribute('aria-pressed', String(showPassword));
                    toggle.setAttribute('aria-label', showPassword ? 'Hide password' : 'Show password');
                    toggle.innerHTML = '<i class="bi ' + (showPassword ? 'bi-eye-slash' : 'bi-eye') + '" aria-hidden="true"></i>';
                });
                group.appendChild(toggle);
            });

        });
    </script>
    <script src="{{ asset('js/cropcircle-motion.js') }}" defer></script>
    @stack('scripts')
</body>
</html>
