@extends('layouts.app')

@section('title', 'Book Car - Hasta GoRent')

@section('content')
<div class="container-fluid px-4 px-md-5" style="padding-top: 40px; padding-bottom: 60px;">
    <div class="row justify-content-center">
        <div class="col-xl-9">
            {{-- HEADER --}}
            <div class="mb-4 d-flex align-items-center gap-3">
                <a href="{{ route('home') }}" class="btn btn-outline-secondary btn-sm rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                    <i class="bi bi-arrow-left"></i>
                </a>
                <div>
                    <h1 class="h3 fw-bold mb-0">Booking Information</h1>
                    <p class="text-muted mb-0 small">Please fill in the details below to secure your rental.</p>
                </div>
            </div>

            <div class="row g-4">
                {{-- LEFT: FORM --}}
                <div class="col-lg-7 col-xl-8">
                    <div class="card border-0 shadow-soft h-100" style="border-radius: 20px;">
                        <div class="card-body p-4 p-md-5">
                            <form action="{{ route('bookings.store') }}" method="POST" id="bookingForm">
                                @csrf
                                <input type="hidden" name="car_id" value="{{ $car->id }}">

                                <h5 class="fw-bold mb-4 pb-2 border-bottom">1. Rental Duration</h5>
                                <div class="row g-3 mb-5">
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold small text-muted text-uppercase">Pickup Date & Time</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-0"><i class="bi bi-calendar-event"></i></span>
                                            <input type="date" class="form-control border-0 bg-light" name="pickup_date" value="{{ $pickup_date ?? date('Y-m-d') }}" required>
                                            <input type="time" class="form-control border-0 bg-light" name="pickup_time" value="{{ $pickup_time ?? '09:00' }}" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold small text-muted text-uppercase">Dropoff Date & Time</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-0"><i class="bi bi-calendar-check"></i></span>
                                            <input type="date" class="form-control border-0 bg-light" name="dropoff_date" value="{{ $dropoff_date ?? date('Y-m-d', strtotime('+1 day')) }}" required>
                                            <input type="time" class="form-control border-0 bg-light" name="dropoff_time" value="{{ $dropoff_time ?? '09:00' }}" required>
                                        </div>
                                    </div>
                                </div>

                                <h5 class="fw-bold mb-4 pb-2 border-bottom">2. Pickup & Dropoff Location</h5>
                                <div class="row g-3 mb-5">
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold small text-muted text-uppercase">Pickup Location</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-0"><i class="bi bi-geo-alt"></i></span>
                                            <input type="text" class="form-control border-0 bg-light" name="pickup_location" value="{{ $pickup_location ?? 'Student Mall' }}" placeholder="Student Mall" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold small text-muted text-uppercase">Dropoff Location</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-0"><i class="bi bi-geo"></i></span>
                                            <input type="text" class="form-control border-0 bg-light" name="dropoff_location" value="{{ $dropoff_location ?? 'Student Mall' }}" placeholder="Student Mall" required>
                                        </div>
                                    </div>
                                </div>

                                <h5 class="fw-bold mb-4 pb-2 border-bottom">3. Billing Details</h5>
                                <div class="row g-3">
                                    <div class="col-md-12">
                                        <label class="form-label fw-semibold small text-muted text-uppercase">Full Name</label>
                                        <input type="text" class="form-control border-0 bg-light p-3" name="name" value="{{ session('auth_name') }}" placeholder="Enter your full name" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold small text-muted text-uppercase">Phone Number</label>
                                        <input type="tel" class="form-control border-0 bg-light p-3" name="phone" placeholder="e.g., 0123456789" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold small text-muted text-uppercase">City</label>
                                        <input type="text" class="form-control border-0 bg-light p-3" name="city" placeholder="e.g., Skudai" required>
                                    </div>
                                    <div class="col-md-12 mb-4">
                                        <label class="form-label fw-semibold small text-muted text-uppercase">Address</label>
                                        <textarea class="form-control border-0 bg-light p-3" name="address" rows="2" placeholder="Your residential college or full address" required></textarea>
                                    </div>
                                </div>

                                <div class="alert alert-info border-0 shadow-sm mb-4" style="background-color: #f0f7ff; color: #0056b3;">
                                    <div class="d-flex gap-3">
                                        <i class="bi bi-info-circle-fill h4 mb-0 mt-1"></i>
                                        <div>
                                            <h6 class="fw-bold mb-1">Deposit Payment Required</h6>
                                            <p class="small mb-0">Your booking will only be confirmed after you upload a RM50.00 deposit receipt on the next page.</p>
                                        </div>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-hasta btn-lg w-100 py-3 shadow-sm fw-bold mt-2">
                                    Review & Proceed to Payment <i class="bi bi-arrow-right ms-2"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- RIGHT: SUMMARY --}}
                <div class="col-lg-5 col-xl-4">
                    <div class="card border-0 shadow-soft sticky-top" style="top: 100px; border-radius: 20px;">
                        <div class="card-body p-4">
                            <h5 class="fw-bold mb-4">Summary</h5>
                            
                            {{-- CAR PREVIEW --}}
                            <div class="bg-light rounded-3 p-3 mb-4 text-center">
                                @if($car->image_url)
                                    <img src="{{ $car->image_url }}" alt="{{ $car->brand }}" class="img-fluid mb-2" style="max-height: 120px; object-fit: contain;">
                                @endif
                                <h6 class="fw-bold mb-1">{{ $car->brand }} {{ $car->model }}</h6>
                                <div class="small text-muted">{{ $car->plate_number }}</div>
                            </div>

                            {{-- PRICE BREAKDOWN --}}
                            <div class="mb-4">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted">Rate per Hour</span>
                                    <span class="fw-semibold text-dark">RM {{ number_format($car->base_rate_per_hour, 2) }}</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2" id="durationRow">
                                    <span class="text-muted">Rental Duration</span>
                                    <span class="fw-semibold text-dark" id="durationText">1 Hour</span>
                                </div>
                                <hr class="my-3 opacity-10">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="fw-bold h6 mb-0">Estimated Total</span>
                                    <span class="h4 fw-bold text-hasta mb-0" id="totalPrice">RM {{ number_format($car->base_rate_per_hour, 2) }}</span>
                                </div>
                            </div>

                            {{-- INCLUSIONS --}}
                            <div class="bg-light rounded-3 p-3 small">
                                <h6 class="fw-bold mb-3" style="font-size: 13px;">INCLUSIONS & POLICIES</h6>
                                <ul class="list-unstyled mb-0 d-grid gap-2">
                                    <li><i class="bi bi-check2 text-success me-2"></i> Standard Insurance</li>
                                    <li><i class="bi bi-check2 text-success me-2"></i> Road Tax Included</li>
                                    <li><i class="bi bi-check2 text-success me-2"></i> 24/7 Support</li>
                                    <li><i class="bi bi-info-circle text-warning me-2"></i> RM 50 Refundable Deposit</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('bookingForm');
        const pickupDate = form.querySelector('[name="pickup_date"]');
        const pickupTime = form.querySelector('[name="pickup_time"]');
        const dropoffDate = form.querySelector('[name="dropoff_date"]');
        const dropoffTime = form.querySelector('[name="dropoff_time"]');
        
        const durationText = document.getElementById('durationText');
        const totalPrice = document.getElementById('totalPrice');
        const baseRate = {{ floatval($car->base_rate_per_hour) }};

        function calculatePrice() {
            const start = new Date(pickupDate.value + 'T' + pickupTime.value);
            const end = new Date(dropoffDate.value + 'T' + dropoffTime.value);
            
            if (isNaN(start.getTime()) || isNaN(end.getTime())) return;

            const diffMs = end - start;
            let diffHours = Math.ceil(diffMs / (1000 * 60 * 60));
            
            if (diffHours < 1) diffHours = 1;

            durationText.textContent = diffHours + (diffHours === 1 ? ' Hour' : ' Hours');
            const total = diffHours * baseRate;
            totalPrice.textContent = 'RM ' + total.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
        }

        [pickupDate, pickupTime, dropoffDate, dropoffTime].forEach(el => {
            el.addEventListener('change', calculatePrice);
        });

        calculatePrice();
    });
</script>
@endpush
@endsection
