<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Hasta Travel RMS')</title>

    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
    :root{
        --hasta:#cb3737;
        --hasta-dark:#a92c2c;
        --bg:#f7f7f8;
    }
    body { background: var(--bg); }
    .navbar-brand { font-weight: 800; letter-spacing: .3px; }
    .btn-hasta{ background:var(--hasta); border-color:var(--hasta); color:#fff; }
    .btn-hasta:hover{ background:var(--hasta-dark); border-color:var(--hasta-dark); color:#fff; }
    .text-hasta{ color:var(--hasta); }
    .bg-hasta{ background:var(--hasta); }
    .shadow-soft { box-shadow: 0 10px 25px rgba(0,0,0,.06); }
    .rounded-xxl{ border-radius: 22px; }
    .hero-wrap{
        background: radial-gradient(1200px 500px at 10% 10%, rgba(203,55,55,.25), transparent 60%),
                    radial-gradient(900px 400px at 90% 0%, rgba(203,55,55,.18), transparent 55%),
                    linear-gradient(180deg, #1b1b1b 0%, #111 100%);
        color:#fff;
        border-radius: 26px;
        overflow:hidden;
        position:relative;
    }
    .hero-overlay{
        position:absolute; inset:0;
        background: linear-gradient(90deg, rgba(0,0,0,.55), rgba(0,0,0,.15));
    }
    .hero-content{ position:relative; z-index:2; }
    .hero-search{
        background:#fff; color:#111;
        border-radius: 999px;
        padding: 14px;
    }
    .hero-search .form-control, .hero-search .form-select{
        border:0; box-shadow:none;
    }
    .hero-search .divider{ width:1px; background:#e9ecef; }
    .promo-card{
        background: rgba(255,255,255,.92);
        border-radius: 18px;
    }

    .hero-gocar{
    position:relative;
    border-radius: 26px;
    overflow:hidden;
    min-height: 460px;

    /* GoCar-like: wide city image + soft overlay */
    background:
        linear-gradient(180deg, rgba(0,0,0,.35) 0%, rgba(0,0,0,.55) 100%),
        url("https://images.unsplash.com/photo-1506377247377-2a5b3b417ebb?auto=format&fit=crop&w=1800&q=80");
    background-size:cover;
    background-position:center;
    }

    .hero-gocar .hero-inner{
    padding: 84px 70px 170px 70px; /* extra bottom space for floating bar */
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

    /* floating bar like GoCar */
    .hero-floatbar{
    position:absolute;
    left:50%;
    transform:translateX(-50%);
    bottom: -44px;
    width: min(1080px, 92%);
    background:#fff;
    border-radius: 999px;
    box-shadow: 0 20px 60px rgba(0,0,0,.22);
    padding: 12px 14px;
    }

    .float-item{
    padding: 10px 14px;
    border-radius: 999px;
    background: transparent;
    }

    .float-label{
    font-size: 12px;
    color:#6c757d;
    margin-bottom: 2px;
    }

    .float-value{
    font-weight: 600;
    color:#111;
    font-size: 14px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 220px;
    }

    .float-divider{
    width:1px;
    background:#e9ecef;
    align-self: stretch;
    margin: 6px 0;
    }

    .btn-search{
    width: 46px;
    height: 46px;
    border-radius: 999px;
    background: var(--hasta);
    display:flex;
    align-items:center;
    justify-content:center;
    border: none;
    }

    .btn-search:hover{ background: var(--hasta-dark); }

    @media (max-width: 768px){
    .hero-gocar{ min-height: 420px; }
    .hero-gocar .hero-inner{ padding: 58px 24px 170px 24px; }
    .hero-title{ font-size: 36px; }
    .float-value{ max-width: 120px; }
    }


    </style>


    @stack('styles')
</head>
<body>

<nav class="navbar navbar-expand-lg bg-white border-bottom">
    <div class="container">
        <a class="navbar-brand" href="/">Hasta Travel</a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMain">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navMain">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item"><a class="nav-link" href="/">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="/cars">Cars</a></li>
                <li class="nav-item"><a class="nav-link" href="/bookings/create">Make Booking</a></li>
                <li class="nav-item"><a class="nav-link" href="/bookings">My Bookings</a></li>
                <li class="nav-item"><a class="nav-link" href="/account">Account</a></li>
                <li class="nav-item"><a class="nav-link" href="/admin/customers">Admin</a></li>
            </ul>

            <div class="d-flex gap-2">
                <!-- UI only for now -->
                <a class="btn btn-outline-dark btn-sm" href="/login">Login</a>
                <a class="btn btn-dark btn-sm" href="/register">Register</a>
            </div>
        </div>
    </div>
</nav>

<main class="container py-4">
    @yield('content')
</main>

<footer class="border-top bg-white">
    <div class="container py-3 small text-muted d-flex justify-content-between">
        <span>© {{ date('Y') }} Hasta Travel</span>
        <span>Car Rental Management System</span>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
