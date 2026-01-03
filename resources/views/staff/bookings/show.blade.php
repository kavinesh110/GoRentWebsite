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
          'ongoing' => 'bg-info text-white',
          'cancelled' => 'bg-danger text-white',
          'confirmed' => 'bg-primary text-white',
          'created' => 'bg-warning text-dark'
        ];
        $statusClass = $statusClasses[$booking->status] ?? 'bg-secondary text-white';
      @endphp
      <span class="badge rounded-pill px-3 py-2 {{ $statusClass }} shadow-sm">
        {{ ucfirst($booking->status ?? 'N/A') }}
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

  <div class="row g-4">
    {{-- LEFT: BOOKING & PAYMENT DETAILS --}}
    <div class="col-lg-8">
      {{-- Booking Overview Card --}}
      <div class="card border-0 shadow-sm mb-4 overflow-hidden">
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
      <div class="card border-0 shadow-sm mb-4 overflow-hidden">
        <div class="card-header bg-white border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
          <h5 class="fw-bold mb-0"><i class="bi bi-cash-stack me-2 text-success"></i>Pricing & Payments</h5>
          <button type="button" class="btn btn-sm btn-hasta shadow-sm" data-bs-toggle="modal" data-bs-target="#addPaymentModal">
            <i class="bi bi-plus-lg me-1"></i>Add Payment
          </button>
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

      {{-- Penalties & Photos Tabs Card --}}
      <div class="card border-0 shadow-sm overflow-hidden">
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

    {{-- RIGHT: ACTIONS PANEL --}}
    <div class="col-lg-4">
      {{-- Update Status Card --}}
      <div class="card border-0 shadow-sm mb-4 overflow-hidden shadow-soft">
        <div class="card-header bg-dark text-white border-0 py-3 px-4">
          <h6 class="fw-bold mb-0">Operational Status</h6>
        </div>
        <div class="card-body p-4">
          <form method="POST" action="{{ route('staff.bookings.update-status', $booking->booking_id) }}">
            @csrf
            @method('PATCH')
            <div class="mb-3">
              <label class="form-label small fw-bold text-muted text-uppercase" style="font-size: 10px;">Change Booking Status</label>
              <select name="status" class="form-select border-2 shadow-none" required>
                <option value="created" {{ $booking->status === 'created' ? 'selected' : '' }}>Created (Awaiting Verif)</option>
                <option value="confirmed" {{ $booking->status === 'confirmed' ? 'selected' : '' }}>Confirmed (Ready for Pickup)</option>
                <option value="ongoing" {{ $booking->status === 'ongoing' ? 'selected' : '' }}>Ongoing (Rented Out)</option>
                <option value="completed" {{ $booking->status === 'completed' ? 'selected' : '' }}>Completed (Returned)</option>
                <option value="cancelled" {{ $booking->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
              </select>
            </div>
            <button type="submit" class="btn btn-hasta w-100 py-2 fw-bold shadow-sm">
              <i class="bi bi-arrow-repeat me-2"></i>Update Status
            </button>
          </form>

          @if($booking->status === 'completed' && !$booking->deposit_decision)
            <hr class="my-4">
            <div class="p-3 bg-warning-subtle rounded-4 border border-warning-subtle">
              <h6 class="fw-bold text-warning-emphasis mb-2"><i class="bi bi-wallet2 me-2"></i>Process Deposit</h6>
              <form method="POST" action="{{ route('staff.bookings.process-refund', $booking->booking_id) }}">
                @csrf
                <div class="mb-2">
                  <select name="deposit_decision" class="form-select form-select-sm mb-2" required>
                    <option value="">Choose decision...</option>
                    <option value="refund">Refund Deposit</option>
                    <option value="burn">Forfeit (Damages/Fines)</option>
                  </select>
                </div>
                <div class="mb-3">
                  <div class="input-group input-group-sm">
                    <span class="input-group-text border-end-0 bg-white">RM</span>
                    <input type="number" name="deposit_refund_amount" class="form-control border-start-0 ps-0" step="0.01" value="{{ $booking->deposit_amount }}" max="{{ $booking->deposit_amount }}" required>
                  </div>
                </div>
                <button type="submit" class="btn btn-sm btn-warning w-100 fw-bold">Process Decision</button>
              </form>
            </div>
          @endif
        </div>
      </div>

      {{-- Record Inspection --}}
      <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white border-0 pt-4 px-4 d-flex align-items-center">
          <h6 class="fw-bold mb-0">Record Inspection</h6>
        </div>
        <div class="card-body p-4 pt-2">
          <form method="POST" action="{{ route('staff.bookings.inspections.store', $booking->booking_id) }}">
            @csrf
            <div class="row g-2">
              <div class="col-6 mb-2">
                <label class="form-label x-small">Type</label>
                <select name="inspection_type" class="form-select form-select-sm" required>
                  <option value="pickup">Pickup</option>
                  <option value="return">Return</option>
                </select>
              </div>
              <div class="col-6 mb-2">
                <label class="form-label x-small">Fuel (0-8)</label>
                <input type="number" name="fuel_level" min="0" max="8" class="form-control form-control-sm" value="8" required>
              </div>
              <div class="col-12 mb-2">
                <label class="form-label x-small">Odometer Reading (km)</label>
                <input type="number" name="odometer_reading" class="form-control form-control-sm" required>
              </div>
              <div class="col-12 mb-3">
                <label class="form-label x-small">Notes</label>
                <textarea name="notes" class="form-control form-control-sm" rows="2"></textarea>
              </div>
            </div>
            <button type="submit" class="btn btn-sm btn-dark w-100">Save Record</button>
          </form>
        </div>
      </div>

      {{-- Add Penalty --}}
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

<style>
  .shadow-soft { box-shadow: 0 10px 20px rgba(0,0,0,0.05) !important; }
  .x-small { font-size: 10px; font-weight: 700; text-transform: uppercase; color: #718096; }
  .nav-pills .nav-link { border-radius: 10px; color: #4a5568; background: #f8fafc; border: 1px solid #edf2f7; }
  .nav-pills .nav-link.active { background: #1a202c; color: #fff; border-color: #1a202c; }
  .progress { border-radius: 10px; background-color: rgba(0,0,0,0.05); }
</style>
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
            <label class="form-label">Payment Type <span class="text-danger">*</span></label>
            <select name="payment_type" class="form-select" required>
              <option value="deposit">Deposit</option>
              <option value="rental">Rental Payment</option>
              <option value="penalty">Penalty Payment</option>
              <option value="refund">Refund</option>
              <option value="installment">Installment</option>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Penalty (if payment type is penalty/installment)</label>
            <select name="penalty_id" class="form-select">
              <option value="">Select penalty (optional)</option>
              @foreach($booking->penalties as $penalty)
                <option value="{{ $penalty->penalty_id }}">{{ ucfirst($penalty->penalty_type) }} - RM {{ number_format($penalty->amount, 2) }}</option>
              @endforeach
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Amount (RM) <span class="text-danger">*</span></label>
            <input type="number" name="amount" step="0.01" min="0" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Payment Method <span class="text-danger">*</span></label>
            <select name="payment_method" class="form-select" required>
              <option value="bank_transfer">Bank Transfer</option>
              <option value="cash">Cash</option>
              <option value="other">Other</option>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Payment Date <span class="text-danger">*</span></label>
            <input type="datetime-local" name="payment_date" class="form-control" value="{{ now()->format('Y-m-d\TH:i') }}" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Receipt/Proof (optional)</label>
            <input type="file" name="receipt" class="form-control" accept="image/*,.pdf">
            <small class="text-muted">Upload payment receipt or proof (image or PDF)</small>
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
@endsection
