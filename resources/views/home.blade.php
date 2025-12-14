@extends('layouts.app')
@section('title','Home - Hasta GoRent')

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

  .promo-carousel-section{
    margin-bottom: 60px;
  }
  .promo-carousel{
    position: relative;
    overflow: hidden;
    border-radius: 20px;
    box-shadow: 0 8px 24px rgba(0,0,0,0.08);
  }
  .promo-slides{
    display: flex;
    transition: transform 0.6s cubic-bezier(0.4, 0, 0.2, 1);
  }
  .promo-slide{
    min-width: 100%;
    height: 380px;
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 60px;
  }
  .promo-slide:nth-child(2){
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
  }
  .promo-slide:nth-child(3){
    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
  }
  .promo-slide:nth-child(4){
    background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
  }
  .promo-content{
    max-width: 600px;
    text-align: center;
    z-index: 2;
  }
  .promo-content h2{
    font-size: 48px;
    font-weight: 800;
    margin-bottom: 16px;
    letter-spacing: -1px;
  }
  .promo-content p{
    font-size: 18px;
    margin-bottom: 24px;
    opacity: 0.95;
  }
  .promo-btn{
    display: inline-block;
    padding: 14px 32px;
    background: rgba(255,255,255,0.25);
    backdrop-filter: blur(10px);
    border: 2px solid rgba(255,255,255,0.5);
    border-radius: 50px;
    color: white;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.3s;
  }
  .promo-btn:hover{
    background: rgba(255,255,255,0.35);
    transform: translateY(-2px);
    color: white;
  }
  .promo-controls{
    position: absolute;
    bottom: 24px;
    left: 50%;
    transform: translateX(-50%);
    display: flex;
    gap: 8px;
    z-index: 10;
  }
  .promo-dot{
    width: 10px;
    height: 10px;
    border-radius: 50%;
    background: rgba(255,255,255,0.4);
    cursor: pointer;
    transition: all 0.3s;
  }
  .promo-dot.active{
    width: 32px;
    border-radius: 5px;
    background: rgba(255,255,255,0.9);
  }
  .promo-nav{
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    background: rgba(255,255,255,0.2);
    backdrop-filter: blur(10px);
    border: none;
    width: 48px;
    height: 48px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    z-index: 10;
    transition: all 0.3s;
  }
  .promo-nav:hover{
    background: rgba(255,255,255,0.35);
    transform: translateY(-50%) scale(1.1);
  }
  .promo-nav.prev{ left: 20px; }
  .promo-nav.next{ right: 20px; }

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
    .promo-slide{
      height: 300px;
      padding: 40px 24px;
    }
    .promo-content h2{
      font-size: 32px;
    }
    .promo-content p{
      font-size: 16px;
    }
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
          Need a car for your next getaway or a quick trip in Johor Bahru? Hasta GoRent has everything you need to rent a car for your Johor Bahru adventure trip.
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

  {{-- PROMOTIONS CAROUSEL SECTION --}}
  <div class="promo-carousel-section">
    <div class="promo-carousel">
      <div class="promo-slides" id="promoSlides">
        <div class="promo-slide">
          <div class="promo-content">
            <h2>Year-End Sale!</h2>
            <p>Get up to 50% off on all car rentals this December. Limited time offer!</p>
            <a href="#" class="promo-btn">Book Now</a>
          </div>
        </div>
        <div class="promo-slide">
          <div class="promo-content">
            <h2>New Member Discount</h2>
            <p>Sign up today and enjoy RM50 off your first rental. Start your journey with us!</p>
            <a href="#" class="promo-btn">Join Today</a>
          </div>
        </div>
        <div class="promo-slide">
          <div class="promo-content">
            <h2>Weekend Special</h2>
            <p>Rent for the weekend and get Monday free! Perfect for extended trips.</p>
            <a href="#" class="promo-btn">Learn More</a>
          </div>
        </div>
        <div class="promo-slide">
          <div class="promo-content">
            <h2>Corporate Packages</h2>
            <p>Special rates for business travelers. Flexible terms and premium vehicles.</p>
            <a href="#" class="promo-btn">Contact Us</a>
          </div>
        </div>
      </div>
      
      <button class="promo-nav prev" onclick="promoCarousel.prev()">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
          <path d="M15 18l-6-6 6-6"/>
        </svg>
      </button>
      <button class="promo-nav next" onclick="promoCarousel.next()">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
          <path d="M9 18l6-6-6-6"/>
        </svg>
      </button>

      <div class="promo-controls" id="promoControls"></div>
    </div>
  </div>

  {{-- SECTION: CAR LISTINGS --}}
  <div class="car-section-header">
    <h2>Car rental deals found in Johor Bahru</h2>
  </div>

  @php
    $cars = [
      ['name'=>'Perodua Axia 2018','rate'=>'RM120/day','img'=>'axia','status'=>'Available'],
      ['name'=>'Perodua Myvi 2015','rate'=>'RM120/day','img'=>'myvi','status'=>'Available'],
      ['name'=>'Perodua Myvi 2020','rate'=>'RM150/day','img'=>'myvi','status'=>'Available'],
      ['name'=>'Perodua Bezza 2018','rate'=>'RM140/day','img'=>'bezza','status'=>'Available'],
      ['name'=>'Perodua Axia 2024','rate'=>'RM130/day','img'=>'axia','status'=>'Available'],
      ['name'=>'Perodua Bezza 2024','rate'=>'RM140/day','img'=>'bezza','status'=>'Available'],
      ['name'=>'Proton Saga 2017','rate'=>'RM120/day','img'=>'saga','status'=>'Available'],
      ['name'=>'Perodua Alza 2019','rate'=>'RM200/day','img'=>'alza','status'=>'Available'],
      ['name'=>'Perodua Aruz 2020','rate'=>'RM180/day','img'=>'aruz','status'=>'Available'],
      ['name'=>'Toyota Vellfire 2020','rate'=>'RM500/day','img'=>'vellfire','status'=>'Available'],
      ['name'=>'Toyota Alphard 2020','rate'=>'RM500/day','img'=>'alphard','status'=>'Available'],
      ['name'=>'Hyundai Starex 2020','rate'=>'RM650/day','img'=>'starex','status'=>'Available'],
      ['name'=>'Hyundai Staria 2020','rate'=>'RM550/day','img'=>'staria','status'=>'Available'],
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
            <a href="{{ route('bookings.create', ['car' => $c['name']]) }}" class="btn btn-hasta w-100" style="border-radius: 8px; padding: 10px; font-weight: 600;">Book Now</a>
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

  const promoCarousel = (function(){
    const slides = document.getElementById('promoSlides');
    const controls = document.getElementById('promoControls');
    const slideCount = slides.children.length;
    let currentIndex = 0;
    let autoPlayInterval;

    for(let i = 0; i < slideCount; i++){
      const dot = document.createElement('div');
      dot.className = 'promo-dot' + (i === 0 ? ' active' : '');
      dot.onclick = () => goToSlide(i);
      controls.appendChild(dot);
    }

    function updateSlide(){
      slides.style.transform = `translateX(-${currentIndex * 100}%)`;
      
      const dots = controls.children;
      for(let i = 0; i < dots.length; i++){
        dots[i].classList.toggle('active', i === currentIndex);
      }
    }

    function goToSlide(index){
      currentIndex = index;
      updateSlide();
      resetAutoPlay();
    }

    function next(){
      currentIndex = (currentIndex + 1) % slideCount;
      updateSlide();
      resetAutoPlay();
    }

    function prev(){
      currentIndex = (currentIndex - 1 + slideCount) % slideCount;
      updateSlide();
      resetAutoPlay();
    }

    function startAutoPlay(){
      autoPlayInterval = setInterval(next, 5000);
    }

    function resetAutoPlay(){
      clearInterval(autoPlayInterval);
      startAutoPlay();
    }

    startAutoPlay();

    return { next, prev, goToSlide };
  })();
</script>
@endpush