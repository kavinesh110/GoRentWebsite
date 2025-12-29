@extends('layouts.app')
@section('title', 'Create Voucher - Staff Dashboard')

@section('content')
<div class="d-flex" style="min-height: calc(100vh - 60px);">
  @include('staff._nav')
  <div class="flex-fill" style="background: var(--bg);">
    <div class="container-fluid px-4 px-md-5" style="padding-top: 32px; padding-bottom: 48px;">
      <div class="mb-4">
        <a href="{{ route('staff.vouchers') }}" class="text-decoration-none small">&larr; Back to vouchers</a>
        <h1 class="h3 fw-bold mt-2 mb-1" style="color:#333;">Create New Voucher</h1>
        <p class="text-muted mb-0" style="font-size: 14px;">Add a new loyalty reward voucher for customers to redeem.</p>
      </div>

      <div class="card border-0 shadow-soft">
        <div class="card-body p-4">
          <form method="POST" action="{{ route('staff.vouchers.store') }}">
            @csrf

            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label">Voucher Code <span class="text-danger">*</span></label>
                <input type="text" name="code" class="form-control" value="{{ old('code') }}" required placeholder="e.g., SUMMER2025">
                <small class="text-muted">Unique code for this voucher (uppercase recommended)</small>
                @error('code')<div class="text-danger small">{{ $message }}</div>@enderror
              </div>
              <div class="col-md-6">
                <label class="form-label">Expiry Date <span class="text-danger">*</span></label>
                <input type="date" name="expiry_date" class="form-control" value="{{ old('expiry_date') }}" required min="{{ date('Y-m-d') }}">
                @error('expiry_date')<div class="text-danger small">{{ $message }}</div>@enderror
              </div>
              <div class="col-12">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control" rows="2" placeholder="Describe this voucher...">{{ old('description') }}</textarea>
                @error('description')<div class="text-danger small">{{ $message }}</div>@enderror
              </div>
              <div class="col-md-6">
                <label class="form-label">Discount Type <span class="text-danger">*</span></label>
                <select name="discount_type" class="form-select" required>
                  <option value="percent" {{ old('discount_type') === 'percent' ? 'selected' : '' }}>Percentage (%)</option>
                  <option value="fixed" {{ old('discount_type') === 'fixed' ? 'selected' : '' }}>Fixed Amount (RM)</option>
                </select>
                @error('discount_type')<div class="text-danger small">{{ $message }}</div>@enderror
              </div>
              <div class="col-md-6">
                <label class="form-label">Discount Value <span class="text-danger">*</span></label>
                <input type="number" name="discount_value" class="form-control" value="{{ old('discount_value') }}" required step="0.01" min="0" placeholder="e.g., 10 or 50.00">
                <small class="text-muted">Enter percentage (e.g., 10) or amount in RM (e.g., 50.00)</small>
                @error('discount_value')<div class="text-danger small">{{ $message }}</div>@enderror
              </div>
              <div class="col-md-6">
                <label class="form-label">Minimum Stamps Required <span class="text-danger">*</span></label>
                <input type="number" name="min_stamps_required" class="form-control" value="{{ old('min_stamps_required', 0) }}" required min="0" placeholder="0">
                <small class="text-muted">Number of loyalty stamps needed to redeem this voucher</small>
                @error('min_stamps_required')<div class="text-danger small">{{ $message }}</div>@enderror
              </div>
              <div class="col-md-6">
                <label class="form-label">Status</label>
                <div class="form-check mt-2">
                  <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                  <label class="form-check-label" for="is_active">
                    Active (voucher is available for redemption)
                  </label>
                </div>
              </div>
              <div class="col-12 mt-4">
                <button type="submit" class="btn btn-hasta">Create Voucher</button>
                <a href="{{ route('staff.vouchers') }}" class="btn btn-outline-secondary ms-2">Cancel</a>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
