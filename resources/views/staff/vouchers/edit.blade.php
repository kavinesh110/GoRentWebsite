@extends('layouts.app')
@section('title', 'Edit Voucher - Staff Dashboard')

@section('content')
<div class="d-flex" style="min-height: calc(100vh - 60px);">
  @include('staff._nav')
  <div class="flex-fill" style="background: var(--bg);">
    <div class="container-fluid px-4 px-md-5" style="padding-top: 32px; padding-bottom: 48px;">
      <div class="mb-4">
        <a href="{{ route('staff.vouchers') }}" class="text-decoration-none small">&larr; Back to vouchers</a>
        <h1 class="h3 fw-bold mt-2 mb-1" style="color:#333;">Edit Voucher: {{ $voucher->code }}</h1>
        <p class="text-muted mb-0" style="font-size: 14px;">Update voucher details and settings.</p>
      </div>

      <div class="card border-0 shadow-soft">
        <div class="card-body p-4">
          <form method="POST" action="{{ route('staff.vouchers.update', $voucher->voucher_id) }}">
            @csrf
            @method('PUT')

            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label">Voucher Code <span class="text-danger">*</span></label>
                <input type="text" name="code" class="form-control" value="{{ old('code', $voucher->code) }}" required>
                @error('code')<div class="text-danger small">{{ $message }}</div>@enderror
              </div>
              <div class="col-md-6">
                <label class="form-label">Expiry Date <span class="text-danger">*</span></label>
                <input type="date" name="expiry_date" class="form-control" value="{{ old('expiry_date', $voucher->expiry_date->format('Y-m-d')) }}" required>
                @error('expiry_date')<div class="text-danger small">{{ $message }}</div>@enderror
              </div>
              <div class="col-12">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control" rows="2">{{ old('description', $voucher->description) }}</textarea>
                @error('description')<div class="text-danger small">{{ $message }}</div>@enderror
              </div>
              <div class="col-md-6">
                <label class="form-label">Discount Type <span class="text-danger">*</span></label>
                <select name="discount_type" class="form-select" required>
                  <option value="percent" {{ old('discount_type', $voucher->discount_type) === 'percent' ? 'selected' : '' }}>Percentage (%)</option>
                  <option value="fixed" {{ old('discount_type', $voucher->discount_type) === 'fixed' ? 'selected' : '' }}>Fixed Amount (RM)</option>
                </select>
                @error('discount_type')<div class="text-danger small">{{ $message }}</div>@enderror
              </div>
              <div class="col-md-6">
                <label class="form-label">Discount Value <span class="text-danger">*</span></label>
                <input type="number" name="discount_value" class="form-control" value="{{ old('discount_value', $voucher->discount_value) }}" required step="0.01" min="0">
                @error('discount_value')<div class="text-danger small">{{ $message }}</div>@enderror
              </div>
              <div class="col-md-6">
                <label class="form-label">Minimum Stamps Required <span class="text-danger">*</span></label>
                <input type="number" name="min_stamps_required" class="form-control" value="{{ old('min_stamps_required', $voucher->min_stamps_required) }}" required min="0">
                @error('min_stamps_required')<div class="text-danger small">{{ $message }}</div>@enderror
              </div>
              <div class="col-md-6">
                <label class="form-label">Status</label>
                <div class="form-check mt-2">
                  <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $voucher->is_active) ? 'checked' : '' }}>
                  <label class="form-check-label" for="is_active">
                    Active (voucher is available for redemption)
                  </label>
                </div>
              </div>
              <div class="col-12 mt-4">
                <button type="submit" class="btn btn-hasta">Update Voucher</button>
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
