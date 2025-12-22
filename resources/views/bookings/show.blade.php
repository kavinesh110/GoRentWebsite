@extends('layouts.app')
@section('title', 'Booking - ' . $car->brand . ' ' . $car->model)

@section('content')

@php
  // ===== CAR INFORMATION =====
  // Get car name and year from new schema (brand + model)
  $carName = ($car->brand ?? 'Car') . ' ' . ($car->model ?? '');
  $year = $car->year ?? null;
  
  // ===== RATING & REVIEWS =====
  // Calculate actual rating from customer feedbacks (average of all ratings)
  // Defaults to 4.0 stars if no reviews exist yet
  $avgRating = $feedbacks ? $feedbacks->avg('rating') : 0;
  $rating = $avgRating > 0 ? $avgRating : 4.0; // Default 4 stars if no reviews
  $reviewCount = $feedbacks ? $feedbacks->count() : 0;
  
  // ===== PRICING CALCULATION =====
  // Calculate default pricing (24 hours = 1 day)
  // Pricing updates dynamically via JavaScript when user selects dates/times
  // Note: Cars use base_rate_per_hour, so pricing is calculated by hours
  $defaultHours = 24; // 1 day = 24 hours (default display value)
  $baseRate = $car->base_rate_per_hour ?? 0;
  $subtotal = $baseRate * $defaultHours;
  $tax = 0.00; // No tax currently applied
  $total = $subtotal + $tax;
@endphp

