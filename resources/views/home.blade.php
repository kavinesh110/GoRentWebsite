@extends('layouts.app')
@section('title', 'Home - Hasta GoRent')

{{-- ===== HOMEPAGE ===== --}}
{{-- Premium UI/UX with Integrated Activity Carousel & Search --}}
@section('content')

<style>
  /* Professional Hero Section with Activity Carousel */
  .hero-section {
    position: relative;
    background: #1e272e;
    border-radius: 0 0 0 0;
    color: #fff;
    overflow: hidden;
    min-height: 550px;
    display: flex;
    align-items: center;
  }
  
  .hero-carousel {
    width: 100%;
    height: 100%;
    min-height: 550px;
  }
  
  .hero-carousel .carousel-item {
    min-height: 550px;
    position: relative;
  }

  /* Background Image styling */
  .hero-bg-image {
    position: absolute;
    top: 0; left: 0; width: 100%; height: 100%;
    object-fit: cover;
    z-index: 1;
  }
  
  /* Text readability overlay */
  .hero-overlay {
    position: absolute;
    top: 0; left: 0; width: 100%; height: 100%;
    background: linear-gradient(to right, rgba(0,0,0,0.85) 0%, rgba(0,0,0,0.3) 100%);
    z-index: 2;
  }

  .hero-content {
    position: relative;
    z-index: 10;
    padding: 100px 60px;
    max-width: 850px;
  }

  .hero-title {
    font-size: 58px;
    font-weight: 800;
    margin-bottom: 24px;
    line-height: 1.1;
    color: #fff;
    text-shadow: 0 2px 10px rgba(0,0,0,0.3);
  }
  .hero-subtitle {
    font-size: 20px;
    opacity: 0.95;
    margin-bottom: 35px;
    max-width: 650px;
    line-height: 1.5;
  }

  /* Floating Search Card */
  .search-card-container {
    max-width: 1200px;
    margin: -70px auto 0 auto;
    padding: 0 24px;
    position: relative;
    z-index: 100;
  }
  .search-card {
    background: #fff;
    border-radius: 24px;
    padding: 30px;
    box-shadow: 0 30px 60px rgba(0,0,0,0.15);
    border: 1px solid #f0f0f0;
  }
  .search-card .form-label {
    font-size: 11px;
    font-weight: 700;
    color: #95a5a6;
    text-transform: uppercase;
    margin-bottom: 10px;
    letter-spacing: 0.5px;
  }
  .search-card .form-control, 
  .search-card .form-select {
    border: 1px solid #eee;
    padding: 14px 16px;
    font-weight: 600;
    font-size: 15px;
    border-radius: 12px;
    transition: 0.2s;
  }
  .search-card .form-control:focus, 
  .search-card .form-select:focus {
    border-color: var(--hasta);
    box-shadow: 0 0 0 4px rgba(203,55,55,0.1);
  }

  /* Car Cards */
  .car-card {
    border: none;
    border-radius: 24px;
    overflow: hidden;
    transition: 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
    background: #FFFFFF !important;
    box-shadow: 0 10px 30px rgba(0,0,0,0.05);
    height: 100%;
    display: flex;
    flex-direction: column;
  }
  .car-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 20px 40px rgba(0,0,0,0.1);
  }
  .car-img-container {
    height: 220px;
    background: #FFFFFF !important;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 30px;
    position: relative;
  }
  .car-img-container img {
    max-width: 100%;
    max-height: 100%;
    object-fit: contain;
    filter: drop-shadow(0 10px 15px rgba(0,0,0,0.08));
  }

  .btn-hasta-primary {
    background: var(--hasta);
    color: #fff;
    border: none;
    padding: 14px 28px;
    border-radius: 14px;
    font-weight: 700;
    transition: 0.3s;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
  }
  .btn-hasta-primary:hover {
    background: var(--hasta-dark);
    color: #fff;
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(203,55,55,0.3);
  }

  @media (max-width: 991px) {
    .hero-title { font-size: 46px; }
    .hero-section { min-height: 500px; }
    .hero-carousel, .hero-carousel .carousel-item { min-height: 500px; }
  }

  @media (max-width: 767px) {
    .hero-title { font-size: 36px; }
    .hero-content { padding: 80px 24px; }
    .search-card { padding: 20px; }
    .search-card-container { margin-top: -50px; }
  }

  /* ===== MOBILE HERO CAROUSEL (independent section) ===== */
  .mobile-hero-section {
    display: none;
  }
  
  @media (max-width: 767px) {
    /* Hide desktop hero on mobile */
    .hero-section {
      display: none !important;
    }
    
    /* Show mobile hero section */
    .mobile-hero-section {
      display: block;
      padding: 20px 16px 0 16px;
      background: var(--bg);
    }
    
    .mobile-hero-carousel {
      width: 100%;
      border-radius: 20px;
      overflow: hidden;
      box-shadow: 0 10px 30px rgba(0,0,0,0.1);
      background: #1e272e;
    }
    
    .mobile-hero-carousel .carousel-item {
      height: 200px;
      position: relative;
    }
    
    .mobile-hero-carousel .carousel-item img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }
    
    .mobile-hero-carousel .carousel-item .mobile-hero-fallback {
      width: 100%;
      height: 100%;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      padding: 24px;
      text-align: center;
      background: linear-gradient(135deg, var(--hasta) 0%, var(--hasta-dark) 100%);
      color: #fff;
    }
    
    .mobile-hero-fallback h2 {
      font-size: 20px;
      font-weight: 700;
      margin-bottom: 8px;
      line-height: 1.3;
    }
    
    .mobile-hero-fallback p {
      font-size: 13px;
      opacity: 0.9;
      margin-bottom: 0;
      line-height: 1.4;
    }
    
    .mobile-hero-carousel .carousel-control-prev,
    .mobile-hero-carousel .carousel-control-next {
      width: 40px;
      height: 40px;
      top: 50%;
      transform: translateY(-50%);
      background: rgba(0,0,0,0.4);
      border-radius: 50%;
      opacity: 1;
    }
    
    .mobile-hero-carousel .carousel-control-prev {
      left: 10px;
    }
    
    .mobile-hero-carousel .carousel-control-next {
      right: 10px;
    }
    
    .mobile-hero-carousel .carousel-control-prev-icon,
    .mobile-hero-carousel .carousel-control-next-icon {
      width: 16px;
      height: 16px;
    }
    
    .mobile-hero-carousel .carousel-indicators {
      margin-bottom: 10px;
    }
    
    .mobile-hero-carousel .carousel-indicators button {
      width: 8px;
      height: 8px;
      border-radius: 50%;
      margin: 0 4px;
    }
    
    /* Adjust search card for mobile - no negative margin */
    .search-card-container {
      margin-top: 20px;
      padding: 0 16px;
    }
  }
