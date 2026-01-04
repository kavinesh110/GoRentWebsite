@extends('layouts.app')
@section('title', 'Edit Car - Staff Dashboard')

@section('content')
<div class="d-flex" style="min-height: calc(100vh - 60px);">
  @include('staff._nav')
  <div class="flex-fill" style="background: var(--bg);">
    <div class="container-fluid px-4 px-md-5" style="padding-top: 32px; padding-bottom: 48px;">
  <div class="mb-4">
    <a href="{{ route('staff.cars') }}" class="text-decoration-none small">&larr; Back to cars</a>
    <h1 class="h3 fw-bold mt-2 mb-1" style="color:#333;">Edit Car: {{ $car->brand }} {{ $car->model }}</h1>
    <p class="text-muted mb-0" style="font-size: 14px;">Update car details, status and maintenance information.</p>
  </div>

  <div class="card border-0 shadow-soft">
    <div class="card-body p-4">
      @if($errors->any() && $errors->has('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
          <strong>Error:</strong> {{ $errors->first('error') }}
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
      @endif
      
      @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
          {{ session('success') }}
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
      @endif

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
            <input type="number" name="base_rate_per_hour" step="0.01" class="form-control @error('base_rate_per_hour') is-invalid @enderror" value="{{ old('base_rate_per_hour', $car->base_rate_per_hour ?? '') }}" min="0" required>
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
            <input type="number" name="current_mileage" class="form-control @error('current_mileage') is-invalid @enderror" value="{{ old('current_mileage', $car->current_mileage ?? '') }}" min="0">
            @error('current_mileage')<div class="invalid-feedback">{{ $message }}</div>@enderror
            @if(isset($car->current_mileage) && isset($car->service_mileage_limit) && $car->current_mileage >= $car->service_mileage_limit)
              <small class="text-warning">⚠ Mileage exceeds service limit. Status will be set to Maintenance.</small>
            @endif
            <small class="text-muted">Leave empty to set to 0</small>
          </div>
          <div class="col-md-6">
            <label class="form-label">Service Mileage Limit (km) <span class="text-danger">*</span></label>
            <input type="number" name="service_mileage_limit" class="form-control @error('service_mileage_limit') is-invalid @enderror" value="{{ old('service_mileage_limit', $car->service_mileage_limit ?? '') }}" min="0" required>
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
          <a href="{{ route('staff.maintenance.index', $car->id) }}" class="btn btn-outline-info">View Maintenance Records</a>
          <a href="{{ route('staff.cars') }}" class="btn btn-outline-secondary">Cancel</a>
        </div>
      </form>
    </div>
  </div>

  {{-- REPORTED CAR ISSUES --}}
  @if(isset($carIssues) && $carIssues->count() > 0)
  <div class="card border-0 shadow-soft mt-4 border-start border-warning border-4">
    <div class="card-header bg-white border-0 pt-4 px-4">
      <div class="d-flex justify-content-between align-items-center">
        <h5 class="fw-bold mb-0">
          <i class="bi bi-exclamation-triangle text-warning me-2"></i>
          Reported Issues ({{ $carIssues->count() }})
        </h5>
        <a href="{{ route('staff.maintenance-issues', ['car_id' => $car->id]) }}" class="btn btn-sm btn-outline-warning">
          View All Issues
        </a>
      </div>
      <p class="text-muted small mb-0 mt-1">Customer-reported issues that may need attention</p>
    </div>
    <div class="card-body p-4">
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th style="font-size: 12px;">Category</th>
              <th style="font-size: 12px;">Subject</th>
              <th style="font-size: 12px;">Reported By</th>
              <th style="font-size: 12px;">Status</th>
              <th style="font-size: 12px;">Date</th>
              <th style="font-size: 12px;"></th>
            </tr>
          </thead>
          <tbody>
            @foreach($carIssues as $issue)
              @php
                $categoryColors = [
                  'cleanliness' => 'bg-info text-white',
                  'lacking_facility' => 'bg-warning text-dark',
                  'bluetooth' => 'bg-purple text-white',
                  'engine' => 'bg-danger text-white',
                  'others' => 'bg-secondary text-white'
                ];
                $statusColors = [
                  'open' => 'bg-danger text-white',
                  'in_progress' => 'bg-primary text-white',
                  'resolved' => 'bg-success text-white',
                  'closed' => 'bg-secondary text-white'
                ];
              @endphp
              <tr>
                <td>
                  <span class="badge {{ $categoryColors[$issue->category] ?? 'bg-secondary' }}" style="font-size: 11px;">
                    {{ ucfirst(str_replace('_', ' ', $issue->category)) }}
                  </span>
                </td>
                <td>
                  <div class="fw-semibold" style="font-size: 13px;">{{ \Illuminate\Support\Str::limit($issue->subject, 30) }}</div>
                  <div class="small text-muted">{{ \Illuminate\Support\Str::limit($issue->description, 50) }}</div>
                </td>
                <td>
                  <div style="font-size: 13px;">{{ $issue->name }}</div>
                </td>
                <td>
                  <span class="badge {{ $statusColors[$issue->status] ?? 'bg-secondary' }}" style="font-size: 10px;">
                    {{ ucfirst(str_replace('_', ' ', $issue->status)) }}
                  </span>
                </td>
                <td>
                  <div style="font-size: 12px;">{{ $issue->created_at->format('d M Y') }}</div>
                </td>
                <td>
                  <a href="{{ route('staff.support-tickets.show', $issue->ticket_id) }}" class="btn btn-sm btn-outline-primary">
                    <i class="bi bi-eye"></i>
                  </a>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>
  @endif

  </div>
</div>
</div>

<style>
  .bg-purple { background-color: #7b1fa2 !important; }
</style>
@endsection
