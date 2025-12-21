<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Hasta GoRent')</title>

    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
    :root{
        --hasta:#cb3737;
        --hasta-dark:#a92c2c;
        --hasta-darker:#8a2424;
        --bg:#F6F7FB;
    }
    body { background: var(--bg); font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; }
    
    /* Top Navigation Bar (Dark Red) */
    .top-navbar{
        background: var(--hasta-darker);
        padding: 10px 0;
        width: 100%;
    }
    .top-navbar .container-custom{
        max-width: 1400px;
        margin: 0 auto;
        padding: 0 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 16px;
    }
    .top-navbar-left{
        display: flex;
        align-items: center;
        gap: 12px;
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
    .top-navbar-badge{
        background: #f4c430;
        color: #111;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
        white-space: nowrap;
    }
    .top-navbar-right{
        display: flex;
        align-items: center;
        gap: 20px;
        flex-wrap: wrap;
    }
    .top-navbar-link{
        color: #fff;
        text-decoration: none;
        font-size: 13px;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 6px;
        transition: opacity 0.2s;
    }
    .top-navbar-link:hover{
        opacity: 0.8;
        color: #fff;
    }
    .top-navbar-link svg{
        width: 16px;
        height: 16px;
    }
    .top-navbar-btn{
        background: var(--hasta);
        color: #fff;
        padding: 8px 20px;
        border-radius: 20px;
        text-decoration: none;
        font-size: 13px;
        font-weight: 600;
        border: none;
        transition: all 0.2s;
    }
    .top-navbar-btn:hover{
        background: var(--hasta);
        opacity: 0.9;
        color: #fff;
        transform: translateY(-1px);
    }
    
    .btn-hasta{ 
        background:var(--hasta); 
        border-color:var(--hasta); 
        color:#fff; 
        font-weight: 600;
    }
    .btn-hasta:hover{ 
        background:var(--hasta-dark); 
        border-color:var(--hasta-dark); 
        color:#fff; 
    }
    .text-hasta{ color:var(--hasta); }
    .bg-hasta{ background:var(--hasta); }
    .shadow-soft { box-shadow: 0 10px 25px rgba(0,0,0,.06); }
    .rounded-xxl{ border-radius: 22px; }

    .hero-gocar{
    position:relative;
    border-radius: 26px;
    overflow:hidden;
    min-height: 460px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #4facfe 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    }

    .hero-gocar .hero-inner{
    padding: 84px 70px 140px 70px;
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    }

    .hero-meta{ 
    max-width: 600px; 
    text-align: center;
    z-index: 2;
    }

    .hero-title{
    font-size: 52px;
    font-weight: 800;
    letter-spacing: -.5px;
    line-height: 1.05;
    color: white;
    margin-bottom: 16px;
    }

    .hero-sub{
    color: rgba(255,255,255,.95);
    margin-top: 10px;
    font-size: 18px;
    margin-bottom: 24px;
    }

    @media (max-width: 768px){
    .top-navbar .container-custom{ flex-direction: column; align-items: flex-start; }
    .top-navbar-right{ width: 100%; justify-content: space-between; }
    .hero-gocar{ min-height: 420px; }
    .hero-gocar .hero-inner{ padding: 58px 24px 140px 24px; }
    .hero-title{ font-size: 36px; }
    }

    </style>


    @stack('styles')
</head>
<body>

{{-- TOP NAVIGATION BAR (Dark Red) --}}
<div class="top-navbar">
    <div class="container-custom">
        <div class="top-navbar-left">
            <a href="{{ route('home') }}" class="top-navbar-brand">
                <span>Hasta GoRent.</span>
            </a>
            <span class="top-navbar-badge">UTM STUDENT & STAFF CAR RENTAL</span>
        </div>
        <div class="top-navbar-right">
            @if(session('auth_role') === 'staff')
                <a href="{{ route('staff.dashboard') }}" class="top-navbar-link">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                        <line x1="9" y1="3" x2="9" y2="21"></line>
                    </svg>
                    <span>Staff Portal</span>
                </a>
                <button type="button" class="top-navbar-link border-0 bg-transparent" style="cursor: pointer;" data-bs-toggle="modal" data-bs-target="#logoutModal">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                        <polyline points="16 17 21 12 16 7"></polyline>
                        <line x1="21" y1="12" x2="9" y2="12"></line>
                    </svg>
                    <span>Logout</span>
                </button>
            @elseif(session('auth_role') === 'customer')
                <a href="{{ route('customer.profile') }}" class="top-navbar-link">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                        <circle cx="12" cy="7" r="4"></circle>
                    </svg>
                    <span>{{ session('auth_name') ?? 'My Account' }}</span>
                </a>
                <button type="button" class="top-navbar-link border-0 bg-transparent" style="cursor: pointer;" data-bs-toggle="modal" data-bs-target="#logoutModal">
                    <span>Logout</span>
                </button>
            @else
                <a href="/login" class="top-navbar-link">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                        <circle cx="12" cy="7" r="4"></circle>
                    </svg>
                    <span>Login / Sign Up</span>
                </a>
            @endif
            <a href="{{ route('home') }}" class="top-navbar-link">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                    <polyline points="9 22 9 12 15 12 15 22"></polyline>
                </svg>
                <span>Home</span>
            </a>
            <a href="#" class="top-navbar-link">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="16" x2="12" y2="12"></line>
                    <line x1="12" y1="8" x2="12.01" y2="8"></line>
                </svg>
                <span>Help</span>
            </a>
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

@stack('scripts')
</body>
</html>