<style>
  .booking-page{
    background: #F6F7FB;
    min-height: 100vh;
    padding: 40px 0;
  }
  .booking-container{
    max-width: 1400px;
    margin: 0 auto;
    padding: 0 24px;
  }
  .booking-grid{
    display: grid;
    grid-template-columns: 1fr 1.2fr;
    gap: 40px;
    margin-top: 30px;
  }
  
  /* Rental Summary Section */
  .rental-summary{
    background: #fff;
    border-radius: 20px;
    padding: 32px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    height: fit-content;
    position: sticky;
    top: 20px;
  }
  .rental-summary h2{
    font-size: 28px;
    font-weight: 700;
    color: #333;
    margin-bottom: 8px;
  }
  .rental-summary-desc{
    font-size: 14px;
    color: #7A7A7A;
    margin-bottom: 32px;
    line-height: 1.5;
  }
  .car-details{
    margin-bottom: 32px;
  }
  .car-name-year{
    font-size: 22px;
    font-weight: 600;
    color: #333;
    margin-bottom: 12px;
  }
  .car-rating{
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 20px;
  }
  .star-rating{
    display: flex;
    gap: 4px;
  }
  .star-filled{
    color: #FFD700;
    width: 18px;
    height: 18px;
  }
  .star-outline{
    color: #ddd;
    width: 18px;
    height: 18px;
  }
  .review-count{
    font-size: 14px;
    color: #7A7A7A;
  }
  .car-image-container{
    width: 100%;
    height: 300px;
    background: #f8f8f8;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    margin-bottom: 32px;
  }
  .car-image-container img{
    width: 100%;
    height: 100%;
    object-fit: contain;
  }
  .cost-breakdown{
    border-top: 1px solid #f0f0f0;
    border-bottom: 1px solid #f0f0f0;
    padding: 20px 0;
    margin-bottom: 24px;
  }
  .cost-row{
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 12px;
  }
  .cost-row:last-child{
    margin-bottom: 0;
  }
  .cost-label{
    font-size: 16px;
    color: #333;
    font-weight: 500;
  }
  .cost-value{
    font-size: 16px;
    color: #333;
    font-weight: 600;
  }
  .promo-section{
    margin-bottom: 32px;
  }
  .promo-input-group{
    display: flex;
    gap: 12px;
  }
  .promo-input{
    flex: 1;
    padding: 12px 16px;
    border: 1px solid #e0e0e0;
    border-radius: 10px;
    font-size: 14px;
    background: #f8f8f8;
    outline: none;
    transition: all 0.2s;
  }
  .promo-input:focus{
    border-color: #F98820;
    background: #fff;
  }
  .promo-btn{
    padding: 12px 24px;
    background: transparent;
    border: none;
    color: #333;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    border-radius: 10px;
    transition: all 0.2s;
  }
  .promo-btn:hover{
    background: #f0f0f0;
  }
  .total-section{
    background: #f8f8f8;
    border-radius: 12px;
    padding: 24px;
  }
  .total-header{
    font-size: 18px;
    font-weight: 600;
    color: #333;
    margin-bottom: 6px;
  }
  .total-desc{
    font-size: 13px;
    color: #7A7A7A;
    margin-bottom: 16px;
  }
  .total-price{
    display: flex;
    justify-content: space-between;
    align-items: center;
  }
  .total-price-label{
    font-size: 18px;
    font-weight: 600;
    color: #333;
  }
  .total-price-value{
    font-size: 36px;
    font-weight: 700;
    color: #333;
  }
  
  /* Booking Form Section */
  .booking-form-section{
    background: #fff;
    border-radius: 20px;
    padding: 32px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    margin-bottom: 24px;
  }
  .form-header{
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 24px;
  }
  .form-title{
    font-size: 28px;
    font-weight: 700;
    color: #333;
    margin-bottom: 6px;
  }
  .form-desc{
    font-size: 14px;
    color: #7A7A7A;
  }
  .step-indicator{
    font-size: 13px;
    color: #7A7A7A;
    font-weight: 500;
  }
  .form-grid{
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
    margin-bottom: 32px;
  }
  .form-group{
    display: flex;
    flex-direction: column;
  }
  .form-group.full-width{
    grid-column: 1 / -1;
  }
  .form-label{
    font-size: 14px;
    font-weight: 600;
    color: #333;
    margin-bottom: 8px;
  }
  .form-input{
    padding: 12px 16px;
    border: 1px solid #e0e0e0;
    border-radius: 10px;
    font-size: 14px;
    background: #f8f8f8;
    outline: none;
    transition: all 0.2s;
    color: #333;
  }
  .form-input:focus{
    border-color: #F98820;
    background: #fff;
  }
  .form-input::placeholder{
    color: #999;
  }
  .rental-section{
    margin-top: 32px;
    padding-top: 32px;
    border-top: 1px solid #f0f0f0;
  }
  .rental-header{
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 20px;
  }
  .rental-icon{
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: #F98820;
    display: flex;
    align-items: center;
    justify-content: center;
  }
  .rental-icon-dot{
    width: 12px;
    height: 12px;
    border-radius: 50%;
    background: #fff;
  }
  .rental-title{
    font-size: 18px;
    font-weight: 600;
    color: #333;
  }
  .next-btn{
    background: #F98820;
    color: #fff;
    border: none;
    border-radius: 10px;
    padding: 14px 32px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
    float: right;
    margin-top: 32px;
  }
  .next-btn:hover{
    background: #e67a1a;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(249, 136, 32, 0.3);
  }
  
  @media (max-width: 1024px){
    .booking-grid{
      grid-template-columns: 1fr;
    }
    .rental-summary{
      position: static;
    }
  }
  
  @media (max-width: 768px){
    .booking-page{
      padding: 20px 0;
    }
    .form-grid{
      grid-template-columns: 1fr;
    }
    .rental-summary, .booking-form-section{
      padding: 24px;
    }
    .total-price-value{
      font-size: 28px;
    }
  }
</style>

