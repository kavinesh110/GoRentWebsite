@extends('layouts.app')
@section('title', 'Manage Cars - Staff Dashboard')

@section('content')
<div class="d-flex" style="min-height: calc(100vh - 60px);">
  @include('staff._nav')
  <div class="flex-fill" style="background: #f8fafc;">
    <div class="container-fluid px-4 px-md-5" style="padding-top: 32px; padding-bottom: 48px;">
  <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
    <div>
      <h1 class="h3 fw-bold mb-1" style="color:#1a202c;">Fleet Management</h1>
      <p class="text-muted mb-0" style="font-size: 14.5px;">Manage your vehicle inventory and monitor maintenance status.</p>
    </div>
    <a href="{{ route('staff.cars.create') }}" class="btn btn-hasta shadow-sm px-4">
      <i class="bi bi-plus-lg me-2"></i>Add New Vehicle
    </a>
  </div>

  @if(session('success'))
    <div class="alert alert-success border-0 shadow-sm d-flex align-items-center gap-2 mb-4">
      <i class="bi bi-check-circle-fill"></i>
      <div>{{ session('success') }}</div>
      <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
    </div>
  @endif

  {{-- FILTERS --}}
  <div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-3">
      <form method="GET" action="{{ route('staff.cars') }}" class="row g-3 align-items-end">
        <div class="col-md-5">
          <label class="form-label small fw-bold text-muted text-uppercase" style="font-size: 10px;">Search Inventory</label>
          <div class="input-group input-group-sm">
            <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
            <input type="text" name="search" class="form-control border-start-0 ps-0" placeholder="Brand, model, plate..." value="{{ $filters['search'] ?? '' }}">
          </div>
        </div>
        <div class="col-md-3">
          <label class="form-label small fw-bold text-muted text-uppercase" style="font-size: 10px;">Status Filter</label>
          <select name="status" class="form-select form-select-sm">
            <option value="">All statuses</option>
            <option value="available" {{ ($filters['status'] ?? '') === 'available' ? 'selected' : '' }}>Available</option>
            <option value="in_use" {{ ($filters['status'] ?? '') === 'in_use' ? 'selected' : '' }}>In Use</option>
            <option value="maintenance" {{ ($filters['status'] ?? '') === 'maintenance' ? 'selected' : '' }}>Maintenance</option>
          </select>
        </div>
        <div class="col-md-4">
          <div class="d-flex gap-2">
            <button type="submit" class="btn btn-sm btn-dark px-4 flex-fill">Apply Filters</button>
            <a href="{{ route('staff.cars') }}" class="btn btn-sm btn-light border px-4">Reset</a>
          </div>
        </div>
      </form>
    </div>
  </div>

  {{-- CARS GRID --}}
  <div class="row g-4">
    @forelse($cars as $car)
      <div class="col-md-6 col-lg-4">
        <div class="card border-0 shadow-sm h-100 car-admin-card overflow-hidden">
          {{-- Status Ribbon --}}
          <div class="position-relative">
            @if($car->image_url)
              <img src="{{ $car->image_url }}" alt="{{ $car->brand }} {{ $car->model }}" class="card-img-top" style="height: 180px; object-fit: cover;">
            @else
              <div class="bg-light d-flex align-items-center justify-content-center" style="height: 180px;">
                <i class="bi bi-image text-muted h1"></i>
              </div>
            @endif
            <div class="position-absolute top-0 end-0 m-3">
              <span class="badge rounded-pill shadow-sm px-3 py-2 
                {{ $car->status === 'available' ? 'bg-success' : ($car->status === 'in_use' ? 'bg-warning text-dark' : 'bg-danger') }}">
                {{ ucfirst($car->status) }}
              </span>
            </div>
          </div>
          
          <div class="card-body p-4">
            <div class="mb-3">
              <h5 class="fw-bold mb-1 text-dark">{{ $car->brand }} {{ $car->model }}</h5>
              <div class="d-flex align-items-center gap-2">
                <span class="text-muted small fw-medium"><i class="bi bi-calendar-event me-1"></i>{{ $car->year }}</span>
                <span class="text-muted small">•</span>
                <span class="badge bg-light text-dark border fw-bold">{{ strtoupper($car->plate_number) }}</span>
              </div>
            </div>

            <div class="row g-3 mb-4">
              <div class="col-6">
                <div class="small text-muted mb-1" style="font-size: 10px; font-weight: 700; text-transform: uppercase;">Hourly Rate</div>
                <div class="fw-bold text-dark">RM {{ number_format($car->base_rate_per_hour, 2) }}</div>
              </div>
              <div class="col-6">
                <div class="small text-muted mb-1" style="font-size: 10px; font-weight: 700; text-transform: uppercase;">Mileage</div>
                <div class="fw-bold text-dark">{{ number_format($car->current_mileage) }} km</div>
              </div>
            </div>

            {{-- Service Progress --}}
            @php
              $progress = ($car->service_mileage_limit > 0) ? ($car->current_mileage / $car->service_mileage_limit * 100) : 0;
              $isServiceDue = $car->current_mileage >= $car->service_mileage_limit;
            @endphp
            <div class="mb-4">
              <div class="d-flex justify-content-between align-items-center mb-1">
                <span class="small text-muted fw-semibold">Maintenance Status</span>
                <span class="small {{ $isServiceDue ? 'text-danger fw-bold' : 'text-muted' }}">
                  {{ number_format($car->service_mileage_limit - $car->current_mileage) }} km left
                </span>
              </div>
              <div class="progress" style="height: 6px;">
                <div class="progress-bar {{ $isServiceDue ? 'bg-danger' : ($progress > 80 ? 'bg-warning' : 'bg-success') }}" 
                     style="width: {{ min(100, $progress) }}%"></div>
              </div>
              @if($isServiceDue)
                <div class="mt-2 text-danger small fw-bold d-flex align-items-center gap-1">
                  <i class="bi bi-exclamation-triangle-fill"></i> Immediate Service Required
                </div>
              @endif
            </div>

            <div class="d-flex gap-2">
              <a href="{{ route('staff.cars.edit', $car->id) }}" class="btn btn-outline-primary flex-fill fw-semibold btn-sm">
                <i class="bi bi-pencil-square me-1"></i>Edit Details
              </a>
              <button type="button" class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteCarModal{{ $car->id }}">
                <i class="bi bi-trash"></i>
              </button>
            </div>
          </div>
        </div>
      </div>
      
      {{-- Delete Confirmation Modal --}}
      <div class="modal fade" id="deleteCarModal{{ $car->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content border-0 shadow">
            <div class="modal-header border-0 pb-0">
              <h5 class="fw-bold">Confirm Removal</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center py-4">
              <div class="bg-danger-subtle text-danger rounded-circle d-inline-flex p-3 mb-3">
                <i class="bi bi-trash h2 mb-0"></i>
              </div>
              <h5>Delete this vehicle?</h5>
              <p class="text-muted px-4">Are you sure you want to delete <strong>{{ $car->brand }} {{ $car->model }}</strong>? This action will also remove its booking history.</p>
              @if($car->bookings_count > 0)
                <div class="alert alert-warning small border-0 mx-3">
                  <i class="bi bi-info-circle me-1"></i> This car has <strong>{{ $car->bookings_count }}</strong> associated booking(s).
                </div>
              @endif
            </div>
            <div class="modal-footer border-0 pt-0 justify-content-center pb-4">
              <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Keep Car</button>
              <form action="{{ route('staff.cars.destroy', $car->id) }}" method="POST">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger px-4 shadow-sm">Delete Forever</button>
              </form>
            </div>
          </div>
        </div>
      </div>
    @empty
      <div class="col-12">
        <div class="card border-0 shadow-sm p-5 text-center bg-white">
          <div class="mb-3">
            <i class="bi bi-car-front text-muted display-4"></i>
          </div>
          <h5 class="fw-bold">No vehicles found</h5>
          <p class="text-muted mb-4">Your search did not match any vehicles in the inventory.</p>
          <a href="{{ route('staff.cars.create') }}" class="btn btn-hasta">Add First Vehicle</a>
        </div>
      </div>
    @endforelse
  </div>

  {{-- PAGINATION --}}
  <div class="mt-5 d-flex justify-content-center">
    {{ $cars->links() }}
  </div>
  </div>
</div>

<style>
  .car-admin-card { transition: all 0.3s ease; }
  .car-admin-card:hover { transform: translateY(-5px); box-shadow: 0 1rem 3rem rgba(0,0,0,.1) !important; }
  .progress { border-radius: 10px; background-color: #f1f5f9; }
  .badge { font-weight: 600; }
  .btn-hasta { background-color: var(--hasta); color: #fff; border: none; }
  .btn-hasta:hover { background-color: var(--hasta-darker); color: #fff; }
</style>
</div>
@endsection
