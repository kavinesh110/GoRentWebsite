@extends('layouts.app')
@section('title','Home - Hasta GoRent')

{{-- ===== HOMEPAGE ===== --}}
{{-- Displays: Secondary navigation, hero section, activities/promotions, car listings --}}
@section('content')

<style>
  .secondary-nav{
    background: #fff;
    padding: 16px 0;
    margin-bottom: 0;
    border-bottom: 1px solid #e9ecef;
  }
  .secondary-nav .nav-items{
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 40px;
    flex-wrap: wrap;
    max-width: 1400px;
    margin: 0 auto;
    padding: 0 24px;
  }
  .secondary-nav .nav-item-icon{
    display: flex;
    flex-direction: row;
    align-items: center;
    gap: 8px;
    text-decoration: none;
    color: #333;
    font-size: 14px;
    font-weight: 500;
    transition: all 0.2s;
    padding: 8px 12px;
    border-radius: 8px;
  }
  .secondary-nav .nav-item-icon:hover{
    color: var(--hasta);
    background: #f8f8f8;
  }
  .secondary-nav .nav-item-icon.active{
    color: var(--hasta);
    font-weight: 600;
  }
  .secondary-nav .icon-circle{
    width: 20px;
    height: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--hasta);
  }
  .secondary-nav .icon-circle svg{
    width: 20px;
    height: 20px;
  }

  .container-custom{
    max-width: 1400px;
    margin: 0 auto;
    padding: 0 24px;
  }

  .hero-gocar{ 
    position: relative; 
    overflow: visible; 
    padding-bottom: 80px;
    padding-top: 80px;
    margin-top: 20px;
    margin-bottom: 120px;
    background: linear-gradient(135deg, rgba(236, 89, 43, 1) 0%, rgba(236, 89, 43, 1) 50%, rgba(236, 89, 43, 1) 100%);
    border-radius: 26px;
    min-height: 460px;
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
  .hero-activity-image{
    margin-bottom: 16px;
    display: flex;
    justify-content: center;
  }
  .hero-activity-image img{
    max-height: 220px;
    width: auto;
    max-width: 100%;
    border-radius: 16px;
    box-shadow: 0 6px 18px rgba(0,0,0,0.25);
    object-fit: contain;
  }
  .hero-gocar .carousel-control-prev,
  .hero-gocar .carousel-control-next{
    width: 50px;
    height: 50px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 50%;
    top: 50%;
    transform: translateY(-50%);
    opacity: 0.8;
    transition: all 0.3s;
  }
  .hero-gocar .carousel-control-prev:hover,
  .hero-gocar .carousel-control-next:hover{
    background: rgba(255, 255, 255, 0.3);
    opacity: 1;
  }
  .hero-gocar .carousel-control-prev{
    left: 20px;
  }
  .hero-gocar .carousel-control-next{
    right: 20px;
  }
  .hero-gocar .carousel-control-prev-icon,
  .hero-gocar .carousel-control-next-icon{
    width: 24px;
    height: 24px;
    filter: brightness(0) invert(1);
  }
  .hero-searchbar{
    position: absolute;
    left: 50%;
    transform: translateX(-50%);
    bottom: -60px;
    background: #fff;
    max-width: 1150px;
    width: calc(100% - 48px);
    padding: 20px 24px;
    border-radius: 20px;
    box-shadow: 0 8px 32px rgba(0,0,0,0.15);
    display: flex;
    align-items: center;
    gap: 12px;
    z-index: 30;
  }
  .hero-searchbar .seg{ 
    flex: 1 1 0; 
    padding: 8px 16px; 
    display:flex; 
    flex-direction:column; 
    justify-content:center; 
    border-right: 1px solid #e9ecef;
    min-width: 0;
  }
  .hero-searchbar .seg.location-seg{
    flex: 1.5 1 0;
  }
  .hero-searchbar .seg:last-of-type{ border-right: none; }
  .hero-searchbar .seg .label{ 
    font-size: 12px; 
    color: #6c757d; 
    font-weight: 600;
    margin-bottom: 6px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
  }
  .hero-searchbar .seg .value{ 
    font-weight:600; 
    font-size:15px; 
    color:#111;
    border: none;
    padding: 0;
    background: transparent;
    width: 100%;
    outline: none;
  }
  /* Dropdown styling for location select in hero searchbar */
  .hero-searchbar .seg.location-seg select.value{
    background: #fff;
    border: none;
    padding: 8px;
    width: 100%;
    font-weight: 600;
    font-size: 15px;
    color: #333;
    cursor: pointer;
  }
  .hero-searchbar .seg .value::placeholder{
    color: #999;
    font-weight: 500;
  }
  .hero-searchbar .search-action{ 
    width:56px; 
    height:56px; 
    border-radius:50%; 
    background: var(--hasta-darker); 
    display:flex; 
    align-items:center; 
    justify-content:center; 
    color:#fff; 
    box-shadow:0 4px 16px rgba(138,36,36,0.4); 
    flex:0 0 56px;
    border: none;
    cursor: pointer;
    transition: all 0.2s;
  }
  .hero-searchbar .search-action:hover{
    background: var(--hasta-darker);
    transform: scale(1.08);
    box-shadow:0 6px 20px rgba(138,36,36,0.5);
  }
  .hero-searchbar .search-action svg{
    width: 22px;
    height: 22px;
  }

  .car-section-header{
    margin-bottom: 32px;
    margin-top: 40px;
  }
  .car-section-header h2{
    font-size: 32px;
    font-weight: 700;
    color: var(--hasta-darker);
    margin-bottom: 4px;
    letter-spacing: -0.5px;
  }
  
  .car-card{
    transition: all 0.3s ease;
    border: none !important;
    background: #fff;
    border-radius: 16px !important;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
  }
  .car-card:hover{
    transform: translateY(-4px);
    box-shadow: 0 8px 24px rgba(0,0,0,0.12) !important;
  }
  .car-card-header{
    padding: 16px 16px 12px 16px;
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    position: relative;
  }
  .car-card-name-year{
    font-size: 16px;
    font-weight: 600;
    color: #333;
    margin-bottom: 4px;
    line-height: 1.3;
  }
  .car-card-type{
    font-size: 13px;
    color: #7A7A7A;
    font-weight: 400;
  }
  .car-card-favorite{
    position: absolute;
    top: 16px;
    right: 16px;
    background: none;
    border: none;
    cursor: pointer;
    padding: 4px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s;
  }
  .car-card-favorite:hover{
    transform: scale(1.1);
  }
  .car-card-favorite svg{
    width: 20px;
    height: 20px;
  }
  .car-card-favorite.favorited svg{
    fill: #e74c3c;
    stroke: #e74c3c;
  }
  .car-card-favorite:not(.favorited) svg{
    fill: none;
    stroke: #ccc;
  }
  .car-card-image{
    height: 180px;
    background: #f8f8f8;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    position: relative;
    padding: 20px;
  }
  .car-card-image img{
    width: 100%;
    height: 100%;
    object-fit: contain;
    transition: transform 0.3s ease;
  }
  .car-card:hover .car-card-image img{
    transform: scale(1.05);
  }
  .car-card-footer{
    padding: 16px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-top: 1px solid #f0f0f0;
  }
  .car-card-price{
    display: flex;
    flex-direction: column;
  }
  .car-card-price-amount{
    font-size: 18px;
    font-weight: 700;
    color: #333;
    line-height: 1.2;
  }
  .car-card-price-period{
    font-size: 12px;
    color: #7A7A7A;
    font-weight: 400;
  }
  .car-card-btn{
    background: #F98820;
    color: #fff;
    border: none;
    border-radius: 8px;
    padding: 10px 20px;
    font-weight: 600;
    font-size: 14px;
    text-decoration: none;
    transition: all 0.2s;
    white-space: nowrap;
  }
  .car-card-btn:hover{
    background: #e67a1a;
    color: #fff;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(249, 136, 32, 0.3);
  }

  /* Tablet responsiveness for hero search bar & cards */
  @media (max-width: 1024px){
    .hero-gocar{
      padding-bottom: 70px;
      margin-bottom: 150px;
      min-height: 420px;
    }
    .hero-gocar .hero-inner{
      padding: 58px 40px 140px 40px;
    }
    .hero-searchbar{
      bottom: -120px;
      padding: 16px 18px;
      width: calc(100% - 40px);
      flex-wrap: wrap;
      align-items: stretch;
      gap: 8px 12px;
    }
    .hero-searchbar .seg{
      flex: 1 1 45%;
      border-right: none;
      border-bottom: 1px solid #e9ecef;
      padding: 10px 12px;
    }
    .hero-searchbar .seg.location-seg{
      flex: 1 1 100%;
      border-bottom: none;
    }
    .hero-searchbar .search-action{
      flex: 1 1 100%;
      width: 100%;
      height: 52px;
      border-radius: 14px;
    }
  }

  @media (max-width: 767px){
    .secondary-nav .nav-items{ gap: 16px; }
    .secondary-nav .nav-item-icon{ flex-direction: column; gap: 4px; }
    .hero-gocar{ 
      padding-bottom: 60px; 
      margin-bottom: 180px; 
      min-height: 420px;
    }
    .hero-gocar .hero-inner{
      padding: 58px 24px 140px 24px;
    }
    .hero-title{ font-size: 36px; }
    .hero-searchbar{ 
      flex-direction: column; 
      width: calc(100% - 32px); 
      bottom: -160px; 
      padding: 16px; 
    }
    .hero-searchbar .seg{ 
      width: 100%; 
      border-right: none; 
      border-bottom: 1px solid #e9ecef; 
      padding: 10px 12px;
    }
    .hero-searchbar .seg:last-of-type{ border-bottom: none; }
    .hero-searchbar .search-action{ width: 100%; border-radius: 12px; height: 48px; }
    .car-section-header h2{ font-size: 24px; }
    .car-card-footer{
      flex-direction: column;
      gap: 12px;
      align-items: flex-start;
    }
    .car-card-btn{
      width: 100%;
      text-align: center;
    }
  }

  /* Hasta GoRent @ UTM intro strip */
  .utm-intro{
    margin-top: 24px;
    margin-bottom: 32px;
    padding: 20px 24px;
    border-radius: 18px;
    background: #fff;
    box-shadow: 0 6px 18px rgba(0,0,0,0.06);
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
    align-items: center;
    justify-content: space-between;
  }
  .utm-intro-main{
    max-width: 520px;
  }
  .utm-intro-badge{
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 4px 10px;
    border-radius: 999px;
    background: rgba(203,55,55,0.08);
    color: var(--hasta-darker);
    font-size: 11px;
    font-weight: 700;
    letter-spacing: .08em;
    text-transform: uppercase;
    margin-bottom: 6px;
  }
  .utm-intro-title{
    font-size: 20px;
    font-weight: 700;
    margin-bottom: 6px;
    color: #222;
  }
  .utm-intro-text{
    font-size: 14px;
    color: #666;
    margin: 0;
  }
  .utm-intro-grid{
    display: grid;
    grid-template-columns: repeat(3,minmax(0,1fr));
    gap: 16px;
    min-width: 260px;
  }
  .utm-intro-pill{
    padding: 10px 12px;
    border-radius: 12px;
    background: #faf5f5;
    font-size: 12px;
    color: #444;
    display: flex;
    flex-direction: column;
    gap: 2px;
  }
  .utm-intro-pill span{
    font-weight: 600;
    color: var(--hasta-darker);
  }

  @media (max-width: 992px){
    .utm-intro{
      flex-direction: column;
      align-items: flex-start;
    }
    .utm-intro-grid{
      width: 100%;
      grid-template-columns: repeat(2,minmax(0,1fr));
    }
  }
  @media (max-width: 576px){
    .utm-intro-grid{
      grid-template-columns: minmax(0,1fr);
    }
  }
</style>

{{-- SECONDARY NAVIGATION --}}
<div class="secondary-nav">
  <div class="nav-items">
    <a href="{{ route('home') }}" class="nav-item-icon {{ request()->routeIs('home') ? 'active' : '' }}">
      <div class="icon-circle">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M5 17h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2z"/>
          <circle cx="7" cy="17" r="2"/>
          <circle cx="17" cy="17" r="2"/>
        </svg>
      </div>
      <span>Car Rental</span>
    </a>
    @if(session('auth_role') === 'customer')
      <a href="{{ route('customer.bookings') }}" class="nav-item-icon {{ request()->routeIs('customer.bookings*') ? 'active' : '' }}">
        <div class="icon-circle">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
            <circle cx="8.5" cy="7" r="4"></circle>
            <polyline points="17 11 19 13 23 9"></polyline>
          </svg>
        </div>
        <span>My Bookings</span>
      </a>
      <a href="{{ route('customer.loyalty-rewards') }}" class="nav-item-icon {{ request()->routeIs('customer.loyalty-rewards') ? 'active' : '' }}">
        <div class="icon-circle">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"></path>
            <line x1="7" y1="7" x2="7.01" y2="7"></line>
          </svg>
        </div>
        <span>Loyalty Rewards</span>
      </a>
    @endif
    <a href="#" class="nav-item-icon">
      <div class="icon-circle">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <circle cx="12" cy="12" r="10"></circle>
          <line x1="12" y1="16" x2="12" y2="12"></line>
          <line x1="12" y1="8" x2="12.01" y2="8"></line>
        </svg>
      </div>
      <span>How It Works</span>
    </a>
    <a href="#" class="nav-item-icon">
      <div class="icon-circle">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
        </svg>
      </div>
      <span>Contact Us</span>
    </a>
  </div>
</div>

<div class="container-custom">
  {{-- HERO SECTION --}}
  <div class="hero-gocar shadow-soft">
    <div class="hero-inner">
      <div class="hero-meta">
        @if(isset($activities) && $activities->count() > 0)
          {{-- Activity & Promotion Slider (cut slider) --}}
          <div id="heroActivityCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="2500">
            <div class="carousel-inner">
              @foreach($activities as $index => $activity)
                <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                  @if($activity->image_url)
                    <div class="hero-activity-image">
                      <img src="{{ $activity->image_url }}" alt="{{ $activity->title }}">
                    </div>
                  @endif
                  <h1 class="hero-title">
                    {{ $activity->title }}
                  </h1>
                  @if($activity->description)
                    <p class="hero-sub">
                      {{ \Illuminate\Support\Str::limit($activity->description, 160) }}
                    </p>
                  @endif
                  <p class="hero-sub" style="font-size: 14px; opacity: 0.9;">
                    {{ $activity->start_date->format('d M Y') }} – {{ $activity->end_date->format('d M Y') }}
                  </p>
                </div>
              @endforeach
            </div>
            @if($activities->count() > 1)
              <button class="carousel-control-prev" type="button" data-bs-target="#heroActivityCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Previous</span>
              </button>
              <button class="carousel-control-next" type="button" data-bs-target="#heroActivityCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Next</span>
              </button>
            @endif
          </div>
        @else
          {{-- Fallback static hero content when no activities --}}
          <h1 class="hero-title">Smart car rentals for UTM students & staff</h1>
          <p class="hero-sub">
            Centralised, deposit-first booking with loyalty rewards, maintenance tracking and campus-focused pricing for Hasta Travels & Tours.
          </p>
        @endif
      </div>
    </div>

    {{-- FLOATING SEARCH BAR --}}
    <form id="heroSearchForm" class="hero-searchbar" method="GET" action="/cars">
      <div class="seg">
        <label class="label" for="start_date">Start Date</label>
        <input id="start_date" name="start_date" type="date" class="value" value="2025-12-14">
      </div>
      <div class="seg">
        <label class="label" for="end_date">End Date</label>
        <input id="end_date" name="end_date" type="date" class="value" value="2025-12-14">
      </div>
      <div class="seg">
        <label class="label" for="start_time">Start Time</label>
        <input id="start_time" name="start_time" type="time" class="value" value="02:00">
      </div>
      <div class="seg">
        <label class="label" for="end_time">End Time</label>
        <input id="end_time" name="end_time" type="time" class="value" value="05:00">
      </div>
      <div class="seg location-seg">
        <label class="label" for="location">Location</label>
        <select id="location" name="location" class="value">
          @if(isset($locations) && $locations->count() > 0)
            @foreach($locations as $location)
              <option value="{{ $location->name }}" {{ $location->name === 'Student Mall' ? 'selected' : '' }}>
                {{ $location->name }}
              </option>
            @endforeach
          @else
            <option value="Student Mall" selected>Student Mall</option>
          @endif
        </select>
      </div>
      <button type="submit" class="search-action" title="Search" aria-label="Search">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
          <circle cx="11" cy="11" r="8"></circle>
          <path d="m21 21-4.35-4.35"></path>
        </svg>
      </button>
    </form>
  </div>

  

  {{-- UTM-FOCUSED SYSTEM INTRO --}}
  <div class="utm-intro">
    <div class="utm-intro-main">
      <div class="utm-intro-badge">Designed for UTM community</div>
      <h2 class="utm-intro-title">Centralised car rental system for Hasta Travels & Tours</h2>
      
    </div>
    <div class="utm-intro-grid">
      <div class="utm-intro-pill">
        <span>Booking & payments</span>
        Deposit-first flow, manual receipt upload and clear agreement signing for every rental.
      </div>
      <div class="utm-intro-pill">
        <span>Fleet & maintenance</span>
        Real-time car status, mileage-based service alerts and before / after inspection photos.
      </div>
      <div class="utm-intro-pill">
        <span>Loyalty & analytics</span>
        Stamps for every 9 rental hours, vouchers for frequent renters and insights by college.
      </div>
    </div>
  </div>

  {{-- ===== CAR LISTINGS SECTION ===== --}}
  {{-- Displays all available cars in a grid layout --}}
  {{-- Each car card shows: image, name, type, price, and "Rent Now" button --}}
  <div class="car-section-header">
    <h2>Car rental deals found in Student Mall</h2>
  </div>

  <div class="row g-4 mb-5">
    @forelse($cars as $car)
      @php
        // Get car name and year from new schema
        $brand = trim($car->brand ?? '');
        $model = trim($car->model ?? '');
        $carName = $brand . ($brand && $model ? ' ' : '') . $model;
        if (empty($carName)) {
            $carName = 'Car #' . $car->id; // Fallback if brand/model are empty
        }
        $year = $car->year ?? null;
        
        // Determine car type (default to Hatchback if not specified)
        $carType = 'Hatchback';
        $modelLower = strtolower($car->model ?? '');
        if(strpos($modelLower, 'suv') !== false || strpos($modelLower, 'aruz') !== false) {
          $carType = 'SUV';
        } elseif(strpos($modelLower, 'sedan') !== false || strpos($modelLower, 'bezza') !== false) {
          $carType = 'Sedan';
        } elseif(strpos($modelLower, 'van') !== false || strpos($modelLower, 'starex') !== false || strpos($modelLower, 'vellfire') !== false || strpos($modelLower, 'alphard') !== false) {
          $carType = 'Van';
        }
        
        // Calculate price per day (base_rate_per_hour * 24)
        $pricePerDay = ($car->base_rate_per_hour ?? 0) * 24;
      @endphp
      <div class="col-md-6 col-lg-4 col-xl-3">
        <div class="card h-100 car-card" data-name="{{ strtolower($carName) }}" data-status="available">
          <div class="car-card-header">
            <div>
              <div class="car-card-name-year">
                {{ trim($carName) }}@if($year) ({{ $year }})@endif
              </div>
              <div class="car-card-type">{{ $carType }}</div>
            </div>
            <button class="car-card-favorite" type="button" onclick="toggleFavorite(this)" aria-label="Add to favorites">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
              </svg>
            </button>
          </div>
          <div class="car-card-image">
            @if($car->image_url)
              <img src="{{ $car->image_url }}" alt="{{ trim($carName) }}">
            @else
              <svg width="120" height="80" viewBox="0 0 120 80" fill="none">
                <rect x="10" y="30" width="100" height="40" rx="8" fill="#ddd"/>
                <circle cx="30" cy="65" r="8" fill="#999"/>
                <circle cx="90" cy="65" r="8" fill="#999"/>
                <path d="M30 30 L50 15 L70 15 L90 30" stroke="#bbb" stroke-width="3" fill="none"/>
              </svg>
            @endif
          </div>
          <div class="car-card-footer">
            <div class="car-card-price">
              <span class="car-card-price-amount">RM {{ number_format($pricePerDay, 0) }}</span>
              <span class="car-card-price-period">/ day</span>
            </div>
            <a href="{{ route('cars.show', $car->id) }}" class="car-card-btn">Rent Now</a>
          </div>
        </div>
      </div>
    @empty
      <div class="col-12">
        <div class="text-center py-5">
          <p class="text-muted">No cars available at the moment. Please check back later.</p>
        </div>
      </div>
    @endforelse
  </div>
</div>

@endsection

@push('scripts')
<script>
  (function(){
    const form = document.getElementById('heroSearchForm');
    if(!form) return;

    const locationField = form.querySelector('[name="location"]');

    function normalize(s){ return (s||'').toString().trim().toLowerCase(); }

    form.addEventListener('submit', function(e){
      e.preventDefault();

      const qLocation = normalize(locationField ? locationField.value : '');
      const qName = qLocation;

      const cards = document.querySelectorAll('.car-card');
      if (cards.length === 0) {
        console.warn('No car cards found on page');
        return;
      }
      
      cards.forEach(card => {
        const name = card.getAttribute('data-name') || '';
        const status = card.getAttribute('data-status') || '';

        // If no search query, show all cards
        const matches = (!qName) || name.indexOf(qName) !== -1;

        card.style.display = matches ? '' : 'none';
      });
    });
  })();

  function toggleFavorite(button) {
    button.classList.toggle('favorited');
  }

</script>
@endpush