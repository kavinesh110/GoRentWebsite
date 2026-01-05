@extends('layouts.app')
@section('title', 'Booking Details - Staff Dashboard')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
<style>
  /* Phase Stepper Styles - Matching Customer Side */
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
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    margin-bottom: 24px;
    overflow: hidden;
  }

  .phase-card-header {
    background: #fff;
    border-bottom: 2px solid #f0f0f0;
    padding: 20px 24px;
    display: flex;
    justify-content: space-between;
    align-items: center;
  }

  .phase-card-header h5 {
    margin: 0;
    font-weight: 700;
    color: #1a202c;
    font-size: 18px;
  }

  .phase-card-body {
    padding: 24px;
    background: #fff;
  }

  .status-info-box {
    padding: 16px;
    border-radius: 8px;
    margin-bottom: 20px;
    border-left: 4px solid;
  }

  .status-info-box.success {
    background: #f0fdf4;
    border-color: #22c55e;
  }

  .status-info-box.info {
    background: #eff6ff;
    border-color: #3b82f6;
  }

  .status-info-box.warning {
    background: #fffbeb;
    border-color: #f59e0b;
  }

  .status-info-box.pending {
    background: #fef3c7;
    border-color: #f59e0b;
  }

  /* Utility Classes */
  .x-small { font-size: 11px; }
  .text-slate-700 { color: #334155; }
  .text-slate-800 { color: #1e293b; }
  
  .btn-white {
    background: #fff;
    color: #1e293b;
    border-color: #e2e8f0;
    transition: all 0.2s;
  }
  .btn-white:hover {
    background: #f8fafc;
    border-color: #cbd5e1;
  }

  .bg-warning-subtle { background-color: #fffbeb !important; }
  .text-warning-emphasis { color: #92400e !important; }
  .border-warning-subtle { border-color: #fef3c7 !important; }
  
  .bg-info-subtle { background-color: #eff6ff !important; }
  .text-info { color: #2563eb !important; }
  .border-info-subtle { border-color: #dbeafe !important; }
  
  .bg-success-subtle { background-color: #f0fdf4 !important; }
  .text-success { color: #16a34a !important; }
  .border-success-subtle { border-color: #dcfce7 !important; }
  
  .bg-danger-subtle { background-color: #fef2f2 !important; }
  .text-danger { color: #dc2626 !important; }
  .border-danger-subtle { border-color: #fee2e2 !important; }
  
  .bg-primary-subtle { background-color: #eef2ff !important; }
  .text-primary { color: #4f46e5 !important; }
  .border-primary-subtle { border-color: #e0e7ff !important; }

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
<div class="d-flex" style="min-height: calc(100vh - 60px);">
  @include('staff._nav')
  <div class="flex-fill" style="background: #f8fafc;">
    <div class="container-fluid px-4 px-md-5" style="padding-top: 32px; padding-bottom: 48px;">
      
      {{-- Header Section --}}
      <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
          <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-2" style="font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">
              <li class="breadcrumb-item"><a href="{{ route('staff.bookings') }}" class="text-decoration-none text-muted">Booking Records</a></li>
              <li class="breadcrumb-item active text-hasta" aria-current="page">Reference #{{ $booking->booking_id }}</li>
            </ol>
          </nav>
          <div class="d-flex align-items-center gap-3">
            <h1 class="h3 fw-bold mb-0" style="color:#1e293b; letter-spacing: -0.5px;">Booking #{{ $booking->booking_id }}</h1>
            @php
              $statusClasses = [
                'completed' => 'bg-success-subtle text-success border-success-subtle',
                'active' => 'bg-info-subtle text-info border-info-subtle',
                'cancelled' => 'bg-danger-subtle text-danger border-danger-subtle',
                'confirmed' => 'bg-primary-subtle text-primary border-primary-subtle',
                'created' => 'bg-warning-subtle text-warning-emphasis border-warning-subtle'
              ];
              $statusClass = $statusClasses[$booking->status] ?? 'bg-secondary-subtle text-secondary border-secondary-subtle';
            @endphp
            <span class="badge rounded-pill border px-3 py-2 {{ $statusClass }} fw-bold text-uppercase shadow-sm" style="font-size: 10px; letter-spacing: 0.5px;">
              {{ ucfirst($booking->status ?? 'N/A') }}
            </span>
          </div>
          <div class="mt-1">
            <span class="text-muted small"><i class="bi bi-clock-history me-1"></i> Placed on {{ $booking->created_at->format('d M Y, h:i A') }}</span>
          </div>
        </div>
        <div class="d-flex gap-2">
          <button type="button" onclick="window.print()" class="btn btn-white border rounded-3 px-3 fw-bold shadow-sm small">
            <i class="bi bi-printer me-1"></i> Print
          </button>
          <div class="dropdown">
            <button class="btn btn-dark rounded-3 px-3 fw-bold shadow-sm small dropdown-toggle" type="button" data-bs-toggle="dropdown">
              Quick Actions
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow border-0 rounded-3">
              <li><a class="dropdown-item small fw-bold py-2" href="{{ route('staff.cars.edit', $booking->car_id) }}"><i class="bi bi-car-front me-2"></i>View Vehicle</a></li>
              @if(Route::has('staff.customers.show'))
                <li><a class="dropdown-item small fw-bold py-2" href="{{ route('staff.customers.show', $booking->customer_id) }}"><i class="bi bi-person me-2"></i>Customer Profile</a></li>
              @endif
              <li><hr class="dropdown-divider"></li>
              <li><a class="dropdown-item small fw-bold py-2 text-danger" href="#" data-bs-toggle="modal" data-bs-target="#issuePenaltyModal"><i class="bi bi-exclamation-triangle me-2"></i>Issue Penalty</a></li>
            </ul>
          </div>
        </div>
      </div>

      @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm d-flex align-items-center gap-2 mb-4">
          <i class="bi bi-check-circle-fill"></i>
          <div>{{ session('success') }}</div>
          <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
        </div>
      @endif

      @if(session('error'))
        <div class="alert alert-danger border-0 shadow-sm d-flex align-items-center gap-2 mb-4">
          <i class="bi bi-exclamation-circle-fill"></i>
          <div>{{ session('error') }}</div>
          <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
        </div>
      @endif

      {{-- Quick Summary Cards --}}
      <div class="row g-4 mb-4">
        <div class="col-lg-4">
          <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
            <div class="card-body p-4">
              <div class="d-flex align-items-center gap-3 mb-3">
                <div class="bg-primary-subtle text-primary rounded-3 p-2">
                  <i class="bi bi-person-badge h4 mb-0"></i>
                </div>
                <div>
                  <div class="text-muted fw-bold text-uppercase" style="font-size: 9px; letter-spacing: 1px;">Customer Details</div>
                  <h6 class="fw-bold text-slate-800 mb-0">{{ $booking->customer->full_name ?? 'N/A' }}</h6>
                </div>
              </div>
              <div class="d-grid gap-2">
                <div class="d-flex align-items-center gap-2 text-muted small">
                  <i class="bi bi-envelope"></i> {{ $booking->customer->email ?? 'No Email' }}
                </div>
                <div class="d-flex align-items-center gap-2 text-muted small">
                  <i class="bi bi-telephone"></i> {{ $booking->customer->phone ?? 'No Phone' }}
                </div>
                <div class="mt-2">
                  <span class="badge bg-light text-slate-700 border fw-bold px-2 py-1 x-small">
                    ID: #{{ $booking->customer_id }}
                  </span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="col-lg-4">
          <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
            <div class="card-body p-4">
              <div class="d-flex align-items-center gap-3 mb-3">
                <div class="bg-dark text-white rounded-3 p-2">
                  <i class="bi bi-car-front h4 mb-0"></i>
                </div>
                <div>
                  <div class="text-muted fw-bold text-uppercase" style="font-size: 9px; letter-spacing: 1px;">Vehicle Information</div>
                  <h6 class="fw-bold text-slate-800 mb-0">{{ $booking->car->brand ?? 'N/A' }} {{ $booking->car->model ?? '' }}</h6>
                </div>
              </div>
              <div class="d-grid gap-2">
                <div class="d-flex align-items-center gap-2 text-muted small">
                  <i class="bi bi-tag"></i> Plate: <span class="fw-bold text-dark">{{ $booking->car->plate_number ?? 'N/A' }}</span>
                </div>
                <div class="d-flex align-items-center gap-2 text-muted small">
                  <i class="bi bi-geo-alt"></i> Location: {{ $booking->car->location->name ?? 'Student Mall' }}
                </div>
                <div class="mt-2">
                  <span class="badge bg-success-subtle text-success border-success-subtle fw-bold px-2 py-1 x-small">
                    {{ ucfirst($booking->car->status ?? 'Available') }}
                  </span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="col-lg-4">
          <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
            <div class="card-body p-4">
              <div class="d-flex align-items-center gap-3 mb-3">
                <div class="bg-warning-subtle text-warning-emphasis rounded-3 p-2">
                  <i class="bi bi-calendar-event h4 mb-0"></i>
                </div>
                <div>
                  <div class="text-muted fw-bold text-uppercase" style="font-size: 9px; letter-spacing: 1px;">Rental Period</div>
                  <h6 class="fw-bold text-slate-800 mb-0">{{ $booking->rental_hours ?? 0 }} Hours Total</h6>
                </div>
              </div>
              <div class="d-grid gap-2">
                <div class="d-flex align-items-center gap-2 text-muted small">
                  <i class="bi bi-arrow-up-circle text-success"></i> Pickup: <strong>{{ $booking->start_datetime?->format('d M, h:i A') }}</strong>
                </div>
                <div class="d-flex align-items-center gap-2 text-muted small">
                  <i class="bi bi-arrow-down-circle text-danger"></i> Return: <strong>{{ $booking->end_datetime?->format('d M, h:i A') }}</strong>
                </div>
                <div class="mt-2">
                  <span class="badge bg-primary-subtle text-primary border-primary-subtle fw-bold px-2 py-1 x-small">
                    RM {{ number_format($booking->final_amount ?? 0, 2) }}
                  </span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      @php
        $phase1Complete = $booking->isPhase1Complete();
        $paymentVerified = $booking->isPaymentVerified();
        $phase2Complete = $booking->isPhase2Complete();
        $phase3Complete = $booking->isPhase3Complete();
        $phase4Complete = $booking->isPhase4Complete();
        $currentPhase = $booking->getCurrentPhase();
      @endphp

      {{-- Phase Stepper --}}
      <div class="card border-0 shadow-sm rounded-4 bg-white mb-4 overflow-hidden">
        <div class="card-header bg-white border-bottom py-3 px-4 d-flex justify-content-between align-items-center">
          <h6 class="fw-bold mb-0 text-slate-800">Rental Progress Lifecycle</h6>
          <span class="badge bg-light text-slate-600 border border-slate-200 x-small px-2 py-1 fw-bold">SYSTEMATIC WORKFLOW</span>
        </div>
        <div class="card-body p-4 pt-5 pb-5">
          <div class="phase-stepper">
            {{-- Phase 1: Payment --}}
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
          </div>
        </div>
      </div>

      <div class="row g-4">
        {{-- LEFT COLUMN: PHASE CONTENT --}}
        <div class="col-lg-8">
          
          {{-- ========== PHASE 1: PAYMENT ========== --}}
          <div class="card border-0 shadow-sm rounded-4 bg-white mb-4 overflow-hidden">
            <div class="card-header bg-white border-bottom py-3 px-4 d-flex justify-content-between align-items-center">
              <h6 class="fw-bold mb-0 text-slate-800"><i class="bi bi-credit-card-fill me-2 text-primary"></i> Phase 1: Payment Verification</h6>
              @if($phase1Complete)
                <span class="badge bg-success-subtle text-success border-success-subtle rounded-pill px-3 py-1 x-small fw-bold">SUBMITTED</span>
              @else
                <span class="badge bg-warning-subtle text-warning-emphasis border-warning-subtle rounded-pill px-3 py-1 x-small fw-bold">PENDING</span>
              @endif
            </div>
            <div class="card-body p-4">
              @if($phase1Complete)
                <div class="status-info-box success">
                  <div class="d-flex align-items-center">
                    <i class="bi bi-check-circle-fill text-success me-3" style="font-size: 24px;"></i>
                    <div>
                      <strong>Payment Submitted by Customer</strong>
                      <p class="mb-0 small text-muted">Customer has uploaded their deposit payment receipt.</p>
                    </div>
                  </div>
                </div>

                {{-- Payment List --}}
                @php
                  $depositPayments = $booking->payments->where('payment_type', 'deposit');
                @endphp
                @if($depositPayments->count() > 0)
                  <h6 class="fw-bold mt-4 mb-3">Deposit Payments</h6>
                  <div class="table-responsive">
                    <table class="table table-sm align-middle mb-0">
                      <thead class="table-light">
                        <tr>
                          <th>Amount</th>
                          <th>Method</th>
                          <th>Date</th>
                          <th>Status</th>
                          <th>Receipt</th>
                          <th>Actions</th>
                        </tr>
                      </thead>
                      <tbody>
                        @foreach($depositPayments as $payment)
                          <tr>
                            <td><strong>RM {{ number_format($payment->amount, 2) }}</strong></td>
                            <td>{{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}</td>
                            <td>{{ $payment->payment_date->format('d M Y') }}</td>
                            <td>
                              <span class="badge {{ $payment->status === 'verified' ? 'bg-success' : 'bg-warning' }}">
                                {{ ucfirst($payment->status) }}
                              </span>
                            </td>
                            <td>
                              @if($payment->receipt_url)
                                <a href="{{ asset('storage/' . $payment->receipt_url) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                  <i class="bi bi-file-earmark-image"></i> View
                                </a>
                              @else
                                <span class="text-muted">-</span>
                              @endif
                            </td>
                            <td>
                              @if($payment->status === 'pending')
                                <form method="POST" action="{{ route('staff.payments.verify', $payment->payment_id) }}" class="d-inline">
                                  @csrf
                                  @method('PATCH')
                                  <button type="submit" class="btn btn-sm btn-success">
                                    <i class="bi bi-check-circle me-1"></i>Verify
                                  </button>
                                </form>
                              @else
                                <span class="text-success"><i class="bi bi-check-circle"></i> Verified</span>
                              @endif
                            </td>
                          </tr>
                        @endforeach
                      </tbody>
                    </table>
                  </div>
                @endif

                {{-- Verify Payment Action --}}
                @if(!$paymentVerified)
                  <div class="mt-4 p-3 bg-warning-subtle rounded border border-warning">
                    <div class="d-flex align-items-center mb-3">
                      <i class="bi bi-exclamation-triangle-fill text-warning me-2"></i>
                      <strong>Action Required</strong>
                    </div>
                    <p class="mb-3 small">Review the payment receipt above and verify the payment to proceed to Phase 2 (Verification).</p>
                    <form method="POST" action="{{ route('staff.payments.verify', $depositPayments->first()->payment_id) }}" class="d-inline">
                      @csrf
                      @method('PATCH')
                      <button type="submit" class="btn btn-warning">
                        <i class="bi bi-shield-check me-2"></i>Verify Payment & Confirm Booking
                      </button>
                    </form>
                  </div>
                @else
                  <div class="mt-4 p-3 bg-success-subtle rounded border border-success">
                    <div class="d-flex align-items-center">
                      <i class="bi bi-check-circle-fill text-success me-2"></i>
                      <strong>Payment Verified</strong>
                    </div>
                    <p class="mb-0 small mt-2">Payment has been verified. Booking is ready for Phase 2 (Verification/Inspection).</p>
                  </div>
                @endif
              @else
                <div class="status-info-box pending">
                  <div class="d-flex align-items-center">
                    <i class="bi bi-exclamation-circle-fill text-warning me-3" style="font-size: 24px;"></i>
                    <div>
                      <strong>Awaiting Customer Payment</strong>
                      <p class="mb-0 small">Customer has not yet uploaded their deposit payment receipt.</p>
                    </div>
                  </div>
                </div>
              @endif
            </div>
          </div>

          {{-- ========== PHASE 2: VERIFICATION ========== --}}
          <div class="card border-0 shadow-sm rounded-4 bg-white mb-4 overflow-hidden">
            <div class="card-header bg-white border-bottom py-3 px-4 d-flex justify-content-between align-items-center">
              <h6 class="fw-bold mb-0 text-slate-800"><i class="bi bi-shield-check-fill me-2 text-info"></i> Phase 2: Booking Confirmation</h6>
              @if($phase2Complete)
                <span class="badge bg-success-subtle text-success border-success-subtle rounded-pill px-3 py-1 x-small fw-bold">CONFIRMED</span>
              @elseif($paymentVerified)
                <span class="badge bg-info-subtle text-info border-info-subtle rounded-pill px-3 py-1 x-small fw-bold">IN PROGRESS</span>
              @else
                <span class="badge bg-secondary-subtle text-secondary border-secondary-subtle rounded-pill px-3 py-1 x-small fw-bold">LOCKED</span>
              @endif
            </div>
            <div class="card-body p-4">
              @if($phase2Complete)
                <div class="status-info-box success">
                  <div class="d-flex align-items-center">
                    <i class="bi bi-patch-check-fill text-success me-3" style="font-size: 24px;"></i>
                    <div>
                      <strong>Booking Confirmed</strong>
                      <p class="mb-0 small text-muted">Booking has been verified and confirmed. Customer can proceed to pickup.</p>
                    </div>
                  </div>
                </div>

                {{-- Show Before-Pickup Inspection --}}
                @php
                  $beforeInspection = $booking->inspections->where('type', 'before')->first();
                @endphp
                @if($beforeInspection)
                  <h6 class="fw-bold mt-4 mb-3">Before-Pickup Inspection</h6>
                  <div class="row g-3">
                    <div class="col-md-6">
                      <div class="p-3 bg-light rounded">
                        <div class="small text-muted">Inspection Date</div>
                        <div class="fw-bold">{{ $beforeInspection->datetime?->format('d M Y, H:i') ?? 'N/A' }}</div>
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="p-3 bg-light rounded">
                        <div class="small text-muted">Fuel Level</div>
                        <div class="fw-bold">{{ $beforeInspection->fuel_level ?? 'N/A' }} / 8 bars</div>
                      </div>
                    </div>
                    @if($beforeInspection->photos && is_array($beforeInspection->photos) && count($beforeInspection->photos) > 0)
                      <div class="col-12">
                        <div class="small text-muted mb-2">Inspection Photos</div>
                        <div class="d-flex gap-2 flex-wrap">
                          @foreach($beforeInspection->photos as $photo)
                            <a href="{{ asset('storage/' . $photo) }}" target="_blank">
                              <img src="{{ asset('storage/' . $photo) }}" alt="Inspection Photo" class="img-thumbnail" style="max-width: 150px; max-height: 150px; object-fit: cover;">
                            </a>
                          @endforeach
                        </div>
                      </div>
                    @endif
                    @if($beforeInspection->notes)
                      <div class="col-12">
                        <div class="p-3 bg-light rounded">
                          <div class="small text-muted">Notes</div>
                          <div>{{ $beforeInspection->notes }}</div>
                        </div>
                      </div>
                    @endif
                  </div>
                @endif
              @elseif($paymentVerified)
                <div class="status-info-box info">
                  <div class="d-flex align-items-center">
                    <i class="bi bi-hourglass-split text-info me-3" style="font-size: 24px;"></i>
                    <div>
                      <strong>Ready for Verification</strong>
                      <p class="mb-0 small">Payment has been verified. Please perform the before-pickup inspection to confirm the booking.</p>
                    </div>
                  </div>
                </div>

                {{-- Before-Pickup Inspection Form --}}
                @php
                  $hasBeforeInspection = $booking->inspections->where('type', 'before')->first();
                @endphp
                <div class="mt-4">
                  <h6 class="fw-bold mb-3">
                    <i class="bi bi-camera me-2"></i>Before-Pickup Inspection
                    @if($hasBeforeInspection)
                      <span class="badge bg-success ms-2">Completed</span>
                    @endif
                  </h6>
                  
                  <form method="POST" action="{{ route('staff.bookings.inspections.store', $booking->booking_id) }}" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="inspection_type" value="before">
                    <div class="row g-3">
                      <div class="col-md-6">
                        <label class="form-label">Inspection Date & Time</label>
                        <input type="datetime-local" name="datetime" class="form-control" value="{{ $hasBeforeInspection?->datetime?->format('Y-m-d\TH:i') ?? now()->format('Y-m-d\TH:i') }}" required>
                      </div>
                      <div class="col-md-6">
                        <label class="form-label">Fuel Level (0-8 bars)</label>
                        <input type="number" name="fuel_level" class="form-control" min="0" max="8" value="{{ $hasBeforeInspection?->fuel_level ?? '' }}" required>
                      </div>
                      <div class="col-12">
                        <label class="form-label">Inspection Photos</label>
                        <input type="file" name="photos[]" class="form-control" accept="image/*" multiple>
                        <small class="text-muted">Upload photos of the vehicle condition before pickup</small>
                      </div>
                      @if($hasBeforeInspection && $hasBeforeInspection->photos && is_array($hasBeforeInspection->photos))
                        <div class="col-12">
                          <div class="small text-muted mb-2">Current Photos</div>
                          <div class="d-flex gap-2 flex-wrap">
                            @foreach($hasBeforeInspection->photos as $photo)
                              <img src="{{ asset('storage/' . $photo) }}" alt="Inspection Photo" class="img-thumbnail" style="max-width: 150px; max-height: 150px; object-fit: cover;">
                            @endforeach
                          </div>
                        </div>
                      @endif
                      <div class="col-12">
                        <label class="form-label">Notes</label>
                        <textarea name="notes" class="form-control" rows="3" placeholder="Any observations or notes about the vehicle condition...">{{ $hasBeforeInspection?->notes ?? '' }}</textarea>
                      </div>
                      <div class="col-12">
                        <button type="submit" class="btn btn-primary">
                          <i class="bi bi-camera me-2"></i>{{ $hasBeforeInspection ? 'Update Inspection' : 'Save Inspection' }}
                        </button>
                      </div>
                    </div>
                  </form>

                  {{-- Confirm Booking Button --}}
                  @if($hasBeforeInspection && $hasBeforeInspection->photos)
                    <div class="mt-4 p-3 bg-info-subtle rounded border border-info">
                      <div class="d-flex align-items-center mb-3">
                        <i class="bi bi-info-circle-fill text-info me-2"></i>
                        <strong>Ready to Confirm</strong>
                      </div>
                      <p class="mb-3 small">Inspection completed. Click below to confirm the booking and allow customer to proceed to pickup.</p>
                      <form method="POST" action="{{ route('staff.bookings.update-status', $booking->booking_id) }}">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="confirmed">
                        <button type="submit" class="btn btn-primary">
                          <i class="bi bi-check-circle me-2"></i>Confirm Booking
                        </button>
                      </form>
                    </div>
                  @endif
                </div>
              @else
                <div class="status-info-box">
                  <div class="d-flex align-items-center">
                    <i class="bi bi-lock-fill text-secondary me-3" style="font-size: 24px;"></i>
                    <div>
                      <strong>Complete Phase 1 First</strong>
                      <p class="mb-0 small text-muted">Please verify the customer's payment in Phase 1 to unlock verification.</p>
                    </div>
                  </div>
                </div>
              @endif
            </div>
          </div>

          {{-- ========== PHASE 3: PICKUP & AGREEMENT ========== --}}
          <div class="card border-0 shadow-sm rounded-4 bg-white mb-4 overflow-hidden">
            <div class="card-header bg-white border-bottom py-3 px-4 d-flex justify-content-between align-items-center">
              <h6 class="fw-bold mb-0 text-slate-800"><i class="bi bi-key-fill me-2 text-warning"></i> Phase 3: Pickup & Agreement</h6>
              @if($phase3Complete || $phase4Complete)
                <span class="badge bg-success-subtle text-success border-success-subtle rounded-pill px-3 py-1 x-small fw-bold">ACTIVE</span>
              @elseif($phase2Complete)
                <span class="badge bg-warning-subtle text-warning-emphasis border-warning-subtle rounded-pill px-3 py-1 x-small fw-bold">IN PROGRESS</span>
              @else
                <span class="badge bg-secondary-subtle text-secondary border-secondary-subtle rounded-pill px-3 py-1 x-small fw-bold">LOCKED</span>
              @endif
            </div>
            <div class="card-body p-4">
              @if($phase3Complete || $phase4Complete || $phase2Complete)
                @if($phase3Complete || $phase4Complete)
                  <div class="status-info-box success mb-3">
                    <div class="d-flex align-items-center">
                      <i class="bi bi-check-circle-fill text-success me-3" style="font-size: 24px;"></i>
                      <div>
                        <strong>Phase 3 Complete</strong>
                        <p class="mb-0 small text-muted">Customer has completed the pickup process.</p>
                      </div>
                    </div>
                  </div>

                  {{-- Show Customer Uploads --}}
                  @php
                    $agreementPhoto = $booking->rentalPhotos->where('photo_type', 'agreement')->first();
                    $keysPhoto = $booking->rentalPhotos->where('photo_type', 'pickup')->first();
                  @endphp
                  @if($agreementPhoto || $keysPhoto)
                    <h6 class="fw-bold mb-3">Customer Uploads</h6>
                    <div class="row g-3 mb-3">
                      @if($agreementPhoto)
                        <div class="col-md-6">
                          <label class="form-label fw-bold"><i class="bi bi-file-image me-2"></i>Signed Agreement Photo</label>
                          <div>
                            <a href="{{ asset('storage/' . $agreementPhoto->photo_url) }}" target="_blank">
                              <img src="{{ asset('storage/' . $agreementPhoto->photo_url) }}" alt="Signed Agreement" class="img-fluid rounded mb-2" style="max-height: 200px; width: 100%; object-fit: cover; cursor: pointer;">
                            </a>
                            <p class="small text-muted mb-0">Uploaded on {{ $agreementPhoto->taken_at->format('d/m/Y H:i') }}</p>
                          </div>
                        </div>
                      @endif
                      @if($keysPhoto)
                        <div class="col-md-6">
                          <label class="form-label fw-bold"><i class="bi bi-key me-2"></i>Receiving Car Keys Photo</label>
                          <div>
                            <a href="{{ asset('storage/' . $keysPhoto->photo_url) }}" target="_blank">
                              <img src="{{ asset('storage/' . $keysPhoto->photo_url) }}" alt="Receiving Keys" class="img-fluid rounded mb-2" style="max-height: 200px; width: 100%; object-fit: cover; cursor: pointer;">
                            </a>
                            <p class="small text-muted mb-0">Uploaded on {{ $keysPhoto->taken_at->format('d/m/Y H:i') }}</p>
                          </div>
                        </div>
                      @endif
                    </div>
                  @endif

                  {{-- Update Status to Active --}}
                  @if($booking->status === 'confirmed')
                    <div class="mt-4 p-3 bg-info-subtle rounded border border-info">
                      <div class="d-flex align-items-center mb-3">
                        <i class="bi bi-info-circle-fill text-info me-2"></i>
                        <strong>Update Booking Status</strong>
                      </div>
                      <p class="mb-3 small">Customer has completed pickup. Update booking status to "Active" to indicate the rental is in progress.</p>
                      <form method="POST" action="{{ route('staff.bookings.update-status', $booking->booking_id) }}">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="active">
                        <button type="submit" class="btn btn-info">
                          <i class="bi bi-arrow-right-circle me-2"></i>Mark as Active
                        </button>
                      </form>
                    </div>
                  @endif
                @elseif($phase2Complete)
                  <div class="status-info-box info mb-3">
                    <div class="d-flex align-items-center">
                      <i class="bi bi-hourglass-split text-info me-3" style="font-size: 24px;"></i>
                      <div>
                        <strong>Awaiting Customer Action</strong>
                        <p class="mb-0 small">Customer needs to upload signed agreement and keys photos at pickup.</p>
                      </div>
                    </div>
                  </div>
                  <p class="text-muted small">Once the customer uploads both photos, you can update the booking status to "Active".</p>
                @endif
              @else
                <div class="status-info-box">
                  <div class="d-flex align-items-center">
                    <i class="bi bi-lock-fill text-secondary me-3" style="font-size: 24px;"></i>
                    <div>
                      <strong>Complete Previous Phases First</strong>
                      <p class="mb-0 small text-muted">This phase will unlock after booking verification is complete.</p>
                    </div>
                  </div>
                </div>
              @endif
            </div>
          </div>

          {{-- ========== PHASE 4: RETURN ========== --}}
          <div class="card border-0 shadow-sm rounded-4 bg-white mb-4 overflow-hidden">
            <div class="card-header bg-white border-bottom py-3 px-4 d-flex justify-content-between align-items-center">
              <h6 class="fw-bold mb-0 text-slate-800"><i class="bi bi-car-front-fill me-2 text-success"></i> Phase 4: Return & Finalization</h6>
              @if($phase4Complete)
                <span class="badge bg-success-subtle text-success border-success-subtle rounded-pill px-3 py-1 x-small fw-bold">COMPLETE</span>
              @elseif($phase3Complete || $booking->status === 'active')
                <span class="badge bg-warning-subtle text-warning-emphasis border-warning-subtle rounded-pill px-3 py-1 x-small fw-bold">PENDING RETURN</span>
              @else
                <span class="badge bg-secondary-subtle text-secondary border-secondary-subtle rounded-pill px-3 py-1 x-small fw-bold">LOCKED</span>
              @endif
            </div>
            <div class="card-body p-4">
              @if($phase4Complete || ($phase3Complete && $booking->status === 'active'))
                @if($phase4Complete)
                  <div class="status-info-box success mb-3">
                    <div class="d-flex align-items-center">
                      <i class="bi bi-trophy-fill text-success me-3" style="font-size: 24px;"></i>
                      <div>
                        <strong>Return Complete</strong>
                        <p class="mb-0 small text-muted">Customer has completed the return process.</p>
                      </div>
                    </div>
                  </div>

                  {{-- Show Customer Return Photos --}}
                  @php
                    $keysInCarPhoto = $booking->rentalPhotos->where('photo_type', 'key')->first();
                    $parkingPhoto = $booking->rentalPhotos->where('photo_type', 'parking')->first();
                  @endphp
                  @if($keysInCarPhoto || $parkingPhoto)
                    <h6 class="fw-bold mb-3">Customer Return Photos</h6>
                    <div class="row g-3 mb-3">
                      @if($keysInCarPhoto)
                        <div class="col-md-6">
                          <label class="form-label fw-bold"><i class="bi bi-key me-2"></i>Keys in Car Photo</label>
                          <div>
                            <a href="{{ asset('storage/' . $keysInCarPhoto->photo_url) }}" target="_blank">
                              <img src="{{ asset('storage/' . $keysInCarPhoto->photo_url) }}" alt="Keys in Car" class="img-fluid rounded mb-2" style="max-height: 200px; width: 100%; object-fit: cover; cursor: pointer;">
                            </a>
                            <p class="small text-muted mb-0">Uploaded on {{ $keysInCarPhoto->taken_at->format('d/m/Y H:i') }}</p>
                          </div>
                        </div>
                      @endif
                      @if($parkingPhoto)
                        <div class="col-md-6">
                          <label class="form-label fw-bold"><i class="bi bi-pin-map me-2"></i>Parking Location Photo</label>
                          <div>
                            <a href="{{ asset('storage/' . $parkingPhoto->photo_url) }}" target="_blank">
                              <img src="{{ asset('storage/' . $parkingPhoto->photo_url) }}" alt="Parking Location" class="img-fluid rounded mb-2" style="max-height: 200px; width: 100%; object-fit: cover; cursor: pointer;">
                            </a>
                            <p class="small text-muted mb-0">Uploaded on {{ $parkingPhoto->taken_at->format('d/m/Y H:i') }}</p>
                          </div>
                        </div>
                      @endif
                    </div>
                  @endif

                  {{-- After-Return Inspection --}}
                  @php
                    $afterInspection = $booking->inspections->where('type', 'after')->first();
                  @endphp
                  <div class="mt-4">
                    <h6 class="fw-bold mb-3">
                      <i class="bi bi-camera me-2"></i>After-Return Inspection
                      @if($afterInspection)
                        <span class="badge bg-success ms-2">Completed</span>
                      @else
                        <span class="badge bg-warning ms-2">Required</span>
                      @endif
                    </h6>
                    
                    @if($afterInspection)
                      <div class="row g-3">
                        <div class="col-md-6">
                          <div class="p-3 bg-light rounded">
                            <div class="small text-muted">Inspection Date</div>
                            <div class="fw-bold">{{ $afterInspection->datetime?->format('d M Y, H:i') ?? 'N/A' }}</div>
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="p-3 bg-light rounded">
                            <div class="small text-muted">Fuel Level</div>
                            <div class="fw-bold">{{ $afterInspection->fuel_level ?? 'N/A' }} / 8 bars</div>
                          </div>
                        </div>
                        @if($afterInspection->photos && is_array($afterInspection->photos) && count($afterInspection->photos) > 0)
                          <div class="col-12">
                            <div class="small text-muted mb-2">Inspection Photos</div>
                            <div class="d-flex gap-2 flex-wrap">
                              @foreach($afterInspection->photos as $photo)
                                <a href="{{ asset('storage/' . $photo) }}" target="_blank">
                                  <img src="{{ asset('storage/' . $photo) }}" alt="Inspection Photo" class="img-thumbnail" style="max-width: 150px; max-height: 150px; object-fit: cover;">
                                </a>
                              @endforeach
                            </div>
                          </div>
                        @endif
                        @if($afterInspection->notes)
                          <div class="col-12">
                            <div class="p-3 bg-light rounded">
                              <div class="small text-muted">Notes</div>
                              <div>{{ $afterInspection->notes }}</div>
                            </div>
                          </div>
                        @endif
                      </div>
                    @else
                      <div class="p-3 bg-warning-subtle rounded border border-warning mb-3">
                        <div class="d-flex align-items-center mb-3">
                          <i class="bi bi-exclamation-triangle-fill text-warning me-2"></i>
                          <strong>Inspection Required</strong>
                        </div>
                        <p class="mb-3 small">Customer has completed the return process. Please perform the after-return inspection.</p>
                      </div>

                      <form method="POST" action="{{ route('staff.bookings.inspections.store', $booking->booking_id) }}" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="inspection_type" value="after">
                        <div class="row g-3">
                          <div class="col-md-6">
                            <label class="form-label">Inspection Date & Time</label>
                            <input type="datetime-local" name="datetime" class="form-control" value="{{ now()->format('Y-m-d\TH:i') }}" required>
                          </div>
                          <div class="col-md-6">
                            <label class="form-label">Fuel Level (0-8 bars)</label>
                            <input type="number" name="fuel_level" class="form-control" min="0" max="8" required>
                          </div>
                          <div class="col-12">
                            <label class="form-label">Inspection Photos</label>
                            <input type="file" name="photos[]" class="form-control" accept="image/*" multiple>
                            <small class="text-muted">Upload photos of the vehicle condition after return</small>
                          </div>
                          <div class="col-12">
                            <label class="form-label">Notes</label>
                            <textarea name="notes" class="form-control" rows="3" placeholder="Any observations, damage, or notes about the vehicle condition..."></textarea>
                          </div>
                          <div class="col-12">
                            <button type="submit" class="btn btn-warning">
                              <i class="bi bi-camera me-2"></i>Save After-Return Inspection
                            </button>
                          </div>
                        </div>
                      </form>
                    @endif
                  </div>

                  {{-- Complete Booking --}}
                  @if($afterInspection && $booking->status !== 'completed')
                    <div class="mt-4 p-3 bg-success-subtle rounded border border-success">
                      <div class="d-flex align-items-center mb-3">
                        <i class="bi bi-check-circle-fill text-success me-2"></i>
                        <strong>Ready to Complete</strong>
                      </div>
                      <p class="mb-3 small">After-return inspection completed. Mark booking as completed to finalize the rental.</p>
                      <form method="POST" action="{{ route('staff.bookings.update-status', $booking->booking_id) }}">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="completed">
                        <button type="submit" class="btn btn-success">
                          <i class="bi bi-check-circle me-2"></i>Mark as Completed
                        </button>
                      </form>
                    </div>
                  @endif
                @else
                  <div class="status-info-box info mb-3">
                    <div class="d-flex align-items-center">
                      <i class="bi bi-info-circle-fill text-info me-3" style="font-size: 24px;"></i>
                      <div>
                        <strong>Awaiting Customer Return</strong>
                        <p class="mb-0 small">Customer needs to upload return photos when they return the vehicle.</p>
                      </div>
                    </div>
                  </div>
                @endif
              @else
                <div class="status-info-box">
                  <div class="d-flex align-items-center">
                    <i class="bi bi-lock-fill text-secondary me-3" style="font-size: 24px;"></i>
                    <div>
                      <strong>Complete Previous Phases First</strong>
                      <p class="mb-0 small text-muted">This phase will unlock after pickup is complete.</p>
                    </div>
                  </div>
                </div>
              @endif
            </div>
          </div>

        </div>

        {{-- RIGHT COLUMN: SIDEBAR INFO --}}
        <div class="col-lg-4">
          
          {{-- Pricing Summary --}}
          <div class="card border-0 shadow-sm rounded-4 bg-white mb-4 overflow-hidden">
            <div class="card-header bg-white border-bottom py-3 px-4">
              <h6 class="fw-bold mb-0 text-slate-800"><i class="bi bi-cash-stack me-2 text-success"></i>Financial Summary</h6>
            </div>
            <div class="card-body p-4">
              <div class="d-flex justify-content-between mb-2">
                <span class="text-muted">Base Price</span>
                <strong>RM {{ number_format($booking->base_price ?? 0, 2) }}</strong>
              </div>
              @if($booking->promo_discount > 0)
                <div class="d-flex justify-content-between mb-2">
                  <span class="text-muted">Promo Discount</span>
                  <strong class="text-success">-RM {{ number_format($booking->promo_discount, 2) }}</strong>
                </div>
              @endif
              @if($booking->voucher_discount > 0)
                <div class="d-flex justify-content-between mb-2">
                  <span class="text-muted">Voucher Discount</span>
                  <strong class="text-success">-RM {{ number_format($booking->voucher_discount, 2) }}</strong>
                </div>
              @endif
              <hr>
              <div class="d-flex justify-content-between mb-2">
                <span class="fw-bold">Total Rental</span>
                <strong class="text-primary">RM {{ number_format($booking->total_rental_amount ?? 0, 2) }}</strong>
              </div>
              <div class="d-flex justify-content-between mb-2">
                <span>Deposit</span>
                <strong>RM {{ number_format($booking->deposit_amount ?? 0, 2) }}</strong>
              </div>
              <hr>
              <div class="d-flex justify-content-between">
                <span class="fw-bold">Final Amount</span>
                <strong class="text-success fs-5">RM {{ number_format($booking->final_amount ?? 0, 2) }}</strong>
              </div>
            </div>
          </div>

          {{-- Penalties Section --}}
          @if($booking->penalties && $booking->penalties->count() > 0)
            <div class="card border-0 shadow-sm rounded-4 bg-white mb-4 overflow-hidden">
              <div class="card-header bg-white border-bottom py-3 px-4">
                <h6 class="fw-bold mb-0 text-slate-800"><i class="bi bi-exclamation-triangle-fill me-2 text-danger"></i>Active Penalties</h6>
              </div>
              <div class="card-body p-4">
                <div class="table-responsive">
                  <table class="table table-sm align-middle mb-0">
                    <thead class="table-light">
                      <tr>
                        <th>Type</th>
                        <th>Amount</th>
                        <th>Status</th>
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
                          <td><strong class="text-danger">RM {{ number_format($penalty->amount, 2) }}</strong></td>
                          <td>
                            <span class="badge {{ $isSettled ? 'bg-success' : 'bg-danger' }}">
                              {{ $isSettled ? 'Settled' : ucfirst(str_replace('_', ' ', $penalty->status)) }}
                            </span>
                          </td>
                        </tr>
                      @endforeach
                    </tbody>
                  </table>
                </div>
                <div class="mt-3">
                  <a href="#penalties" class="btn btn-sm btn-outline-danger w-100" data-bs-toggle="collapse" data-bs-target="#penaltySection">
                    <i class="bi bi-plus-circle me-1"></i>Issue New Penalty
                  </a>
                </div>
              </div>
            </div>
          @endif

          {{-- Issue Penalty Section (Collapsible) --}}
          <div class="card border-0 shadow-sm rounded-4 bg-white mb-4 collapse overflow-hidden" id="penaltySection">
            <div class="card-header bg-white border-bottom py-3 px-4">
              <h6 class="fw-bold mb-0 text-slate-800"><i class="bi bi-calculator-fill me-2 text-danger"></i>Issue New Penalty</h6>
            </div>
            <div class="card-body p-4">
              <form method="POST" action="{{ route('staff.bookings.penalties.store', $booking->booking_id) }}" id="penaltyForm">
                @csrf
                <div class="mb-3">
                  <label class="form-label small">Penalty Type</label>
                  <select name="penalty_type" id="penaltyType" class="form-select form-select-sm" required>
                    <option value="">Select type...</option>
                    <option value="late">Late Return</option>
                    <option value="fuel">Fuel Shortage</option>
                    <option value="damage">Damage/Accident</option>
                    <option value="accident">Accident</option>
                    <option value="other">Other</option>
                  </select>
                </div>

                {{-- Late Return Calculator --}}
                <div id="lateReturnSection" class="penalty-section" style="display: none;">
                  <div class="alert alert-info py-2 mb-3">
                    <small><strong>Formula:</strong> RM30 per hour late</small>
                  </div>
                  <div class="mb-2">
                    <label class="form-label small">Actual Return Time</label>
                    <input type="datetime-local" name="actual_return_time" id="actualReturnTime" class="form-control form-control-sm" onchange="calculateLateFee()">
                  </div>
                  <div class="mb-2">
                    <label class="form-label small">Hours Late</label>
                    <input type="number" name="hours_late" id="hoursLate" step="0.5" min="0" class="form-control form-control-sm" readonly>
                  </div>
                  <div class="mb-3">
                    <label class="form-label small">Calculated Amount</label>
                    <div class="input-group input-group-sm">
                      <span class="input-group-text">RM</span>
                      <input type="number" name="amount" id="lateAmount" step="0.01" class="form-control" readonly required>
                    </div>
                  </div>
                </div>

                {{-- Fuel Shortage Calculator --}}
                <div id="fuelShortageSection" class="penalty-section" style="display: none;">
                  <div class="alert alert-info py-2 mb-3">
                    <small><strong>Formula:</strong> RM10 per bar shortage</small>
                  </div>
                  @php
                    $beforeFuel = $booking->inspections ? $booking->inspections->where('type', 'before')->first()?->fuel_level : null;
                    $afterFuel = $booking->inspections ? $booking->inspections->where('type', 'after')->first()?->fuel_level : null;
                  @endphp
                  <div class="mb-2">
                    <label class="form-label small">Fuel Level at Pickup</label>
                    <input type="number" name="pickup_fuel" id="pickupFuel" min="0" max="8" class="form-control form-control-sm" value="{{ $beforeFuel ?? '' }}" onchange="calculateFuelPenalty()">
                  </div>
                  <div class="mb-2">
                    <label class="form-label small">Fuel Level at Return</label>
                    <input type="number" name="return_fuel" id="returnFuel" min="0" max="8" class="form-control form-control-sm" value="{{ $afterFuel ?? '' }}" onchange="calculateFuelPenalty()">
                  </div>
                  <div class="mb-2">
                    <label class="form-label small">Fuel Shortage (bars)</label>
                    <input type="number" name="fuel_shortage" id="fuelShortage" step="0.5" min="0" class="form-control form-control-sm" readonly>
                  </div>
                  <div class="mb-3">
                    <label class="form-label small">Calculated Amount</label>
                    <div class="input-group input-group-sm">
                      <span class="input-group-text">RM</span>
                      <input type="number" name="amount" id="fuelAmount" step="0.01" class="form-control" readonly required>
                    </div>
                  </div>
                </div>

                {{-- Damage/Accident Penalty --}}
                <div id="damageSection" class="penalty-section" style="display: none;">
                  <div class="mb-2">
                    <label class="form-label small">Damage Description</label>
                    <textarea name="description" class="form-control form-control-sm" rows="2" placeholder="Describe the damage/accident..."></textarea>
                  </div>
                  <div class="mb-2">
                    <label class="form-label small">Penalty Amount (RM)</label>
                    <input type="number" name="amount" id="damageAmount" step="0.01" min="0" class="form-control form-control-sm" onchange="calculateInstallment()" required>
                  </div>
                  <div class="mb-3">
                    <div class="form-check">
                      <input class="form-check-input" type="checkbox" name="is_installment" id="isInstallment" value="1" onchange="toggleInstallmentFields()">
                      <label class="form-check-label small" for="isInstallment">
                        Allow installment payment
                      </label>
                    </div>
                  </div>
                  <div id="installmentFields" style="display: none;">
                    <div class="mb-2">
                      <label class="form-label small">Number of Installments</label>
                      <input type="number" name="installment_count" id="installmentCount" min="2" max="12" class="form-control form-control-sm" value="3" onchange="calculateInstallment()">
                    </div>
                    <div class="mb-2">
                      <label class="form-label small">Monthly Payment (RM)</label>
                      <div class="input-group input-group-sm">
                        <span class="input-group-text">RM</span>
                        <input type="number" name="installment_amount" id="installmentAmount" step="0.01" class="form-control" readonly>
                      </div>
                    </div>
                  </div>
                </div>

                {{-- Other Penalty --}}
                <div id="otherSection" class="penalty-section" style="display: none;">
                  <div class="mb-2">
                    <label class="form-label small">Description</label>
                    <textarea name="description" class="form-control form-control-sm" rows="2" placeholder="Describe the penalty..."></textarea>
                  </div>
                  <div class="mb-3">
                    <label class="form-label small">Amount (RM)</label>
                    <input type="number" name="amount" id="otherAmount" step="0.01" min="0" class="form-control form-control-sm" required>
                  </div>
                </div>

                <button type="submit" class="btn btn-sm btn-outline-danger w-100" id="issuePenaltyBtn">
                  <i class="bi bi-exclamation-triangle me-1"></i>Issue Penalty
                </button>
              </form>
            </div>
          </div>

          {{-- Quick Actions --}}
          <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden">
            <div class="card-header bg-white border-bottom py-3 px-4">
              <h6 class="fw-bold mb-0 text-slate-800"><i class="bi bi-lightning-fill me-2 text-warning"></i>Operational Actions</h6>
            </div>
            <div class="card-body p-4">
              <button type="button" class="btn btn-sm btn-outline-primary w-100 mb-2" data-bs-toggle="modal" data-bs-target="#addPaymentModal">
                <i class="bi bi-plus-lg me-1"></i>Add Payment
              </button>
              <a href="{{ route('staff.cars.edit', $booking->car_id) }}" class="btn btn-sm btn-outline-secondary w-100 mb-2">
                <i class="bi bi-car-front me-1"></i>View Car Details
              </a>
              @if(Route::has('staff.customers.show'))
                <a href="{{ route('staff.customers.show', $booking->customer_id) }}" class="btn btn-sm btn-outline-secondary w-100">
                  <i class="bi bi-person me-1"></i>View Customer Profile
                </a>
              @endif
            </div>
          </div>

        </div>
      </div>
    </div>
  </div>
</div>

{{-- Add Payment Modal --}}
<div class="modal fade" id="addPaymentModal" tabindex="-1" aria-labelledby="addPaymentModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addPaymentModalLabel">Add Payment</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form method="POST" action="{{ route('staff.payments.store', $booking->booking_id) }}" enctype="multipart/form-data">
        @csrf
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Payment Type</label>
            <select name="payment_type" class="form-select" required>
              <option value="deposit">Deposit</option>
              <option value="rental">Rental Payment</option>
              <option value="penalty">Penalty Payment</option>
              <option value="refund">Refund</option>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Amount (RM)</label>
            <input type="number" name="amount" step="0.01" min="0" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Payment Method</label>
            <select name="payment_method" class="form-select" required>
              <option value="bank_transfer">Bank Transfer</option>
              <option value="cash">Cash</option>
              <option value="other">Other</option>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Payment Date</label>
            <input type="date" name="payment_date" class="form-control" value="{{ now()->format('Y-m-d') }}" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Receipt (Optional)</label>
            <input type="file" name="receipt" class="form-control" accept="image/*,.pdf">
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-hasta">Add Payment</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  // Penalty type dropdown handler
  const penaltyType = document.getElementById('penaltyType');
  if (penaltyType) {
    penaltyType.addEventListener('change', function() {
      // Hide all sections
      document.querySelectorAll('.penalty-section').forEach(section => {
        section.style.display = 'none';
      });
      
      // Show relevant section
      const type = this.value;
      if (type === 'late') {
        document.getElementById('lateReturnSection').style.display = 'block';
      } else if (type === 'fuel') {
        document.getElementById('fuelShortageSection').style.display = 'block';
      } else if (type === 'damage' || type === 'accident') {
        document.getElementById('damageSection').style.display = 'block';
      } else if (type === 'other') {
        document.getElementById('otherSection').style.display = 'block';
      }
    });
  }
  
  // Penalty form submission handler
  const penaltyForm = document.getElementById('penaltyForm');
  if (penaltyForm) {
    penaltyForm.addEventListener('submit', function(e) {
      // Basic validation - check penalty type
      const penaltyTypeEl = document.getElementById('penaltyType');
      if (!penaltyTypeEl || !penaltyTypeEl.value) {
        e.preventDefault();
        alert('Please select a penalty type.');
        return false;
      }
      
      const type = penaltyTypeEl.value;
      
      // Remove name attribute from all amount fields first
      const allAmountFields = ['lateAmount', 'fuelAmount', 'damageAmount', 'otherAmount'];
      allAmountFields.forEach(function(fieldId) {
        const field = document.getElementById(fieldId);
        if (field) {
          field.removeAttribute('name');
        }
      });
      
      // Find and activate the correct amount field based on type
      let amountField = null;
      if (type === 'late') {
        amountField = document.getElementById('lateAmount');
      } else if (type === 'fuel') {
        amountField = document.getElementById('fuelAmount');
      } else if (type === 'damage' || type === 'accident') {
        amountField = document.getElementById('damageAmount');
      } else if (type === 'other') {
        amountField = document.getElementById('otherAmount');
      }
      
      // Validate amount if field exists
      if (amountField) {
        // Set name attribute only on the active field
        amountField.setAttribute('name', 'amount');
        const amount = parseFloat(amountField.value) || 0;
        if (amount <= 0) {
          e.preventDefault();
          alert('Please ensure the penalty amount is calculated or entered correctly.');
          return false;
        }
      } else {
        e.preventDefault();
        alert('Amount field not found. Please refresh the page and try again.');
        return false;
      }
      
      // Show loading state
      const submitBtn = document.getElementById('issuePenaltyBtn');
      if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i>Issuing...';
      }
    });
  }
});

function calculateLateFee() {
  const actualReturnTime = document.getElementById('actualReturnTime').value;
  if (!actualReturnTime) return;
  
  const expectedReturnStr = '{{ $booking->end_datetime?->format("Y-m-d\TH:i") ?? "" }}';
  if (!expectedReturnStr) return;
  
  const expectedReturn = new Date(expectedReturnStr.replace(' ', 'T'));
  const actualReturn = new Date(actualReturnTime);
  
  if (actualReturn > expectedReturn) {
    const diffMs = actualReturn - expectedReturn;
    const diffHours = Math.ceil(diffMs / (1000 * 60 * 60));
    const amount = diffHours * 30;
    
    document.getElementById('hoursLate').value = diffHours;
    document.getElementById('lateAmount').value = amount.toFixed(2);
  } else {
    document.getElementById('hoursLate').value = 0;
    document.getElementById('lateAmount').value = 0;
  }
}

function calculateFuelPenalty() {
  const pickupFuel = parseFloat(document.getElementById('pickupFuel').value) || 0;
  const returnFuel = parseFloat(document.getElementById('returnFuel').value) || 0;
  
  if (pickupFuel > 0 && returnFuel >= 0) {
    const shortage = Math.max(0, pickupFuel - returnFuel);
    const amount = shortage * 10;
    
    document.getElementById('fuelShortage').value = shortage.toFixed(1);
    document.getElementById('fuelAmount').value = amount.toFixed(2);
  }
}

function toggleInstallmentFields() {
  const isInstallment = document.getElementById('isInstallment').checked;
  const installmentFields = document.getElementById('installmentFields');
  
  if (isInstallment) {
    installmentFields.style.display = 'block';
    calculateInstallment();
  } else {
    installmentFields.style.display = 'none';
  }
}

function calculateInstallment() {
  const totalAmount = parseFloat(document.getElementById('damageAmount').value) || 0;
  const installmentCount = parseInt(document.getElementById('installmentCount').value) || 1;
  
  if (totalAmount > 0 && installmentCount > 0) {
    const monthlyPayment = totalAmount / installmentCount;
    document.getElementById('installmentAmount').value = monthlyPayment.toFixed(2);
  }
}
</script>
@endsection
