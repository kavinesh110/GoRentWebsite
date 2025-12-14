@extends('layouts.app')
@section('title','Home - Hasta Travel')

@section('content')

<style>
  .secondary-nav{
    background: #fff;
    padding: 16px 0;
    margin-bottom: 24px;
    border-bottom: 1px solid #e9ecef;
  }
  .secondary-nav .nav-items{
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 32px;
    flex-wrap: wrap;
  }
  .secondary-nav .nav-item-icon{
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 6px;
    text-decoration: none;
    color: #333;
    font-size: 14px;
    font-weight: 500;
    transition: all 0.2s;
  }
  .secondary-nav .nav-item-icon:hover{
    color: var(--hasta);
  }
  .secondary-nav .icon-circle{
    width: 42px;
    height: 42px;
    border-radius: 50%;
    background: #f0f0f0;
    display: flex;
    align-items: center;
    justify-content: center;
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
    padding: 16px 20px;
    border-radius: 16px;
    box-shadow: 0 8px 24px rgba(0,0,0,0.12);
    display: flex;
    align-items: center;
    gap: 8px;
    z-index: 30;
  }
  .hero-searchbar .seg{ 
    flex: 1 1 0; 
    padding: 12px 16px; 
    display:flex; 
    flex-direction:column; 
    justify-content:center; 
    border-right: 1px solid #e9ecef;
  }
  .hero-searchbar .seg:last-of-type{ border-right: none; }
  .hero-searchbar .seg .label{ 
    font-size: 12px; 
    color: #6c757d; 
    font-weight: 600;
    margin-bottom: 4px;
  }
  .hero-searchbar .seg .value{ 
    font-weight:600; 
    font-size:14px; 
    color:#111;
    border: none;
    padding: 0;
    background: transparent;
    width: 100%;
  }
  .hero-searchbar .search-action{ 
    width:48px; 
    height:48px; 
    border-radius:50%; 
    background: var(--hasta); 
    display:flex; 
    align-items:center; 
    justify-content:center; 
    color:#fff; 
    box-shadow:0 4px 12px rgba(203,55,55,0.3); 
    flex:0 0 48px;
    border: none;
    cursor: pointer;
    transition: all 0.2s;
  }
  .hero-searchbar .search-action:hover{
    background: var(--hasta-dark);
    transform: scale(1.05);
  }

  .car-section-header{
    margin-bottom: 24px;
  }
  .car-section-header h2{
    font-size: 24px;
    font-weight: 700;
    color: #111;
    margin-bottom: 4px;
  }

  @media (max-width: 767px){
    .secondary-nav .nav-items{ gap: 16px; }
    .hero-gocar{ padding-bottom: 60px; margin-bottom: 180px; }
    .hero-searchbar{ 
      flex-direction: column; 
      width: calc(100% - 32px); 
      bottom: -160px; 
      padding: 14px; 
    }
    .hero-searchbar .seg{ 
      width: 100%; 
      border-right: none; 
      border-bottom: 1px solid #e9ecef; 
      padding: 10px 12px;
    }
    .hero-searchbar .seg:last-of-type{ border-bottom: none; }
    .hero-searchbar .search-action{ width: 100%; border-radius: 8px; }
  }
</style>

