@extends('layouts.app')
@section('title','Home - Hasta Travel')

@section('content')

<style>
  .hero-gocar{ position: relative; overflow: visible; padding-bottom: 86px; }
  .hero-searchbar{
    position: absolute;
    left: 50%;
    transform: translateX(-50%);
    bottom: 12px;
    background: #fff;
    max-width: 1100px;
    width: calc(100% - 160px);
    padding: 12px 16px;
    border-radius: 48px;
    box-shadow: 0 10px 30px rgba(22,40,32,0.08);
    display: flex;
    align-items: center;
    gap: 6px;
    z-index: 30;
  }
  .hero-searchbar .seg{ flex: 1 1 0; padding: 8px 14px; display:flex; flex-direction:column; justify-content:center; }
  .hero-searchbar .seg .label{ font-size: 12px; color: #7a7a7a; }
  .hero-searchbar .seg .value{ font-weight:700; margin-top:4px; font-size:14px; color:#0b3b2f; }
  .hero-searchbar .seg-divider{ height:36px; width:1px; background:#eef0ee; margin:0 6px; }
  .hero-searchbar .search-action{ width:64px; height:64px; border-radius:50%; background:#10a37f; display:flex; align-items:center; justify-content:center; color:#fff; box-shadow:0 6px 18px rgba(16,163,127,0.18); flex:0 0 64px; }
  .hero-searchbar .search-action svg{ filter: drop-shadow(0 1px 0 rgba(255,255,255,0.06)); }

  /* Responsive: stack segments and make action smaller */
  @media (max-width: 767px){
    .hero-searchbar{ flex-wrap:wrap; width:calc(100% - 32px); left:50%; transform:translateX(-50%); bottom: -10px; padding:10px; }
    .hero-searchbar .seg{ flex: 1 1 45%; min-width: 140px; padding:8px 10px; }
    .hero-searchbar .seg-divider{ display:none; }
    .hero-searchbar .search-action{ width:48px; height:48px; flex:0 0 48px; align-self:flex-end; }
  }
</style>

{{-- HERO (GoCar-like) --}}
<div class="hero-gocar shadow-soft mb-5">
  <div class="hero-inner">
    <div class="hero-meta">
      <div class="small text-white-50">Hasta Travel</div>
      <div class="hero-title text-white">Rent a car with confidence</div>
      <div class="hero-sub">
        UTM student/staff booking • Manual receipt upload • Deposit RM50
      </div>
    </div>

    <div class="mt-4">
      <a href="/bookings/create" class="btn btn-hasta rounded-pill px-4 py-2">Book Now</a>
    </div>
  </div>

  {{-- FLOATING SEARCH / FILTER BAR (GoCar-style) --}}
  <form id="heroSearchForm" class="hero-searchbar" method="GET" action="/cars">
    <div class="seg">
      <label class="label" for="start_date">Start Date</label>
      <input id="start_date" name="start_date" type="date" class="form-control value" value="2025-12-14">
    </div>
    <div class="seg">
      <label class="label" for="end_date">End Date</label>
      <input id="end_date" name="end_date" type="date" class="form-control value" value="2025-12-14">
    </div>
    <div class="seg">
      <label class="label" for="start_time">Start Time</label>
      <input id="start_time" name="start_time" type="time" class="form-control value" value="02:00">
    </div>
    <div class="seg">
      <label class="label" for="end_time">End Time</label>
      <input id="end_time" name="end_time" type="time" class="form-control value" value="05:00">
    </div>
    <div class="seg">
      <label class="label" for="location">Location</label>
      <input id="location" name="location" type="text" class="form-control value" placeholder="Johor Bahru, Malaysia" value="Johor Bahru, Malaysia">
    </div>
    <div class="seg-divider"></div>
    <button type="submit" class="search-action" title="Search" aria-label="Search">
      <svg width="22" height="22" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M21 21l-4.35-4.35" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        <circle cx="11" cy="11" r="6" stroke="currentColor" stroke-width="2"/>
      </svg>
    </button>
  </form>

  {{-- FLOATING PROMO/ACTIVITY BAR (slider) --}}
  <div class="hero-floatbar">
    <div id="activityPromoCarousel" class="carousel slide" data-bs-ride="carousel">
      <div class="carousel-inner">

        <div class="carousel-item active">
          <div class="d-flex align-items-center justify-content-between gap-3">
            <div class="pill flex-grow-1">
              <div class="small text-muted">Promotion</div>
              <div class="fw-semibold">Weekday Promo (Mon–Thu) — auto price applied</div>
              <div class="small text-muted">Promo will be shown on website when applied.</div>
            </div>
            <a class="btn btn-hasta rounded-pill px-3" href="/cars">View Cars</a>
          </div>
        </div>

        <div class="carousel-item">
          <div class="d-flex align-items-center justify-content-between gap-3">
            <div class="pill flex-grow-1">
              <div class="small text-muted">Activity</div>
              <div class="fw-semibold">Latest Hasta activity & announcements</div>
              <div class="small text-muted">Calendar-style display (UI placeholder).</div>
            </div>
            <a class="btn btn-hasta rounded-pill px-3" href="#">View Update</a>
          </div>
        </div>

        <div class="carousel-item">
          <div class="d-flex align-items-center justify-content-between gap-3">
            <div class="pill flex-grow-1">
              <div class="small text-muted">Policy</div>
              <div class="fw-semibold">Deposit RM50 — refund within 10 working days (if selected)</div>
              <div class="small text-muted">Or carry forward deposit to the next booking.</div>
            </div>
            <a class="btn btn-hasta rounded-pill px-3" href="/bookings">My Bookings</a>
          </div>
        </div>

      </div>

      <button class="carousel-control-prev" type="button" data-bs-target="#activityPromoCarousel" data-bs-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Previous</span>
      </button>
      <button class="carousel-control-next" type="button" data-bs-target="#activityPromoCarousel" data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Next</span>
      </button>
    </div>
  </div>
</div>

{{-- SECTION: AVAILABLE CARS --}}
<div class="d-flex align-items-end justify-content-between mb-2">
  <div>
    <h2 class="h4 fw-bold mb-0">Car rental deals</h2>
    <div class="text-muted">Preview listing (dummy data for now)</div>
  </div>
  <a class="btn btn-outline-dark btn-sm rounded-pill" href="/cars">View all</a>
</div>

@php
  $cars = [
    ['name'=>'Perodua Myvi','rate'=>'RM12/hour','fuel'=>'Petrol','status'=>'Available'],
    ['name'=>'Honda City','rate'=>'RM18/hour','fuel'=>'Petrol','status'=>'Available'],
    ['name'=>'Proton X50','rate'=>'RM20/hour','fuel'=>'Petrol','status'=>'Maintenance'],
    ['name'=>'Toyota Vios','rate'=>'RM17/hour','fuel'=>'Petrol','status'=>'Available'],
  ];
@endphp

<div class="row g-3">
  @foreach($cars as $c)
      <div class="col-md-3">
        <div class="card shadow-soft rounded-xxl p-3 h-100 car-card" data-name="{{ strtolower($c['name']) }}" data-status="{{ strtolower($c['status']) }}">
        <div class="d-flex justify-content-between align-items-start">
          <div class="fw-semibold">{{ $c['name'] }}</div>
          <span class="badge {{ $c['status']=='Available' ? 'text-bg-success' : 'text-bg-secondary' }}">
            {{ $c['status'] }}
          </span>
        </div>

        <div class="text-muted small mt-1">{{ $c['fuel'] }}</div>

        <div class="mt-3 fw-bold text-hasta">{{ $c['rate'] }}</div>

        <div class="mt-3">
          <a href="/bookings/create" class="btn btn-hasta btn-sm rounded-pill w-100">Book</a>
        </div>
      </div>
    </div>
  @endforeach
</div>

@endsection

@push('scripts')
<script>
  (function(){
    // Client-side filtering for the demo view
    const form = document.getElementById('heroSearchForm');
    if(!form) return;

    function normalize(s){ return (s||'').toString().trim().toLowerCase(); }

    form.addEventListener('submit', function(e){
      // Prevent full page navigation for quick demo filtering
      e.preventDefault();

      const qLocation = normalize(form.location.value);
      const qName = normalize(form.location.value); // using location as a simple search box for names in this demo

      const cards = document.querySelectorAll('.car-card');
      cards.forEach(card => {
        const name = card.getAttribute('data-name') || '';
        const status = card.getAttribute('data-status') || '';

        // Basic matching: show card if name contains the search term OR if location is empty
        const matches = (!qName) || name.indexOf(qName) !== -1;

        card.style.display = matches ? '' : 'none';
      });
    });
  })();
</script>
@endpush
