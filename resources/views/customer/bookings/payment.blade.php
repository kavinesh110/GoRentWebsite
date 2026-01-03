@extends('layouts.app')
@section('title', 'Booking Payment')

@section('content')

<style>
  :root {
    --hasta-red: #cb3737;
    --hasta-dark: #a92c2c;
    --bg-light: #f8f9fa;
    --text-main: #2d3436;
    --text-muted: #636e72;
  }

  .payment-hero {
    background: linear-gradient(135deg, #1e272e 0%, #000 100%);
    padding: 60px 0 100px 0;
    color: #fff;
    position: relative;
    overflow: hidden;
  }
  .payment-hero::after {
    content: '';
    position: absolute;
    top: 0; right: 0; bottom: 0; left: 0;
    background: url('https://www.transparenttextures.com/patterns/carbon-fibre.png');
    opacity: 0.1;
  }

  .payment-card {
    background: #fff;
    border-radius: 24px;
    padding: 35px;
    box-shadow: 0 20px 40px rgba(0,0,0,0.1);
    margin-top: -60px;
    position: relative;
    z-index: 10;
    border: 1px solid #f0f0f0;
  }

  .qr-container {
    background: #fff;
    border: 2px dashed #eee;
    border-radius: 24px;
    padding: 25px;
    text-align: center;
    transition: 0.3s;
  }
  .qr-placeholder {
    width: 200px;
    height: 200px;
    background: var(--bg-light);
    border-radius: 16px;
    margin: 0 auto 20px auto;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #ccc;
    font-size: 12px;
    font-weight: 700;
  }

  .form-label {
    font-size: 11px;
    font-weight: 700;
    color: #95a5a6;
    text-transform: uppercase;
    margin-bottom: 8px;
    letter-spacing: 0.5px;
  }
  .form-control, .form-select {
    border: 1px solid #eee;
    padding: 12px 16px;
    font-weight: 600;
    border-radius: 12px;
    background: var(--bg-light);
  }

  .btn-upload {
    background: var(--hasta);
    color: #fff;
    border: none;
    padding: 16px 32px;
    border-radius: 14px;
    font-weight: 700;
    width: 100%;
    transition: 0.3s;
  }
  .btn-upload:hover {
    background: var(--hasta-dark);
    transform: translateY(-2px);
    box-shadow: 0 10px 20px rgba(203,55,55,0.2);
  }

  @media (max-width: 768px) {
    .payment-hero { padding: 40px 0 80px 0; }
    .payment-card { padding: 25px; }
  }
</style>

{{-- HERO SECTION --}}
<div class="payment-hero">
  <div class="container text-center position-relative" style="z-index: 2;">
    <h1 class="display-5 fw-800 text-white mb-2">Complete Your Payment</h1>
    <p class="text-white-50">Secure your booking by paying the deposit and uploading your receipt.</p>
  </div>
</div>

<div class="container pb-5">
  <div class="row g-4">
    {{-- LEFT: SUMMARY & QR --}}
    <div class="col-lg-5">
      <div class="payment-card mb-4">
        <h5 class="fw-800 mb-4">Payment Summary</h5>
        <div class="d-flex align-items-center gap-3 mb-4 p-3 bg-light rounded-4">
          @if($car->image_url)
            <img src="{{ $car->image_url }}" alt="Car" style="height: 60px; filter: drop-shadow(0 5px 10px rgba(0,0,0,0.05));">
          @endif
          <div>
            <h6 class="fw-bold mb-1">{{ $car->brand ?? 'N/A' }} {{ $car->model ?? '' }}</h6>
            <small class="text-muted">{{ $durationHours }} hours rental</small>
          </div>
        </div>

        <div class="d-flex justify-content-between mb-2">
          <span class="text-muted small">Rental Amount</span>
          <span class="fw-bold">RM {{ number_format($pending['total_rental_amount'] ?? 0, 2) }}</span>
        </div>
        <div class="d-flex justify-content-between mb-3 pb-3 border-bottom">
          <span class="text-muted small">Refundable Deposit</span>
          <span class="fw-bold">RM {{ number_format($pending['deposit_amount'] ?? 0, 2) }}</span>
        </div>

        <div class="d-flex justify-content-between align-items-center mt-3">
          <h5 class="fw-bold mb-0">Total Due</h5>
          <span class="h3 fw-800 text-hasta-red mb-0">RM {{ number_format($totalToPay, 2) }}</span>
        </div>
      </div>

      <div class="qr-container bg-white shadow-soft">
        <h6 class="fw-bold mb-3">Scan to Pay</h6>
        <div class="qr-placeholder">
          <i class="bi bi-qr-code" style="font-size: 3rem; color: #eee;"></i>
          <span class="ms-2">QR PLACEHOLDER</span>
        </div>
        <p class="small text-muted mb-0">Scan with your banking app to transfer exactly <strong>RM {{ number_format($totalToPay, 2) }}</strong></p>
      </div>
    </div>

    {{-- RIGHT: FORM --}}
    <div class="col-lg-7">
      <div class="payment-card">
        <h5 class="fw-800 mb-4">Upload Deposit Receipt</h5>
        
        <form method="POST" action="{{ route('customer.bookings.payment.submit') }}" enctype="multipart/form-data">
          @csrf
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Payment Type</label>
              <select name="payment_type" class="form-select" readonly>
                <option value="deposit" selected>Deposit Payment</option>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label">Amount (RM)</label>
              <input type="number" name="amount" step="0.01" value="{{ $totalToPay }}" class="form-control" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Method</label>
              <select name="payment_method" class="form-select" required>
                <option value="bank_transfer">Bank Transfer</option>
                <option value="e-wallet">E-Wallet</option>
                <option value="other">Other</option>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label">Date of Payment</label>
              <input type="date" name="payment_date" class="form-control" value="{{ now()->format('Y-m-d') }}" required>
            </div>
            <div class="col-12 mt-4">
              <label class="form-label d-block mb-3">Upload Receipt (Image or PDF)</label>
              <div class="bg-light p-4 rounded-4 text-center border-2 border-dashed">
                <input type="file" name="receipt" class="form-control bg-white" accept="image/*,.pdf" required>
                <small class="text-muted d-block mt-2">Maximum file size: 5MB</small>
              </div>
            </div>
            
            {{-- Cancellation Policy Notice --}}
            <div class="col-12 mt-4">
              <div class="alert alert-info border-0 shadow-sm" role="alert" style="background: #e7f3ff; border-left: 4px solid #0d6efd !important;">
                <div class="d-flex align-items-start">
                  <i class="bi bi-info-circle-fill me-2 mt-1" style="color: #0d6efd; font-size: 18px;"></i>
                  <div>
                    <strong class="d-block mb-1" style="color: #0d6efd;">Cancellation Policy</strong>
                    <p class="mb-0 small" style="color: #0a58ca;">
                      Please note that cancellation requests are only available <strong>24 hours before</strong> the desired renting/pickup time. 
                      Cancellations made after this period may be subject to fees as per our cancellation policy.
                    </p>
                  </div>
                </div>
              </div>
            </div>
            
            <div class="col-12 mt-4">
              <button type="submit" class="btn-upload">
                <i class="bi bi-cloud-upload me-2"></i> Confirm & Complete Booking
              </button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