{{-- ===== BOOKING PAGE LAYOUT ===== --}}
{{-- Two-column layout: Left side = Rental Summary, Right side = Booking Form --}}
<div class="booking-page">
  <div class="booking-container">
    <div class="booking-grid">
      {{-- LEFT COLUMN: Rental Summary (sticky sidebar) --}}
      {{-- Shows car details, rating, image, pricing breakdown, and total --}}
      <div class="rental-summary">
        <h2>Rental Summary</h2>
        <p class="rental-summary-desc">
          Prices may change depending on the length of the rental and the price of your rental car.
        </p>
        
        <div class="car-details">
          <div class="car-name-year">{{ $carName }}@if($year) ({{ $year }})@endif</div>
          <div class="car-rating">
            <div class="star-rating">
              @for($i = 1; $i <= 5; $i++)
                @if($i <= floor($rating))
                  <svg class="star-filled" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                  </svg>
                @else
                  <svg class="star-outline" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                  </svg>
                @endif
              @endfor
            </div>
            <span class="review-count">{{ $reviewCount }} {{ $reviewCount === 1 ? 'Review' : 'Reviews' }}</span>
            @if($avgRating > 0)
              <span class="review-count ms-2">({{ number_format($avgRating, 1) }} avg)</span>
            @endif
          </div>
          
          <div class="car-image-container">
            @if($car->image_url)
              <img src="{{ $car->image_url }}" alt="{{ $carName }}">
            @else
              <svg width="200" height="120" viewBox="0 0 200 120" fill="none">
                <rect x="20" y="50" width="160" height="60" rx="12" fill="#ddd"/>
                <circle cx="50" cy="105" r="12" fill="#999"/>
                <circle cx="150" cy="105" r="12" fill="#999"/>
                <path d="M50 50 L100 30 L150 30 L180 50" stroke="#bbb" stroke-width="4" fill="none"/>
              </svg>
            @endif
          </div>
        </div>
        
        <div class="cost-breakdown">
          <div class="cost-row">
            <span class="cost-label">Subtotal</span>
            <span class="cost-value" id="subtotal-display">RM {{ number_format($subtotal, 2) }}</span>
          </div>
          <div class="cost-row">
            <span class="cost-label">Tax</span>
            <span class="cost-value">RM {{ number_format($tax, 2) }}</span>
          </div>
        </div>
        
        {{-- Voucher Selection Section --}}
        @if(isset($availableVouchers) && $availableVouchers->count() > 0)
        <div class="promo-section" style="margin-top: 16px;">
          <label class="form-label small fw-semibold mb-2" style="display: block;">Apply Voucher (Optional)</label>
          <select id="voucher-select" name="voucher_id" class="form-select" style="padding: 10px; border-radius: 8px; border: 1px solid #ddd;">
            <option value="">No voucher</option>
            @foreach($availableVouchers as $voucher)
              <option value="{{ $voucher->voucher_id }}" 
                data-discount-type="{{ $voucher->discount_type }}"
                data-discount-value="{{ $voucher->discount_value }}">
                {{ $voucher->code }} - 
                @if($voucher->discount_type === 'percent')
                  {{ number_format($voucher->discount_value) }}% off
                @else
                  RM {{ number_format($voucher->discount_value, 2) }} off
                @endif
              </option>
            @endforeach
          </select>
          <small class="text-muted" style="display: block; margin-top: 4px; font-size: 12px;">
            Select a voucher to apply discount to your booking
          </small>
        </div>
        @endif
        
        <div class="total-section">
          <div class="total-header">Total Rental Price</div>
          <div class="total-desc">Overall price and includes rental discount</div>
          <div class="total-price">
            <span class="total-price-label">Total Rental Price</span>
            <span class="total-price-value">RM {{ number_format($total, 0) }}</span>
          </div>
        </div>
      </div>
      
      {{-- RIGHT COLUMN: Booking Form --}}
      {{-- Contains billing information and rental date/time selection --}}
      <div>
        {{-- Billing Information Section --}}
        {{-- Customer name, phone, address, city --}}
        <div class="booking-form-section">
          <div class="form-header">
            <div>
              <h2 class="form-title">Billing Info</h2>
              <p class="form-desc">Please enter your billing info</p>
            </div>
            <span class="step-indicator">Step 1 of 4</span>
          </div>
          
          <form id="bookingForm" method="POST" action="{{ route('bookings.store') }}">
            @csrf
            <input type="hidden" name="car_id" value="{{ $car->id }}">
            
            <div class="form-grid">
              <div class="form-group">
                <label class="form-label" for="name">Name</label>
                <input type="text" id="name" name="name" class="form-input" placeholder="Your name" value="{{ $customer->full_name ?? '' }}" required>
              </div>
              <div class="form-group">
                <label class="form-label" for="phone">Phone Number</label>
                <input type="tel" id="phone" name="phone" class="form-input" placeholder="Phone number" value="{{ $customer->phone ?? '' }}" required>
              </div>
              <div class="form-group full-width">
                <label class="form-label" for="address">Address</label>
                <input type="text" id="address" name="address" class="form-input" placeholder="Address" required>
              </div>
              <div class="form-group">
                <label class="form-label" for="city">Town / City</label>
                <input type="text" id="city" name="city" class="form-input" placeholder="Town or city" required>
              </div>
            </div>
            
            {{-- Rental Information Section --}}
            {{-- Pickup and dropoff location, date, and time selection --}}
            <div class="rental-section">
              <div class="form-header">
                <div>
                  <h2 class="form-title">Rental Info</h2>
                  <p class="form-desc">Please select your rental date</p>
                </div>
                <span class="step-indicator">Step 2 of 4</span>
              </div>
              
              {{-- Pick-Up Details --}}
              <div style="margin-bottom: 32px;">
                <div class="rental-header">
                  <div class="rental-icon">
                    <div class="rental-icon-dot"></div>
                  </div>
                  <div class="rental-title">Pick - Up</div>
                </div>
                <div class="form-grid">
                  <div class="form-group">
                    <label class="form-label" for="pickup_location">Locations</label>
                    <select id="pickup_location" name="pickup_location" class="form-input" required>
                      <option value="">Select pickup location</option>
                      @foreach($locations as $location)
                        @if(in_array($location->type, ['pickup', 'both']))
                          <option value="{{ $location->name }}">{{ $location->name }}</option>
                        @endif
                      @endforeach
                    </select>
                  </div>
                  <div class="form-group">
                    <label class="form-label" for="pickup_date">Date</label>
                    <input type="date" id="pickup_date" name="pickup_date" class="form-input" placeholder="Select your date" required>
                  </div>
                  <div class="form-group">
                    <label class="form-label" for="pickup_time">Time</label>
                    <input type="time" id="pickup_time" name="pickup_time" class="form-input" placeholder="Select your time" required>
                  </div>
                </div>
              </div>
              
              {{-- Drop-Off Details --}}
              <div>
                <div class="rental-header">
                  <div class="rental-icon">
                    <div class="rental-icon-dot"></div>
                  </div>
                  <div class="rental-title">Drop - Off</div>
                </div>
                <div class="form-grid">
                  <div class="form-group">
                    <label class="form-label" for="dropoff_location">Locations</label>
                    <select id="dropoff_location" name="dropoff_location" class="form-input" required>
                      <option value="">Select dropoff location</option>
                      @foreach($locations as $location)
                        @if(in_array($location->type, ['dropoff', 'both']))
                          <option value="{{ $location->name }}">{{ $location->name }}</option>
                        @endif
                      @endforeach
                    </select>
                  </div>
                  <div class="form-group">
                    <label class="form-label" for="dropoff_date">Date</label>
                    <input type="date" id="dropoff_date" name="dropoff_date" class="form-input" placeholder="Select your date" required>
                  </div>
                  <div class="form-group">
                    <label class="form-label" for="dropoff_time">Time</label>
                    <input type="time" id="dropoff_time" name="dropoff_time" class="form-input" placeholder="Select your time" required>
                  </div>
                </div>
              </div>
            </div>
            
            <button type="submit" class="next-btn">Next</button>
            <div style="clear: both;"></div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