</style>

{{-- ===== MOBILE HERO CAROUSEL (separate from floating filter) ===== --}}
<div class="mobile-hero-section">
  @if(isset($activities) && $activities->count() > 0)
    <div id="mobileHeroCarousel" class="carousel slide mobile-hero-carousel" data-bs-ride="carousel" data-bs-interval="4000">
      <div class="carousel-inner">
        @foreach($activities as $index => $activity)
          <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
            @if($activity->image_url)
              <img src="{{ $activity->image_url }}" alt="{{ $activity->title }}">
            @else
              <div class="mobile-hero-fallback">
                <h2>{{ $activity->title }}</h2>
                <p>{{ Str::limit($activity->description, 80) }}</p>
              </div>
            @endif
          </div>
        @endforeach
      </div>
      @if($activities->count() > 1)
        <div class="carousel-indicators">
          @foreach($activities as $index => $activity)
            <button type="button" data-bs-target="#mobileHeroCarousel" data-bs-slide-to="{{ $index }}" class="{{ $index === 0 ? 'active' : '' }}"></button>
          @endforeach
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#mobileHeroCarousel" data-bs-slide="prev">
          <span class="carousel-control-prev-icon"></span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#mobileHeroCarousel" data-bs-slide="next">
          <span class="carousel-control-next-icon"></span>
        </button>
      @endif
    </div>
  @else
    <div class="mobile-hero-carousel">
      <div class="carousel-item active" style="height: 200px;">
        <div class="mobile-hero-fallback">
          <h2>Your Premium UTM Ride. Simplified.</h2>
          <p>Affordable, reliable, and verified vehicles for UTM students and staff.</p>
        </div>
      </div>
    </div>
  @endif