{{-- SECONDARY NAVIGATION --}}
<div class="secondary-nav">
  <div class="nav-items">
    <a href="/cars" class="nav-item-icon">
      <div class="icon-circle">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M5 17h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2z"/>
          <circle cx="7" cy="17" r="2"/>
          <circle cx="17" cy="17" r="2"/>
        </svg>
      </div>
      <span>Car Rental</span>
    </a>
    <a href="#" class="nav-item-icon">
      <div class="icon-circle">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M12 20V10M6 20V4m12 16V6"/>
        </svg>
      </div>
      <span>Car Service</span>
    </a>
    <a href="#" class="nav-item-icon">
      <div class="icon-circle">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/>
        </svg>
      </div>
      <span>Subscription</span>
    </a>
    <a href="#" class="nav-item-icon">
      <div class="icon-circle">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/>
        </svg>
      </div>
      <span>EV Hub</span>
    </a>
    <a href="#" class="nav-item-icon">
      <div class="icon-circle">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
        </svg>
      </div>
      <span>Insurance</span>
    </a>
    <a href="#" class="nav-item-icon">
      <div class="icon-circle">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <circle cx="12" cy="12" r="10"/>
          <path d="M12 6v6l4 2"/>
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
        <h1 class="hero-title text-white">Rent a car in Johor Bahru</h1>
        <p class="hero-sub">
          Need a car for your next getaway or a quick trip in Johor Bahru? GoCar Sharing has everything you need to rent a car for your Johor Bahru adventure trip.
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
      <div class="seg">
        <label class="label" for="location">Location</label>
        <input id="location" name="location" type="text" class="value" placeholder="Johor Bahru, Malaysia" value="Johor Bahru, Malaysia">
      </div>
      <button type="submit" class="search-action" title="Search" aria-label="Search">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M21 21l-4.35-4.35" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
          <circle cx="11" cy="11" r="6" stroke="currentColor" stroke-width="2.5"/>
        </svg>
      </button>
    </form>
  </div>

  {{-- SECTION: CAR LISTINGS --}}
  <div class="car-section-header">
    <h2>Car rental deals found in Johor Bahru</h2>
  </div>

  @php
    $cars = [
      ['name'=>'Nissan KICKS e-POWER','rate'=>'RM163 / day','img'=>'kicks','status'=>'Available'],
      ['name'=>'Honda City','rate'=>'RM163 / day','img'=>'city','status'=>'Available'],
      ['name'=>'Renault Captur FL','rate'=>'RM159 / day','img'=>'captur','status'=>'Available'],
      ['name'=>'Renault Captur FL','rate'=>'RM159 / day','img'=>'captur2','status'=>'Available'],
      ['name'=>'Renault Koleos','rate'=>'RM189 / day','img'=>'koleos','status'=>'Available'],
    ];
  @endphp

  <div class="row g-4 mb-5">
    @foreach($cars as $c)
      <div class="col-md-6 col-lg-3">
        <div class="card shadow-soft h-100 car-card" data-name="{{ strtolower($c['name']) }}" data-status="{{ strtolower($c['status']) }}" style="border-radius: 16px; border: none; overflow: hidden;">
          <div style="height: 200px; background: #f5f5f5; display: flex; align-items: center; justify-content: center; overflow: hidden;">
            <svg width="120" height="80" viewBox="0 0 120 80" fill="none">
              <rect x="10" y="30" width="100" height="40" rx="8" fill="#ddd"/>
              <circle cx="30" cy="65" r="8" fill="#999"/>
              <circle cx="90" cy="65" r="8" fill="#999"/>
              <path d="M30 30 L50 15 L70 15 L90 30" stroke="#bbb" stroke-width="3" fill="none"/>
            </svg>
          </div>
          <div class="card-body" style="padding: 16px;">
            <h5 class="card-title" style="font-size: 16px; font-weight: 700; margin-bottom: 8px; color: #111;">{{ $c['name'] }}</h5>
            <p class="card-text" style="font-size: 14px; font-weight: 600; color: #111; margin-bottom: 12px;">
              starting from <strong>{{ $c['rate'] }}</strong>
            </p>
            <a href="/bookings/create" class="btn btn-hasta w-100" style="border-radius: 8px; padding: 10px; font-weight: 600;">Book Now</a>
          </div>
        </div>
      </div>
    @endforeach
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
      cards.forEach(card => {
        const name = card.getAttribute('data-name') || '';
        const status = card.getAttribute('data-status') || '';

        const matches = (!qName) || name.indexOf(qName) !== -1;

        card.style.display = matches ? '' : 'none';
      });
    });
  })();
</script>
@endpush