@push('scripts')
<script>
  // ===== DYNAMIC PRICING CALCULATION =====
  // Updates rental price automatically when user changes pickup/dropoff dates/times
  // Calculates hours difference and multiplies by hourly rate
  
  const pickupDateInput = document.getElementById('pickup_date');
  const dropoffDateInput = document.getElementById('dropoff_date');
  const pickupTimeInput = document.getElementById('pickup_time');
  const dropoffTimeInput = document.getElementById('dropoff_time');
  const pricePerHour = Number({{ json_encode(floatval($car->base_rate_per_hour ?? 0)) }});
  const subtotalDisplay = document.getElementById('subtotal-display');
  const totalDisplay = document.querySelector('.total-price-value');
  
  /**
   * Calculate and update pricing display based on selected dates/times and voucher
   * Minimum rental is 1 hour
   */
  function updatePricing() {
    if (pickupDateInput.value && dropoffDateInput.value && pickupTimeInput.value && dropoffTimeInput.value) {
      // Parse selected dates and times into Date objects
      const pickup = new Date(pickupDateInput.value + 'T' + pickupTimeInput.value);
      const dropoff = new Date(dropoffDateInput.value + 'T' + dropoffTimeInput.value);
      
      // Validation: Ensure dropoff is after pickup (prevent invalid selections)
      if (dropoff < pickup) {
        dropoffDateInput.value = pickupDateInput.value;
        dropoffTimeInput.value = pickupTimeInput.value;
        return;
      }
      
      // Calculate rental duration in hours
      const diffTime = Math.abs(dropoff - pickup);
      const diffHours = Math.ceil(diffTime / (1000 * 60 * 60)); // Convert milliseconds to hours
      const hours = Math.max(1, diffHours); // Minimum 1 hour rental
      
      // Calculate base pricing: hourly rate × hours
      const subtotal = pricePerHour * hours;
      
      // Calculate voucher discount if voucher is selected
      let voucherDiscount = 0;
      const voucherSelect = document.getElementById('voucher-select');
      if (voucherSelect && voucherSelect.value) {
        const selectedOption = voucherSelect.options[voucherSelect.selectedIndex];
        const discountType = selectedOption.getAttribute('data-discount-type');
        const discountValue = parseFloat(selectedOption.getAttribute('data-discount-value'));
        
        if (discountType === 'percent') {
          voucherDiscount = (subtotal * discountValue) / 100;
        } else {
          // Fixed amount discount (can't exceed subtotal)
          voucherDiscount = Math.min(discountValue, subtotal);
        }
      }
      
      const tax = 0.00; // No tax currently
      const total = subtotal - voucherDiscount + tax;
      
      // Update display
      if (subtotalDisplay) {
        subtotalDisplay.textContent = 'RM ' + subtotal.toFixed(2);
      }
      if (totalDisplay) {
        totalDisplay.textContent = 'RM ' + Math.round(total);
      }
      
      // Show/hide voucher discount display
      let voucherDiscountRow = document.getElementById('voucher-discount-row');
      if (voucherDiscount > 0) {
        if (!voucherDiscountRow) {
          // Create voucher discount row if it doesn't exist
          const costBreakdown = document.querySelector('.cost-breakdown');
          if (costBreakdown) {
            voucherDiscountRow = document.createElement('div');
            voucherDiscountRow.id = 'voucher-discount-row';
            voucherDiscountRow.className = 'cost-row';
            voucherDiscountRow.style.color = '#28a745';
            voucherDiscountRow.innerHTML = '<span class="cost-label">Voucher Discount</span><span class="cost-value">-RM <span id="voucher-discount-amount">0.00</span></span>';
            costBreakdown.appendChild(voucherDiscountRow);
          }
        }
        if (voucherDiscountRow) {
          document.getElementById('voucher-discount-amount').textContent = voucherDiscount.toFixed(2);
          voucherDiscountRow.style.display = 'flex';
        }
      } else if (voucherDiscountRow) {
        voucherDiscountRow.style.display = 'none';
      }
    }
  }
  
  if (pickupDateInput && dropoffDateInput && pickupTimeInput && dropoffTimeInput) {
    pickupDateInput.addEventListener('change', function() {
      // Set minimum dropoff date to pickup date
      dropoffDateInput.setAttribute('min', pickupDateInput.value);
      updatePricing();
    });
    dropoffDateInput.addEventListener('change', updatePricing);
    pickupTimeInput.addEventListener('change', updatePricing);
    dropoffTimeInput.addEventListener('change', updatePricing);
  }
  
  // Update pricing when voucher selection changes
  const voucherSelect = document.getElementById('voucher-select');
  if (voucherSelect) {
    voucherSelect.addEventListener('change', updatePricing);
  }
  
  // ===== DATE VALIDATION =====
  // Set minimum selectable date to today (prevent booking past dates)
  const today = new Date().toISOString().split('T')[0];
  if (pickupDateInput) {
    pickupDateInput.setAttribute('min', today);
    pickupDateInput.value = today;
  }
  if (dropoffDateInput) {
    dropoffDateInput.setAttribute('min', today);
    dropoffDateInput.value = today;
  }
