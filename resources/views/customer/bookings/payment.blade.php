@extends('layouts.app')
@section('title', 'Booking Payment')

@section('content')
<div class="container-fluid px-4 px-md-5" style="padding-top: 32px; padding-bottom: 48px;">
  <div class="mb-4">
    <a href="{{ route('customer.bookings') }}" class="text-decoration-none small">&larr; Back to My Bookings</a>
    <h1 class="h3 fw-bold mt-2 mb-1" style="color:#333;">Complete Your Payment</h1>
    <p class="text-muted mb-0" style="font-size: 14px;">Scan the QR code to pay the deposit and upload your payment receipt.</p>
  </div>

  @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      {{ session('success') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  @endif

  @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
      <ul class="mb-0">
        @foreach($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  @endif

  <div class="row g-3">
    {{-- LEFT: PAYMENT SUMMARY & QR CODE --}}
    <div class="col-lg-5">
      <div class="card border-0 shadow-soft mb-3">
        <div class="card-body p-4">
          <h5 class="fw-bold mb-3">Payment Summary</h5>
          <div class="mb-2 small text-muted">Car</div>
          <div class="fw-semibold mb-2">
            {{ $car->brand ?? 'N/A' }} {{ $car->model ?? '' }} ({{ $car->year ?? '' }})
          </div>

          <div class="mb-3 small text-muted">Rental Period</div>
          <div class="mb-2">
            <span class="fw-semibold">{{ $pickupFormatted }}</span>
            <span class="text-muted">&nbsp;to&nbsp;</span>
            <span class="fw-semibold">{{ $dropoffFormatted }}</span>
          </div>
          <div class="small text-muted mb-3">Duration: {{ $durationHours }} hours</div>

          <hr>
          <div class="d-flex justify-content-between mb-2">
            <span class="small text-muted">Rental Amount</span>
            <span class="fw-semibold">RM {{ number_format($pending['total_rental_amount'] ?? 0, 2) }}</span>
          </div>
          <div class="d-flex justify-content-between mb-2">
            <span class="small text-muted">Deposit</span>
            <span class="fw-semibold">RM {{ number_format($pending['deposit_amount'] ?? 0, 2) }}</span>
          </div>
          <hr>
          <div class="d-flex justify-content-between align-items-center">
            <span class="fw-bold">Total to Pay</span>
            <span class="h4 fw-bold text-hasta mb-0">
              RM {{ number_format($totalToPay, 2) }}
            </span>
          </div>
        </div>
      </div>

      <div class="card border-0 shadow-soft">
        <div class="card-body p-4 text-center">
          <h5 class="fw-bold mb-3">Scan to Pay</h5>
          <p class="small text-muted mb-3">
            This is a placeholder area for your official payment QR code. Hasta staff can replace this with the real QR image later.
          </p>

          <div class="d-flex justify-content-center mb-3">
            <div style="width: 220px; height: 220px; border-radius: 16px; background: #f5f5f5; display:flex; align-items:center; justify-content:center; border:1px dashed #ccc;">
              <span class="text-muted small">QR CODE<br>PLACEHOLDER</span>
            </div>
          </div>

          <p class="small text-muted mb-0">
            After making payment, upload your deposit receipt using the form on the right.
          </p>
        </div>
      </div>
    </div>

    {{-- RIGHT: RECEIPT UPLOAD --}}
    <div class="col-lg-7">
      <div class="card border-0 shadow-soft mb-3">
        <div class="card-body p-4">
          <h5 class="fw-bold mb-3">Upload Deposit Receipt</h5>
          <p class="small text-muted mb-3">
            Upload the receipt or screenshot after you have completed the deposit payment using the QR code.
          </p>

          <form method="POST" action="{{ route('customer.bookings.payment.submit') }}" enctype="multipart/form-data">
            @csrf
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label">Payment Type <span class="text-danger">*</span></label>
                <select name="payment_type" class="form-select" required>
                  <option value="deposit">Deposit</option>
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
              <div class="col-12 d-flex justify-content-end mt-2">
                <button type="submit" class="btn btn-hasta">Upload Receipt & Create Booking</button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
