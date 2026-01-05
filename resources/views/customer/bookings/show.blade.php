@extends('layouts.app')
@section('title', 'Booking Details - Hasta GoRent')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
<style>
  /* Phase Stepper Styles */
  .phase-stepper {
    display: flex;
    justify-content: space-between;
    align-items: center;
    max-width: 800px;
    margin: 0 auto 40px auto;
    padding: 20px 0;
    position: relative;
  }
  
  .phase-stepper::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 0;
    right: 0;
    height: 4px;
    background: #e9ecef;
    transform: translateY(-50%);
    z-index: 1;
  }
  
  .phase-step {
    display: flex;
    flex-direction: column;
    align-items: center;
    position: relative;
    z-index: 2;
    flex: 1;
  }
  
  .phase-circle {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background: #e9ecef;
    border: 3px solid #e9ecef;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 18px;
    color: #6c757d;
    transition: all 0.3s ease;
    margin-bottom: 10px;
  }
  
  .phase-step.completed .phase-circle {
    background: var(--hasta);
    border-color: var(--hasta);
    color: #fff;
  }
  
  .phase-step.active .phase-circle {
    background: #fff;
    border-color: var(--hasta);
    color: var(--hasta);
    box-shadow: 0 0 0 4px rgba(203, 55, 55, 0.2);
  }
  
  .phase-step.completed .phase-circle i {
    font-size: 20px;
  }
  
  .phase-label {
    font-size: 12px;
    font-weight: 600;
    color: #6c757d;
    text-align: center;
    max-width: 100px;
  }
  
  .phase-step.completed .phase-label,
  .phase-step.active .phase-label {
    color: var(--hasta);
  }
  
  .phase-line {
    flex: 1;
    height: 4px;
    background: #e9ecef;
    position: relative;
    z-index: 1;
    margin: 0 -10px;
    margin-top: -35px;
  }
  
  .phase-line.completed {
    background: var(--hasta);
  }

  /* Phase Content Cards */
  .phase-card {
    border: none;
    border-radius: 16px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.08);
    margin-bottom: 24px;
    overflow: hidden;
  }
  
  .phase-card-header {
    background: linear-gradient(135deg, var(--hasta) 0%, #a92c2c 100%);
    color: #fff;
    padding: 20px 24px;
  }
  
  .phase-card-header h5 {
    margin: 0;
    font-weight: 700;
  }
  
  .phase-card-body {
    padding: 24px;
  }

  /* Upload Box Styles */
  .upload-box {
    border: 2px dashed #dee2e6;
    border-radius: 12px;
    padding: 30px;
    text-align: center;
    transition: all 0.3s ease;
    cursor: pointer;
    background: #f8f9fa;
  }
  
  .upload-box:hover {
    border-color: var(--hasta);
    background: #fff5f5;
  }
  
  .upload-box i {
    font-size: 48px;
    color: #adb5bd;
    margin-bottom: 15px;
  }
  
  .upload-box:hover i {
    color: var(--hasta);
  }

  /* Photo Gallery */
  .photo-gallery {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
    gap: 15px;
  }
  
  .photo-item {
    position: relative;
    border-radius: 12px;
    overflow: hidden;
    aspect-ratio: 1;
  }
  
  .photo-item img {
    width: 100%;
    height: 100%;
    object-fit: cover;
  }
  
  .photo-item .photo-badge {
    position: absolute;
    top: 8px;
    left: 8px;
    font-size: 10px;
    padding: 4px 8px;
  }

  /* Status Info Box */
  .status-info-box {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 20px;
  }
  
  .status-info-box.pending {
    background: linear-gradient(135deg, #fff3cd 0%, #ffeeba 100%);
  }
  
  .status-info-box.success {
    background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
  }
  
  .status-info-box.info {
    background: linear-gradient(135deg, #d1ecf1 0%, #bee5eb 100%);
  }

  @media (max-width: 768px) {
    .phase-stepper {
      flex-wrap: wrap;
      gap: 20px;
    }
    .phase-line {
      display: none;
    }
    .phase-circle {
      width: 40px;
      height: 40px;
      font-size: 14px;
    }
    .phase-label {
      font-size: 10px;
    }
  }
</style>
@endpush

@section('content')
@php
  $currentPhase = $booking->getCurrentPhase();
  $phase1Complete = $booking->isPhase1Complete();
  $paymentVerified = $booking->isPaymentVerified();
  $phase2Complete = $booking->isPhase2Complete();
  $phase3Complete = $booking->isPhase3Complete();
  $phase4Complete = $booking->isPhase4Complete();
  $phase5Complete = $booking->deposit_decision !== null || $booking->status === 'deposit_returned';
  
  // Check if cancellation is allowed (24 hours before pickup)
  $canCancel = false;
  $hoursUntilPickup = null;
  if ($booking->start_datetime) {
    $hoursUntilPickup = now()->diffInHours($booking->start_datetime, false);
    $canCancel = $hoursUntilPickup >= 24;
  }
@endphp

{{-- MOBILE BACK BUTTON --}}
<div class="d-md-none" style="background: var(--hasta-darker); padding: 12px 16px; margin-bottom: 0;">
  <a href="{{ route('customer.bookings') }}" class="text-white text-decoration-none d-inline-flex align-items-center gap-2" style="font-size: 14px; font-weight: 500;">
    <i class="bi bi-arrow-left"></i> Back to My Bookings
  </a>
</div>

<div class="container-fluid px-4 px-md-5" style="padding-top: 32px; padding-bottom: 48px;">
  {{-- Header --}}
  <div class="mb-4">
    <a href="{{ route('customer.bookings') }}" class="text-decoration-none small d-none d-md-inline">&larr; Back to My Bookings</a>
    <h1 class="h3 fw-bold mt-2 mb-1" style="color:#333;">Booking #{{ $booking->booking_id }}</h1>
    <p class="text-muted mb-0">{{ $booking->car->brand ?? '' }} {{ $booking->car->model ?? '' }} | {{ $booking->start_datetime?->format('d M Y') }} - {{ $booking->end_datetime?->format('d M Y') }}</p>
  </div>

  {{-- Alerts --}}
  @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      {{ session('success') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  @endif

  @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
      {{ session('error') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  @endif

  {{-- ========== PHASE STEPPER UI ========== --}}
  <div class="card phase-card mb-4">
    <div class="phase-card-body">
      <div class="phase-stepper">
        {{-- Phase 1: Payment --}}
        {{-- Phase 1 is active if payment is not yet submitted --}}
        <div class="phase-step {{ $phase1Complete ? 'completed' : 'active' }}">
          <div class="phase-circle">
            @if($phase1Complete)
              <i class="bi bi-check-lg"></i>
            @else
              1
            @endif
          </div>
          <div class="phase-label">Payment</div>
        </div>
        
        <div class="phase-line {{ $phase1Complete ? 'completed' : '' }}"></div>
        
        {{-- Phase 2: Verification --}}
        {{-- Phase 2 is active when Phase 1 is complete but Phase 2 is not yet complete --}}
        <div class="phase-step {{ $phase2Complete ? 'completed' : ($phase1Complete ? 'active' : '') }}">
          <div class="phase-circle">
            @if($phase2Complete)
              <i class="bi bi-check-lg"></i>
            @else
              2
            @endif
          </div>
          <div class="phase-label">Verification</div>
        </div>
        
        <div class="phase-line {{ $phase2Complete ? 'completed' : '' }}"></div>
        
        {{-- Phase 3: Pickup --}}
        {{-- Phase 3 is active when Phase 2 is complete but Phase 3 is not yet complete --}}
        <div class="phase-step {{ $phase3Complete ? 'completed' : ($phase2Complete ? 'active' : '') }}">
          <div class="phase-circle">
            @if($phase3Complete)
              <i class="bi bi-check-lg"></i>
            @else
              3
            @endif
          </div>
          <div class="phase-label">Pickup & Agreement</div>
        </div>
        
        <div class="phase-line {{ $phase3Complete ? 'completed' : '' }}"></div>
        
        {{-- Phase 4: Return --}}
        {{-- Phase 4 is active when Phase 3 is complete but Phase 4 is not yet complete --}}
        <div class="phase-step {{ $phase4Complete ? 'completed' : ($phase3Complete ? 'active' : '') }}">
          <div class="phase-circle">
            @if($phase4Complete)
              <i class="bi bi-check-lg"></i>
            @else
              4
            @endif
          </div>
          <div class="phase-label">Return</div>
        </div>
        
        <div class="phase-line {{ $phase4Complete ? 'completed' : '' }}"></div>
        
        {{-- Phase 5: Deposit Returned --}}
        {{-- Phase 5 is active when Phase 4 is complete but Phase 5 is not yet complete --}}
        <div class="phase-step {{ $phase5Complete ? 'completed' : ($phase4Complete ? 'active' : '') }}">
          <div class="phase-circle">
            @if($phase5Complete)
              <i class="bi bi-check-lg"></i>
            @else
              5
            @endif
          </div>
          <div class="phase-label">Deposit Returned</div>
        </div>
      </div>
    </div>
  </div>

  <div class="row g-4">
    {{-- ========== LEFT COLUMN: PHASE CONTENT ========== --}}
    <div class="col-lg-8">
      
      {{-- ========== PHASE 1: PAYMENT ========== --}}
      <div class="card phase-card">
        <div class="phase-card-header d-flex justify-content-between align-items-center">
          <h5><i class="bi bi-credit-card me-2"></i> Phase 1: Payment</h5>
          @if($phase1Complete)
            <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i> Submitted</span>
          @else
            <span class="badge bg-warning text-dark"><i class="bi bi-clock me-1"></i> Pending</span>
          @endif
        </div>
        <div class="phase-card-body">
          @if($phase1Complete)
            {{-- Payment has been submitted --}}
            <div class="status-info-box success">
              <div class="d-flex align-items-center">
                <i class="bi bi-check-circle-fill text-success me-3" style="font-size: 24px;"></i>
                <div>
                  <strong>Payment Submitted</strong>
                  <p class="mb-0 small text-muted">Your deposit payment receipt has been uploaded successfully.</p>
                </div>
              </div>
            </div>
          @else
            {{-- No payment yet --}}
            <div class="status-info-box pending">
              <div class="d-flex align-items-center">
                <i class="bi bi-exclamation-circle-fill text-warning me-3" style="font-size: 24px;"></i>
                <div>
                  <strong>Payment Required</strong>
                  <p class="mb-0 small">Please upload your deposit payment receipt to proceed with your booking.</p>
                </div>
              </div>
            </div>
            
            {{-- Payment Upload Form --}}
            <form method="POST" action="{{ route('customer.bookings.receipt.upload', $booking->booking_id) }}" enctype="multipart/form-data" class="mt-4">
              @csrf
              <div class="row g-3">
                <div class="col-md-6">
                  <label class="form-label">Payment Type <span class="text-danger">*</span></label>
                  <select name="payment_type" class="form-select" required>
                    <option value="deposit">Deposit</option>
                    <option value="rental">Rental Payment</option>
                  </select>
                </div>
                <div class="col-md-6">
                  <label class="form-label">Amount (RM) <span class="text-danger">*</span></label>
                  <input type="number" name="amount" step="0.01" min="0" class="form-control" value="{{ $booking->deposit_amount ?? '' }}" required>
                </div>
                <div class="col-md-6">
                  <label class="form-label">Payment Method <span class="text-danger">*</span></label>
                  <select name="payment_method" class="form-select" required>
                    <option value="bank_transfer">Bank Transfer</option>
                    <option value="cash">Cash</option>
                    <option value="other">Other</option>
                  </select>
                </div>
                <div class="col-md-6">
                  <label class="form-label">Payment Date <span class="text-danger">*</span></label>
                  <input type="date" name="payment_date" class="form-control" value="{{ now()->format('Y-m-d') }}" required>
                </div>
                <div class="col-12">
                  <label class="form-label">Receipt/Proof <span class="text-danger">*</span></label>
                  <input type="file" name="receipt" class="form-control" accept="image/*,.pdf" required>
                  <small class="text-muted">Upload payment receipt (image or PDF, max 5MB)</small>
                </div>
                <div class="col-12">
                  <button type="submit" class="btn btn-hasta"><i class="bi bi-upload me-2"></i> Upload Receipt</button>
                </div>
              </div>
            </form>
          @endif

          {{-- Payment History --}}
          @if($booking->payments && $booking->payments->count() > 0)
            <h6 class="fw-bold mt-4 mb-3">Payment History</h6>
            <div class="table-responsive">
              <table class="table table-sm align-middle mb-0">
                <thead class="table-light">
                  <tr>
                    <th>Type</th>
                    <th>Amount</th>
                    <th>Method</th>
                    <th>Date</th>
                    <th>Status</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($booking->payments as $payment)
                    <tr>
                      <td>{{ ucfirst($payment->payment_type) }}</td>
                      <td><strong>RM {{ number_format($payment->amount, 2) }}</strong></td>
                      <td>{{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}</td>
                      <td>{{ $payment->payment_date->format('d M Y') }}</td>
                      <td>
                        <span class="badge {{ $payment->status === 'verified' ? 'bg-success' : 'bg-warning' }}">
                          {{ ucfirst($payment->status) }}
                        </span>
                      </td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          @endif
        </div>
      </div>

      {{-- ========== PHASE 2: VERIFICATION ========== --}}
      <div class="card phase-card">
        <div class="phase-card-header d-flex justify-content-between align-items-center">
          <h5><i class="bi bi-shield-check me-2"></i> Phase 2: Verification</h5>
          @if($phase2Complete)
            <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i> Verified</span>
          @elseif($phase1Complete)
            <span class="badge bg-info"><i class="bi bi-hourglass-split me-1"></i> In Progress</span>
          @else
            <span class="badge bg-secondary"><i class="bi bi-lock me-1"></i> Locked</span>
          @endif
        </div>
        <div class="phase-card-body">
          @if($phase2Complete)
            <div class="status-info-box success">
              <div class="d-flex align-items-center">
                <i class="bi bi-patch-check-fill text-success me-3" style="font-size: 24px;"></i>
                <div>
                  <strong>Booking Confirmed</strong>
                  <p class="mb-0 small text-muted">Your booking has been verified by Hasta staff. Please arrive on time for pickup.</p>
                </div>
              </div>
            </div>
            <div class="row g-3 mt-2">
              <div class="col-md-6">
                <div class="p-3 bg-light rounded">
                  <div class="small text-muted">Pickup Date & Time</div>
                  <div class="fw-bold">{{ $booking->start_datetime?->format('d M Y, H:i') }}</div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="p-3 bg-light rounded">
                  <div class="small text-muted">Pickup Location</div>
                  <div class="fw-bold">{{ $booking->pickupLocation->name ?? 'N/A' }}</div>
                </div>
              </div>
            </div>
          @elseif($phase1Complete)
            <div class="status-info-box info">
              <div class="d-flex align-items-center">
                <i class="bi bi-hourglass-split text-info me-3" style="font-size: 24px;"></i>
                <div>
                  <strong>Awaiting Verification</strong>
                  <p class="mb-0 small">Your payment receipt has been submitted. Hasta staff is reviewing your booking and will verify it shortly.</p>
                </div>
              </div>
            </div>
            
            {{-- Show booking summary while waiting --}}
            <div class="mt-4">
              <h6 class="fw-bold mb-3"><i class="bi bi-info-circle me-2"></i> Your Booking Details</h6>
              <div class="row g-3">
                <div class="col-md-6">
                  <div class="p-3 bg-light rounded">
                    <div class="small text-muted">Pickup Date & Time</div>
                    <div class="fw-bold">{{ $booking->start_datetime?->format('d M Y, H:i') }}</div>
                    @if($booking->pickupLocation)
                      <div class="small text-muted mt-1"><i class="bi bi-geo-alt me-1"></i> {{ $booking->pickupLocation->name }}</div>
                    @endif
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="p-3 bg-light rounded">
                    <div class="small text-muted">Return Date & Time</div>
                    <div class="fw-bold">{{ $booking->end_datetime?->format('d M Y, H:i') }}</div>
                    @if($booking->dropoffLocation)
                      <div class="small text-muted mt-1"><i class="bi bi-geo-alt me-1"></i> {{ $booking->dropoffLocation->name }}</div>
                    @endif
                  </div>
                </div>
              </div>
              
              <div class="alert alert-info mt-3 mb-0">
                <div class="d-flex">
                  <i class="bi bi-lightbulb me-2"></i>
                  <div>
                    <strong>What happens next?</strong>
                    <ul class="mb-0 mt-2 small">
                      <li>Hasta staff will verify your payment within 24 hours</li>
                      <li>Once verified, you'll receive a confirmation notification</li>
                      <li>Arrive at the pickup location on time with your ID</li>
                    </ul>
                  </div>
                </div>
              </div>
            </div>
          @else
            <div class="status-info-box">
              <div class="d-flex align-items-center">
                <i class="bi bi-lock-fill text-secondary me-3" style="font-size: 24px;"></i>
                <div>
                  <strong>Complete Phase 1 First</strong>
                  <p class="mb-0 small text-muted">Please complete your payment to unlock verification.</p>
                </div>
              </div>
            </div>
          @endif
        </div>
      </div>

      {{-- ========== PHASE 3: PICKUP & AGREEMENT ========== --}}
      <div class="card phase-card">
        <div class="phase-card-header d-flex justify-content-between align-items-center">
          <h5><i class="bi bi-pen me-2"></i> Phase 3: Pickup & Agreement</h5>
          @if($phase3Complete || $phase4Complete)
            <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i> Complete</span>
          @elseif($phase2Complete)
            <span class="badge bg-warning text-dark"><i class="bi bi-clock me-1"></i> Action Required</span>
          @else
            <span class="badge bg-secondary"><i class="bi bi-lock me-1"></i> Locked</span>
          @endif
        </div>
        <div class="phase-card-body">
          @if($phase3Complete || $phase4Complete || $phase2Complete)
            @if($phase3Complete || $phase4Complete)
              {{-- Show completion message if Phase 3 or 4 is complete --}}
              <div class="status-info-box success mb-3">
                <div class="d-flex align-items-center">
                  <i class="bi bi-check-circle-fill text-success me-3" style="font-size: 24px;"></i>
                  <div>
                    <strong>Phase 3 Complete</strong>
                    <p class="mb-0 small text-muted">You have successfully completed the pickup process.</p>
                  </div>
                </div>
              </div>
              
              {{-- Show uploaded photos --}}
              @php
                $agreementPhoto = $booking->rentalPhotos->where('photo_type', 'agreement')->first();
                $keysPhoto = $booking->rentalPhotos->where('photo_type', 'pickup')->first();
              @endphp
              @if($agreementPhoto || $keysPhoto)
                <div class="row g-3 mb-3">
                  @if($agreementPhoto)
                    <div class="col-md-6">
                      <label class="form-label fw-bold"><i class="bi bi-file-image me-2"></i>Signed Agreement Photo</label>
                      <div>
                        <img src="{{ asset('storage/' . $agreementPhoto->photo_url) }}" alt="Signed Agreement" class="img-fluid rounded mb-2 clickable-photo" style="max-height: 150px; width: 100%; object-fit: cover; cursor: pointer;" data-url="{{ asset('storage/' . $agreementPhoto->photo_url) }}">
                        <p class="small text-success mb-0"><i class="bi bi-check-circle me-1"></i>Uploaded on {{ $agreementPhoto->taken_at->format('d/m/Y') }}</p>
                      </div>
                    </div>
                  @endif
                  @if($keysPhoto)
                    <div class="col-md-6">
                      <label class="form-label fw-bold"><i class="bi bi-key me-2"></i>Receiving Car Keys Photo</label>
                      <div>
                        <img src="{{ asset('storage/' . $keysPhoto->photo_url) }}" alt="Receiving Keys" class="img-fluid rounded mb-2 clickable-photo" style="max-height: 150px; width: 100%; object-fit: cover; cursor: pointer;" data-url="{{ asset('storage/' . $keysPhoto->photo_url) }}">
                        <p class="small text-success mb-0"><i class="bi bi-check-circle me-1"></i>Uploaded on {{ $keysPhoto->taken_at->format('d/m/Y') }}</p>
                      </div>
                    </div>
                  @endif
                </div>
              @endif
            @elseif($booking->agreement_signed_at)
              <div class="status-info-box success mb-3">
                <div class="d-flex align-items-center">
                  <i class="bi bi-file-earmark-check-fill text-success me-3" style="font-size: 24px;"></i>
                  <div>
                    <strong>Agreement Signed</strong>
                    <p class="mb-0 small text-muted">Signed on {{ $booking->agreement_signed_at->format('d M Y, H:i') }}</p>
                  </div>
                </div>
              </div>
            @elseif(!$phase3Complete && !$phase4Complete)
              <div class="status-info-box pending mb-3">
                <div class="d-flex align-items-center">
                  <i class="bi bi-pen-fill text-warning me-3" style="font-size: 24px;"></i>
                  <div>
                    <strong>Digital Agreement Required</strong>
                    <p class="mb-0 small">Please sign the rental agreement when you meet the Hasta staff at pickup.</p>
                  </div>
                </div>
              </div>
              
              {{-- Sign Agreement Button --}}
              <div class="mb-4">
                <div class="p-4 bg-light rounded border">
                  <h6 class="fw-bold mb-3"><i class="bi bi-file-text me-2"></i> Rental Agreement</h6>
                  <div class="small mb-3" style="max-height: 200px; overflow-y: auto;">
                    <p><strong>Terms and Conditions:</strong></p>
                    <ol>
                      <li>The renter agrees to return the vehicle in the same condition as received.</li>
                      <li>The renter is responsible for any damage, traffic violations, or fines during the rental period.</li>
                      <li>The vehicle must be returned with the same fuel level as pickup.</li>
                      <li>Late returns will incur additional charges as per the hourly rate.</li>
                      <li>The deposit will be refunded after vehicle inspection upon return.</li>
                      <li>Smoking and pets are not allowed in the vehicle.</li>
                      <li>The renter must have a valid driving license at all times.</li>
                    </ol>
                  </div>
                  <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" id="agreeTerms">
                    <label class="form-check-label" for="agreeTerms">
                      I have read and agree to the rental terms and conditions
                    </label>
                  </div>
                  <a href="{{ route('customer.bookings.download-agreement', $booking->booking_id) }}" class="btn btn-hasta" id="downloadAgreementBtn" onclick="return checkAgreementTerms()">
                    <i class="bi bi-download me-2"></i> Sign Agreement Digitally
                  </a>
                </div>
              </div>
            @endif

            {{-- Pickup Photo Upload -- Combined --}}
            <h6 class="fw-bold mb-3">Upload Pickup Photos</h6>
            <p class="small text-muted mb-3">Upload both photos: 1) The signed agreement, 2) You receiving the car keys</p>
            
            @php
              $agreementPhoto = $booking->rentalPhotos->where('photo_type', 'agreement')->first();
              $keysPhoto = $booking->rentalPhotos->where('photo_type', 'pickup')->first();
              $bothPhotosUploaded = $agreementPhoto && $keysPhoto;
            @endphp
            
            <div class="card border mb-4">
              <div class="card-body">
                @if($bothPhotosUploaded)
                  <div class="alert alert-success mb-0">
                    <i class="bi bi-check-circle me-2"></i><strong>Photos uploaded!</strong> Phase 3 is complete. You can proceed to Phase 4.
                  </div>
                @else
                  <div class="alert alert-info mb-3">
                    <i class="bi bi-info-circle me-2"></i>Please upload both photos to complete Phase 3 and proceed to Phase 4.
                  </div>
                  
                  <form method="POST" action="{{ route('customer.bookings.photos.upload', $booking->booking_id) }}" enctype="multipart/form-data" id="pickupPhotosForm">
                  @csrf
                  <input type="hidden" name="taken_at" value="{{ now()->format('Y-m-d') }}">
                  
                  <div class="row g-3" id="pickupPhotosInputContainer">
                    {{-- Signed Agreement Photo --}}
                    <div class="col-md-6">
                      <label class="form-label fw-bold"><i class="bi bi-file-earmark-check me-2"></i>Signed Agreement Photo <span class="text-danger">*</span></label>
                      @if($agreementPhoto)
                      <div class="mb-2">
                        <img src="{{ asset('storage/' . $agreementPhoto->photo_url) }}" alt="Signed Agreement" class="img-fluid rounded mb-2 clickable-photo" style="max-height: 150px; width: 100%; object-fit: cover; cursor: pointer;" data-url="{{ asset('storage/' . $agreementPhoto->photo_url) }}">
                        <p class="small text-success mb-0"><i class="bi bi-check-circle me-1"></i>Uploaded on {{ $agreementPhoto->taken_at->format('d/m/Y') }}</p>
                      </div>
                      @endif
                      <input type="file" name="agreement_photo" id="agreementPhoto" class="form-control" accept="image/*" {{ $bothPhotosUploaded ? '' : 'required' }}>
                      <small class="text-muted">Upload photo of the signed agreement</small>
                    </div>

                    {{-- Receiving Keys Photo --}}
                    <div class="col-md-6">
                      <label class="form-label fw-bold"><i class="bi bi-key me-2"></i>Receiving Car Keys Photo <span class="text-danger">*</span></label>
                      @if($keysPhoto)
                      <div class="mb-2">
                        <img src="{{ asset('storage/' . $keysPhoto->photo_url) }}" alt="Receiving Keys" class="img-fluid rounded mb-2 clickable-photo" style="max-height: 150px; width: 100%; object-fit: cover; cursor: pointer;" data-url="{{ asset('storage/' . $keysPhoto->photo_url) }}">
                        <p class="small text-success mb-0"><i class="bi bi-check-circle me-1"></i>Uploaded on {{ $keysPhoto->taken_at->format('d/m/Y') }}</p>
                      </div>
                      @endif
                      <input type="file" name="keys_photo" id="keysPhoto" class="form-control" accept="image/*" {{ $bothPhotosUploaded ? '' : 'required' }}>
                      <small class="text-muted">Upload photo of you receiving the car keys</small>
                    </div>
                  </div>
                  
                  <div class="mt-3" id="pickupPhotosButtonContainer">
                    <button type="submit" class="btn btn-hasta w-100">
                      <i class="bi bi-upload me-2"></i>
                      @if($bothPhotosUploaded)
                        Update Photos
                      @else
                        Upload Both Photos
                      @endif
                    </button>
                  </div>
                  
                  @if(!$bothPhotosUploaded)
                    <div class="mt-2 text-center" id="pickupPhotosWarning">
                      <small class="text-muted">
                        @if($agreementPhoto && !$keysPhoto)
                          <i class="bi bi-exclamation-triangle text-warning me-1"></i>Missing: Receiving Keys photo
                        @elseif(!$agreementPhoto && $keysPhoto)
                          <i class="bi bi-exclamation-triangle text-warning me-1"></i>Missing: Signed Agreement photo
                        @else
                          <i class="bi bi-exclamation-triangle text-warning me-1"></i>Both photos required to proceed
                        @endif
                      </small>
                    </div>
                  @endif
                  </form>
                @endif
              </div>
            </div>

          @else
            <div class="status-info-box">
              <div class="d-flex align-items-center">
                <i class="bi bi-lock-fill text-secondary me-3" style="font-size: 24px;"></i>
                <div>
                  <strong>Complete Previous Phases First</strong>
                  <p class="mb-0 small text-muted">This phase will unlock after your booking is verified.</p>
                </div>
              </div>
            </div>
          @endif
        </div>
      </div>

      {{-- ========== PHASE 4: RETURN ========== --}}
      <div class="card phase-card">
        <div class="phase-card-header d-flex justify-content-between align-items-center">
          <h5><i class="bi bi-car-front me-2"></i> Phase 4: Return</h5>
          @if($phase4Complete)
            <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i> Complete</span>
          @elseif($phase3Complete || $booking->status === 'active')
            <span class="badge bg-warning text-dark"><i class="bi bi-clock me-1"></i> Pending Return</span>
          @else
            <span class="badge bg-secondary"><i class="bi bi-lock me-1"></i> Locked</span>
          @endif
        </div>
        <div class="phase-card-body">
          @if($phase3Complete || in_array($booking->status, ['active', 'completed']))
            @if($booking->status === 'completed')
              <div class="status-info-box success mb-3">
                <div class="d-flex align-items-center">
                  <i class="bi bi-trophy-fill text-success me-3" style="font-size: 24px;"></i>
                  <div>
                    <strong>Rental Complete!</strong>
                    <p class="mb-0 small text-muted">Thank you for using Hasta GoRent. We hope you had a great experience!</p>
                  </div>
                </div>
              </div>
            @else
              <div class="status-info-box info mb-3">
                <div class="d-flex align-items-center">
                  <i class="bi bi-info-circle-fill text-info me-3" style="font-size: 24px;"></i>
                  <div>
                    <strong>Return Instructions</strong>
                    <p class="mb-0 small">When returning the car, take photos of: 1) Keys in the car, 2) Where you parked the car</p>
                  </div>
                </div>
              </div>
            @endif

            {{-- Return Photo Upload -- Combined --}}
            @if($phase3Complete || $booking->status === 'active')
              <h6 class="fw-bold mb-3">Upload Return Photos</h6>
              <p class="small text-muted mb-3">Upload both photos: 1) Keys in the car, 2) Where you parked the car</p>
              
              @php
                $keysInCarPhoto = $booking->rentalPhotos->where('photo_type', 'key')->first();
                $parkingPhoto = $booking->rentalPhotos->where('photo_type', 'parking')->first();
                $bothReturnPhotosUploaded = $keysInCarPhoto && $parkingPhoto;
              @endphp
              
              <div class="card border mb-4">
                <div class="card-body">
                  @if($bothReturnPhotosUploaded)
                    <div class="alert alert-success mb-0">
                      <i class="bi bi-check-circle me-2"></i><strong>Photos uploaded!</strong> Your booking return process is complete.
                    </div>
                  @else
                    <div class="alert alert-info mb-3">
                      <i class="bi bi-info-circle me-2"></i>Please upload both photos to complete the return process.
                    </div>
                    
                    <form method="POST" action="{{ route('customer.bookings.photos.upload', $booking->booking_id) }}" enctype="multipart/form-data" id="returnPhotosForm">
                    @csrf
                    <input type="hidden" name="taken_at" value="{{ now()->format('Y-m-d') }}">
                    
                    <div class="row g-3" id="returnPhotosInputContainer">
                      {{-- Keys in Car Photo --}}
                      <div class="col-md-6">
                        <label class="form-label fw-bold"><i class="bi bi-key me-2"></i>Keys in Car Photo <span class="text-danger">*</span></label>
                        @if($keysInCarPhoto)
                          <div class="mb-2">
                        <img src="{{ asset('storage/' . $keysInCarPhoto->photo_url) }}" alt="Keys in Car" class="img-fluid rounded mb-2 clickable-photo" style="max-height: 150px; width: 100%; object-fit: cover; cursor: pointer;" data-url="{{ asset('storage/' . $keysInCarPhoto->photo_url) }}">
                            <p class="small text-success mb-0"><i class="bi bi-check-circle me-1"></i>Uploaded on {{ $keysInCarPhoto->taken_at->format('d/m/Y') }}</p>
                          </div>
                        @endif
                        <input type="file" name="keys_in_car_photo" id="keysInCarPhoto" class="form-control" accept="image/*" {{ $bothReturnPhotosUploaded ? '' : 'required' }}>
                        <small class="text-muted">Upload photo of keys left in the car</small>
                      </div>

                      {{-- Parking Location Photo --}}
                      <div class="col-md-6">
                        <label class="form-label fw-bold"><i class="bi bi-geo-alt me-2"></i>Parking Location Photo <span class="text-danger">*</span></label>
                        @if($parkingPhoto)
                          <div class="mb-2">
                        <img src="{{ asset('storage/' . $parkingPhoto->photo_url) }}" alt="Parking Location" class="img-fluid rounded mb-2 clickable-photo" style="max-height: 150px; width: 100%; object-fit: cover; cursor: pointer;" data-url="{{ asset('storage/' . $parkingPhoto->photo_url) }}">
                            <p class="small text-success mb-0"><i class="bi bi-check-circle me-1"></i>Uploaded on {{ $parkingPhoto->taken_at->format('d/m/Y') }}</p>
                          </div>
                        @endif
                        <input type="file" name="parking_location_photo" id="parkingLocationPhoto" class="form-control" accept="image/*" {{ $bothReturnPhotosUploaded ? '' : 'required' }}>
                        <small class="text-muted">Upload photo of where you parked the car</small>
                      </div>
                    </div>
                    
                    <div class="mt-3" id="returnPhotosButtonContainer">
                      <button type="submit" class="btn btn-hasta w-100">
                        <i class="bi bi-upload me-2"></i>
                        @if($bothReturnPhotosUploaded)
                          Update Photos
                        @else
                          Upload Both Photos
                        @endif
                      </button>
                    </div>
                    
                    @if(!$bothReturnPhotosUploaded)
                      <div class="mt-2 text-center" id="returnPhotosWarning">
                        <small class="text-muted">
                          @if($keysInCarPhoto && !$parkingPhoto)
                            <i class="bi bi-exclamation-triangle text-warning me-1"></i>Missing: Parking Location photo
                          @elseif(!$keysInCarPhoto && $parkingPhoto)
                            <i class="bi bi-exclamation-triangle text-warning me-1"></i>Missing: Keys in Car photo
                          @else
                            <i class="bi bi-exclamation-triangle text-warning me-1"></i>Both photos required to complete return
                          @endif
                        </small>
                      </div>
                    @endif
                    </form>
                  @endif
                </div>
              </div>
            @endif

          @else
            <div class="status-info-box">
              <div class="d-flex align-items-center">
                <i class="bi bi-lock-fill text-secondary me-3" style="font-size: 24px;"></i>
                <div>
                  <strong>Complete Previous Phases First</strong>
                  <p class="mb-0 small text-muted">This phase will unlock after you complete Phase 3 (upload pickup photos).</p>
                </div>
              </div>
            </div>
          @endif
        </div>
      </div>

      {{-- ========== PHASE 5: DEPOSIT RETURNED ========== --}}
      <div class="card phase-card">
        <div class="phase-card-header d-flex justify-content-between align-items-center">
          <h5><i class="bi bi-wallet2 me-2"></i> Phase 5: Deposit Returned</h5>
          @if($phase5Complete)
            <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i> Complete</span>
          @elseif($phase4Complete)
            <span class="badge bg-info"><i class="bi bi-hourglass-split me-1"></i> In Progress</span>
          @else
            <span class="badge bg-secondary"><i class="bi bi-lock me-1"></i> Locked</span>
          @endif
        </div>
        <div class="phase-card-body">
          @if($phase5Complete)
            <div class="status-info-box success">
              <div class="d-flex align-items-center">
                <i class="bi bi-wallet2 text-success me-3" style="font-size: 24px;"></i>
                <div>
                  <strong>Deposit Returned</strong>
                  <p class="mb-0 small text-muted">
                    @if($booking->deposit_decision === 'refund')
                      Your deposit of <strong>RM {{ number_format($booking->deposit_refund_amount ?? $booking->deposit_amount ?? 0, 2) }}</strong> has been refunded.
                    @elseif($booking->deposit_decision === 'carry_forward')
                      Your deposit has been carried forward to your next booking.
                    @elseif($booking->deposit_decision === 'burn')
                      Your deposit has been applied to cover penalties or damages.
                    @else
                      Your deposit has been processed by Hasta staff.
                    @endif
                  </p>
                </div>
              </div>
            </div>
            
            @if($booking->deposit_decision === 'refund' && $booking->deposit_refund_amount)
              <div class="row g-3 mt-3">
                <div class="col-md-6">
                  <div class="p-3 bg-light rounded">
                    <div class="small text-muted">Refund Amount</div>
                    <div class="fw-bold text-success">RM {{ number_format($booking->deposit_refund_amount, 2) }}</div>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="p-3 bg-light rounded">
                    <div class="small text-muted">Status</div>
                    <div class="fw-bold">Processed</div>
                  </div>
                </div>
              </div>
            @endif
          @elseif($phase4Complete)
            <div class="status-info-box info">
              <div class="d-flex align-items-center">
                <i class="bi bi-hourglass-split text-info me-3" style="font-size: 24px;"></i>
                <div>
                  <strong>Awaiting Deposit Processing</strong>
                  <p class="mb-0 small">Hasta staff is processing your deposit return. You will be notified once it's completed.</p>
                </div>
              </div>
            </div>
          @else
            <div class="status-info-box">
              <div class="d-flex align-items-center">
                <i class="bi bi-lock-fill text-secondary me-3" style="font-size: 24px;"></i>
                <div>
                  <strong>Complete Previous Phases First</strong>
                  <p class="mb-0 small text-muted">This phase will unlock after you complete Phase 4 (return the car).</p>
                </div>
              </div>
            </div>
          @endif
        </div>
      </div>

      {{-- PENALTIES (if any) --}}
      @if($booking->penalties && $booking->penalties->count() > 0)
      <div class="card phase-card">
        <div class="phase-card-header bg-danger">
          <h5><i class="bi bi-exclamation-triangle me-2"></i> Penalties</h5>
        </div>
        <div class="phase-card-body">
          @php
            $unpaidPenalties = $booking->penalties->where('status', '!=', 'settled');
            $totalUnpaid = $unpaidPenalties->sum('amount');
          @endphp
          
          @if($unpaidPenalties->count() > 0)
            <div class="alert alert-danger mb-4">
              <div class="d-flex align-items-center">
                <i class="bi bi-exclamation-triangle-fill me-3" style="font-size: 24px;"></i>
                <div>
                  <strong>Outstanding Penalties</strong>
                  <p class="mb-0">You have <strong>{{ $unpaidPenalties->count() }}</strong> unpaid penalty(ies) totaling <strong>RM {{ number_format($totalUnpaid, 2) }}</strong>. Please settle these penalties below.</p>
                </div>
              </div>
            </div>
          @endif
          
          <div class="table-responsive mb-4">
            <table class="table table-sm align-middle mb-0">
              <thead class="table-light">
                <tr>
                  <th>Type</th>
                  <th>Amount</th>
                  <th>Status</th>
                  <th>Description</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                @foreach($booking->penalties as $penalty)
                  @php
                    $paidAmount = $penalty->payments ? $penalty->payments->where('status', 'verified')->sum('amount') : 0;
                    $remainingAmount = max(0, $penalty->amount - $paidAmount);
                    $isSettled = $penalty->status === 'settled' || $remainingAmount <= 0;
                  @endphp
                  <tr>
                    <td>{{ ucfirst($penalty->penalty_type) }}</td>
                    <td>
                      <strong class="text-danger">RM {{ number_format($penalty->amount, 2) }}</strong>
                      @if($penalty->is_installment && !$isSettled)
                        <br><small class="text-muted">Installment plan available</small>
                      @endif
                    </td>
                    <td>
                      <span class="badge {{ $isSettled ? 'bg-success' : 'bg-danger' }}">
                        {{ $isSettled ? 'Settled' : ucfirst(str_replace('_', ' ', $penalty->status)) }}
                      </span>
                      @if(!$isSettled && $paidAmount > 0)
                        <br><small class="text-muted">Paid: RM {{ number_format($paidAmount, 2) }}</small>
                      @endif
                    </td>
                    <td>{{ $penalty->description ?? '-' }}</td>
                    <td>
                      @if(!$isSettled)
                        <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#penaltyPaymentModal{{ $penalty->penalty_id }}">
                          <i class="bi bi-credit-card me-1"></i>Pay Now
                        </button>
                      @else
                        <span class="text-success"><i class="bi bi-check-circle me-1"></i>Paid</span>
                      @endif
                    </td>
                  </tr>
                  
                  {{-- Penalty Payment Modal --}}
                  @if(!$isSettled)
                  <div class="modal fade" id="penaltyPaymentModal{{ $penalty->penalty_id }}" tabindex="-1" aria-labelledby="penaltyPaymentModalLabel{{ $penalty->penalty_id }}" aria-hidden="true">
                    <div class="modal-dialog">
                      <div class="modal-content">
                        <div class="modal-header bg-danger text-white">
                          <h5 class="modal-title" id="penaltyPaymentModalLabel{{ $penalty->penalty_id }}">
                            <i class="bi bi-exclamation-triangle me-2"></i>Pay Penalty
                          </h5>
                          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form method="POST" action="{{ route('customer.bookings.penalty.pay', $booking->booking_id) }}" enctype="multipart/form-data">
                          @csrf
                          <input type="hidden" name="penalty_id" value="{{ $penalty->penalty_id }}">
                          <div class="modal-body">
                            <div class="alert alert-info">
                              <strong>Penalty Details:</strong><br>
                              Type: {{ ucfirst($penalty->penalty_type) }}<br>
                              Total Amount: <strong>RM {{ number_format($penalty->amount, 2) }}</strong><br>
                              @if($paidAmount > 0)
                                Already Paid: RM {{ number_format($paidAmount, 2) }}<br>
                              @endif
                              <strong class="text-danger">Remaining: RM {{ number_format($remainingAmount, 2) }}</strong>
                            </div>
                            
                            <div class="mb-3">
                              <label class="form-label">Payment Amount (RM) <span class="text-danger">*</span></label>
                              <input type="number" name="amount" class="form-control" step="0.01" min="0.01" max="{{ $remainingAmount }}" value="{{ $remainingAmount }}" required>
                              <small class="text-muted">You can pay the full amount or a partial payment (minimum RM 0.01)</small>
                            </div>
                            
                            <div class="mb-3">
                              <label class="form-label">Payment Method <span class="text-danger">*</span></label>
                              <select name="payment_method" class="form-select" required>
                                <option value="">Select payment method...</option>
                                <option value="bank_transfer">Bank Transfer</option>
                                <option value="cash">Cash</option>
                                <option value="other">Other</option>
                              </select>
                            </div>
                            
                            <div class="mb-3">
                              <label class="form-label">Payment Date <span class="text-danger">*</span></label>
                              <input type="date" name="payment_date" class="form-control" value="{{ now()->format('Y-m-d') }}" required>
                            </div>
                            
                            <div class="mb-3">
                              <label class="form-label">Payment Receipt <span class="text-danger">*</span></label>
                              <input type="file" name="receipt" class="form-control" accept="image/*,application/pdf" required>
                              <small class="text-muted">Upload proof of payment (image or PDF, max 5MB)</small>
                            </div>
                          </div>
                          <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-danger">
                              <i class="bi bi-credit-card me-2"></i>Submit Payment
                            </button>
                          </div>
                        </form>
                      </div>
                    </div>
                  </div>
                  @endif
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
      </div>
      @endif
    </div>

    {{-- ========== RIGHT COLUMN: BOOKING SUMMARY ========== --}}
    <div class="col-lg-4">
      {{-- Booking Summary Card --}}
      <div class="card phase-card">
        <div class="phase-card-body">
          <h6 class="fw-bold mb-3">Booking Summary</h6>
          
          {{-- Car Image --}}
          @if($booking->car && $booking->car->image_url)
            <img src="{{ $booking->car->image_url }}" alt="{{ $booking->car->brand }} {{ $booking->car->model }}" class="img-fluid rounded mb-3" style="width: 100%; height: 150px; object-fit: cover;">
          @endif
          
          <div class="mb-3">
            <div class="fw-bold">{{ $booking->car->brand ?? 'N/A' }} {{ $booking->car->model ?? '' }}</div>
            <div class="small text-muted">{{ $booking->car->year ?? '' }} | {{ $booking->car->plate_number ?? 'N/A' }}</div>
          </div>
          
          <hr>
          
          <div class="small">
            <div class="d-flex justify-content-between mb-2">
              <span class="text-muted">Pickup:</span>
              <span>{{ $booking->start_datetime?->format('d M Y, H:i') }}</span>
            </div>
            <div class="d-flex justify-content-between mb-2">
              <span class="text-muted">Return:</span>
              <span>{{ $booking->end_datetime?->format('d M Y, H:i') }}</span>
            </div>
            <div class="d-flex justify-content-between mb-2">
              <span class="text-muted">Duration:</span>
              <span>{{ $booking->rental_hours }} hours</span>
            </div>
          </div>
          
          <hr>
          
          <div class="small">
            <div class="d-flex justify-content-between mb-2">
              <span>Base Price:</span>
              <span>RM {{ number_format($booking->base_price ?? 0, 2) }}</span>
            </div>
            @if(($booking->promo_discount ?? 0) > 0)
            <div class="d-flex justify-content-between mb-2">
              <span>Promo Discount:</span>
              <span class="text-success">-RM {{ number_format($booking->promo_discount, 2) }}</span>
            </div>
            @endif
            <div class="d-flex justify-content-between mb-2">
              <span>Deposit:</span>
              <span>RM {{ number_format($booking->deposit_amount ?? 0, 2) }}</span>
            </div>
            <hr>
            <div class="d-flex justify-content-between">
              <span class="fw-bold">Total:</span>
              <span class="fw-bold text-hasta" style="font-size: 18px;">RM {{ number_format($booking->final_amount ?? 0, 2) }}</span>
            </div>
          </div>
        </div>
      </div>

      {{-- Feedback Form (Only for completed bookings) --}}
      @if(in_array($booking->status, ['completed', 'deposit_returned']))
        @if(!$booking->feedback)
        <div class="card phase-card" id="feedback">
          <div class="phase-card-body">
            <h6 class="fw-bold mb-3"><i class="bi bi-star me-2"></i> Rate Your Experience</h6>
            <p class="small text-muted mb-3">Help us improve by sharing your feedback about this rental experience.</p>
            <form method="POST" action="{{ route('customer.bookings.feedback.store', $booking->booking_id) }}">
              @csrf
              <div class="mb-3">
                <label class="form-label">Rating <span class="text-danger">*</span></label>
                <div class="d-flex gap-2">
                  @for($i = 1; $i <= 5; $i++)
                    <label style="cursor: pointer;">
                      <input type="radio" name="rating" value="{{ $i }}" class="d-none rating-input" required>
                      <i class="bi bi-star-fill rating-star" style="font-size: 24px; color: #ddd;" data-value="{{ $i }}"></i>
                    </label>
                  @endfor
                </div>
                <small class="text-muted">Click on a star to rate (1 = Poor, 5 = Excellent)</small>
              </div>
              <div class="mb-3">
                <label class="form-label">Your Feedback</label>
                <textarea name="comment" class="form-control" rows="4" placeholder="Share your experience with this car rental..."></textarea>
                <small class="text-muted">Tell us what you liked or what we can improve</small>
              </div>
              <button type="submit" class="btn btn-hasta w-100">
                <i class="bi bi-send me-2"></i>Submit Feedback
              </button>
            </form>
          </div>
        </div>
        @else
        <div class="card phase-card" id="feedback">
          <div class="phase-card-body">
            <h6 class="fw-bold mb-3"><i class="bi bi-star-fill text-warning me-2"></i> Your Rating</h6>
            <div class="d-flex align-items-center mb-3">
              @for($i = 1; $i <= 5; $i++)
                <i class="bi bi-star-fill" style="font-size: 20px; color: {{ $i <= $booking->feedback->rating ? '#FFD700' : '#ddd' }};"></i>
              @endfor
              <span class="ms-2 small text-muted">({{ $booking->feedback->rating }}/5)</span>
            </div>
            @if($booking->feedback->comment)
              <div class="p-3 bg-light rounded">
                <p class="small mb-0">"{{ $booking->feedback->comment }}"</p>
              </div>
            @endif
            <p class="small text-muted mt-2 mb-0">
              <i class="bi bi-check-circle text-success me-1"></i>Thank you for your feedback!
            </p>
          </div>
        </div>
        @endif
      @endif

      {{-- Cancellation Request Status (if exists) --}}
      @if($booking->cancellationRequest)
        @php $cancelRequest = $booking->cancellationRequest; @endphp
        <div class="card phase-card">
          <div class="phase-card-body">
            <h6 class="fw-bold mb-3"><i class="bi bi-x-circle me-2 text-danger"></i> Cancellation Request</h6>
            
            <div class="mb-3">
              <span class="badge 
                {{ $cancelRequest->status === 'pending' ? 'bg-warning text-dark' : '' }}
                {{ $cancelRequest->status === 'approved' ? 'bg-success' : '' }}
                {{ $cancelRequest->status === 'rejected' ? 'bg-danger' : '' }}
                {{ $cancelRequest->status === 'refunded' ? 'bg-info' : '' }}
              ">
                {{ ucfirst($cancelRequest->status) }}
              </span>
            </div>
            
            <div class="small">
              <div class="mb-2">
                <span class="text-muted">Reason:</span><br>
                <span>{{ $cancelRequest->reason_label }}</span>
              </div>
              <div class="mb-2">
                <span class="text-muted">Submitted:</span><br>
                <span>{{ $cancelRequest->created_at->format('d M Y, H:i') }}</span>
              </div>
              
              @if($cancelRequest->status === 'pending')
                <div class="alert alert-warning small py-2 px-3 mb-0 mt-3">
                  <i class="bi bi-clock me-1"></i> Your request is being reviewed. We'll notify you once processed.
                </div>
              @elseif($cancelRequest->status === 'approved')
                <div class="alert alert-success small py-2 px-3 mb-0 mt-3">
                  <i class="bi bi-check-circle me-1"></i> Your cancellation has been approved. Refund is being processed.
                </div>
              @elseif($cancelRequest->status === 'rejected')
                <div class="alert alert-danger small py-2 px-3 mb-0 mt-3">
                  <i class="bi bi-x-circle me-1"></i> Your cancellation request was rejected.
                  @if($cancelRequest->staff_notes)
                    <br><strong>Reason:</strong> {{ $cancelRequest->staff_notes }}
                  @endif
                </div>
              @elseif($cancelRequest->status === 'refunded')
                <div class="alert alert-info small py-2 px-3 mb-0 mt-3">
                  <i class="bi bi-cash me-1"></i> Refund of <strong>RM {{ number_format($cancelRequest->refund_amount, 2) }}</strong> has been processed.
                  @if($cancelRequest->refund_reference)
                    <br><small>Ref: {{ $cancelRequest->refund_reference }}</small>
                  @endif
                </div>
              @endif
            </div>
          </div>
        </div>
      @endif

      {{-- Quick Actions --}}
      <div class="card phase-card">
        <div class="phase-card-body">
          <h6 class="fw-bold mb-3">Quick Actions</h6>
          
          @if(in_array($booking->status, ['created', 'confirmed']) && !$booking->hasCancellationRequest())
            @if($canCancel)
              <a href="{{ route('customer.bookings.cancel.form', $booking->booking_id) }}" class="btn btn-outline-danger btn-sm w-100 mb-2">
                <i class="bi bi-x-circle me-2"></i> Request Cancellation
              </a>
            @else
              <div class="alert alert-warning small py-2 px-3 mb-2" role="alert">
                <div class="d-flex align-items-start">
                  <i class="bi bi-exclamation-triangle-fill me-2 mt-1"></i>
                  <div>
                    <strong>Cancellation Not Available</strong>
                    <p class="mb-0 mt-1" style="font-size: 12px;">
                      Cancellation requests are only available at least 24 hours before pickup time. 
                      @if($hoursUntilPickup !== null && $hoursUntilPickup > 0)
                        Your pickup is in {{ round($hoursUntilPickup, 1) }} hours. Please proceed with your booking.
                      @elseif($hoursUntilPickup !== null && $hoursUntilPickup <= 0)
                        Your pickup time has passed or is very soon. Please proceed with your booking.
                      @endif
                    </p>
                  </div>
                </div>
              </div>
            @endif
          @endif
          
          <a href="{{ route('customer.bookings') }}" class="btn btn-outline-secondary btn-sm w-100">
            <i class="bi bi-arrow-left me-2"></i> Back to Bookings
          </a>
        </div>
      </div>

    </div>
  </div>
</div>

@push('scripts')
<script>
// Check agreement terms before download
function checkAgreementTerms() {
  const checkbox = document.getElementById('agreeTerms');
  if (!checkbox.checked) {
    alert('Please check the agreement checkbox before downloading the agreement.');
    return false;
  }
  return true;
}

// Handle combined pickup photos form submission
const pickupPhotosForm = document.getElementById('pickupPhotosForm');
if (pickupPhotosForm) {
  pickupPhotosForm.addEventListener('submit', function(e) {
    const agreementPhoto = document.getElementById('agreementPhoto');
    const keysPhoto = document.getElementById('keysPhoto');
    
    // Check if at least one photo is selected
    if ((!agreementPhoto.files || !agreementPhoto.files[0]) && (!keysPhoto.files || !keysPhoto.files[0])) {
      e.preventDefault();
      alert('Please select at least one photo to upload.');
      return false;
    }
    
    // Hide the entire form after submission
    this.style.display = 'none';
    
    // Show loading state
    const submitBtn = this.querySelector('button[type="submit"]');
    if (submitBtn) {
      submitBtn.disabled = true;
      submitBtn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Uploading...';
    }
  });
}

// Handle combined return photos form submission
const returnPhotosForm = document.getElementById('returnPhotosForm');
if (returnPhotosForm) {
  returnPhotosForm.addEventListener('submit', function(e) {
    const keysInCarPhoto = document.getElementById('keysInCarPhoto');
    const parkingLocationPhoto = document.getElementById('parkingLocationPhoto');
    
    // Check if at least one photo is selected
    if ((!keysInCarPhoto.files || !keysInCarPhoto.files[0]) && (!parkingLocationPhoto.files || !parkingLocationPhoto.files[0])) {
      e.preventDefault();
      alert('Please select at least one photo to upload.');
      return false;
    }
    
    // Hide the entire form after submission
    this.style.display = 'none';
    
    // Show loading state
    const submitBtn = this.querySelector('button[type="submit"]');
    if (submitBtn) {
      submitBtn.disabled = true;
      submitBtn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Uploading...';
    }
  });
}

// Star rating interaction
document.querySelectorAll('.rating-star').forEach(star => {
  star.addEventListener('click', function() {
    const value = this.dataset.value;
    const container = this.closest('.d-flex');
    
    container.querySelectorAll('.rating-star').forEach((s, index) => {
      if (index < value) {
        s.style.color = '#FFD700';
      } else {
        s.style.color = '#ddd';
      }
    });
    
    // Check the corresponding radio input
    const radio = this.previousElementSibling;
    if (radio) radio.checked = true;
  });
  
  star.addEventListener('mouseover', function() {
    const value = this.dataset.value;
    const container = this.closest('.d-flex');
    
    container.querySelectorAll('.rating-star').forEach((s, index) => {
      if (index < value) {
        s.style.color = '#FFD700';
      } else {
        s.style.color = '#ddd';
      }
    });
  });
});

// Photo click handler to replace inline onclick
document.querySelectorAll('.clickable-photo').forEach(photo => {
  photo.addEventListener('click', function() {
    window.open(this.dataset.url, '_blank');
  });
});
</script>
@endpush
@endsection
