@extends('layouts.app')
@section('title', 'Manage Cars - Staff Dashboard')

@section('content')
<div class="d-flex" style="min-height: calc(100vh - 60px);">
  @include('staff._nav')
  <div class="flex-fill" style="background: var(--bg);">
    <div class="container-fluid px-4 px-md-5" style="padding-top: 32px; padding-bottom: 48px;">
  <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
    <div>
      <h1 class="h3 fw-bold mb-1" style="color:#333;">Car Fleet Management</h1>
      <p class="text-muted mb-0" style="font-size: 14px;">Add, edit and monitor all cars in the Hasta Travels &amp; Tours fleet.</p>
    </div>
    <a href="{{ route('staff.cars.create') }}" class="btn btn-hasta">+ Add New Car</a>
  </div>

  @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      {{ session('success') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  @endif

  @if($errors->any() && $errors->has('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
      <strong>Error:</strong> {{ $errors->first('error') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  @endif

  {{-- FILTERS --}}
  <div class="card border-0 shadow-soft mb-4">
    <div class="card-body p-3">
      <form method="GET" action="{{ route('staff.cars') }}" class="row g-2 align-items-end">
        <div class="col-md-4">
          <label class="form-label small">Search</label>
          <input type="text" name="search" class="form-control form-control-sm" placeholder="Brand, model, plate..." value="{{ $filters['search'] ?? '' }}">
        </div>
        <div class="col-md-3">
          <label class="form-label small">Status</label>
          <select name="status" class="form-select form-select-sm">
            <option value="">All statuses</option>
            <option value="available" {{ ($filters['status'] ?? '') === 'available' ? 'selected' : '' }}>Available</option>
            <option value="in_use" {{ ($filters['status'] ?? '') === 'in_use' ? 'selected' : '' }}>In Use</option>
            <option value="maintenance" {{ ($filters['status'] ?? '') === 'maintenance' ? 'selected' : '' }}>Maintenance</option>
          </select>
        </div>
        <div class="col-md-3">
          <button type="submit" class="btn btn-sm btn-hasta w-100">Filter</button>
        </div>
        <div class="col-md-2">
          <a href="{{ route('staff.cars') }}" class="btn btn-sm btn-outline-secondary w-100">Clear</a>
        </div>
      </form>
    </div>
  </div>

  {{-- CARS GRID --}}
  <div class="row g-3">
    @forelse($cars as $car)
      <div class="col-md-6 col-lg-4">
        <div class="card border-0 shadow-soft h-100">
          <div class="card-body p-3">
            <div class="d-flex justify-content-between align-items-start mb-2">
              <div>
                <h6 class="fw-bold mb-0">{{ $car->brand }} {{ $car->model }}</h6>
                <small class="text-muted">{{ $car->year }} • {{ strtoupper($car->plate_number) }}</small>
              </div>
              <span class="badge 
                {{ $car->status === 'available' ? 'bg-success' : ($car->status === 'in_use' ? 'bg-warning' : 'bg-danger') }}">
                {{ ucfirst($car->status) }}
              </span>
            </div>
            
            @if($car->image_url)
              <img src="{{ $car->image_url }}" alt="{{ $car->brand }} {{ $car->model }}" class="img-fluid rounded mb-2" style="height: 120px; width: 100%; object-fit: cover;">
            @endif

            <div class="small mb-2">
              <div class="d-flex justify-content-between">
                <span class="text-muted">Rate:</span>
                <strong>RM {{ number_format($car->base_rate_per_hour, 2) }}/hr</strong>
              </div>
              <div class="d-flex justify-content-between">
                <span class="text-muted">Mileage:</span>
                <strong>{{ number_format($car->current_mileage) }} km</strong>
              </div>
              <div class="d-flex justify-content-between">
                <span class="text-muted">Service limit:</span>
                <strong>{{ number_format($car->service_mileage_limit) }} km</strong>
              </div>
              @if($car->current_mileage >= $car->service_mileage_limit)
                <div class="alert alert-warning alert-sm py-1 px-2 mt-2 mb-0">
                  <small>⚠ Service needed</small>
                </div>
              @endif
            </div>

            <div class="d-flex gap-2">
              <a href="{{ route('staff.cars.edit', $car->id) }}" class="btn btn-sm btn-outline-primary flex-fill">Edit</a>
              <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteCarModal{{ $car->id }}">Delete</button>
            </div>
            
            {{-- Delete Confirmation Modal --}}
            <div class="modal fade" id="deleteCarModal{{ $car->id }}" tabindex="-1" aria-labelledby="deleteCarModalLabel{{ $car->id }}" aria-hidden="true">
              <div class="modal-dialog">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title" id="deleteCarModalLabel{{ $car->id }}">Confirm Delete Car</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <div class="modal-body">
                    <p>Are you sure you want to delete <strong>{{ $car->brand }} {{ $car->model }} ({{ strtoupper($car->plate_number) }})</strong>?</p>
                    @if($car->bookings_count > 0)
                      <div class="alert alert-warning">
                        <strong>Warning:</strong> This car has <strong>{{ $car->bookings_count }}</strong> associated booking(s) that will also be deleted.
                      </div>
                    @endif
                    <p class="text-danger mb-0"><strong>This action cannot be undone.</strong></p>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form action="{{ route('staff.cars.destroy', $car->id) }}" method="POST" class="d-inline">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="btn btn-danger">Delete Car</button>
                    </form>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    @empty
      <div class="col-12">
        <div class="text-center py-5">
          <p class="text-muted">No cars found. <a href="{{ route('staff.cars.create') }}">Add your first car</a></p>
        </div>
      </div>
    @endforelse
  </div>

  {{-- PAGINATION --}}
  <div class="mt-4">
    {{ $cars->links() }}
  </div>
  </div>
</div>
</div>
@endsection
