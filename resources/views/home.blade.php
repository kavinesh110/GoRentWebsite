@extends('layouts.app')
@section('title','Home - Hasta GoRent')

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
    margin-bottom: 120px;
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

  @media (max-width: 767px){
    .secondary-nav .nav-items{ gap: 16px; }
    .secondary-nav .nav-item-icon{ flex-direction: column; gap: 4px; }
    .hero-gocar{ padding-bottom: 60px; margin-bottom: 180px; }
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
</style>

{{-- SECONDARY NAVIGATION --}}
<div class="secondary-nav">
  <div class="nav-items">
    <a href="/cars" class="nav-item-icon active">
      <div class="icon-circle">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M5 17h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2z"/>
          <circle cx="7" cy="17" r="2"/>
          <circle cx="17" cy="17" r="2"/>
        </svg>
      </div>
      <span>Car Rental</span>
    </a>
    <a href="#" class="nav-item-icon">
      <div class="icon-circle">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/>
        </svg>
      </div>
      <span>Car Service & Repair</span>
    </a>
    <a href="#" class="nav-item-icon">
      <div class="icon-circle">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <line x1="12" y1="5" x2="12" y2="19"></line>
          <line x1="5" y1="12" x2="19" y2="12"></line>
        </svg>
      </div>
      <span>Subscription</span>
    </a>
    <a href="#" class="nav-item-icon">
      <div class="icon-circle">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon>
        </svg>
      </div>
      <span>EV Hub</span>
    </a>
    <a href="#" class="nav-item-icon">
      <div class="icon-circle">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
        </svg>
      </div>
      <span>Insurance & Roadtax</span>
    </a>
    <a href="#" class="nav-item-icon">
      <div class="icon-circle">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M20.59 13.41l-7.17 7.17a2 2 0 01-2.83 0L2 12V2h10l8.59 8.59a2 2 0 010 2.82z"></path>
          <line x1="7" y1="7" x2="7.01" y2="7"></line>
        </svg>
      </div>
      <span>Cuba Beli</span>
    </a>
  </div>
</div>

<div class="container-custom">
  {{-- HERO SECTION --}}
  <div class="hero-gocar shadow-soft">
    <div class="hero-inner">
      <div class="hero-meta">
        <h1 class="hero-title">Year-End Sale!</h1>
        <p class="hero-sub">
          Get up to 50% off on all car rentals this December. Limited time offer!
        </p>
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
        <input id="location" name="location" type="text" class="value" placeholder="Johor Bahru, Malaysia" value="Johor Bahru, Malaysia">
      </div>
      <button type="submit" class="search-action" title="Search" aria-label="Search">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
          <circle cx="11" cy="11" r="8"></circle>
          <path d="m21 21-4.35-4.35"></path>
        </svg>
      </button>
    </form>
  </div>

  {{-- SECTION: CAR LISTINGS --}}
  <div class="car-section-header">
    <h2>Car rental deals found in Johor Bahru</h2>
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

    function normalize(s){ return (s||'').toString().trim().toLowerCase(); }

    form.addEventListener('submit', function(e){
      e.preventDefault();

      const qLocation = normalize(form.location.value);
      const qName = normalize(form.location.value);

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