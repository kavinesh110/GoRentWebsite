@extends('layouts.app')
@section('title', 'Add Maintenance Record - Staff Dashboard')

@section('content')
<div class="d-flex" style="min-height: calc(100vh - 60px);">
  @include('staff._nav')
  <div class="flex-fill" style="background: var(--bg);">
    <div class="container-fluid px-4 px-md-5" style="padding-top: 32px; padding-bottom: 48px;">
      <div class="mb-4">
        <a href="{{ route('staff.maintenance.index', $car->id) }}" class="text-decoration-none small">&larr; Back to maintenance records</a>
        <h1 class="h3 fw-bold mt-2 mb-1" style="color:#333;">Add Maintenance Record</h1>
        <p class="text-muted mb-0" style="font-size: 14px;">Record service/maintenance for {{ $car->brand }} {{ $car->model }} ({{ $car->plate_number }})</p>
      </div>

      <div class="card border-0 shadow-soft">
        <div class="card-body p-4">
          <form method="POST" action="{{ route('staff.maintenance.store', $car->id) }}">
            @csrf

            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label">Service Date <span class="text-danger">*</span></label>
                <input type="date" name="service_date" class="form-control" value="{{ old('service_date', now()->format('Y-m-d')) }}" required>
                @error('service_date')<div class="text-danger small">{{ $message }}</div>@enderror
              </div>
              <div class="col-md-6">
                <label class="form-label">Mileage at Service <span class="text-danger">*</span></label>
                <input type="number" name="mileage_at_service" class="form-control" value="{{ old('mileage_at_service', $car->current_mileage) }}" required min="0" placeholder="Current: {{ number_format($car->current_mileage) }} km">
                @error('mileage_at_service')<div class="text-danger small">{{ $message }}</div>@enderror
              </div>
              <div class="col-12">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control" rows="3" placeholder="Service type, work performed, parts replaced...">{{ old('description') }}</textarea>
                @error('description')<div class="text-danger small">{{ $message }}</div>@enderror
              </div>
              <div class="col-md-6">
                <label class="form-label">Service Cost (RM)</label>
                <input type="number" name="cost" class="form-control" value="{{ old('cost') }}" step="0.01" min="0" placeholder="0.00">
                @error('cost')<div class="text-danger small">{{ $message }}</div>@enderror
              </div>
              <div class="col-md-6">
                <label class="form-label">Auto-Update Car Information</label>
                <div class="form-check mt-2">
                  <input class="form-check-input" type="checkbox" name="update_car_mileage" id="update_car_mileage" value="1" checked>
                  <label class="form-check-label" for="update_car_mileage">
                    Update car's current mileage to service mileage
                  </label>
                </div>
                <div class="form-check mt-2">
                  <input class="form-check-input" type="checkbox" name="update_last_service_date" id="update_last_service_date" value="1" checked>
                  <label class="form-check-label" for="update_last_service_date">
                    Update car's last service date
                  </label>
                </div>
                @if($car->status === 'maintenance')
                <div class="form-check mt-3 p-3 rounded" style="background: #d1fae5; border: 1px solid #6ee7b7;">
                  <input class="form-check-input" type="checkbox" name="mark_complete" id="mark_complete" value="1" checked>
                  <label class="form-check-label fw-bold" for="mark_complete" style="color: #065f46;">
                    <i class="bi bi-check-circle-fill me-1"></i>Mark maintenance complete (set car to Available)
                  </label>
                </div>
                @endif
              </div>
              <div class="col-12 mt-4">
                <button type="submit" class="btn btn-hasta">Save Maintenance Record</button>
                <a href="{{ route('staff.maintenance.index', $car->id) }}" class="btn btn-outline-secondary ms-2">Cancel</a>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
