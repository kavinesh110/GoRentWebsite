@extends('layouts.app')
@section('title', 'Booking Details - Staff Dashboard')

@section('content')
<div class="d-flex" style="min-height: calc(100vh - 60px);">
  @include('staff._nav')
  <div class="flex-fill" style="background: #f8fafc;">
    <div class="container-fluid px-4 px-md-5" style="padding-top: 32px; padding-bottom: 48px;">
  <div class="mb-4">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb mb-2">
        <li class="breadcrumb-item"><a href="{{ route('staff.bookings') }}" class="text-decoration-none">Bookings</a></li>
        <li class="breadcrumb-item active">#{{ $booking->booking_id }}</li>
      </ol>
    </nav>
    <div class="d-flex align-items-center gap-3">
      <h1 class="h3 fw-bold mb-0" style="color:#1a202c;">Booking Details</h1>
      @php
        $statusClasses = [
          'completed' => 'bg-success text-white',
          'deposit_returned' => 'bg-success text-white',
          'ongoing' => 'bg-info text-white',
          'active' => 'bg-info text-white',
          'cancelled' => 'bg-danger text-white',
          'confirmed' => 'bg-primary text-white',
          'verified' => 'bg-info text-white',
          'created' => 'bg-warning text-dark'
        ];
        $statusLabels = [
          'created' => 'Created',
          'verified' => 'Verified',
          'confirmed' => 'Before Pickup Inspection',
          'active' => 'Ongoing',
          'ongoing' => 'Ongoing',
          'completed' => 'Car Returned',
          'deposit_returned' => 'Deposit Returned',
          'cancelled' => 'Cancelled'
        ];
        $statusClass = $statusClasses[$booking->status] ?? 'bg-secondary text-white';
        $statusLabel = $statusLabels[$booking->status] ?? ucfirst($booking->status ?? 'N/A');
      @endphp
      <span class="badge rounded-pill px-3 py-2 {{ $statusClass }} shadow-sm">
        {{ $statusLabel }}
      </span>
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

  <div class="row g-4">
    {{-- FULL WIDTH: BOOKING & PAYMENT DETAILS --}}
    <div class="col-12">
      @php
        $hasBeforeInspection = $booking->inspections && $booking->inspections->where('type', 'before')->whereNotNull('photos')->count() > 0;
        $beforeInspection = $booking->inspections ? $booking->inspections->where('type', 'before')->first() : null;
        $afterInspection = $booking->inspections ? $booking->inspections->where('type', 'after')->first() : null;
        $hasAfterInspection = $afterInspection && $afterInspection->status === 'completed' && $afterInspection->photos;
        
        // Check if "Before Pickup Inspection" (confirmed) stage is complete
        // Requires: Before pickup inspection form submitted (staff)
        $hasAgreementSigned = $booking->agreement_signed_at !== null;
        $hasAgreementPhoto = $booking->rentalPhotos && $booking->rentalPhotos->where('photo_type', 'agreement')->count() > 0;
        $hasPickupPhoto = $booking->rentalPhotos && $booking->rentalPhotos->where('photo_type', 'pickup')->count() > 0;
        $isPrePickupInspectionComplete = $hasBeforeInspection;
        
        // Check if "Car Collected" (active) stage is complete
        // Requires: 1) Signed Agreement Photo uploaded (customer), 2) Receiving Car Keys Photo uploaded (customer), 3) Before Pickup Inspection form submitted (staff)
        $isCarCollectedComplete = $hasAgreementPhoto && $hasPickupPhoto && $hasBeforeInspection;
      @endphp

      {{-- BOOKING PROGRESS BAR --}}
      <div class="card border-0 shadow-sm mb-4 overflow-hidden shadow-soft">
        <div class="card-header bg-dark text-white border-0 py-3 px-4">
          <h6 class="fw-bold mb-0"><i class="bi bi-graph-up me-2"></i>Booking Progress</h6>
        </div>
        <div class="card-body p-4">
          @php
            $statusOrder = ['created', 'verified', 'confirmed', 'active', 'completed', 'after_inspection', 'deposit_returned'];
            $statusIcons = [
              'created' => 'bi-file-earmark-plus',
              'verified' => 'bi-check-circle',
              'confirmed' => 'bi-box-arrow-in-right',
              'active' => 'bi-car-front',
              'completed' => 'bi-check-circle-fill',
              'after_inspection' => 'bi-clipboard-check',
              'deposit_returned' => 'bi-wallet2',
              'cancelled' => 'bi-x-circle'
            ];
            $statusNames = [
              'created' => 'Created',
              'verified' => 'Verified',
              'confirmed' => 'Before Pickup Inspection',
              'active' => 'Car Collected',
              'completed' => 'Car Returned',
              'after_inspection' => 'After Return Inspection',
              'deposit_returned' => 'Deposit Returned',
              'cancelled' => 'Cancelled'
            ];
            
            // Determine effective status for progress bar
            // If after return inspection is completed and status is still 'completed',
            // treat it as if we're at after_inspection stage
            // But if deposit is already paid (deposit_decision is set), use actual status
            $effectiveStatus = $booking->status;
            if ($booking->status === 'completed' && $hasAfterInspection && !$booking->deposit_decision) {
              $effectiveStatus = 'after_inspection';
            }
            // If deposit is paid, deposit_returned should be completed, not current
            if ($booking->deposit_decision && $booking->status === 'deposit_returned') {
              // Use a status that's beyond deposit_returned (or keep as deposit_returned but mark as completed)
              // We'll handle this in the isCurrent check below
            }
            
            $currentStatusIndex = array_search($effectiveStatus, $statusOrder);
            if ($currentStatusIndex === false) $currentStatusIndex = -1;
          @endphp

          {{-- Progress Bar --}}
          <div class="booking-progress mb-4">
            <div class="progress-steps">
              @foreach($statusOrder as $index => $status)
                @php
                  // Determine completion status for each step (no current stage display)
                  if ($status === 'created') {
                    // Created is always completed (by default, booking exists)
                    $isCompleted = true;
                  } elseif ($status === 'verified') {
                    // Verified is completed when status is verified or beyond
                    $isCompleted = in_array($effectiveStatus, ['verified', 'confirmed', 'active', 'completed', 'after_inspection', 'deposit_returned']);
                  } elseif ($status === 'confirmed') {
                    // "Before Pickup Inspection" is completed only when before pickup inspection form is filled
                    $isCompleted = $isPrePickupInspectionComplete;
                  } elseif ($status === 'active') {
                    // "Car Collected" is completed only when:
                    // 1) Status is active or beyond, AND
                    // 2) Signed Agreement Photo uploaded (customer), AND
                    // 3) Receiving Car Keys Photo uploaded (customer), AND
                    // 4) Before Pickup Inspection form submitted (staff)
                    $isCompleted = in_array($effectiveStatus, ['active', 'completed', 'after_inspection', 'deposit_returned']) && $isCarCollectedComplete;
                  } elseif ($status === 'completed') {
                    // "Car Returned" is completed when booking status is completed or beyond
                    // This should always be true once car is returned, regardless of after inspection status
                    // Check the actual booking status, not the effective status
                    $isCompleted = in_array($booking->status, ['completed', 'deposit_returned']);
                  } elseif ($status === 'after_inspection') {
                    // "After Return Inspection" is completed only when after return inspection form is submitted
                    $isCompleted = $hasAfterInspection;
                  } elseif ($status === 'deposit_returned') {
                    // For deposit_returned step, also check if deposit_decision is set
                    if ($booking->deposit_decision !== null) {
                      $isCompleted = true;
                    } else {
                      // Only completed if we've passed it
                      $isCompleted = $index < $currentStatusIndex;
                    }
                  }
                  
                  $isCancelled = $booking->status === 'cancelled';
                @endphp
                <div class="progress-step {{ $isCompleted ? 'completed' : '' }} {{ $isCancelled && $status !== 'cancelled' ? 'cancelled' : '' }}">
                  <div class="step-circle {{ $isCompleted ? 'bg-success' : 'bg-light border' }}">
                    @if($isCompleted)
                      <i class="bi bi-check-lg text-white"></i>
                    @else
                      <i class="bi {{ $statusIcons[$status] ?? 'bi-circle' }} text-muted"></i>
                    @endif
                  </div>
                  <div class="step-label mt-2">
                    <div class="fw-bold small {{ $isCompleted ? 'text-success' : 'text-muted' }}">
                      {{ $statusNames[$status] }}
                    </div>
                  </div>
                </div>
              @endforeach
            </div>
          </div>

          @if($booking->status === 'cancelled')
            <div class="alert alert-danger border-0 mt-3 mb-0">
              <i class="bi bi-exclamation-triangle me-2"></i>
              <strong>Booking Cancelled</strong>
            </div>
          @endif
        </div>
      </div>

      {{-- CREATED SECTION (Non-collapsible) --}}
      <div class="card border-0 shadow-sm mb-3">
        <div class="card-header bg-white border-0 pt-4 px-4">
          <div class="d-flex align-items-center justify-content-between">
            <h5 class="fw-bold mb-0"><i class="bi bi-file-earmark-plus me-2 text-warning"></i>Created</h5>
          </div>
        </div>
        <div class="card-body p-0">
          {{-- Booking Overview Card --}}
          <div class="card border-0">
            <div class="card-header bg-white border-0 pt-4 px-4">
              <h5 class="fw-bold mb-0"><i class="bi bi-info-circle me-2 text-primary"></i>Overview</h5>
            </div>
            <div class="card-body p-4">
              <div class="row g-4">
                <div class="col-md-6">
                  <div class="p-3 rounded-4 bg-light border-start border-4 border-primary">
                    <div class="small text-muted fw-bold text-uppercase mb-2" style="font-size: 10px;">Customer</div>
                    <div class="d-flex align-items-center gap-3">
                      <div class="bg-white rounded-circle d-flex align-items-center justify-content-center fw-bold text-primary shadow-sm" style="width: 40px; height: 40px;">
                        {{ substr($booking->customer->full_name ?? 'N', 0, 1) }}
                      </div>
                      <div>
                        <div class="fw-bold text-dark">{{ $booking->customer->full_name ?? 'N/A' }}</div>
                        <div class="small text-muted">{{ $booking->customer->email ?? '' }}</div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="p-3 rounded-4 bg-light border-start border-4 border-dark">
                    <div class="small text-muted fw-bold text-uppercase mb-2" style="font-size: 10px;">Vehicle</div>
                    <div class="d-flex align-items-center gap-3">
                      <div class="bg-white rounded-4 d-flex align-items-center justify-content-center shadow-sm" style="width: 40px; height: 40px;">
                        <i class="bi bi-car-front text-dark h5 mb-0"></i>
                      </div>
                      <div>
                        <div class="fw-bold text-dark">{{ $booking->car->brand ?? 'N/A' }} {{ $booking->car->model ?? '' }}</div>
                        <div class="small text-muted">Plate: <span class="badge bg-white text-dark border py-0">{{ $booking->car->plate_number ?? 'N/A' }}</span></div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="small text-muted fw-bold text-uppercase mb-1" style="font-size: 10px;">Pickup Schedule</div>
                  <div class="fw-semibold text-dark"><i class="bi bi-calendar-check me-2 text-muted"></i>{{ $booking->start_datetime?->format('D, d M Y') ?? 'N/A' }}</div>
                  <div class="small text-muted ms-4">{{ $booking->start_datetime?->format('H:i') ?? '' }} @ {{ $booking->pickup_location ?? 'N/A' }}</div>
                </div>
                <div class="col-md-6">
                  <div class="small text-muted fw-bold text-uppercase mb-1" style="font-size: 10px;">Return Schedule</div>
                  <div class="fw-semibold text-dark"><i class="bi bi-calendar-x me-2 text-muted"></i>{{ $booking->end_datetime?->format('D, d M Y') ?? 'N/A' }}</div>
                  <div class="small text-muted ms-4">{{ $booking->end_datetime?->format('H:i') ?? '' }} @ {{ $booking->dropoff_location ?? 'N/A' }}</div>
                </div>
              </div>
            </div>
          </div>

          {{-- Pricing & Payments Card --}}
          <div class="card border-0">
            <div class="card-header bg-white border-0 pt-4 px-4">
              <h5 class="fw-bold mb-0"><i class="bi bi-cash-stack me-2 text-success"></i>Pricing & Payments</h5>
            </div>
            <div class="card-body p-4">
              <div class="row g-4 mb-4">
                <div class="col-md-6">
                  <div class="p-4 rounded-4 bg-dark text-white shadow-soft h-100">
                    <div class="small text-white-50 fw-bold text-uppercase mb-1" style="font-size: 10px;">Final Amount Due</div>
                    <div class="h2 fw-bold mb-0">RM {{ number_format($booking->final_amount ?? 0, 2) }}</div>
                    <hr class="border-white opacity-10 my-3">
                    <div class="d-flex justify-content-between small opacity-75">
                      <span>Base Price ({{ $booking->rental_hours }}h)</span>
                      <span>RM {{ number_format($booking->base_price ?? 0, 2) }}</span>
                    </div>
                    @if(($booking->promo_discount ?? 0) > 0)
                      <div class="d-flex justify-content-between small text-success">
                        <span>Promo Discount</span>
                        <span>-RM {{ number_format($booking->promo_discount, 2) }}</span>
                      </div>
                    @endif
                    @if(($booking->voucher_discount ?? 0) > 0)
                      <div class="d-flex justify-content-between small text-success">
                        <span>Voucher Applied</span>
                        <span>-RM {{ number_format($booking->voucher_discount, 2) }}</span>
                      </div>
                    @endif
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="p-4 rounded-4 bg-light border h-100 d-flex flex-column justify-content-center">
                    @php
                      $totalPaid = $booking->payments->where('status', 'verified')->sum('amount');
                      $totalPending = $booking->payments->where('status', 'pending')->sum('amount');
                      $remaining = max(0, ($booking->final_amount ?? 0) - $totalPaid);
                    @endphp
                    <div class="d-flex justify-content-between align-items-center mb-3">
                      <div class="small text-muted fw-bold text-uppercase" style="font-size: 10px;">Payment Status</div>
                      @if($remaining <= 0)
                        <span class="badge bg-success-subtle text-success border border-success-subtle px-3">Fully Paid</span>
                      @else
                        <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-3">Pending RM {{ number_format($remaining, 2) }}</span>
                      @endif
                    </div>
                    <div class="mb-3">
                      <div class="d-flex justify-content-between small mb-1">
                        <span class="text-muted">Collected</span>
                        <span class="fw-bold text-success">RM {{ number_format($totalPaid, 2) }}</span>
                      </div>
                      <div class="progress" style="height: 8px;">
                        <div class="progress-bar bg-success" style="width: {{ ($booking->final_amount > 0) ? ($totalPaid / $booking->final_amount * 100) : 0 }}%"></div>
                      </div>
                    </div>
                    @if($totalPending > 0)
                      <div class="d-flex align-items-center gap-2 text-warning small fw-bold">
                        <i class="bi bi-clock-history"></i>
                        Awaiting verification: RM {{ number_format($totalPending, 2) }}
                      </div>
                    @endif
                  </div>
                </div>
              </div>

              @if($booking->payments && $booking->payments->count() > 0)
                <div class="table-responsive rounded-4 border">
                  <table class="table table-hover align-middle mb-0 small">
                    <thead class="bg-light">
                      <tr>
                        <th class="ps-3">Type</th>
                        <th>Method</th>
                        <th>Amount</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th class="text-end pe-3">Action</th>
                      </tr>
                    </thead>
                    <tbody>
                      @foreach($booking->payments as $payment)
                        <tr>
                          <td class="ps-3 fw-bold">{{ ucfirst($payment->payment_type) }}</td>
                          <td>
                            <span class="text-muted">{{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}</span>
                          </td>
                          <td class="fw-bold">RM {{ number_format($payment->amount, 2) }}</td>
                          <td class="text-muted">{{ $payment->payment_date->format('d/m H:i') }}</td>
                          <td>
                            <span class="badge {{ $payment->status === 'verified' ? 'bg-success-subtle text-success' : 'bg-warning-subtle text-warning' }} rounded-pill px-2">
                              {{ ucfirst($payment->status) }}
                            </span>
                          </td>
                          <td class="text-end pe-3">
                            <div class="d-flex gap-2 justify-content-end">
                              @if($payment->receipt_url)
                                <a href="{{ $payment->receipt_url }}" target="_blank" class="btn btn-sm btn-white border px-2 py-0" title="View Receipt">
                                  <i class="bi bi-file-earmark-image"></i>
                                </a>
                              @endif
                              @if($payment->status === 'pending')
                                <form method="POST" action="{{ route('staff.payments.verify', $payment->payment_id) }}" class="d-inline">
                                  @csrf
                                  @method('PATCH')
                                  <button type="submit" class="btn btn-sm btn-success py-0 px-2 fw-bold">Verify</button>
                                </form>
                              @endif
                            </div>
                          </td>
                        </tr>
                      @endforeach
                    </tbody>
                  </table>
                </div>
              @endif
            </div>
          </div>
        </div>
      </div>

      {{-- Status Accordion Sections (Collapsible) --}}
      <div class="accordion" id="statusAccordion">

      {{-- VERIFIED SECTION --}}
      <div class="accordion-item border-0 shadow-sm mb-3">
        <h2 class="accordion-header">
          <button class="accordion-button {{ $booking->status === 'verified' ? '' : 'collapsed' }}" type="button" data-bs-toggle="collapse" data-bs-target="#verifiedSection" aria-expanded="{{ $booking->status === 'verified' ? 'true' : 'false' }}">
            <span class="fw-bold me-2"><i class="bi bi-check-circle me-2 text-info"></i>Verified</span>
          </button>
        </h2>
        <div id="verifiedSection" class="accordion-collapse collapse {{ $booking->status === 'verified' ? 'show' : '' }}" data-bs-parent="#statusAccordion">
          <div class="accordion-body p-0">
            <div class="card border-0">
              <div class="card-body p-4">
                @if($booking->status === 'created')
                  <div class="alert alert-info border-0 shadow-sm mb-0">
                    <div class="d-flex align-items-center justify-content-between">
                      <div>
                        <i class="bi bi-info-circle me-2"></i>
                        <strong>Verify this booking</strong> to proceed to the next step.
                      </div>
                      <form method="POST" action="{{ route('staff.bookings.update-status', $booking->booking_id) }}" class="d-inline">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="verified">
                        <button type="submit" class="btn btn-info text-white shadow-sm">
                          <i class="bi bi-check-circle me-2"></i>Verify Booking
                        </button>
                      </form>
                    </div>
                  </div>
                @else
                  <div class="text-center py-4">
                    <i class="bi bi-check-circle-fill text-success display-4 mb-3"></i>
                    <p class="text-muted mb-0">This booking has been verified.</p>
                  </div>
                @endif
              </div>
            </div>
          </div>
        </div>
      </div>

      {{-- BEFORE PICKUP INSPECTION SECTION --}}
      <div class="accordion-item border-0 shadow-sm mb-3">
        <h2 class="accordion-header">
          <button class="accordion-button {{ $booking->status === 'confirmed' ? '' : 'collapsed' }}" type="button" data-bs-toggle="collapse" data-bs-target="#readyToPickupSection" aria-expanded="{{ $booking->status === 'confirmed' ? 'true' : 'false' }}">
            <span class="fw-bold me-2"><i class="bi bi-box-arrow-in-right me-2 text-primary"></i>Before Pickup Inspection</span>
          </button>
        </h2>
        <div id="readyToPickupSection" class="accordion-collapse collapse {{ $booking->status === 'confirmed' ? 'show' : '' }}" data-bs-parent="#statusAccordion">
          <div class="accordion-body p-0">
            {{-- Before-Pickup Inspection --}}
            <div class="card border-0 {{ in_array($booking->status, ['created', 'verified']) && !$hasBeforeInspection ? 'border-start border-4 border-warning' : '' }}">
              <div class="card-header bg-white border-0 pt-4 px-4">
                <div class="d-flex align-items-center justify-content-between">
                  <h6 class="fw-bold mb-0"><i class="bi bi-clipboard-check me-2"></i>Before-Pickup Inspection</h6>
                  @if($hasBeforeInspection)
                    <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Completed</span>
                  @else
                    <span class="badge bg-warning text-dark"><i class="bi bi-exclamation-triangle me-1"></i>Required</span>
                  @endif
                </div>
                @if(in_array($booking->status, ['created', 'verified']) && !$hasBeforeInspection)
                  <p class="text-muted small mb-0 mt-2"><i class="bi bi-info-circle me-1"></i>Upload inspection photos before confirming this booking.</p>
                @endif
              </div>
              <div class="card-body p-4 pt-2">
                @if($beforeInspection && $beforeInspection->photos)
                  <div class="row g-2 mb-3">
                    @foreach($beforeInspection->photos as $photo)
                      <div class="col-4">
                        <img src="{{ asset('storage/' . $photo) }}" alt="Inspection" class="img-fluid rounded-3" style="height: 80px; width: 100%; object-fit: cover; cursor: pointer;" onclick="window.open('{{ asset('storage/' . $photo) }}', '_blank')">
                      </div>
                    @endforeach
                  </div>
                @endif
                <form method="POST" action="{{ route('staff.bookings.inspections.store', $booking->booking_id) }}" enctype="multipart/form-data">
                  @csrf
                  <input type="hidden" name="inspection_type" value="before">
                  <div class="row g-2">
                    <div class="col-6 mb-2">
                      <label class="form-label x-small">Fuel Level (0-8)</label>
                      <input type="number" name="fuel_level" min="0" max="8" class="form-control form-control-sm" value="{{ $beforeInspection->fuel_level ?? 8 }}" required>
                    </div>
                    <div class="col-6 mb-2">
                      <label class="form-label x-small">Odometer (km)</label>
                      <input type="number" name="odometer_reading" class="form-control form-control-sm" value="{{ $beforeInspection->odometer_reading ?? '' }}" required>
                    </div>
                    <div class="col-12 mb-2">
                      <label class="form-label x-small">Inspection Photos <span class="text-danger">*</span></label>
                      <input type="file" name="photos[]" class="form-control form-control-sm" accept="image/*" multiple {{ $hasBeforeInspection ? '' : 'required' }}>
                      <small class="text-muted">Upload car condition photos (max 10)</small>
                    </div>
                    <div class="col-12 mb-2">
                      <label class="form-label x-small">Notes</label>
                      <textarea name="notes" class="form-control form-control-sm" rows="2">{{ $beforeInspection->notes ?? '' }}</textarea>
                    </div>
                  </div>
                  <button type="submit" class="btn btn-sm btn-dark w-100">
                    <i class="bi bi-camera me-1"></i>{{ $hasBeforeInspection ? 'Update Inspection' : 'Save Inspection' }}
                  </button>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>

      {{-- AFTER RETURN INSPECTION SECTION --}}
      <div class="accordion-item border-0 shadow-sm mb-3">
        <h2 class="accordion-header">
          <button class="accordion-button {{ in_array($booking->status, ['completed', 'deposit_returned']) ? '' : 'collapsed' }}" type="button" data-bs-toggle="collapse" data-bs-target="#afterReturnInspectionSection" aria-expanded="{{ in_array($booking->status, ['completed', 'deposit_returned']) ? 'true' : 'false' }}">
            <span class="fw-bold me-2"><i class="bi bi-clipboard-check me-2 text-info"></i>After Return Inspection</span>
          </button>
        </h2>
        <div id="afterReturnInspectionSection" class="accordion-collapse collapse {{ in_array($booking->status, ['completed', 'deposit_returned']) ? 'show' : '' }}" data-bs-parent="#statusAccordion">
          <div class="accordion-body p-0">
            <div class="card border-0">
              <div class="card-header bg-white border-0 pt-4 px-4">
                <div class="d-flex align-items-center justify-content-between">
                  <h6 class="fw-bold mb-0"><i class="bi bi-clipboard-check me-2"></i>After-Return Inspection</h6>
                  @if($hasAfterInspection)
                    <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Completed</span>
                  @else
                    <span class="badge bg-secondary">Pending</span>
                  @endif
                </div>
              </div>
              <div class="card-body p-4 pt-2">
                @if($afterInspection && $afterInspection->photos)
                  <div class="row g-2 mb-3">
                    @foreach($afterInspection->photos as $photo)
                      <div class="col-4">
                        <img src="{{ asset('storage/' . $photo) }}" alt="Inspection" class="img-fluid rounded-3" style="height: 80px; width: 100%; object-fit: cover; cursor: pointer;" onclick="window.open('{{ asset('storage/' . $photo) }}', '_blank')">
                      </div>
                    @endforeach
                  </div>
                @endif
                <form method="POST" action="{{ route('staff.bookings.inspections.store', $booking->booking_id) }}" enctype="multipart/form-data">
                  @csrf
                  <input type="hidden" name="inspection_type" value="after">
                  <div class="row g-2">
                    <div class="col-6 mb-2">
                      <label class="form-label x-small">Fuel Level (0-8)</label>
                      <input type="number" name="fuel_level" min="0" max="8" class="form-control form-control-sm" value="{{ $afterInspection->fuel_level ?? '' }}" required>
                    </div>
                    <div class="col-6 mb-2">
                      <label class="form-label x-small">Odometer (km)</label>
                      <input type="number" name="odometer_reading" class="form-control form-control-sm" value="{{ $afterInspection->odometer_reading ?? '' }}" required>
                    </div>
                    <div class="col-12 mb-2">
                      <label class="form-label x-small">Inspection Photos</label>
                      <input type="file" name="photos[]" class="form-control form-control-sm" accept="image/*" multiple>
                      <small class="text-muted">Upload car condition photos (max 10)</small>
                    </div>
                    <div class="col-12 mb-2">
                      <label class="form-label x-small">Notes</label>
                      <textarea name="notes" class="form-control form-control-sm" rows="2">{{ $afterInspection->notes ?? '' }}</textarea>
                    </div>
                  </div>
                  <button type="submit" class="btn btn-sm btn-dark w-100">
                    <i class="bi bi-camera me-1"></i>{{ $hasAfterInspection ? 'Update Inspection' : 'Save Inspection' }}
                  </button>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>

      {{-- DEPOSIT RETURNED SECTION --}}
      <div class="accordion-item border-0 shadow-sm mb-3">
        <h2 class="accordion-header">
          <button class="accordion-button {{ $booking->status === 'deposit_returned' ? '' : 'collapsed' }}" type="button" data-bs-toggle="collapse" data-bs-target="#depositReturnedSection" aria-expanded="{{ $booking->status === 'deposit_returned' ? 'true' : 'false' }}">
            <span class="fw-bold me-2"><i class="bi bi-arrow-return-left me-2 text-success"></i>Deposit Returned</span>
          </button>
        </h2>
        <div id="depositReturnedSection" class="accordion-collapse collapse {{ $booking->status === 'deposit_returned' ? 'show' : '' }}" data-bs-parent="#statusAccordion">
          <div class="accordion-body p-0">
            <div class="card border-0">
              <div class="card-body p-4">
                @if($booking->status === 'deposit_returned' || $booking->deposit_decision)
                  <div class="text-center py-4">
                    <i class="bi bi-check-circle-fill text-success display-4 mb-3"></i>
                    <p class="text-muted mb-0">Deposit has been paid for this booking.</p>
                  </div>
                @elseif($booking->status === 'completed' && !$booking->deposit_decision)
                  @php
                    // Get the deposit payment to display bank details
                    $depositPayment = $booking->payments->where('payment_type', 'deposit')->where('status', 'verified')->first();
                  @endphp
                  {{-- Process Deposit Form --}}
                  <div class="card border-0 shadow-sm mb-0">
                    <div class="card-header bg-warning-subtle text-warning-emphasis border-0 py-3 px-4">
                      <h6 class="fw-bold mb-0"><i class="bi bi-wallet2 me-2"></i>Process Deposit</h6>
                    </div>
                    <div class="card-body p-4">
                      <form method="POST" action="{{ route('staff.bookings.process-refund', $booking->booking_id) }}" enctype="multipart/form-data">
                        @csrf
                        {{-- Fixed Decision --}}
                        <div class="mb-2">
                          <label class="form-label small">Decision</label>
                          <input type="text" class="form-control bg-light" value="Refund Deposit" readonly>
                          <input type="hidden" name="deposit_decision" value="refund">
                        </div>
                        {{-- Fixed Amount --}}
                        <div class="mb-2">
                          <label class="form-label small">Amount (RM)</label>
                          <div class="input-group">
                            <span class="input-group-text border-end-0 bg-white">RM</span>
                            <input type="text" class="form-control border-start-0 ps-0 bg-light" value="{{ number_format($booking->deposit_amount, 2) }}" readonly>
                            <input type="hidden" name="deposit_refund_amount" value="{{ $booking->deposit_amount }}">
                          </div>
                        </div>
                        {{-- Display Bank Details --}}
                        @if($depositPayment)
                          <div class="mb-3 p-3 bg-light rounded-3 border">
                            <h6 class="fw-bold small mb-3"><i class="bi bi-bank me-2"></i>Customer Bank Details</h6>
                            <div class="row g-2">
                              <div class="col-12">
                                <label class="form-label x-small text-muted mb-1">Bank Name</label>
                                <div class="fw-bold text-dark">{{ $depositPayment->bank_name ?? 'N/A' }}</div>
                              </div>
                              <div class="col-12">
                                <label class="form-label x-small text-muted mb-1">Account Holder Name</label>
                                <div class="fw-bold text-dark">{{ $depositPayment->account_holder_name ?? 'N/A' }}</div>
                              </div>
                              <div class="col-12">
                                <label class="form-label x-small text-muted mb-1">Account Number</label>
                                <div class="fw-bold text-dark">{{ $depositPayment->account_number ?? 'N/A' }}</div>
                              </div>
                            </div>
                          </div>
                        @else
                          <div class="mb-3 p-3 bg-warning-subtle rounded-3 border border-warning">
                            <p class="small mb-0 text-warning-emphasis">
                              <i class="bi bi-exclamation-triangle me-2"></i>Bank details not available. Deposit payment may not be verified yet.
                            </p>
                          </div>
                        @endif
                        {{-- Upload Receipt --}}
                        <div class="mb-3">
                          <label class="form-label small">Refund Receipt <span class="text-danger">*</span></label>
                          <input type="file" name="refund_receipt" class="form-control" accept="image/*,.pdf" required>
                          <small class="text-muted">Upload receipt or proof of refund transaction (image or PDF, max 5MB)</small>
                        </div>
                        <button type="submit" class="btn btn-warning w-100 fw-bold">Process Decision</button>
                      </form>
                    </div>
                  </div>
                @else
                  <div class="alert alert-info border-0 shadow-sm mb-0">
                    <i class="bi bi-info-circle me-2"></i>
                    <strong>Deposit return</strong> will be processed after the booking is completed and after return inspection is done.
                  </div>
                @endif
              </div>
            </div>
          </div>
        </div>
      </div>

      {{-- Close Accordion --}}
      </div>

      {{-- Penalties & Photos Tabs Card (Keep outside accordion) --}}
      <div class="card border-0 shadow-sm overflow-hidden mb-4">
        <div class="card-header bg-white border-0 pt-4 px-4">
          <ul class="nav nav-pills gap-2" id="detailTabs" role="tablist">
            <li class="nav-item">
              <button class="nav-link active fw-bold small text-uppercase px-4" id="penalties-tab" data-bs-toggle="pill" data-bs-target="#penalties" type="button">Penalties</button>
            </li>
            <li class="nav-item">
              <button class="nav-link fw-bold small text-uppercase px-4" id="photos-tab" data-bs-toggle="pill" data-bs-target="#photos" type="button">Rental Photos</button>
            </li>
          </ul>
        </div>
        <div class="card-body p-4 pt-0">
          <div class="tab-content mt-4" id="detailTabsContent">
            {{-- Penalties Tab --}}
            <div class="tab-pane fade show active" id="penalties" role="tabpanel">
              @if($booking->penalties && $booking->penalties->count() > 0)
                <div class="table-responsive rounded-4 border">
                  <table class="table table-hover align-middle mb-0 small">
                    <thead class="bg-light">
                      <tr>
                        <th class="ps-3">Reason</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th class="pe-3">Description</th>
                      </tr>
                    </thead>
                    <tbody>
                      @foreach($booking->penalties as $penalty)
                        <tr>
                          <td class="ps-3 fw-bold">{{ ucfirst($penalty->penalty_type) }}</td>
                          <td class="fw-bold">RM {{ number_format($penalty->amount, 2) }}</td>
                          <td>
                            <span class="badge rounded-pill {{ $penalty->status === 'settled' ? 'bg-success' : 'bg-danger' }} px-3">
                              {{ ucfirst(str_replace('_', ' ', $penalty->status)) }}
                            </span>
                          </td>
                          <td class="text-muted pe-3">{{ $penalty->description ?? '-' }}</td>
                        </tr>
                      @endforeach
                    </tbody>
                  </table>
                </div>
              @else
                <div class="text-center py-4 bg-light rounded-4">
                  <i class="bi bi-shield-check text-success h2 d-block mb-2"></i>
                  <p class="text-muted mb-0">No penalties recorded for this session.</p>
                </div>
              @endif
            </div>

            {{-- Photos Tab --}}
            <div class="tab-pane fade" id="photos" role="tabpanel">
              @if($booking->rentalPhotos && $booking->rentalPhotos->count() > 0)
                <div class="row g-3">
                  @foreach($booking->rentalPhotos as $photo)
                    <div class="col-md-4">
                      <div class="card border shadow-none overflow-hidden h-100">
                        <img src="{{ $photo->photo_url }}" class="card-img-top" style="height: 150px; object-fit: cover; cursor: pointer;" onclick="window.open('{{ $photo->photo_url }}', '_blank')">
                        <div class="card-body p-2 bg-light">
                          <div class="d-flex justify-content-between align-items-center">
                            <span class="badge bg-dark small">{{ ucfirst($photo->photo_type) }}</span>
                            <small class="text-muted">{{ $photo->taken_at->format('d/m H:i') }}</small>
                          </div>
                        </div>
                      </div>
                    </div>
                  @endforeach
                </div>
              @else
                <div class="text-center py-4 bg-light rounded-4">
                  <i class="bi bi-camera text-muted h2 d-block mb-2"></i>
                  <p class="text-muted mb-0">No vehicle inspection photos uploaded yet.</p>
                </div>
              @endif
            </div>
          </div>
        </div>
      </div>
    </div>

    {{-- ACTIONS PANEL (Full Width Below) --}}
    <div class="col-12">
      <div class="row g-4">
        {{-- Add Penalty --}}
        <div class="col-md-12">
          <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 pt-4 px-4">
              <h6 class="fw-bold mb-0">Issue Penalty</h6>
            </div>
            <div class="card-body p-4 pt-2">
              <form method="POST" action="{{ route('staff.bookings.penalties.store', $booking->booking_id) }}">
                @csrf
                <div class="mb-2">
                  <label class="form-label x-small">Reason</label>
                  <select name="penalty_type" class="form-select form-select-sm" required>
                    <option value="late">Late return</option>
                    <option value="fuel">Fuel difference</option>
                    <option value="damage">Damage</option>
                    <option value="other">Other</option>
                  </select>
                </div>
                <div class="mb-3">
                  <label class="form-label x-small">Amount (RM)</label>
                  <input type="number" name="amount" step="0.01" class="form-control form-control-sm" required>
                </div>
                <button type="submit" class="btn btn-sm btn-outline-danger w-100">Charge Penalty</button>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  </div>
</div>

<style>
  .shadow-soft { box-shadow: 0 10px 20px rgba(0,0,0,0.05) !important; }
  .x-small { font-size: 10px; font-weight: 700; text-transform: uppercase; color: #718096; }
  .nav-pills .nav-link { border-radius: 10px; color: #4a5568; background: #f8fafc; border: 1px solid #edf2f7; }
  .nav-pills .nav-link.active { background: #1a202c; color: #fff; border-color: #1a202c; }
  .progress { border-radius: 10px; background-color: rgba(0,0,0,0.05); }

  /* Booking Progress Bar Styles */
  .booking-progress {
    position: relative;
    padding: 20px 0;
  }

  .progress-steps {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    position: relative;
  }

  .progress-step {
    flex: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    position: relative;
    z-index: 2;
  }

  .step-circle {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    transition: all 0.3s ease;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    position: relative;
    z-index: 3;
  }

  .step-circle.bg-light {
    background-color: #f8fafc !important;
    border: 2px solid #e2e8f0 !important;
  }

  .step-circle.pulse {
    animation: pulse 2s infinite;
  }

  @keyframes pulse {
    0%, 100% {
      transform: scale(1);
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    50% {
      transform: scale(1.05);
      box-shadow: 0 4px 16px rgba(13, 110, 253, 0.3);
    }
  }

  .step-label {
    text-align: center;
    margin-top: 8px;
    min-height: 50px;
  }

  .progress-step.completed .step-circle {
    background-color: #28a745 !important;
    border-color: #28a745 !important;
  }

  .progress-step.current .step-circle {
    background-color: #0d6efd !important;
    border-color: #0d6efd !important;
    transform: scale(1.1);
  }

  .progress-step.cancelled .step-circle {
    background-color: #dc3545 !important;
    border-color: #dc3545 !important;
  }
</style>
  </div>
</div>
</div>

@endsection
