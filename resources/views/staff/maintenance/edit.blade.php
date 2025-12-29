@extends('layouts.app')
@section('title', 'Edit Maintenance Record - Staff Dashboard')

@section('content')
<div class="d-flex" style="min-height: calc(100vh - 60px);">
  @include('staff._nav')
  <div class="flex-fill" style="background: var(--bg);">
    <div class="container-fluid px-4 px-md-5" style="padding-top: 32px; padding-bottom: 48px;">
      <div class="mb-4">
        <a href="{{ route('staff.maintenance.index', $car->id) }}" class="text-decoration-none small">&larr; Back to maintenance records</a>
        <h1 class="h3 fw-bold mt-2 mb-1" style="color:#333;">Edit Maintenance Record</h1>
        <p class="text-muted mb-0" style="font-size: 14px;">Update maintenance record for {{ $car->brand }} {{ $car->model }} ({{ $car->plate_number }})</p>
      </div>

      <div class="card border-0 shadow-soft">
        <div class="card-body p-4">
          <form method="POST" action="{{ route('staff.maintenance.update', [$car->id, $record->maintenance_id]) }}">
            @csrf
            @method('PUT')

            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label">Service Date <span class="text-danger">*</span></label>
                <input type="date" name="service_date" class="form-control" value="{{ old('service_date', $record->service_date->format('Y-m-d')) }}" required>
                @error('service_date')<div class="text-danger small">{{ $message }}</div>@enderror
              </div>
              <div class="col-md-6">
                <label class="form-label">Mileage at Service <span class="text-danger">*</span></label>
                <input type="number" name="mileage_at_service" class="form-control" value="{{ old('mileage_at_service', $record->mileage_at_service) }}" required min="0">
                @error('mileage_at_service')<div class="text-danger small">{{ $message }}</div>@enderror
              </div>
              <div class="col-12">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control" rows="3">{{ old('description', $record->description) }}</textarea>
                @error('description')<div class="text-danger small">{{ $message }}</div>@enderror
              </div>
              <div class="col-md-6">
                <label class="form-label">Service Cost (RM)</label>
                <input type="number" name="cost" class="form-control" value="{{ old('cost', $record->cost) }}" step="0.01" min="0">
                @error('cost')<div class="text-danger small">{{ $message }}</div>@enderror
              </div>
              <div class="col-12 mt-4">
                <button type="submit" class="btn btn-hasta">Update Maintenance Record</button>
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