</script>
@endpush

@if(isset($feedbacks) && $feedbacks && $feedbacks->count() > 0)
<div class="container-custom mt-5">
  <div class="card border-0 shadow-soft">
    <div class="card-body p-4">
      <h5 class="fw-bold mb-4">Customer Reviews</h5>
      <div class="row g-3">
        @foreach($feedbacks as $feedback)
          <div class="col-12">
            <div class="border-bottom pb-3 mb-3">
              <div class="d-flex justify-content-between align-items-start mb-2">
                <div>
                  <strong>{{ $feedback->customer->full_name ?? 'Anonymous' }}</strong>
                  <div class="small text-muted">{{ $feedback->created_at->format('d M Y') }}</div>
                </div>
                <div class="d-flex gap-1">
                  @for($i = 1; $i <= 5; $i++)
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="{{ $i <= $feedback->rating ? '#FFD700' : '#ddd' }}" stroke="currentColor" stroke-width="2">
                      <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                    </svg>
                  @endfor
                </div>
              </div>
              @if($feedback->comment)
                <p class="mb-0">{{ $feedback->comment }}</p>
              @endif
              @if($feedback->reported_issue)
                <small class="text-warning">⚠ Issue reported</small>
              @endif
            </div>
          </div>
        @endforeach
      </div>
    </div>
  </div>
</div>
@endif

@endsection
