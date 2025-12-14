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
        --bg:#f7f7f8;
    }
    body { background: var(--bg); font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; }
    .navbar-brand { 
        font-weight: 700; 
        letter-spacing: .3px; 
        font-size: 18px;
        color: #111;
    }
    .navbar{
        background: #fff;
        border-bottom: 1px solid #e9ecef;
        padding: 12px 0;
    }
    .navbar .nav-link{
        color: #333;
        font-weight: 500;
        font-size: 14px;
        padding: 8px 16px;
    }
    .navbar .nav-link:hover{
        color: var(--hasta);
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

    background:
        linear-gradient(180deg, rgba(0,0,0,.35) 0%, rgba(0,0,0,.55) 100%),
        url("https://images.unsplash.com/photo-1506377247377-2a5b3b417ebb?auto=format&fit=crop&w=1800&q=80");
    background-size:cover;
    background-position:center;
    }

    .hero-gocar .hero-inner{
    padding: 84px 70px 140px 70px;
    }

    .hero-meta{ max-width: 760px; }

    .hero-title{
    font-size: 52px;
    font-weight: 800;
    letter-spacing: -.5px;
    line-height: 1.05;
    }

    .hero-sub{
    color: rgba(255,255,255,.88);
    margin-top: 10px;
    font-size: 16px;
    }

    @media (max-width: 768px){
    .hero-gocar{ min-height: 420px; }
    .hero-gocar .hero-inner{ padding: 58px 24px 140px 24px; }
    .hero-title{ font-size: 36px; }
    }


    </style>


    @stack('styles')
</head>
<body>

<nav class="navbar navbar-expand-lg">
    <div class="container-fluid" style="max-width: 1400px; margin: 0 auto; padding: 0 24px;">
        <a class="navbar-brand d-flex align-items-center gap-2" href="/">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M5 17h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2z"/>
                <circle cx="7" cy="17" r="2"/>
                <circle cx="17" cy="17" r="2"/>
            </svg>
            <span>Hasta GoRent</span>
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMain">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navMain">
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0 align-items-center">
                <li class="nav-item"><a class="nav-link" href="/"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align: -2px; margin-right: 4px;"><path d="M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z"/></svg> Login / Sign Up Account</a></li>
                <li class="nav-item"><a class="nav-link" href="#"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align: -2px; margin-right: 4px;"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4M17 8l-5-5-5 5M12 3v12"/></svg> Latest Update</a></li>
                <li class="nav-item"><a class="nav-link" href="#"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align: -2px; margin-right: 4px;"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg> Choose language</a></li>
                <li class="nav-item">
                    <a class="btn btn-hasta rounded-pill px-4 py-2 ms-2" href="/bookings/create" style="font-size: 14px;">Download Hasta GoRent App</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

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
@stack('scripts')
</body>
</html>