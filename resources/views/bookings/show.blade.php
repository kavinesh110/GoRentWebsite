@extends('layouts.app')
@section('title', 'Booking - ' . $car->brand . ' ' . $car->model)

@section('content')

@php
  $carName = ($car->brand ?? 'Car') . ' ' . ($car->model ?? '');
  $year = $car->year ?? null;
  $avgRating = isset($feedbacks) && $feedbacks ? $feedbacks->avg('rating') : 0;
  $rating = $avgRating > 0 ? $avgRating : 4.0;
  $reviewCount = isset($feedbacks) && $feedbacks ? $feedbacks->count() : 0;
  $baseRate = $car->base_rate_per_hour ?? 0;
@endphp

<style>
  .detail-hero {
    background: linear-gradient(135deg, #1e272e 0%, #000 100%);
    padding: 60px 0 100px 0;
    color: #fff;
    position: relative;
    overflow: hidden;
  }
  .detail-hero::after {
    content: '';
    position: absolute;
    top: 0; right: 0; bottom: 0; left: 0;
    background: url('https://www.transparenttextures.com/patterns/carbon-fibre.png');
    opacity: 0.1;
  }

  .car-specs-card {
    background: #fff;
    border-radius: 24px;
    padding: 32px;
    box-shadow: 0 20px 40px rgba(0,0,0,0.1);
    margin-top: -60px;
    position: relative;
    z-index: 10;
    border: 1px solid #f0f0f0;
  }

  .spec-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 15px;
    background: var(--bg-light);
    border-radius: 16px;
    height: 100%;
  }
  .spec-icon {
    width: 40px;
    height: 40px;
    background: #fff;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--hasta);
    box-shadow: 0 4px 10px rgba(0,0,0,0.05);
  }

  .booking-form-card {
    background: #fff;
    border-radius: 24px;
    padding: 40px;
    border: 1px solid #f0f0f0;
    box-shadow: 0 10px 30px rgba(0,0,0,0.02);
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

  .btn-book-now {
    background: var(--hasta);
    color: #fff;
    border: none;
    padding: 16px;
    border-radius: 14px;
    font-weight: 700;
    width: 100%;
    transition: 0.3s;
  }
  .btn-book-now:hover {
    background: var(--hasta-dark);
    transform: translateY(-2px);
    box-shadow: 0 10px 20px rgba(203,55,55,0.2);
  }

  .review-card {
    background: #fff;
    padding: 25px;
    border-radius: 20px;
    border: 1px solid #eee;
    height: 100%;
  }

  @media (max-width: 768px) {
    .detail-hero { padding: 40px 0 80px 0; }
    .car-specs-card { padding: 20px; }
    .booking-form-card { padding: 25px; }
  }
</style>

{{-- MOBILE BACK BUTTON --}}
<div class="d-md-none" style="background: linear-gradient(135deg, #1e272e 0%, #000 100%); padding: 12px 16px;">
  <a href="{{ route('cars.index') }}" class="text-white text-decoration-none d-inline-flex align-items-center gap-2" style="font-size: 14px; font-weight: 500;">
    <i class="bi bi-arrow-left"></i> Back to Cars
  </a>
</div>

{{-- HERO SECTION --}}
<div class="detail-hero">
  <div class="container text-center position-relative" style="z-index: 2;">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb justify-content-center mb-3">
        <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-white-50 text-decoration-none">Home</a></li>
        <li class="breadcrumb-item active text-white" aria-current="page">Book Car</li>
      </ol>
    </nav>
    <h1 class="display-5 fw-800 text-white mb-2">{{ $carName }}</h1>
    <p class="text-white-50">{{ $car->plate_number }} • Registered Vehicle</p>
  </div>
</div>

<div class="container pb-5">
  {{-- CAR SPECS CARD --}}
  <div class="car-specs-card mb-5">
    <div class="row align-items-center g-4">
      <div class="col-lg-5 text-center">
        @if($car->image_url)
          <img src="{{ $car->image_url }}" alt="{{ $carName }}" class="img-fluid" style="max-height: 250px; filter: drop-shadow(0 15px 30px rgba(0,0,0,0.1));">
        @else
          <i class="bi bi-image text-muted" style="font-size: 5rem;"></i>
        @endif
      </div>
      <div class="col-lg-7">
        <div class="row g-3">
          <div class="col-6 col-md-4">
            <div class="spec-item">
              <div class="spec-icon"><i class="bi bi-people"></i></div>
              <div><small class="text-muted d-block">Capacity</small><strong>5 Seats</strong></div>
            </div>
          </div>
          <div class="col-6 col-md-4">
            <div class="spec-item">
              <div class="spec-icon"><i class="bi bi-gear"></i></div>
              <div><small class="text-muted d-block">Trans.</small><strong>Auto</strong></div>
            </div>
          </div>
          <div class="col-6 col-md-4">
            <div class="spec-item">
              <div class="spec-icon"><i class="bi bi-fuel-pump"></i></div>
              <div><small class="text-muted d-block">Fuel</small><strong>Petrol</strong></div>
            </div>
          </div>
          <div class="col-6 col-md-4">
            <div class="spec-item">
              <div class="spec-icon"><i class="bi bi-calendar3"></i></div>
              <div><small class="text-muted d-block">Year</small><strong>{{ $car->year }}</strong></div>
            </div>
          </div>
          <div class="col-6 col-md-4">
            <div class="spec-item border-warning border">
              <div class="spec-icon bg-warning-subtle text-warning"><i class="bi bi-star-fill"></i></div>
              <div><small class="text-muted d-block">Rating</small><strong>{{ number_format($rating, 1) }} ({{ $reviewCount }})</strong></div>
            </div>
          </div>
          <div class="col-6 col-md-4">
            <div class="spec-item">
              <div class="spec-icon"><i class="bi bi-shield-check"></i></div>
              <div><small class="text-muted d-block">Service</small><strong>Verified</strong></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="row g-4">
    {{-- LEFT: BOOKING FORM --}}
    <div class="col-lg-8">
      <div class="booking-form-card">
        <form id="bookingForm" method="POST" action="{{ route('bookings.store') }}">
          @csrf
          <input type="hidden" name="car_id" value="{{ $car->id }}">

          <h5 class="form-section-title"><span>1</span> Personal Information</h5>
          <div class="row g-3 mb-5">
            <div class="col-md-6">
              <label class="form-label">Full Name</label>
              <input type="text" name="name" class="form-control" value="{{ $customer->full_name ?? '' }}" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Phone Number</label>
              <input type="tel" name="phone" class="form-control" value="{{ $customer->phone ?? '' }}" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Residential College (Kolej)</label>
              <input type="text" name="college" class="form-control" value="{{ $customer->college_name ?? '' }}" placeholder="e.g., Kolej Tun Razak">
            </div>
            <div class="col-md-6">
              <label class="form-label">City</label>
              <input type="text" name="city" class="form-control" required>
            </div>
            <div class="col-md-12">
              <label class="form-label">Address</label>
              <input type="text" name="address" class="form-control" placeholder="Street address or room number" required>
            </div>
          </div>

          <h5 class="form-section-title"><span>2</span> Rental Details</h5>
          <div class="row g-3 mb-4">
            <div class="col-md-6">
              <label class="form-label">Pickup Location</label>
              <select name="pickup_location" class="form-select" id="pickup_location" required>
                @foreach($locations as $loc)
                  @if(in_array($loc->type, ['pickup', 'both']))
                    <option value="{{ $loc->name }}">{{ $loc->name }}</option>
                  @endif
                @endforeach
              </select>
            </div>
            <div class="col-md-3 col-6">
              <label class="form-label">Pickup Date</label>
              <input type="date" name="pickup_date" id="pickup_date" class="form-control" required>
            </div>
            <div class="col-md-3 col-6">
              <label class="form-label">Pickup Time</label>
              <input type="time" name="pickup_time" id="pickup_time" class="form-control" value="09:00" required>
            </div>

            <div class="col-md-6">
              <label class="form-label">Dropoff Location</label>
              <select name="dropoff_location" class="form-select" id="dropoff_location" required>
                @foreach($locations as $loc)
                  @if(in_array($loc->type, ['dropoff', 'both']))
                    <option value="{{ $loc->name }}">{{ $loc->name }}</option>
                  @endif
                @endforeach
              </select>
            </div>
            <div class="col-md-3 col-6">
              <label class="form-label">Dropoff Date</label>
              <input type="date" name="dropoff_date" id="dropoff_date" class="form-control" required>
            </div>
            <div class="col-md-3 col-6">
              <label class="form-label">Dropoff Time</label>
              <input type="time" name="dropoff_time" id="dropoff_time" class="form-control" value="09:00" required>
            </div>
          </div>

          {{-- Hidden voucher field for form submission --}}
          @if(isset($availableVouchers) && $availableVouchers->count() > 0)
            <select id="voucher-select-hidden" name="voucher_id" style="display: none;">
              <option value="">No voucher applied</option>
              @foreach($availableVouchers as $voucher)
                <option value="{{ $voucher->voucher_id }}" data-discount-type="{{ $voucher->discount_type }}" data-discount-value="{{ $voucher->discount_value }}">
                  {{ $voucher->code }} ({{ $voucher->discount_type === 'percent' ? $voucher->discount_value.'%' : 'RM'.$voucher->discount_value }} Off)
                </option>
              @endforeach
            </select>
          @else
            <input type="hidden" name="voucher_id" value="">
          @endif
        </form>
      </div>
    </div>

    {{-- RIGHT: SUMMARY --}}
    <div class="col-lg-4">
      <div class="summary-sticky">
        <h5 class="fw-800 mb-4">Rental Summary</h5>
        <div class="d-flex justify-content-between mb-2">
          <span class="text-muted">Rate per Hour</span>
          <span class="fw-bold">RM {{ number_format($baseRate, 2) }}</span>
        </div>
        <div class="d-flex justify-content-between mb-3 pb-3 border-bottom">
          <span class="text-muted">Base Subtotal</span>
          <span id="subtotal-display" class="fw-bold">RM 0.00</span>
        </div>
        
        <div id="voucher-discount-row" class="justify-content-between mb-2 text-success" style="display: none;">
          <span>Voucher Discount</span>
          <span class="fw-bold">-RM <span id="voucher-discount-amount">0.00</span></span>
        </div>

        <div class="d-flex justify-content-between align-items-center mt-4">
          <div>
            <h6 class="fw-bold mb-0">Total Price</h6>
            <small class="text-muted">Estimated amount</small>
          </div>
          <div class="text-end">
            <span class="h3 fw-800 text-hasta-red mb-0 total-price-value">RM 0</span>
          </div>
        </div>

        {{-- Available Vouchers --}}
        @if(isset($availableVouchers) && $availableVouchers->count() > 0)
          <div class="mt-4 pt-4 border-top">
            <label class="form-label mb-2">Available Vouchers</label>
            <select id="voucher-select" class="form-select form-select-sm">
              <option value="">No voucher applied</option>
              @foreach($availableVouchers as $voucher)
                <option value="{{ $voucher->voucher_id }}" data-discount-type="{{ $voucher->discount_type }}" data-discount-value="{{ $voucher->discount_value }}">
                  {{ $voucher->code }} ({{ $voucher->discount_type === 'percent' ? $voucher->discount_value.'%' : 'RM'.$voucher->discount_value }} Off)
                </option>
              @endforeach
            </select>
          </div>
        @endif

        {{-- Notice Alert --}}
        <div class="mt-4">
          <div class="alert alert-warning border-0 d-flex gap-2 p-3 rounded-3 mb-0">
            <i class="bi bi-exclamation-triangle-fill"></i>
            <div class="small">
              <strong>Notice:</strong> A RM 50 deposit is required to confirm this booking. You will be asked to upload the payment receipt on the next page.
            </div>
          </div>
        </div>

        {{-- Proceed Button --}}
        <button type="button" id="submitBookingForm" class="btn-book-now mt-4 w-100">
          Proceed to Payment <i class="bi bi-arrow-right ms-2"></i>
        </button>

        <div class="mt-4 pt-4 border-top">
          <div class="d-flex gap-2 mb-2">
            <i class="bi bi-check-circle-fill text-success"></i>
            <small>Insurance included</small>
          </div>
          <div class="d-flex gap-2 mb-2">
            <i class="bi bi-check-circle-fill text-success"></i>
            <small>Verified by UTM Staff</small>
          </div>
          <div class="d-flex gap-2">
            <i class="bi bi-info-circle-fill text-primary"></i>
            <small>Refundable RM 50 Deposit</small>
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- REVIEWS SECTION --}}
  @if(isset($feedbacks) && $feedbacks->count() > 0)
    <div class="mt-5 pt-5">
      <h4 class="fw-800 mb-4">Customer Reviews</h4>
      <div class="row g-4">
        @foreach($feedbacks as $feedback)
          <div class="col-md-6">
            <div class="review-card">
              <div class="d-flex justify-content-between align-items-start mb-3">
                <div>
                  <h6 class="fw-bold mb-0">{{ $feedback->customer->full_name ?? 'Anonymous' }}</h6>
                  <small class="text-muted">{{ $feedback->created_at->format('d M Y') }}</small>
                </div>
                <div class="text-warning">
                  @for($i = 1; $i <= 5; $i++)
                    <i class="bi bi-star{{ $i <= $feedback->rating ? '-fill' : '' }}"></i>
                  @endfor
                </div>
              </div>
              <p class="small text-muted mb-0">{{ $feedback->comment }}</p>
            </div>
          </div>
        @endforeach
      </div>
    </div>
  @endif
