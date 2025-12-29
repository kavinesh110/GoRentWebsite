@extends('layouts.app')
@section('title', 'Add New Car - Staff Dashboard')

@section('content')
<div class="d-flex" style="min-height: calc(100vh - 60px);">
  @include('staff._nav')
  <div class="flex-fill" style="background: var(--bg);">
    <div class="container-fluid px-4 px-md-5" style="padding-top: 32px; padding-bottom: 48px;">
  <div class="mb-4">
    <a href="{{ route('staff.cars') }}" class="text-decoration-none small">&larr; Back to cars</a>
    <h1 class="h3 fw-bold mt-2 mb-1" style="color:#333;">Add New Car</h1>
    <p class="text-muted mb-0" style="font-size: 14px;">Register a new vehicle in the Hasta Travels &amp; Tours fleet.</p>
  </div>

  <div class="card border-0 shadow-soft">
    <div class="card-body p-4">
      <form method="POST" action="{{ route('staff.cars.store') }}" enctype="multipart/form-data">
        @csrf

        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label">Plate Number <span class="text-danger">*</span></label>
            <input type="text" name="plate_number" class="form-control @error('plate_number') is-invalid @enderror" value="{{ old('plate_number') }}" required>
            @error('plate_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-6">
            <label class="form-label">Fuel Type <span class="text-danger">*</span></label>
            <select name="fuel_type" class="form-select @error('fuel_type') is-invalid @enderror" required>
              <option value="">Select...</option>
              <option value="petrol" {{ old('fuel_type') === 'petrol' ? 'selected' : '' }}>Petrol</option>
              <option value="diesel" {{ old('fuel_type') === 'diesel' ? 'selected' : '' }}>Diesel</option>
              <option value="hybrid" {{ old('fuel_type') === 'hybrid' ? 'selected' : '' }}>Hybrid</option>
              <option value="ev" {{ old('fuel_type') === 'ev' ? 'selected' : '' }}>Electric (EV)</option>
              <option value="other" {{ old('fuel_type') === 'other' ? 'selected' : '' }}>Other</option>
            </select>
            @error('fuel_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-6">
            <label class="form-label">Brand <span class="text-danger">*</span></label>
            <input type="text" name="brand" class="form-control @error('brand') is-invalid @enderror" value="{{ old('brand') }}" required>
            @error('brand')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-6">
            <label class="form-label">Model <span class="text-danger">*</span></label>
            <input type="text" name="model" class="form-control @error('model') is-invalid @enderror" value="{{ old('model') }}" required>
            @error('model')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-6">
            <label class="form-label">Year <span class="text-danger">*</span></label>
            <input type="number" name="year" class="form-control @error('year') is-invalid @enderror" value="{{ old('year') }}" min="1900" max="{{ date('Y') + 1 }}" required>
            @error('year')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-6">
            <label class="form-label">Base Rate (per hour, RM) <span class="text-danger">*</span></label>
            <input type="number" name="base_rate_per_hour" step="0.01" class="form-control @error('base_rate_per_hour') is-invalid @enderror" value="{{ old('base_rate_per_hour') }}" min="0" required>
            @error('base_rate_per_hour')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-6">
            <label class="form-label">Status <span class="text-danger">*</span></label>
            <select name="status" class="form-select @error('status') is-invalid @enderror" required>
              <option value="available" {{ old('status', 'available') === 'available' ? 'selected' : '' }}>Available</option>
              <option value="in_use" {{ old('status') === 'in_use' ? 'selected' : '' }}>In Use</option>
              <option value="maintenance" {{ old('status') === 'maintenance' ? 'selected' : '' }}>Maintenance</option>
            </select>
            @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-6">
            <label class="form-label">Current Mileage (km)</label>
            <input type="number" name="current_mileage" class="form-control @error('current_mileage') is-invalid @enderror" value="{{ old('current_mileage', 0) }}" min="0">
            @error('current_mileage')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-6">
            <label class="form-label">Service Mileage Limit (km) <span class="text-danger">*</span></label>
            <input type="number" name="service_mileage_limit" class="form-control @error('service_mileage_limit') is-invalid @enderror" value="{{ old('service_mileage_limit') }}" min="0" required>
            @error('service_mileage_limit')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-6">
            <label class="form-label">Last Service Date</label>
            <input type="date" name="last_service_date" class="form-control @error('last_service_date') is-invalid @enderror" value="{{ old('last_service_date') }}">
            @error('last_service_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-12">
            <label class="form-label">Car Image</label>
            <input type="file" name="car_image" class="form-control @error('car_image') is-invalid @enderror" accept="image/jpeg,image/png,image/jpg,image/gif,image/webp">
            @error('car_image')<div class="invalid-feedback">{{ $message }}</div>@enderror
            <small class="text-muted">Accepted formats: JPEG, PNG, GIF, WebP. Max size: 5MB</small>
          </div>
          <div class="col-12">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" name="gps_enabled" id="gps_enabled" {{ old('gps_enabled') ? 'checked' : '' }}>
              <label class="form-check-label" for="gps_enabled">GPS Enabled</label>
            </div>
          </div>
        </div>

        <div class="mt-4 d-flex gap-2">
          <button type="submit" class="btn btn-hasta">Add Car</button>
          <a href="{{ route('staff.cars') }}" class="btn btn-outline-secondary">Cancel</a>
        </div>
      </form>
    </div>
  </div>
  </div>
</div>
</div>
@endsection
