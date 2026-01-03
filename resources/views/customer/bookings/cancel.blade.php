@extends('layouts.app')
@section('title', 'Request Cancellation - Hasta GoRent')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
<style>
  .cancel-hero {
    background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
    border-radius: 0 0 30px 30px;
    padding: 40px 0;
    margin-bottom: -60px;
    position: relative;
  }
  
  .cancel-card {
    border: none;
    border-radius: 20px;
    box-shadow: 0 15px 40px rgba(0,0,0,0.1);
    overflow: hidden;
  }
  
  .cancel-card-header {
    background: #f8f9fa;
    padding: 20px 24px;
    border-bottom: 1px solid #eee;
  }
  
  .cancel-card-body {
    padding: 30px;
  }
  
  .form-section {
    margin-bottom: 30px;
    padding-bottom: 30px;
    border-bottom: 1px solid #eee;
  }
  
  .form-section:last-child {
    border-bottom: none;
    margin-bottom: 0;
    padding-bottom: 0;
  }
  
  .form-section-title {
    font-size: 16px;
    font-weight: 700;
    color: #333;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 10px;
  }
  
  .form-section-title .badge {
    font-size: 11px;
    padding: 4px 8px;
  }
  
  .booking-summary-mini {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-radius: 16px;
    padding: 20px;
  }
  
  .info-box {
    background: #fff3cd;
    border: 1px solid #ffc107;
    border-radius: 12px;
    padding: 16px;
    margin-bottom: 20px;
  }
  
  .info-box.danger {
    background: #f8d7da;
    border-color: #dc3545;
  }
  
  .upload-area {
    border: 2px dashed #dee2e6;
    border-radius: 12px;
    padding: 30px;
    text-align: center;
    transition: all 0.3s ease;
    cursor: pointer;
    background: #f8f9fa;
  }
  
  .upload-area:hover {
    border-color: var(--hasta);
    background: #fff5f5;
  }
  
  .upload-area i {
    font-size: 40px;
    color: #adb5bd;
    margin-bottom: 10px;
  }
  
  .btn-cancel-submit {
    background: #dc3545;
    color: #fff;
    border: none;
    padding: 14px 30px;
    border-radius: 12px;
    font-weight: 700;
    transition: all 0.3s ease;
  }
  
  .btn-cancel-submit:hover {
    background: #c82333;
    color: #fff;
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(220, 53, 69, 0.3);
  }
  
  .bank-icon {
    width: 40px;
    height: 40px;
    background: #e9ecef;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    color: #6c757d;
  }
</style>
@endpush

@section('content')
{{-- Hero Section --}}
<div class="cancel-hero">
  <div class="container text-center text-white">
    <h1 class="fw-bold mb-2"><i class="bi bi-x-circle me-2"></i> Request Cancellation</h1>
    <p class="mb-0 opacity-75">Booking #{{ $booking->booking_id }}</p>
  </div>
</div>

