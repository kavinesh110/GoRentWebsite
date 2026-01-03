<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Hasta GoRent')</title>

    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css">

    <style>
    :root{
        --hasta: #cb3737;
        --hasta-dark: #a92c2c;
        --hasta-darker: #8a2424;
        --bg: #F6F7FB;
        --bg-light: #f8f9fa;
        --text-main: #2d3436;
        --text-muted: #636e72;
    }
    body { background: var(--bg); font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; }
    
    /* Top Navigation Bar (Dark Red) */
    .top-navbar{
        background: var(--hasta-darker);
        padding: 0;
        height: 70px;
        width: 100%;
        position: sticky;
        top: 0;
        z-index: 1050;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        display: flex;
        align-items: center;
    }
    .top-navbar .container-fluid{
        max-width: 100%;
        padding: 0 30px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        height: 100%;
        gap: 20px;
    }
    .top-navbar-left{
        display: flex;
        align-items: center;
        gap: 15px;
        flex: 1;
    }
    .top-navbar-brand{
        font-weight: 700;
        letter-spacing: .3px;
        font-size: 18px;
        color: #fff;
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .navbar-logo{
        height: 32px;
        width: auto;
        max-width: 200px;
        object-fit: contain;
    }
    .top-navbar-badge{
        background: #f4c430;
        color: #111;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 9px;
        font-weight: 800;
        white-space: nowrap;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .top-navbar-nav{
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 25px;
        flex: 2;
    }
    .top-navbar-right{
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 15px;
        flex: 1;
    }
    .top-navbar-link{
        color: rgba(255,255,255,0.9);
        text-decoration: none;
        font-size: 14px;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 6px;
        transition: all 0.2s;
    }
    .top-navbar-link:hover, .top-navbar-link.active{
        color: #fff;
        opacity: 1;
    }
    .top-navbar-link svg, .top-navbar-link i{
        font-size: 16px;
    }
    .top-navbar-btn{
        background: #fff;
        color: var(--hasta-darker);
        padding: 8px 18px;
        border-radius: 50px;
        text-decoration: none;
        font-size: 13px;
        font-weight: 700;
        border: none;
        transition: all 0.2s;
    }
    .top-navbar-btn:hover{
        background: #f8f8f8;
        color: var(--hasta-darker);
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    
    .btn-hasta{ 
        background: var(--hasta); 
        border-color: var(--hasta); 
        color: #fff; 
        font-weight: 600;
    }
    .btn-hasta:hover{ 
        background: var(--hasta-dark); 
        border-color: var(--hasta-dark); 
        color: #fff; 
    }
    .text-hasta{ color: var(--hasta); }
    .bg-hasta{ background: var(--hasta); }
    .shadow-soft { box-shadow: 0 10px 25px rgba(0,0,0,.06); }
    .rounded-xxl{ border-radius: 22px; }
    
    @media (max-width: 992px){
        .top-navbar-nav { display: none; }
        .top-navbar-badge { display: none; }
        .top-navbar-left, .top-navbar-right { flex: initial; }
    }

    @media (max-width: 768px){
        .top-navbar .container-custom{ padding: 0 15px; }
        .top-navbar-right{ gap: 10px; }
        .top-navbar-link span { display: none; }
        .top-navbar-link i, .top-navbar-link svg { font-size: 18px; }
    }

    /* ===== MOBILE SIDEBAR (768px and below only) ===== */
    .mobile-sidebar-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.5);
        z-index: 1100;
        opacity: 0;
        visibility: hidden;
        pointer-events: none;
        transition: opacity 0.3s ease, visibility 0.3s ease;
    }
    .mobile-sidebar-overlay.active {
        opacity: 1;
        visibility: visible;
        pointer-events: auto;
    }
    .mobile-sidebar {
        position: fixed;
        top: 0;
        left: -100%;
        width: 280px;
        height: 100%;
        background: #fff;
        z-index: 1200;
        transition: left 0.3s ease;
        overflow-y: auto;
        box-shadow: 4px 0 20px rgba(0,0,0,0.1);
        visibility: hidden;
    }
    .mobile-sidebar.active {
        left: 0;
        visibility: visible;
    }
    .mobile-sidebar-header {
        background: var(--hasta-darker);
        color: #fff;
        padding: 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .mobile-sidebar-header img {
        height: 32px;
    }
    .mobile-sidebar-close {
        background: transparent;
        border: none;
        color: #fff;
        font-size: 24px;
        cursor: pointer;
        padding: 8px;
        border-radius: 8px;
        transition: background 0.2s;
    }
    .mobile-sidebar-close:hover {
        background: rgba(255,255,255,0.1);
    }
    .mobile-sidebar-nav {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    .mobile-sidebar-nav-item {
        border-bottom: 1px solid #f0f0f0;
    }
    .mobile-sidebar-nav-link {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 16px 20px;
        color: #333;
        text-decoration: none;
        font-size: 15px;
        font-weight: 500;
        transition: all 0.2s;
    }
    .mobile-sidebar-nav-link:hover,
    .mobile-sidebar-nav-link.active {
        background: #f8f9fa;
        color: var(--hasta);
    }
    .mobile-sidebar-nav-link i {
        font-size: 20px;
        width: 24px;
        text-align: center;
        color: #666;
    }
    .mobile-sidebar-nav-link.active i,
    .mobile-sidebar-nav-link:hover i {
        color: var(--hasta);
    }
    .mobile-sidebar-divider {
        height: 8px;
        background: #f0f0f0;
    }
    .mobile-sidebar-section-title {
        padding: 16px 20px 8px;
        font-size: 11px;
        font-weight: 700;
        color: #999;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .mobile-menu-toggle {
        display: none;
    }
    
    @media (max-width: 768px) {
        .mobile-menu-toggle {
            display: flex;
            background: transparent;
            border: none;
            color: #fff;
            font-size: 24px;
            padding: 8px;
            cursor: pointer;
            align-items: center;
            justify-content: center;
            min-height: 44px;
            min-width: 44px;
            border-radius: 8px;
            transition: background 0.2s;
        }
        .mobile-menu-toggle:hover {
            background: rgba(255,255,255,0.1);
        }
        .top-navbar {
            height: 60px;
        }
        .top-navbar .container-fluid {
            padding: 0 16px;
        }
        .top-navbar-badge {
            display: none;
        }
        .top-navbar-nav {
            display: none;
        }
        .top-navbar-right .top-navbar-link span {
            display: none;
        }
        .navbar-logo {
            height: 28px;
        }
    }

    </style>


    @stack('styles')
</head>
<body>

{{-- MOBILE SIDEBAR OVERLAY --}}
<div class="mobile-sidebar-overlay" id="mobileSidebarOverlay"></div>

{{-- MOBILE SIDEBAR --}}
<div class="mobile-sidebar" id="mobileSidebar">
    <div class="mobile-sidebar-header">
        <img src="{{ asset('images/hastalogo.jpg') }}" alt="Hasta GoRent" onerror="this.outerHTML='<span style=\'font-weight:700;font-size:16px;\'>Hasta GoRent</span>'">
        <button type="button" class="mobile-sidebar-close" id="mobileSidebarClose">
            <i class="bi bi-x-lg"></i>
        </button>
    </div>
    <ul class="mobile-sidebar-nav">
        @if(session('auth_role') === 'customer')
            <div class="mobile-sidebar-section-title">My Account</div>
            <li class="mobile-sidebar-nav-item">
                <a href="{{ route('customer.profile') }}" class="mobile-sidebar-nav-link {{ request()->routeIs('customer.profile') ? 'active' : '' }}">
                    <i class="bi bi-person-circle"></i>
                    <span>My Profile</span>
                </a>
            </li>
            <li class="mobile-sidebar-nav-item">
                <a href="{{ route('customer.bookings') }}" class="mobile-sidebar-nav-link {{ request()->routeIs('customer.bookings*') ? 'active' : '' }}">
                    <i class="bi bi-calendar-check"></i>
                    <span>My Bookings</span>
                </a>
            </li>
            <li class="mobile-sidebar-nav-item">
                <a href="{{ route('customer.loyalty-rewards') }}" class="mobile-sidebar-nav-link {{ request()->routeIs('customer.loyalty-rewards') ? 'active' : '' }}">
                    <i class="bi bi-gift"></i>
                    <span>Loyalty Rewards</span>
                </a>
            </li>
            <div class="mobile-sidebar-divider"></div>
        @endif
        
        <div class="mobile-sidebar-section-title">Browse</div>
        <li class="mobile-sidebar-nav-item">
            <a href="{{ route('home') }}" class="mobile-sidebar-nav-link {{ request()->routeIs('home') ? 'active' : '' }}">
                <i class="bi bi-house"></i>
                <span>Home</span>
            </a>
        </li>
        <li class="mobile-sidebar-nav-item">
            <a href="{{ route('cars.index') }}" class="mobile-sidebar-nav-link {{ request()->routeIs('cars.*') ? 'active' : '' }}">
                <i class="bi bi-car-front"></i>
                <span>Browse Cars</span>
            </a>
        </li>
        <li class="mobile-sidebar-nav-item">
            <a href="/#how-it-works" class="mobile-sidebar-nav-link">
                <i class="bi bi-info-circle"></i>
                <span>How It Works</span>
            </a>
        </li>
        <li class="mobile-sidebar-nav-item">
            <a href="/#support" class="mobile-sidebar-nav-link">
                <i class="bi bi-envelope"></i>
                <span>Support</span>
            </a>
        </li>
        
        <div class="mobile-sidebar-divider"></div>
        
        @if(session('auth_role') === 'staff')
            <li class="mobile-sidebar-nav-item">
                <a href="{{ route('staff.dashboard') }}" class="mobile-sidebar-nav-link">
                    <i class="bi bi-speedometer2"></i>
                    <span>Staff Portal</span>
                </a>
            </li>
            <li class="mobile-sidebar-nav-item">
                <button type="button" class="mobile-sidebar-nav-link w-100 text-start border-0 bg-transparent" data-bs-toggle="modal" data-bs-target="#logoutModal">
                    <i class="bi bi-box-arrow-right"></i>
                    <span>Logout</span>
                </button>
            </li>
        @elseif(session('auth_role') === 'customer')
            <li class="mobile-sidebar-nav-item">
                <button type="button" class="mobile-sidebar-nav-link w-100 text-start border-0 bg-transparent" data-bs-toggle="modal" data-bs-target="#logoutModal">
                    <i class="bi bi-box-arrow-right"></i>
                    <span>Logout</span>
                </button>
            </li>
        @else
            <li class="mobile-sidebar-nav-item">
                <a href="{{ route('login') }}" class="mobile-sidebar-nav-link">
                    <i class="bi bi-box-arrow-in-right"></i>
                    <span>Login</span>
                </a>
            </li>
            <li class="mobile-sidebar-nav-item">
                <a href="{{ route('register') }}" class="mobile-sidebar-nav-link">
                    <i class="bi bi-person-plus"></i>
                    <span>Register</span>
                </a>
            </li>
        @endif
    </ul>
</div>

{{-- TOP NAVIGATION BAR (Dark Red) --}}
<div class="top-navbar">
    <div class="container-fluid">
        <div class="top-navbar-left">
            <button type="button" class="mobile-menu-toggle" id="mobileMenuToggle">
                <i class="bi bi-list"></i>
            </button>
            <a href="{{ route('home') }}" class="top-navbar-brand">
                <img src="{{ asset('images/hastalogo.jpg') }}" alt="Hasta GoRent Logo" class="navbar-logo" onerror="this.style.display='none'; this.nextElementSibling.style.display='inline';">
                <span style="display: none;">Hasta GoRent.</span>
            </a>
            <span class="top-navbar-badge">UTM Student & Staff</span>
        </div>

        <div class="top-navbar-nav">
            <a href="{{ route('cars.index') }}" class="top-navbar-link {{ request()->routeIs('cars.index') ? 'active' : '' }}">
                <i class="bi bi-car-front"></i> <span>Browse Cars</span>
            </a>
            <a href="/#how-it-works" class="top-navbar-link">
                <i class="bi bi-info-circle"></i> <span>How It Works</span>
            </a>
            <a href="/#support" class="top-navbar-link">
                <i class="bi bi-envelope"></i> <span>Support</span>
            </a>
        </div>
        
        <div class="top-navbar-right">
            @if(session('auth_role') === 'staff')
                <a href="{{ route('staff.dashboard') }}" class="top-navbar-link">
                    <i class="bi bi-layout-text-sidebar-reverse"></i>
                    <span>Staff Portal</span>
                </a>
                <button type="button" class="top-navbar-link border-0 bg-transparent" style="cursor: pointer;" data-bs-toggle="modal" data-bs-target="#logoutModal">
                    <i class="bi bi-box-arrow-right"></i>
                    <span>Logout</span>
                </button>
            @elseif(session('auth_role') === 'customer')
                <a href="{{ route('customer.profile') }}" class="top-navbar-link">
                    <i class="bi bi-person-circle"></i>
                    <span>{{ explode(' ', session('auth_name') ?? 'User')[0] }}...</span>
                </a>
                <a href="{{ route('customer.bookings') }}" class="top-navbar-link">
                    <i class="bi bi-calendar-check"></i>
                    <span>My Bookings</span>
                </a>
                <button type="button" class="top-navbar-link border-0 bg-transparent" style="cursor: pointer;" data-bs-toggle="modal" data-bs-target="#logoutModal">
                    <i class="bi bi-box-arrow-right"></i>
                    <span>Logout</span>
                </button>
            @else
                <a href="{{ route('login') }}" class="top-navbar-link">
                    <i class="bi bi-box-arrow-in-right"></i>
                    <span>Login</span>
                </a>
                <a href="{{ route('register') }}" class="top-navbar-btn">
                    Register
                </a>
            @endif
        </div>
    </div>
</div>

<main style="margin: 0;">
    @yield('content')
</main>

<footer class="border-top bg-white mt-5">
    <div class="container py-4">
        <div class="row">
            <div class="col-md-3 mb-3">
                <h6 class="fw-bold mb-3">About Hasta GoRent</h6>
                <ul class="list-unstyled small">
                    <li class="mb-2"><a href="#" class="text-decoration-none text-muted">About Us</a></li>
                    <li class="mb-2"><a href="#" class="text-decoration-none text-muted">Careers</a></li>
                    <li class="mb-2"><a href="#" class="text-decoration-none text-muted">Press</a></li>
                </ul>
            </div>
            <div class="col-md-3 mb-3">
                <h6 class="fw-bold mb-3">Services</h6>
                <ul class="list-unstyled small">
                    <li class="mb-2"><a href="#" class="text-decoration-none text-muted">Car Rental</a></li>
                    <li class="mb-2"><a href="#" class="text-decoration-none text-muted">Car Service</a></li>
                    <li class="mb-2"><a href="#" class="text-decoration-none text-muted">Subscription</a></li>
                </ul>
            </div>
            <div class="col-md-3 mb-3">
                <h6 class="fw-bold mb-3">Support</h6>
                <ul class="list-unstyled small">
                    <li class="mb-2"><a href="#" class="text-decoration-none text-muted">Help Center</a></li>
                    <li class="mb-2"><a href="#" class="text-decoration-none text-muted">Contact Us</a></li>
                    <li class="mb-2"><a href="#" class="text-decoration-none text-muted">FAQs</a></li>
                </ul>
            </div>
            <div class="col-md-3 mb-3">
                <h6 class="fw-bold mb-3">Legal</h6>
                <ul class="list-unstyled small">
                    <li class="mb-2"><a href="#" class="text-decoration-none text-muted">Terms of Service</a></li>
                    <li class="mb-2"><a href="#" class="text-decoration-none text-muted">Privacy Policy</a></li>
                </ul>
            </div>
        </div>
        <hr class="my-4">
        <div class="text-center small text-muted">
            © {{ date('Y') }} Hasta GoRent. All rights reserved.
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

{{-- Logout Confirmation Modal --}}
@if(session('auth_role'))
<div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title" id="logoutModalLabel">Confirm Logout</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body py-4">
                <p class="mb-0">Are you sure you want to logout? You will need to login again to access your account.</p>
            </div>
            <div class="modal-footer border-top-0 pt-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form method="POST" action="{{ route('logout') }}" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-hasta">Logout</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endif

{{-- Mobile Sidebar Toggle Script --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.getElementById('mobileSidebar');
    const overlay = document.getElementById('mobileSidebarOverlay');
    const toggleBtn = document.getElementById('mobileMenuToggle');
    const closeBtn = document.getElementById('mobileSidebarClose');

    function openSidebar() {
        sidebar.classList.add('active');
        overlay.classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function closeSidebar() {
        sidebar.classList.remove('active');
        overlay.classList.remove('active');
        document.body.style.overflow = '';
    }

    if (toggleBtn) toggleBtn.addEventListener('click', openSidebar);
    if (closeBtn) closeBtn.addEventListener('click', closeSidebar);
    if (overlay) overlay.addEventListener('click', closeSidebar);

    // Close sidebar when clicking a link
    document.querySelectorAll('.mobile-sidebar-nav-link').forEach(function(link) {
        link.addEventListener('click', closeSidebar);
    });
});
</script>

@stack('scripts')
</body>
</html>