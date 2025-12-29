@extends('layouts.app')
@section('title', 'Booking Details - Staff Dashboard')

@section('content')
<div class="d-flex" style="min-height: calc(100vh - 60px);">
  @include('staff._nav')
  <div class="flex-fill" style="background: var(--bg);">
    <div class="container-fluid px-4 px-md-5" style="padding-top: 32px; padding-bottom: 48px;">
  <div class="mb-4">
    <a href="{{ route('staff.bookings') }}" class="text-decoration-none small">&larr; Back to bookings</a>
    <h1 class="h3 fw-bold mt-2 mb-1" style="color:#333;">Booking #{{ $booking->booking_id }}</h1>
  </div>

  @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      {{ session('success') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  @endif

  <div class="row g-3">
    {{-- BOOKING DETAILS --}}
    <div class="col-lg-8">
      <div class="card border-0 shadow-soft mb-3">
        <div class="card-body p-4">
          <h5 class="fw-bold mb-3">Booking Information</h5>
          <div class="row g-3">
            <div class="col-md-6">
              <div class="small text-muted">Customer</div>
              <div class="fw-semibold">{{ $booking->customer->full_name ?? 'N/A' }}</div>
              <div class="small text-muted">{{ $booking->customer->email ?? '' }}</div>
            </div>
            <div class="col-md-6">
              <div class="small text-muted">Car</div>
              <div class="fw-semibold">{{ $booking->car->brand ?? 'N/A' }} {{ $booking->car->model ?? '' }} ({{ $booking->car->year ?? '' }})</div>
              <div class="small text-muted">Plate: {{ $booking->car->plate_number ?? 'N/A' }}</div>
            </div>
            <div class="col-md-6">
              <div class="small text-muted">Pickup</div>
              <div class="fw-semibold">{{ $booking->start_datetime?->format('d M Y, H:i') ?? 'N/A' }}</div>
            </div>
            <div class="col-md-6">
              <div class="small text-muted">Dropoff</div>
              <div class="fw-semibold">{{ $booking->end_datetime?->format('d M Y, H:i') ?? 'N/A' }}</div>
            </div>
            <div class="col-md-6">
              <div class="small text-muted">Rental Duration</div>
              <div class="fw-semibold">{{ $booking->rental_hours ?? 0 }} hours</div>
            </div>
            <div class="col-md-6">
              <div class="small text-muted">Status</div>
              <div>
                <span class="badge 
                  {{ $booking->status === 'completed' ? 'bg-success' : ($booking->status === 'ongoing' ? 'bg-warning' : ($booking->status === 'cancelled' ? 'bg-danger' : 'bg-secondary')) }}">
                  {{ ucfirst($booking->status ?? 'N/A') }}
                </span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="card border-0 shadow-soft mb-3">
        <div class="card-body p-4">
          <h5 class="fw-bold mb-3">Pricing Breakdown</h5>
          <div class="row g-2">
            <div class="col-12 d-flex justify-content-between">
              <span>Base Price:</span>
              <strong>RM {{ number_format($booking->base_price ?? 0, 2) }}</strong>
            </div>
            <div class="col-12 d-flex justify-content-between">
              <span>Promo Discount:</span>
              <strong class="text-success">-RM {{ number_format($booking->promo_discount ?? 0, 2) }}</strong>
            </div>
            <div class="col-12 d-flex justify-content-between">
              <span>Voucher Discount:</span>
              <strong class="text-success">-RM {{ number_format($booking->voucher_discount ?? 0, 2) }}</strong>
            </div>
            <div class="col-12 d-flex justify-content-between">
              <span>Deposit Amount:</span>
              <strong>RM {{ number_format($booking->deposit_amount ?? 0, 2) }}</strong>
            </div>
            <hr>
            <div class="col-12 d-flex justify-content-between">
              <span class="fw-bold">Final Amount:</span>
              <strong class="h5 mb-0">RM {{ number_format($booking->final_amount ?? 0, 2) }}</strong>
            </div>
          </div>
        </div>
      </div>

      {{-- PAYMENTS LIST --}}
      <div class="card border-0 shadow-soft mb-3">
        <div class="card-body p-4">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-bold mb-0">Payments</h5>
            <button type="button" class="btn btn-sm btn-hasta" data-bs-toggle="modal" data-bs-target="#addPaymentModal">
              + Add Payment
            </button>
          </div>
          @if($booking->payments && $booking->payments->count() > 0)
            <div class="table-responsive">
              <table class="table table-sm align-middle mb-0">
                <thead>
                  <tr>
                    <th>Type</th>
                    <th>Amount (RM)</th>
                    <th>Method</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Receipt</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($booking->payments as $payment)
                    <tr>
                      <td>{{ ucfirst($payment->payment_type) }}</td>
                      <td><strong>RM {{ number_format($payment->amount, 2) }}</strong></td>
                      <td>{{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}</td>
                      <td>{{ $payment->payment_date->format('d M Y, H:i') }}</td>
                      <td>
                        <span class="badge {{ $payment->status === 'verified' ? 'bg-success' : 'bg-warning' }}">
                          {{ ucfirst($payment->status) }}
                        </span>
                      </td>
                      <td>
                        @if($payment->receipt_url)
                          <a href="{{ $payment->receipt_url }}" target="_blank" class="btn btn-sm btn-outline-primary">View</a>
                        @else
                          <span class="text-muted">-</span>
                        @endif
                      </td>
                      <td>
                        @if($payment->status === 'pending')
                          <form method="POST" action="{{ route('staff.payments.verify', $payment->payment_id) }}" class="d-inline">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-sm btn-success">Verify</button>
                          </form>
                        @endif
                      </td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
            @php
              $totalPaid = $booking->payments->where('status', 'verified')->sum('amount');
              $totalPending = $booking->payments->where('status', 'pending')->sum('amount');
            @endphp
            <div class="mt-3 pt-3 border-top">
              <div class="row g-2 small">
                <div class="col-6">
                  <span class="text-muted">Total Verified:</span>
                  <strong class="text-success">RM {{ number_format($totalPaid, 2) }}</strong>
                </div>
                <div class="col-6">
                  <span class="text-muted">Pending:</span>
                  <strong class="text-warning">RM {{ number_format($totalPending, 2) }}</strong>
                </div>
                <div class="col-12">
                  <span class="text-muted">Booking Amount:</span>
                  <strong>RM {{ number_format($booking->final_amount ?? 0, 2) }}</strong>
                </div>
              </div>
            </div>
          @else
            <p class="text-muted mb-0">No payments recorded for this booking.</p>
          @endif
        </div>
      </div>

      {{-- DEPOSIT REFUND (Only for completed bookings) --}}
      @if($booking->status === 'completed')
        <div class="card border-0 shadow-soft mb-3 border-warning">
          <div class="card-body p-4">
            <h5 class="fw-bold mb-3">Deposit Refund Processing</h5>
            @if($booking->deposit_decision)
              <div class="alert alert-info mb-3">
                <strong>Decision:</strong> {{ ucfirst(str_replace('_', ' ', $booking->deposit_decision)) }}<br>
                <strong>Refund Amount:</strong> RM {{ number_format($booking->deposit_refund_amount ?? 0, 2) }}
              </div>
            @else
              <form method="POST" action="{{ route('staff.bookings.process-refund', $booking->booking_id) }}">
                @csrf
                <div class="mb-3">
                  <label class="form-label">Deposit Decision <span class="text-danger">*</span></label>
                  <select name="deposit_decision" class="form-select" required>
                    <option value="">Select decision...</option>
                    <option value="refund">Refund to Customer Deposit Balance</option>
                    <option value="carry_forward">Carry Forward to Next Booking</option>
                    <option value="burn">Forfeit Deposit</option>
                  </select>
                </div>
                <div class="mb-3">
                  <label class="form-label">Refund Amount (RM) <span class="text-danger">*</span></label>
                  <input type="number" name="deposit_refund_amount" class="form-control" step="0.01" min="0" max="{{ $booking->deposit_amount }}" value="{{ $booking->deposit_amount }}" required>
                  <small class="text-muted">Maximum: RM {{ number_format($booking->deposit_amount, 2) }}</small>
                </div>
                <button type="submit" class="btn btn-warning">Process Deposit Refund</button>
              </form>
            @endif
          </div>
        </div>
      @endif

      {{-- PENALTIES LIST --}}
      <div class="card border-0 shadow-soft mb-3">
        <div class="card-body p-4">
          <h5 class="fw-bold mb-3">Penalties for this booking</h5>
          @if($booking->penalties && $booking->penalties->count() > 0)
            <div class="table-responsive">
              <table class="table table-sm align-middle mb-0">
                <thead>
                  <tr>
                    <th>Type</th>
                    <th>Amount (RM)</th>
                    <th>Status</th>
                    <th>Description</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($booking->penalties as $penalty)
                    <tr>
                      <td>{{ ucfirst($penalty->penalty_type) }}</td>
                      <td>{{ number_format($penalty->amount, 2) }}</td>
                      <td>
                        <span class="badge
                          {{ $penalty->status === 'settled' ? 'bg-success' : ($penalty->status === 'partially_paid' ? 'bg-warning' : 'bg-danger') }}">
                          {{ ucfirst(str_replace('_', ' ', $penalty->status)) }}
                        </span>
                      </td>
                      <td>{{ $penalty->description ?? '-' }}</td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          @else
            <p class="text-muted mb-0">No penalties recorded for this booking.</p>
          @endif
        </div>
      </div>

      {{-- RENTAL PHOTOS --}}
      <div class="card border-0 shadow-soft">
        <div class="card-body p-4">
          <h5 class="fw-bold mb-3">Rental Photos</h5>
          @if($booking->rentalPhotos && $booking->rentalPhotos->count() > 0)
            <div class="row g-3">
              @foreach($booking->rentalPhotos as $photo)
                <div class="col-md-4 col-sm-6">
                  <div class="position-relative border rounded overflow-hidden">
                    <img src="{{ $photo->photo_url }}" alt="Rental Photo" class="img-fluid w-100" style="height: 200px; object-fit: cover; cursor: pointer;" onclick="window.open('{{ $photo->photo_url }}', '_blank')">
                    <div class="position-absolute top-0 start-0 m-2">
                      <span class="badge bg-dark">{{ ucfirst($photo->photo_type) }}</span>
                    </div>
                    <div class="p-2 bg-light">
                      <div class="small">
                        <strong>Uploaded by:</strong> {{ $photo->uploaded_by_role === 'customer' ? 'Customer' : 'Staff' }}
                      </div>
                      <div class="small text-muted">
                        Taken: {{ $photo->taken_at->format('d M Y, H:i') }}
                      </div>
                    </div>
                  </div>
                </div>
              @endforeach
            </div>
          @else
            <p class="text-muted mb-0">No rental photos uploaded for this booking.</p>
          @endif
        </div>
      </div>
    </div>

    {{-- ACTIONS --}}
    <div class="col-lg-4">
      <div class="card border-0 shadow-soft mb-3">
        <div class="card-body p-4">
          <h5 class="fw-bold mb-3">Update Status</h5>
          <form method="POST" action="{{ route('staff.bookings.update-status', $booking->booking_id) }}">
            @csrf
            @method('PATCH')
            <div class="mb-3">
              <select name="status" class="form-select" required>
                <option value="created" {{ $booking->status === 'created' ? 'selected' : '' }}>Created</option>
                <option value="confirmed" {{ $booking->status === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                <option value="ongoing" {{ $booking->status === 'ongoing' ? 'selected' : '' }}>Ongoing</option>
                <option value="completed" {{ $booking->status === 'completed' ? 'selected' : '' }}>Completed</option>
                <option value="cancelled" {{ $booking->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
              </select>
            </div>
            <button type="submit" class="btn btn-hasta w-100">Update Status</button>
          </form>
        </div>
      </div>

      {{-- ADD INSPECTION --}}
      <div class="card border-0 shadow-soft mb-3">
        <div class="card-body p-4">
          <h5 class="fw-bold mb-3">Record Inspection</h5>
          <form method="POST" action="{{ route('staff.bookings.inspections.store', $booking->booking_id) }}">
            @csrf
            <div class="mb-2">
              <label class="form-label small">Inspection type</label>
              <select name="inspection_type" class="form-select form-select-sm" required>
                <option value="pickup">Pickup</option>
                <option value="return">Return</option>
                <option value="other">Other</option>
              </select>
            </div>
            <div class="mb-2">
              <label class="form-label small">Date & time</label>
              <input type="datetime-local" name="datetime" class="form-control form-control-sm" value="{{ now()->format('Y-m-d\TH:i') }}" required>
            </div>
            <div class="mb-2">
              <label class="form-label small">Fuel level (bars 0-8)</label>
              <input type="number" name="fuel_level" min="0" max="8" class="form-control form-control-sm" value="8" required>
            </div>
            <div class="mb-2">
              <label class="form-label small">Odometer (km)</label>
              <input type="number" name="odometer_reading" min="0" class="form-control form-control-sm" required>
            </div>
            <div class="mb-3">
              <label class="form-label small">Notes</label>
              <textarea name="notes" class="form-control form-control-sm" rows="2" placeholder="Any visible damage, remarks..."></textarea>
            </div>
            <button type="submit" class="btn btn-sm btn-outline-secondary w-100">Save Inspection</button>
          </form>
        </div>
      </div>

      {{-- ADD PENALTY --}}
      <div class="card border-0 shadow-soft">
        <div class="card-body p-4">
          <h5 class="fw-bold mb-3">Add Penalty</h5>
          <form method="POST" action="{{ route('staff.bookings.penalties.store', $booking->booking_id) }}">
            @csrf
            <div class="mb-2">
              <label class="form-label small">Type</label>
              <select name="penalty_type" class="form-select form-select-sm" required>
                <option value="late">Late return</option>
                <option value="fuel">Fuel difference</option>
                <option value="damage">Damage</option>
                <option value="accident">Accident</option>
                <option value="other">Other</option>
              </select>
            </div>
            <div class="mb-2">
              <label class="form-label small">Amount (RM)</label>
              <input type="number" name="amount" step="0.01" min="0" class="form-control form-control-sm" required>
            </div>
            <div class="mb-2 form-check">
              <input type="checkbox" class="form-check-input" id="is_installment" name="is_installment" value="1">
              <label class="form-check-label small" for="is_installment">Allow installment payment</label>
            </div>
            <div class="mb-2">
              <label class="form-label small">Status</label>
              <select name="status" class="form-select form-select-sm" required>
                <option value="pending">Pending</option>
                <option value="partially_paid">Partially paid</option>
                <option value="settled">Settled</option>
              </select>
            </div>
            <div class="mb-3">
              <label class="form-label small">Description</label>
              <textarea name="description" class="form-control form-control-sm" rows="2" placeholder="Explain the penalty..."></textarea>
            </div>
            <button type="submit" class="btn btn-sm btn-danger w-100">Save Penalty</button>
          </form>
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