<div class="container" style="padding-top: 80px; padding-bottom: 60px;">
  <div class="row justify-content-center">
    <div class="col-lg-8">
      
      {{-- Booking Summary --}}
      <div class="booking-summary-mini mb-4">
        <div class="row align-items-center">
          <div class="col-md-2 text-center mb-3 mb-md-0">
            @if($booking->car && $booking->car->image_url)
              <img src="{{ $booking->car->image_url }}" alt="Car" class="img-fluid rounded" style="max-height: 80px; object-fit: cover;">
            @else
              <div class="bg-secondary rounded d-flex align-items-center justify-content-center" style="width: 80px; height: 60px;">
                <i class="bi bi-car-front text-white" style="font-size: 24px;"></i>
              </div>
            @endif
          </div>
          <div class="col-md-6">
            <h6 class="fw-bold mb-1">{{ $booking->car->brand ?? 'N/A' }} {{ $booking->car->model ?? '' }}</h6>
            <div class="small text-muted">
              <i class="bi bi-calendar me-1"></i> {{ $booking->start_datetime?->format('d M Y') }} - {{ $booking->end_datetime?->format('d M Y') }}
            </div>
          </div>
          <div class="col-md-4 text-md-end mt-2 mt-md-0">
            <div class="small text-muted">Total Amount</div>
            <div class="fw-bold text-danger" style="font-size: 18px;">RM {{ number_format($booking->final_amount ?? 0, 2) }}</div>
          </div>
        </div>
      </div>

      {{-- Warning Box --}}
      <div class="info-box danger">
        <div class="d-flex">
          <i class="bi bi-exclamation-triangle-fill text-danger me-3" style="font-size: 24px;"></i>
          <div>
            <strong>Important Notice</strong>
            <p class="mb-0 small mt-1">
              Cancellation requests are subject to review by our staff. Refund amounts may vary based on our cancellation policy and the time of cancellation. Processing typically takes 3-5 business days.
            </p>
          </div>
        </div>
      </div>

      {{-- Cancellation Form --}}
      <div class="card cancel-card">
        <div class="cancel-card-header">
          <h5 class="fw-bold mb-0"><i class="bi bi-clipboard-minus me-2"></i> Cancellation Request Form</h5>
        </div>
        <div class="cancel-card-body">
          <form method="POST" action="{{ route('customer.bookings.cancel.submit', $booking->booking_id) }}" enctype="multipart/form-data">
            @csrf
            
            {{-- Section 1: Reason --}}
            <div class="form-section">
              <div class="form-section-title">
                <span class="badge bg-danger">1</span>
                Reason for Cancellation
              </div>
              
              <div class="mb-3">
                <label class="form-label">Why are you cancelling this booking? <span class="text-danger">*</span></label>
                <select name="reason_type" class="form-select @error('reason_type') is-invalid @enderror" required>
                  <option value="">-- Select a reason --</option>
                  @foreach($reasonTypes as $value => $label)
                    <option value="{{ $value }}" {{ old('reason_type') === $value ? 'selected' : '' }}>{{ $label }}</option>
                  @endforeach
                </select>
                @error('reason_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
              
              <div class="mb-0">
                <label class="form-label">Additional Details (Optional)</label>
                <textarea name="reason_details" class="form-control @error('reason_details') is-invalid @enderror" rows="3" placeholder="Please provide any additional information that might help us process your request...">{{ old('reason_details') }}</textarea>
                @error('reason_details')<div class="invalid-feedback">{{ $message }}</div>@enderror
                <div class="form-text">Maximum 1000 characters</div>
              </div>
            </div>

            {{-- Section 2: Bank Details --}}
            <div class="form-section">
              <div class="form-section-title">
                <span class="badge bg-danger">2</span>
                Refund Bank Details
              </div>
              
              <div class="info-box mb-4">
                <div class="d-flex align-items-center">
                  <i class="bi bi-info-circle text-warning me-2"></i>
                  <span class="small">Please ensure your bank details are correct. Refunds will be processed to this account.</span>
                </div>
              </div>
              
              <div class="row g-3">
                <div class="col-md-12">
                  <label class="form-label">Bank Name <span class="text-danger">*</span></label>
                  <select name="bank_name" class="form-select @error('bank_name') is-invalid @enderror" required>
                    <option value="">-- Select your bank --</option>
                    <option value="Maybank" {{ old('bank_name') === 'Maybank' ? 'selected' : '' }}>Maybank</option>
                    <option value="CIMB Bank" {{ old('bank_name') === 'CIMB Bank' ? 'selected' : '' }}>CIMB Bank</option>
                    <option value="Public Bank" {{ old('bank_name') === 'Public Bank' ? 'selected' : '' }}>Public Bank</option>
                    <option value="RHB Bank" {{ old('bank_name') === 'RHB Bank' ? 'selected' : '' }}>RHB Bank</option>
                    <option value="Hong Leong Bank" {{ old('bank_name') === 'Hong Leong Bank' ? 'selected' : '' }}>Hong Leong Bank</option>
                    <option value="AmBank" {{ old('bank_name') === 'AmBank' ? 'selected' : '' }}>AmBank</option>
                    <option value="Bank Islam" {{ old('bank_name') === 'Bank Islam' ? 'selected' : '' }}>Bank Islam</option>
                    <option value="Bank Rakyat" {{ old('bank_name') === 'Bank Rakyat' ? 'selected' : '' }}>Bank Rakyat</option>
                    <option value="BSN" {{ old('bank_name') === 'BSN' ? 'selected' : '' }}>BSN (Bank Simpanan Nasional)</option>
                    <option value="Affin Bank" {{ old('bank_name') === 'Affin Bank' ? 'selected' : '' }}>Affin Bank</option>
                    <option value="Alliance Bank" {{ old('bank_name') === 'Alliance Bank' ? 'selected' : '' }}>Alliance Bank</option>
                    <option value="OCBC Bank" {{ old('bank_name') === 'OCBC Bank' ? 'selected' : '' }}>OCBC Bank</option>
                    <option value="HSBC" {{ old('bank_name') === 'HSBC' ? 'selected' : '' }}>HSBC</option>
                    <option value="Standard Chartered" {{ old('bank_name') === 'Standard Chartered' ? 'selected' : '' }}>Standard Chartered</option>
                    <option value="UOB" {{ old('bank_name') === 'UOB' ? 'selected' : '' }}>UOB</option>
                    <option value="Agrobank" {{ old('bank_name') === 'Agrobank' ? 'selected' : '' }}>Agrobank</option>
                    <option value="Bank Muamalat" {{ old('bank_name') === 'Bank Muamalat' ? 'selected' : '' }}>Bank Muamalat</option>
                    <option value="Other" {{ old('bank_name') === 'Other' ? 'selected' : '' }}>Other</option>
                  </select>
                  @error('bank_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                
                <div class="col-md-6">
                  <label class="form-label">Account Number <span class="text-danger">*</span></label>
                  <input type="text" name="bank_account_number" class="form-control @error('bank_account_number') is-invalid @enderror" placeholder="e.g., 1234567890" value="{{ old('bank_account_number') }}" required>
                  @error('bank_account_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                
                <div class="col-md-6">
                  <label class="form-label">Account Holder Name <span class="text-danger">*</span></label>
                  <input type="text" name="bank_account_holder" class="form-control @error('bank_account_holder') is-invalid @enderror" placeholder="Name as per bank account" value="{{ old('bank_account_holder') }}" required>
                  @error('bank_account_holder')<div class="invalid-feedback">{{ $message }}</div>@enderror
                  <div class="form-text">Must match the name on your bank account</div>
                </div>
              </div>
            </div>

            {{-- Section 3: Supporting Documents --}}
            <div class="form-section">
              <div class="form-section-title">
                <span class="badge bg-danger">3</span>
                Supporting Documents
                <span class="badge bg-secondary">Optional</span>
              </div>
              
              <p class="text-muted small mb-3">
                If applicable, please upload any supporting documents (e.g., medical certificate for emergency cancellations, screenshots of alternative bookings, etc.)
              </p>
              
              <div class="mb-3">
                <label class="form-label">Proof/Supporting Document</label>
                <input type="file" name="proof_document" class="form-control @error('proof_document') is-invalid @enderror" accept=".jpeg,.jpg,.png,.pdf">
                @error('proof_document')<div class="invalid-feedback">{{ $message }}</div>@enderror
                <div class="form-text">Accepted formats: JPEG, PNG, PDF. Maximum size: 5MB</div>
              </div>
            </div>

            {{-- Section 4: Terms & Submit --}}
            <div class="form-section">
              <div class="form-section-title">
                <span class="badge bg-danger">4</span>
                Acknowledgement
              </div>
              
              <div class="form-check mb-3">
                <input class="form-check-input @error('agree_terms') is-invalid @enderror" type="checkbox" name="agree_terms" id="agreeTerms" value="1" required>
                <label class="form-check-label" for="agreeTerms">
                  I understand that:
                </label>
                @error('agree_terms')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
              
              <ul class="small text-muted mb-4">
                <li>This cancellation request is subject to approval by Hasta staff</li>
                <li>Refund amount may be subject to cancellation fees as per the cancellation policy</li>
                <li>Refunds will be processed within 3-5 business days after approval</li>
                <li>I have provided accurate bank details for the refund</li>
              </ul>
              
              <div class="d-flex gap-3">
                <button type="submit" class="btn btn-cancel-submit">
                  <i class="bi bi-send me-2"></i> Submit Cancellation Request
                </button>
                <a href="{{ route('customer.bookings.show', $booking->booking_id) }}" class="btn btn-outline-secondary">
                  <i class="bi bi-arrow-left me-2"></i> Go Back
                </a>
              </div>
            </div>
          </form>
        </div>
      </div>
      
    </div>
  </div>
</div>
@endsection
