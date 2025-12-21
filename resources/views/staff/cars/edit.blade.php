@extends('layouts.app')
@section('title', 'Edit Car - Staff Dashboard')

@section('content')
<div class="d-flex" style="min-height: calc(100vh - 60px);">
  @include('staff._nav')
  <div class="flex-fill" style="background: var(--bg);">
    <div class="container-custom" style="padding: 32px 24px 48px; max-width: 900px;">
  <div class="mb-4">
    <a href="{{ route('staff.cars') }}" class="text-decoration-none small">&larr; Back to cars</a>
    <h1 class="h3 fw-bold mt-2 mb-1" style="color:#333;">Edit Car: {{ $car->brand }} {{ $car->model }}</h1>
    <p class="text-muted mb-0" style="font-size: 14px;">Update car details, status and maintenance information.</p>
  </div>

  <div class="card border-0 shadow-soft">
    <div class="card-body p-4">
      <form method="POST" action="{{ route('staff.cars.update', $car->id) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label">Plate Number <span class="text-danger">*</span></label>
            <input type="text" name="plate_number" class="form-control @error('plate_number') is-invalid @enderror" value="{{ old('plate_number', $car->plate_number) }}" required>
            @error('plate_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-6">
            <label class="form-label">Fuel Type <span class="text-danger">*</span></label>
            <select name="fuel_type" class="form-select @error('fuel_type') is-invalid @enderror" required>
              <option value="petrol" {{ old('fuel_type', $car->fuel_type) === 'petrol' ? 'selected' : '' }}>Petrol</option>
              <option value="diesel" {{ old('fuel_type', $car->fuel_type) === 'diesel' ? 'selected' : '' }}>Diesel</option>
              <option value="hybrid" {{ old('fuel_type', $car->fuel_type) === 'hybrid' ? 'selected' : '' }}>Hybrid</option>
              <option value="ev" {{ old('fuel_type', $car->fuel_type) === 'ev' ? 'selected' : '' }}>Electric (EV)</option>
              <option value="other" {{ old('fuel_type', $car->fuel_type) === 'other' ? 'selected' : '' }}>Other</option>
            </select>
            @error('fuel_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-6">
            <label class="form-label">Brand <span class="text-danger">*</span></label>
            <input type="text" name="brand" class="form-control @error('brand') is-invalid @enderror" value="{{ old('brand', $car->brand) }}" required>
            @error('brand')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-6">
            <label class="form-label">Model <span class="text-danger">*</span></label>
            <input type="text" name="model" class="form-control @error('model') is-invalid @enderror" value="{{ old('model', $car->model) }}" required>
            @error('model')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-6">
            <label class="form-label">Year <span class="text-danger">*</span></label>
            <input type="number" name="year" class="form-control @error('year') is-invalid @enderror" value="{{ old('year', $car->year) }}" min="1900" max="{{ date('Y') + 1 }}" required>
            @error('year')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-6">
            <label class="form-label">Base Rate (per hour, RM) <span class="text-danger">*</span></label>
            <input type="number" name="base_rate_per_hour" step="0.01" class="form-control @error('base_rate_per_hour') is-invalid @enderror" value="{{ old('base_rate_per_hour', $car->base_rate_per_hour) }}" min="0" required>
            @error('base_rate_per_hour')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-6">
            <label class="form-label">Status <span class="text-danger">*</span></label>
            <select name="status" class="form-select @error('status') is-invalid @enderror" required>
              <option value="available" {{ old('status', $car->status) === 'available' ? 'selected' : '' }}>Available</option>
              <option value="in_use" {{ old('status', $car->status) === 'in_use' ? 'selected' : '' }}>In Use</option>
              <option value="maintenance" {{ old('status', $car->status) === 'maintenance' ? 'selected' : '' }}>Maintenance</option>
            </select>
            @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-6">
            <label class="form-label">Current Mileage (km)</label>
            <input type="number" name="current_mileage" class="form-control @error('current_mileage') is-invalid @enderror" value="{{ old('current_mileage', $car->current_mileage) }}" min="0">
            @error('current_mileage')<div class="invalid-feedback">{{ $message }}</div>@enderror
            @if($car->current_mileage >= $car->service_mileage_limit)
              <small class="text-warning">⚠ Mileage exceeds service limit. Status will be set to Maintenance.</small>
            @endif
          </div>
          <div class="col-md-6">
            <label class="form-label">Service Mileage Limit (km) <span class="text-danger">*</span></label>
            <input type="number" name="service_mileage_limit" class="form-control @error('service_mileage_limit') is-invalid @enderror" value="{{ old('service_mileage_limit', $car->service_mileage_limit) }}" min="0" required>
            @error('service_mileage_limit')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-6">
            <label class="form-label">Last Service Date</label>
            <input type="date" name="last_service_date" class="form-control @error('last_service_date') is-invalid @enderror" value="{{ old('last_service_date', $car->last_service_date?->format('Y-m-d')) }}">
            @error('last_service_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-12">
            <label class="form-label">Car Image</label>
            @if($car->image_url)
              <div class="mb-2">
                <img src="{{ $car->image_url }}" alt="{{ $car->brand }} {{ $car->model }}" class="img-thumbnail" style="max-width: 300px; max-height: 200px; object-fit: cover;">
                <div class="small text-muted mt-1">Current image</div>
              </div>
            @endif
            <input type="file" name="car_image" class="form-control @error('car_image') is-invalid @enderror" accept="image/jpeg,image/png,image/jpg,image/gif,image/webp">
            @error('car_image')<div class="invalid-feedback">{{ $message }}</div>@enderror
            <small class="text-muted">Accepted formats: JPEG, PNG, GIF, WebP. Max size: 5MB. Leave empty to keep current image.</small>
          </div>
          <div class="col-12">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" name="gps_enabled" id="gps_enabled" {{ old('gps_enabled', $car->gps_enabled) ? 'checked' : '' }}>
              <label class="form-check-label" for="gps_enabled">GPS Enabled</label>
            </div>
          </div>
        </div>

        <div class="mt-4 d-flex gap-2">
          <button type="submit" class="btn btn-hasta">Update Car</button>
          <a href="{{ route('staff.cars') }}" class="btn btn-outline-secondary">Cancel</a>
        </div>
      </form>
    </div>
  </div>
  </div>
</div>
</div>
@endsection
