@extends('layouts.app')
@section('title', 'Booking Details - Staff Dashboard')

@section('content')
<div class="d-flex" style="min-height: calc(100vh - 60px);">
  @include('staff._nav')
  <div class="flex-fill" style="background: var(--bg);">
    <div class="container-custom" style="padding: 32px 24px 48px; max-width: 1000px;">
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

      {{-- PENALTIES LIST --}}
      <div class="card border-0 shadow-soft">
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
@endsection