</div>

{{-- ===== DESKTOP HERO SECTION WITH ACTIVITY SLIDER ===== --}}
<div class="hero-section">
  @if(isset($activities) && $activities->count() > 0)
    <div id="heroCarousel" class="carousel slide hero-carousel" data-bs-ride="carousel" data-bs-interval="4000">
      <div class="carousel-inner h-100">
        @foreach($activities as $index => $activity)
          <div class="carousel-item h-100 {{ $index === 0 ? 'active' : '' }}">
            @if($activity->image_url)
              <img src="{{ $activity->image_url }}" class="hero-bg-image" alt="{{ $activity->title }}">
            @else
              <div class="container h-100 d-flex align-items-center">
                <div class="hero-content">
                  <h1 class="hero-title">{{ $activity->title }}</h1>
                  <p class="hero-subtitle">{{ $activity->description }}</p>
                  <div class="badge bg-primary px-3 py-2">
                    <i class="bi bi-calendar-event me-2"></i> Active: {{ $activity->start_date->format('d M') }} - {{ $activity->end_date->format('d M Y') }}
                  </div>
                </div>
              </div>
            @endif
          </div>
        @endforeach
      </div>
      @if($activities->count() > 1)
        <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev" style="z-index: 30; width: 5%;">
          <span class="carousel-control-prev-icon"></span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next" style="z-index: 30; width: 5%;">
          <span class="carousel-control-next-icon"></span>
        </button>
      @endif
    </div>
  @else
    <div class="container hero-content">
      <h1 class="hero-title">Your Premium UTM Ride.<br>Simplified.</h1>
      <p class="hero-subtitle">Affordable, reliable, and verified vehicles for UTM students and staff. Zero hidden fees.</p>
    </div>
  @endif
</div>

{{-- FLOATING SEARCH CARD --}}
<div class="search-card-container">
  <div class="search-card">
    <form action="{{ route('cars.index') }}" method="GET" class="row g-3">
      <div class="col-lg-3 col-md-6">
        <label class="form-label">Pickup Location</label>
        <div class="input-group">
          <span class="input-group-text bg-white border-end-0"><i class="bi bi-geo-alt text-muted"></i></span>
          <select name="location" class="form-select border-start-0">
            @if(isset($locations) && $locations->count() > 0)
              @foreach($locations as $loc)
                <option value="{{ $loc->name }}" {{ $loc->name === 'Student Mall' ? 'selected' : '' }}>{{ $loc->name }}</option>
              @endforeach
            @else
              <option value="Student Mall" selected>Student Mall</option>
            @endif
          </select>
        </div>
      </div>
      <div class="col-lg-2 col-md-3 col-6">
        <label class="form-label">Pickup Date</label>
        <input type="date" name="start_date" class="form-control" value="{{ date('Y-m-d') }}">
      </div>
      <div class="col-lg-2 col-md-3 col-6">
        <label class="form-label">Return Date</label>
        <input type="date" name="end_date" class="form-control" value="{{ date('Y-m-d', strtotime('+1 day')) }}">
      </div>
      <div class="col-lg-3 col-md-6">
        <label class="form-label">Car Type</label>
        <div class="input-group">
          <span class="input-group-text bg-white border-end-0"><i class="bi bi-funnel text-muted"></i></span>
          <select name="type" class="form-select border-start-0">
            <option value="">All Categories</option>
            <option value="hatchback">Hatchback (Budget)</option>
            <option value="sedan">Sedan (Comfort)</option>
            <option value="suv">SUV (Family)</option>
            <option value="van">Van (Group)</option>
          </select>
        </div>
      </div>
      <div class="col-lg-2 col-md-6 d-flex align-items-end">
        <button type="submit" class="btn-hasta-primary w-100"><i class="bi bi-search me-2"></i> Find Cars</button>
      </div>
    </form>
  </div>
</div>

