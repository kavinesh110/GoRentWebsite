@extends('layouts.app')
@section('title', 'Booking Details - Hasta GoRent')

@section('content')
<div class="container-fluid px-4 px-md-5" style="padding-top: 32px; padding-bottom: 48px;">
  <div class="mb-4">
    <a href="{{ route('customer.bookings') }}" class="text-decoration-none small">&larr; Back to My Bookings</a>
    <h1 class="h3 fw-bold mt-2 mb-1" style="color:#333;">Booking #{{ $booking->booking_id }}</h1>
  </div>

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

  <div class="row g-3">
    {{-- BOOKING DETAILS --}}
    <div class="col-lg-8">
      <div class="card border-0 shadow-soft mb-3">
        <div class="card-body p-4">
          <h5 class="fw-bold mb-3">Booking Information</h5>
          <div class="row g-3">
            <div class="col-md-6">
              <div class="small text-muted">Car</div>
              <div class="fw-semibold">{{ $booking->car->brand ?? 'N/A' }} {{ $booking->car->model ?? '' }} ({{ $booking->car->year ?? '' }})</div>
              <div class="small text-muted">Plate: {{ $booking->car->plate_number ?? 'N/A' }}</div>
            </div>
            <div class="col-md-6">
              <div class="small text-muted">Status</div>
              <div>
                <span class="badge 
                  {{ $booking->status === 'completed' ? 'bg-success' : ($booking->status === 'active' ? 'bg-warning' : ($booking->status === 'cancelled' ? 'bg-danger' : 'bg-secondary')) }}">
                  {{ ucfirst($booking->status ?? 'N/A') }}
                </span>
              </div>
            </div>
            <div class="col-md-6">
              <div class="small text-muted">Pickup</div>
              <div class="fw-semibold">{{ $booking->start_datetime?->format('d M Y, H:i') ?? 'N/A' }}</div>
              @if($booking->pickupLocation)
                <div class="small text-muted">{{ $booking->pickupLocation->name }}</div>
              @endif
            </div>
            <div class="col-md-6">
              <div class="small text-muted">Dropoff</div>
              <div class="fw-semibold">{{ $booking->end_datetime?->format('d M Y, H:i') ?? 'N/A' }}</div>
              @if($booking->dropoffLocation)
                <div class="small text-muted">{{ $booking->dropoffLocation->name }}</div>
              @endif
            </div>
            <div class="col-md-6">
              <div class="small text-muted">Rental Duration</div>
              <div class="fw-semibold">{{ $booking->rental_hours ?? 0 }} hours</div>
            </div>
            <div class="col-md-6">
              <div class="small text-muted">Booked On</div>
              <div class="fw-semibold">{{ $booking->created_at?->format('d M Y, H:i') ?? 'N/A' }}</div>
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
            @if(($booking->promo_discount ?? 0) > 0)
            <div class="col-12 d-flex justify-content-between">
              <span>Promo Discount:</span>
              <strong class="text-success">-RM {{ number_format($booking->promo_discount ?? 0, 2) }}</strong>
            </div>
            @endif
            @if(($booking->voucher_discount ?? 0) > 0)
            <div class="col-12 d-flex justify-content-between">
              <span>Voucher Discount:</span>
              <strong class="text-success">-RM {{ number_format($booking->voucher_discount ?? 0, 2) }}</strong>
            </div>
            @endif
            <div class="col-12 d-flex justify-content-between">
              <span>Deposit Amount:</span>
              <strong>RM {{ number_format($booking->deposit_amount ?? 0, 2) }}</strong>
            </div>
            <hr>
            <div class="col-12 d-flex justify-content-between">
              <span class="fw-bold">Final Amount:</span>
              <strong class="h5 mb-0 text-hasta">RM {{ number_format($booking->final_amount ?? 0, 2) }}</strong>
            </div>
          </div>
        </div>
      </div>

      {{-- PAYMENT RECEIPT UPLOAD --}}
      {{-- Only show upload form if booking is in valid status AND no payments have been uploaded yet --}}
      @if(in_array($booking->status, ['created', 'confirmed', 'active']) && (!$booking->payments || $booking->payments->count() === 0))
      <div class="card border-0 shadow-soft mb-3">
        <div class="card-body p-4">
          <h5 class="fw-bold mb-3">Upload Payment Receipt</h5>
          <p class="small text-muted mb-3">Upload your payment receipt for deposit or rental payment. Staff will verify your payment.</p>
          
          <form method="POST" action="{{ route('customer.bookings.receipt.upload', $booking->booking_id) }}" enctype="multipart/form-data">
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
                <input type="number" name="amount" step="0.01" min="0" class="form-control" required>
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
                <button type="submit" class="btn btn-hasta">Upload Receipt</button>
              </div>
            </div>
          </form>
        </div>
      </div>
      @endif

      {{-- PAYMENT HISTORY --}}
      @if($booking->payments && $booking->payments->count() > 0)
      <div class="card border-0 shadow-soft mb-3">
        <div class="card-body p-4">
          <h5 class="fw-bold mb-3">Payment History</h5>
          <div class="table-responsive">
            <table class="table table-sm align-middle mb-0">
              <thead>
                <tr>
                  <th>Type</th>
                  <th>Amount</th>
                  <th>Method</th>
                  <th>Date</th>
                  <th>Status</th>
                  <th>Receipt</th>
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
                    <td>
                      @if($payment->receipt_url)
                        <a href="{{ $payment->receipt_url }}" target="_blank" class="btn btn-sm btn-outline-primary">View</a>
                      @else
                        <span class="text-muted">-</span>
                      @endif
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
      </div>
      @endif

      {{-- RENTAL PHOTOS --}}
      @if(in_array($booking->status, ['active', 'completed']))
      <div class="card border-0 shadow-soft mb-3">
        <div class="card-body p-4">
          <h5 class="fw-bold mb-3">Rental Photos</h5>
          <p class="small text-muted mb-3">Upload photos of the car during your rental period (before pickup, after return, damage, etc.)</p>
          
          {{-- UPLOAD FORM --}}
          <form method="POST" action="{{ route('customer.bookings.photos.upload', $booking->booking_id) }}" enctype="multipart/form-data" class="mb-4">
            @csrf
            <div class="row g-3">
              <div class="col-md-4">
                <label class="form-label">Photo Type <span class="text-danger">*</span></label>
                <select name="photo_type" class="form-select" required>
                  <option value="before">Before Pickup</option>
                  <option value="after">After Return</option>
                  <option value="damage">Damage Report</option>
                  <option value="key">Key/Vehicle</option>
                  <option value="other">Other</option>
                </select>
              </div>
              <div class="col-md-4">
                <label class="form-label">Date Taken <span class="text-danger">*</span></label>
                <input type="date" name="taken_at" class="form-control" value="{{ now()->format('Y-m-d') }}" required>
              </div>
              <div class="col-md-4">
                <label class="form-label">Photo <span class="text-danger">*</span></label>
                <input type="file" name="photo" class="form-control" accept="image/jpeg,image/png,image/jpg" required>
                <small class="text-muted">JPEG, PNG only. Max 5MB</small>
              </div>
              <div class="col-12">
                <button type="submit" class="btn btn-hasta btn-sm">Upload Photo</button>
              </div>
            </div>
          </form>

          {{-- UPLOADED PHOTOS GALLERY --}}
          @if($booking->rentalPhotos && $booking->rentalPhotos->count() > 0)
            <div class="row g-2">
              @foreach($booking->rentalPhotos as $photo)
                <div class="col-md-3 col-sm-4 col-6">
                  <div class="position-relative">
                    <img src="{{ $photo->photo_url }}" alt="Rental Photo" class="img-fluid rounded" style="height: 150px; width: 100%; object-fit: cover; cursor: pointer;" onclick="window.open('{{ $photo->photo_url }}', '_blank')">
                    <div class="position-absolute top-0 start-0 m-2">
                      <span class="badge bg-dark">{{ ucfirst($photo->photo_type) }}</span>
                    </div>
                    <div class="position-absolute bottom-0 start-0 end-0 bg-dark bg-opacity-75 text-white p-1 rounded-bottom">
                      <small>{{ $photo->taken_at->format('d M Y') }}</small>
                    </div>
                  </div>
                </div>
              @endforeach
            </div>
          @else
            <p class="text-muted mb-0 small">No photos uploaded yet.</p>
          @endif
        </div>
      </div>
      @endif

      @if($booking->penalties && $booking->penalties->count() > 0)
      <div class="card border-0 shadow-soft">
        <div class="card-body p-4">
          <h5 class="fw-bold mb-3">Penalties</h5>
          <div class="table-responsive">
            <table class="table table-sm align-middle mb-0">
              <thead>
                <tr>
                  <th>Type</th>
                  <th>Amount</th>
                  <th>Status</th>
                  <th>Description</th>
                </tr>
              </thead>
              <tbody>
                @foreach($booking->penalties as $penalty)
                  <tr>
                    <td>{{ ucfirst($penalty->penalty_type) }}</td>
                    <td>RM {{ number_format($penalty->amount, 2) }}</td>
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
        </div>
      </div>
      @endif
    </div>

    {{-- SIDEBAR INFO --}}
    <div class="col-lg-4">
      <div class="card border-0 shadow-soft">
        <div class="card-body p-4">
          <h6 class="fw-bold mb-3">Booking Status</h6>
          <div class="small mb-3">
            <div class="d-flex justify-content-between mb-2">
              <span class="text-muted">Current Status:</span>
              <span class="badge 
                {{ $booking->status === 'completed' ? 'bg-success' : ($booking->status === 'active' ? 'bg-warning' : ($booking->status === 'cancelled' ? 'bg-danger' : 'bg-secondary')) }}">
                {{ ucfirst($booking->status ?? 'N/A') }}
              </span>
            </div>
          </div>
          
          @if($booking->status === 'created')
            <div class="alert alert-info small mb-3">
              <strong>Pending:</strong> Your booking is awaiting confirmation from Hasta staff.
            </div>
          @elseif($booking->status === 'confirmed')
            <div class="alert alert-success small mb-3">
              <strong>Confirmed:</strong> Your booking has been confirmed. Please arrive on time for pickup.
            </div>
          @elseif($booking->status === 'active')
            <div class="alert alert-warning small mb-3">
              <strong>Active:</strong> Your rental is currently ongoing.
            </div>
          @elseif($booking->status === 'completed')
            <div class="alert alert-success small mb-3">
              <strong>Completed:</strong> Your rental has been completed.
            </div>
          @elseif($booking->status === 'cancelled')
            <div class="alert alert-danger small mb-3">
              <strong>Cancelled:</strong> This booking has been cancelled.
            </div>
          @endif

          @if(in_array($booking->status, ['created', 'confirmed']))
            <form method="POST" action="{{ route('customer.bookings.cancel', $booking->booking_id) }}" class="mt-3" onsubmit="return confirm('Are you sure you want to cancel this booking?');">
              @csrf
              <button type="submit" class="btn btn-danger btn-sm w-100">Cancel Booking</button>
            </form>
          @endif
        </div>
      </div>


      {{-- FEEDBACK FORM --}}
      @if($booking->status === 'completed' && !$booking->feedback)
      <div class="card border-0 shadow-soft mt-3">
        <div class="card-body p-4">
          <h6 class="fw-bold mb-3">Share Your Experience</h6>
          <form method="POST" action="{{ route('customer.bookings.feedback.store', $booking->booking_id) }}">
            @csrf
            <div class="mb-3">
              <label class="form-label">Rating <span class="text-danger">*</span></label>
              <div class="d-flex gap-2">
                @for($i = 1; $i <= 5; $i++)
                  <label class="form-check-label" style="cursor: pointer;">
                    <input type="radio" name="rating" value="{{ $i }}" class="form-check-input" required {{ old('rating') == $i ? 'checked' : '' }}>
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="{{ old('rating') >= $i ? '#FFD700' : 'none' }}" stroke="currentColor" stroke-width="2">
                      <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                    </svg>
                  </label>
                @endfor
              </div>
              @error('rating')<div class="text-danger small">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
              <label class="form-label">Comment</label>
              <textarea name="comment" class="form-control" rows="3" placeholder="Share your experience...">{{ old('comment') }}</textarea>
              @error('comment')<div class="text-danger small">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="reported_issue" id="reported_issue" value="1" {{ old('reported_issue') ? 'checked' : '' }}>
                <label class="form-check-label" for="reported_issue">
                  Report an issue with this rental
                </label>
              </div>
            </div>
            <button type="submit" class="btn btn-hasta btn-sm">Submit Feedback</button>
          </form>
        </div>
      </div>
      @elseif($booking->feedback)
      <div class="card border-0 shadow-soft mt-3">
        <div class="card-body p-4">
          <h6 class="fw-bold mb-3">Your Feedback</h6>
          <div class="mb-2">
            <div class="d-flex align-items-center gap-2 mb-2">
              <strong>Rating:</strong>
              @for($i = 1; $i <= 5; $i++)
                <svg width="20" height="20" viewBox="0 0 24 24" fill="{{ $i <= $booking->feedback->rating ? '#FFD700' : '#ddd' }}" stroke="currentColor" stroke-width="2">
                  <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                </svg>
              @endfor
              @if($booking->feedback->reported_issue)
                <span class="badge bg-warning text-dark">Issue Reported</span>
              @endif
            </div>
            @if($booking->feedback->comment)
              <div class="mt-2">
                <strong>Comment:</strong>
                <p class="mb-0">{{ $booking->feedback->comment }}</p>
              </div>
            @endif
          </div>
        </div>
      </div>
      @endif
    </div>
  </div>
</div>
@endsection