</div>

@endsection

@push('scripts')
<script>
  const pickupDateInput = document.getElementById('pickup_date');
  const dropoffDateInput = document.getElementById('dropoff_date');
  const pickupTimeInput = document.getElementById('pickup_time');
  const dropoffTimeInput = document.getElementById('dropoff_time');
  const pricePerHour = Number('{{ $baseRate }}');
  const subtotalDisplay = document.getElementById('subtotal-display');
  const totalDisplay = document.querySelector('.total-price-value');
  
  function updatePricing() {
    if (pickupDateInput.value && dropoffDateInput.value && pickupTimeInput.value && dropoffTimeInput.value) {
      const pickup = new Date(pickupDateInput.value + 'T' + pickupTimeInput.value);
      const dropoff = new Date(dropoffDateInput.value + 'T' + dropoffTimeInput.value);
      
      if (dropoff < pickup) {
        dropoffDateInput.value = pickupDateInput.value;
        return;
      }
      
      const diffHours = Math.ceil(Math.abs(dropoff - pickup) / (1000 * 60 * 60));
      const hours = Math.max(1, diffHours);
      const subtotal = pricePerHour * hours;
      
      let voucherDiscount = 0;
      const voucherSelect = document.getElementById('voucher-select');
      if (voucherSelect && voucherSelect.value) {
        const opt = voucherSelect.options[voucherSelect.selectedIndex];
        const type = opt.getAttribute('data-discount-type');
        const val = parseFloat(opt.getAttribute('data-discount-value'));
        voucherDiscount = (type === 'percent') ? (subtotal * val / 100) : Math.min(val, subtotal);
      }
      
      const total = subtotal - voucherDiscount;
      
      if (subtotalDisplay) subtotalDisplay.textContent = 'RM ' + subtotal.toFixed(2);
      if (totalDisplay) totalDisplay.textContent = 'RM ' + Math.round(total);
      
      const discRow = document.getElementById('voucher-discount-row');
      if (voucherDiscount > 0) {
        document.getElementById('voucher-discount-amount').textContent = voucherDiscount.toFixed(2);
        discRow.style.display = 'flex';
      } else {
        discRow.style.display = 'none';
      }
    }
  }
  
  [pickupDateInput, dropoffDateInput, pickupTimeInput, dropoffTimeInput].forEach(el => {
    if (el) el.addEventListener('change', updatePricing);
  });
  
  // Handle voucher selection (sync visible select with hidden form field)
  const vSelect = document.getElementById('voucher-select');
  const vSelectHidden = document.getElementById('voucher-select-hidden');
  
  if (vSelect && vSelectHidden) {
    vSelect.addEventListener('change', function() {
      // Sync visible select value to hidden select for form submission
      vSelectHidden.value = this.value;
      updatePricing();
    });
  } else if (vSelect) {
    vSelect.addEventListener('change', updatePricing);
  }

  // Handle form submission from summary button
  const submitBtn = document.getElementById('submitBookingForm');
  const bookingForm = document.getElementById('bookingForm');
  if (submitBtn && bookingForm) {
    submitBtn.addEventListener('click', function() {
      // Sync voucher value before submission
      if (vSelect && vSelectHidden) {
        vSelectHidden.value = vSelect.value;
      }
      bookingForm.submit();
    });
  }
  
  const today = new Date().toISOString().split('T')[0];
  if (pickupDateInput) {
    pickupDateInput.setAttribute('min', today);
    pickupDateInput.value = today;
  }
  if (dropoffDateInput) {
    dropoffDateInput.setAttribute('min', today);
    dropoffDateInput.value = today;
  }
  updatePricing();
</script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css">
@endpush