<div class="container mt-5 pt-5">
  {{-- CAR GRID --}}
  <div class="d-flex justify-content-between align-items-end mb-4">
    <div>
      <h2 class="fw-bold mb-1">Recommended Vehicles</h2>
      <p class="text-muted small">Hand-picked deals for the UTM community.</p>
    </div>
    <a href="{{ route('cars.index') }}" class="text-decoration-none fw-bold" style="color: var(--hasta);">View All Fleet <i class="bi bi-arrow-right ms-1"></i></a>
  </div>

  <div class="row g-4 mb-5 pb-5">
    @forelse($cars->take(4) as $car)
      <div class="col-lg-3 col-md-6">
        <div class="car-card">
          <div class="car-img-container">
            @if($car->image_url)
              <img src="{{ $car->image_url }}" alt="{{ $car->brand }}">
            @else
              <i class="bi bi-image text-muted" style="font-size: 3rem;"></i>
            @endif
          </div>
          <div class="card-body p-4">
            <h6 class="fw-bold mb-1">{{ $car->brand }} {{ $car->model }}</h6>
            <div class="d-flex gap-3 mb-4 text-muted" style="font-size: 12px;">
              <span><i class="bi bi-people me-1"></i> 5 Seats</span>
              <span><i class="bi bi-gear me-1"></i> Auto</span>
            </div>
            <div class="d-flex justify-content-between align-items-center mt-auto">
              <div>
                <span class="h5 fw-bold mb-0">RM {{ number_format($car->base_rate_per_hour * 24, 0) }}</span>
                <span class="text-muted small">/day</span>
              </div>
              <a href="{{ route('cars.show', $car->id) }}" class="btn btn-outline-dark btn-sm rounded-pill px-3 fw-bold">Rent Now</a>
            </div>
          </div>
        </div>
      </div>
    @empty
      <div class="col-12 text-center py-5">
        <p class="text-muted">No cars available for these dates.</p>
      </div>
    @endforelse
  </div>
</div>

{{-- HOW IT WORKS --}}
<section id="how-it-works" class="py-5" style="background: #fafafa; border-top: 1px solid #eee;">
  <div class="container py-5">
    <div class="text-center mb-5">
      <h6 class="text-uppercase fw-bold text-primary small mb-2" style="letter-spacing: 2px;">The Process</h6>
      <h2 class="fw-bold">How to Rent Your Car</h2>
    </div>
    <div class="row g-4">
      <div class="col-md-4 text-center">
        <div class="bg-white shadow-sm rounded-circle d-inline-flex align-items-center justify-content-center mb-4" style="width: 80px; height: 80px;">
          <i class="bi bi-search h3 mb-0 text-primary"></i>
        </div>
        <h5 class="fw-bold">1. Select Car</h5>
        <p class="text-muted small px-lg-4">Choose from our verified fleet based on your needs and budget.</p>
      </div>
      <div class="col-md-4 text-center">
        <div class="bg-white shadow-sm rounded-circle d-inline-flex align-items-center justify-content-center mb-4" style="width: 80px; height: 80px;">
          <i class="bi bi-credit-card h3 mb-0 text-primary"></i>
        </div>
        <h5 class="fw-bold">2. Pay Deposit</h5>
        <p class="text-muted small px-lg-4">Secure your booking with a simple RM 50 deposit and receipt upload.</p>
      </div>
      <div class="col-md-4 text-center">
        <div class="bg-white shadow-sm rounded-circle d-inline-flex align-items-center justify-content-center mb-4" style="width: 80px; height: 80px;">
          <i class="bi bi-key h3 mb-0 text-primary"></i>
        </div>
        <h5 class="fw-bold">3. Pick Up & Go</h5>
        <p class="text-muted small px-lg-4">Meet us at Student Mall and drive away in minutes.</p>
      </div>
    </div>
  </div>
</section>

