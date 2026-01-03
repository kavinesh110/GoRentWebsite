@extends('layouts.app')

@section('title', 'Book Car - Hasta GoRent')

@section('content')

<style>
  :root {
    --hasta-red: #cb3737;
    --hasta-dark: #a92c2c;
    --bg-light: #f8f9fa;
    --text-main: #2d3436;
    --text-muted: #636e72;
  }

  .booking-hero {
    background: linear-gradient(135deg, #1e272e 0%, #000 100%);
    padding: 60px 0 100px 0;
    color: #fff;
    position: relative;
    overflow: hidden;
  }
  .booking-hero::after {
    content: '';
    position: absolute;
    top: 0; right: 0; bottom: 0; left: 0;
    background: url('https://www.transparenttextures.com/patterns/carbon-fibre.png');
    opacity: 0.1;
  }

  .booking-card {
    background: #fff;
    border-radius: 24px;
    padding: 40px;
    box-shadow: 0 20px 40px rgba(0,0,0,0.1);
    margin-top: -60px;
    position: relative;
    z-index: 10;
    border: 1px solid #f0f0f0;
  }

  .summary-sticky {
    position: sticky;
    top: 100px;
    background: #fff;
    border-radius: 24px;
    padding: 30px;
    border: 1px solid #f0f0f0;
    box-shadow: 0 10px 30px rgba(0,0,0,0.05);
  }

  .form-section-title {
    font-size: 18px;
    font-weight: 800;
    margin-bottom: 25px;
    display: flex;
    align-items: center;
    gap: 10px;
    color: var(--text-main);
  }
  .form-section-title span {
    width: 28px;
    height: 28px;
    background: var(--hasta);
    color: #fff;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
  }

  .form-label {
    font-size: 11px;
    font-weight: 700;
    color: #95a5a6;
    text-transform: uppercase;
    margin-bottom: 8px;
    letter-spacing: 0.5px;
  }
  .form-control, .form-select {
    border: 1px solid #eee;
    padding: 12px 16px;
    font-weight: 600;
    border-radius: 12px;
    background: var(--bg-light);
  }
  .form-control:focus, .form-select:focus {
    background: #fff;
    border-color: var(--hasta);
    box-shadow: 0 0 0 4px rgba(203,55,55,0.05);
  }

  .btn-next {
    background: var(--hasta);
    color: #fff;
    border: none;
    padding: 16px;
    border-radius: 14px;
    font-weight: 700;
    width: 100%;
    transition: 0.3s;
  }
  .btn-next:hover {
    background: var(--hasta-dark);
    transform: translateY(-2px);
    box-shadow: 0 10px 20px rgba(203,55,55,0.2);
  }

  @media (max-width: 768px) {
    .booking-hero { padding: 40px 0 80px 0; }
    .booking-card { padding: 25px; }
  }
</style>

{{-- MOBILE BACK BUTTON --}}
<div class="d-md-none" style="background: var(--hasta-darker); padding: 12px 16px;">
  <a href="{{ url()->previous() }}" class="text-white text-decoration-none d-inline-flex align-items-center gap-2" style="font-size: 14px; font-weight: 500;">
    <i class="bi bi-arrow-left"></i> Back
  </a>
</div>

{{-- HERO SECTION --}}
<div class="booking-hero">
  <div class="container text-center position-relative" style="z-index: 2;">
    <h1 class="display-5 fw-800 text-white mb-2">Booking Information</h1>
    <p class="text-white-50">Please fill in the details below to secure your rental.</p>
  </div>
</div>

<div class="container pb-5">
  <div class="row g-4">
    {{-- LEFT: FORM --}}
    <div class="col-lg-8">
      <div class="booking-card">
        <form action="{{ route('bookings.store') }}" method="POST" id="bookingForm">
          @csrf
          <input type="hidden" name="car_id" value="{{ $car->id }}">

          <h5 class="form-section-title"><span>1</span> Rental Duration</h5>
          <div class="row g-3 mb-5">
            <div class="col-md-6">
              <label class="form-label">Pickup Date & Time</label>
              <div class="input-group">
                <input type="date" class="form-control" name="pickup_date" id="pickup_date" value="{{ $pickup_date ?? date('Y-m-d') }}" required>
                <input type="time" class="form-control" name="pickup_time" id="pickup_time" value="{{ $pickup_time ?? '09:00' }}" required>
              </div>
            </div>
            <div class="col-md-6">
              <label class="form-label">Dropoff Date & Time</label>
              <div class="input-group">
                <input type="date" class="form-control" name="dropoff_date" id="dropoff_date" value="{{ $dropoff_date ?? date('Y-m-d', strtotime('+1 day')) }}" required>
                <input type="time" class="form-control" name="dropoff_time" id="dropoff_time" value="{{ $dropoff_time ?? '09:00' }}" required>
              </div>
            </div>
          </div>

          <h5 class="form-section-title"><span>2</span> Locations</h5>
          <div class="row g-3 mb-5">
            <div class="col-md-6">
              <label class="form-label">Pickup Location</label>
              <input type="text" class="form-control" name="pickup_location" value="{{ $pickup_location ?? 'Student Mall' }}" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Dropoff Location</label>
              <input type="text" class="form-control" name="dropoff_location" value="{{ $dropoff_location ?? 'Student Mall' }}" required>
            </div>
          </div>

          <h5 class="form-section-title"><span>3</span> Billing Details</h5>
          <div class="row g-3">
            <div class="col-md-12">
              <label class="form-label">Full Name</label>
              <input type="text" class="form-control" name="name" value="{{ session('auth_name') }}" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Phone Number</label>
              <input type="tel" class="form-control" name="phone" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">City</label>
              <input type="text" class="form-control" name="city" required>
            </div>
            <div class="col-md-12 mb-4">
              <label class="form-label">Address</label>
              <textarea class="form-control" name="address" rows="2" required></textarea>
            </div>
          </div>

          <button type="submit" class="btn-next mt-4">
            Review & Proceed to Payment <i class="bi bi-arrow-right ms-2"></i>
          </button>
        </form>
      </div>
    </div>

    {{-- RIGHT: SUMMARY --}}
    <div class="col-lg-4">
      <div class="summary-sticky">
        <h5 class="fw-800 mb-4">Summary</h5>
        
        <div class="bg-light rounded-4 p-3 mb-4 text-center">
          @if($car->image_url)
            <img src="{{ $car->image_url }}" alt="{{ $car->brand }}" class="img-fluid mb-2" style="max-height: 120px; filter: drop-shadow(0 10px 20px rgba(0,0,0,0.05));">
          @endif
          <h6 class="fw-bold mb-1">{{ $car->brand }} {{ $car->model }}</h6>
          <div class="small text-muted">{{ $car->plate_number }}</div>
        </div>

        <div class="d-flex justify-content-between mb-2">
          <span class="text-muted small">Rate per Hour</span>
          <span class="fw-bold">RM {{ number_format($car->base_rate_per_hour, 2) }}</span>
        </div>
        <div class="d-flex justify-content-between mb-3 pb-3 border-bottom">
          <span class="text-muted small">Duration</span>
          <span id="durationText" class="fw-bold">1 Hour</span>
        </div>

        <div class="d-flex justify-content-between align-items-center mt-4">
          <h6 class="fw-bold mb-0">Total</h6>
          <span id="totalPrice" class="h3 fw-800 text-hasta-red mb-0">RM {{ number_format($car->base_rate_per_hour, 2) }}</span>
        </div>
      </div>
    </div>
  </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('bookingForm');
        const pDate = document.getElementById('pickup_date');
        const pTime = document.getElementById('pickup_time');
        const dDate = document.getElementById('dropoff_date');
        const dTime = document.getElementById('dropoff_time');
        
        const durTxt = document.getElementById('durationText');
        const totPr = document.getElementById('totalPrice');
        const baseRate = {{ floatval($car->base_rate_per_hour) }};

        function update() {
            const start = new Date(pDate.value + 'T' + pTime.value);
            const end = new Date(dDate.value + 'T' + dTime.value);
            if (isNaN(start) || isNaN(end)) return;
            const hours = Math.max(1, Math.ceil((end - start) / 3600000));
            durTxt.textContent = hours + (hours === 1 ? ' Hour' : ' Hours');
            totPr.textContent = 'RM ' + (hours * baseRate).toLocaleString(undefined, {minimumFractionDigits: 2});
        }

        [pDate, pTime, dDate, dTime].forEach(el => el.addEventListener('change', update));
        update();
    });
</script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css">
@endpush
@endsection