{{-- SUPPORT SECTION --}}
<section id="support" class="py-5" style="background: #fff; border-top: 1px solid #eee;">
  <div class="container py-5">
    <div class="text-center mb-5">
      <h6 class="text-uppercase fw-bold text-primary small mb-2" style="letter-spacing: 2px;">Report Car Issues</h6>
      <h2 class="fw-bold mb-3">Contact Our Support Team</h2>
      <p class="text-muted">Experiencing issues with your rental car? Report it here and we'll help you resolve it.</p>
    </div>
    
    <div class="row justify-content-center">
      <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
          <div class="card-body p-4 p-md-5">
            @if(session('success'))
              <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
              </div>
            @endif

            @if(session('error'))
              <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
              </div>
            @endif

            @if($errors->any())
              <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Please fix the following errors:</strong>
                <ul class="mb-0 mt-2">
                  @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                  @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
              </div>
            @endif

            @if(!session('auth_role') || session('auth_role') !== 'customer')
              <div class="alert alert-info mb-4">
                <i class="bi bi-info-circle me-2"></i>
                <strong>Please login</strong> to report car issues. Support is available for customers who have completed at least one booking.
              </div>
            @else
              @if(!isset($hasCompletedBooking) || !$hasCompletedBooking)
                <div class="alert alert-warning mb-4">
                  <i class="bi bi-exclamation-triangle me-2"></i>
                  <strong>Support access:</strong> Car issue reports can only be submitted after you have completed at least one booking with us.
                </div>
              @else
                <form method="POST" action="{{ route('support.submit') }}">
                  @csrf
                  
                  <div class="row g-3 mb-4">
                    <div class="col-md-6">
                      <label class="form-label fw-bold">Full Name <span class="text-danger">*</span></label>
                      <input type="text" name="name" class="form-control" value="{{ session('auth_name') ?? old('name') }}" required>
                    </div>
                    <div class="col-md-6">
                      <label class="form-label fw-bold">Email Address <span class="text-danger">*</span></label>
                      <input type="email" name="email" class="form-control" value="{{ $customerEmail ?? old('email') }}" required>
                    </div>
                    <div class="col-md-6">
                      <label class="form-label fw-bold">Phone Number</label>
                      <input type="tel" name="phone" class="form-control" value="{{ old('phone') }}" placeholder="Optional">
                    </div>
                    <div class="col-md-6">
                      <label class="form-label fw-bold">Car Issues <span class="text-danger">*</span></label>
                      <select name="category" class="form-select" required>
                        <option value="">Select car issue</option>
                        <option value="cleanliness" {{ old('category') === 'cleanliness' ? 'selected' : '' }}>Cleanliness</option>
                        <option value="lacking_facility" {{ old('category') === 'lacking_facility' ? 'selected' : '' }}>Lacking Facility</option>
                        <option value="bluetooth" {{ old('category') === 'bluetooth' ? 'selected' : '' }}>Bluetooth</option>
                        <option value="engine" {{ old('category') === 'engine' ? 'selected' : '' }}>Engine</option>
                        <option value="others" {{ old('category') === 'others' ? 'selected' : '' }}>Others</option>
                      </select>
                    </div>
                    <div class="col-12">
                      <label class="form-label fw-bold">Subject <span class="text-danger">*</span></label>
                      <input type="text" name="subject" class="form-control" value="{{ old('subject') }}" placeholder="Brief summary of your issue" required>
                    </div>
                    <div class="col-12">
                      <label class="form-label fw-bold">Description <span class="text-danger">*</span></label>
                      <textarea name="description" class="form-control" rows="6" placeholder="Please provide detailed information about your issue..." required>{{ old('description') }}</textarea>
                      <small class="text-muted">Please include as much detail as possible to help us assist you better.</small>
                    </div>
                  </div>

                  <div class="d-grid">
                    <button type="submit" class="btn btn-hasta btn-lg">
                      <i class="bi bi-send me-2"></i>Submit Support Request
                    </button>
                  </div>
                </form>
              @endif
            @endif
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

@endsection

@push('scripts')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css">
<script>
  // Smooth scroll to anchor on page load
  document.addEventListener('DOMContentLoaded', function() {
    if (window.location.hash) {
      const target = document.querySelector(window.location.hash);
      if (target) {
        setTimeout(function() {
          target.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }, 100);
      }
    }
  });
  
  // Smooth scroll for anchor links
  document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
      const href = this.getAttribute('href');
      if (href !== '#' && href.length > 1) {
        const target = document.querySelector(href);
        if (target) {
          e.preventDefault();
          target.scrollIntoView({ behavior: 'smooth', block: 'start' });
          // Update URL without triggering scroll
          if (history.pushState) {
            history.pushState(null, null, href);
          }
        }
      }
    });
  });
</script>
@endpush
