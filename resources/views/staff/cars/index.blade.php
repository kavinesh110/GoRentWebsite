@extends('layouts.app')
@section('title', 'Manage Cars - Staff Dashboard')

@push('styles')
<style>
  .text-slate-700 { color: #334155; }
  .text-slate-800 { color: #1e293b; }
  .bg-light-subtle { background-color: #f8fafc !important; }
  
  .car-admin-card {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    border: 1px solid #f1f5f9 !important;
  }
  .car-admin-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1), 0 10px 10px -5px rgba(0,0,0,0.04) !important;
  }
  
  .progress { background-color: #f1f5f9; }
  .badge { font-weight: 700; letter-spacing: 0.3px; }
  
  .btn-warning-subtle {
    background-color: #fffbeb;
    color: #92400e;
    transition: all 0.2s;
  }
  .btn-warning-subtle:hover {
    background-color: #fef3c7;
    transform: scale(1.01);
  }

  /* Custom Pagination Styles */
  .pagination { 
    margin-bottom: 0; 
    gap: 5px;
  }
  .page-item .page-link { 
    border-radius: 10px !important; 
    padding: 8px 16px;
    color: #475569;
    border: 1px solid #e2e8f0;
    font-weight: 600;
    font-size: 13px;
    transition: all 0.2s;
    box-shadow: 0 2px 4px rgba(0,0,0,0.02);
  }
  .page-item:first-child .page-link, .page-item:last-child .page-link {
    border-radius: 10px !important;
  }
  .page-item.active .page-link { 
    background-color: var(--hasta); 
    border-color: var(--hasta); 
    color: #fff; 
    box-shadow: 0 4px 6px -1px rgba(203, 55, 55, 0.2);
  }
  .page-item .page-link:hover:not(.active) {
    background-color: #f8fafc;
    border-color: #cbd5e1;
    color: var(--hasta);
    transform: translateY(-1px);
  }
  .page-item.disabled .page-link {
    background-color: #f1f5f9;
    color: #94a3b8;
    border-color: #e2e8f0;
  }
</style>
@endpush

@section('content')
<div class="d-flex" style="min-height: calc(100vh - 70px);">
  @include('staff._nav')
  <div class="flex-fill" style="background: #f8fafc;">
    <div class="container-fluid px-4 px-md-5 py-4">
      
      {{-- Page Header --}}
      <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
          <h1 class="h4 fw-bold mb-1" style="color:#1e293b; letter-spacing: -0.5px;">Fleet Management</h1>
          <p class="text-muted mb-0 small">Monitor vehicle availability, maintenance schedules, and performance.</p>
        </div>
        <div class="d-flex gap-2">
          <a href="{{ route('staff.cars.create') }}" class="btn btn-hasta shadow-sm px-4 rounded-3 fw-bold">
            <i class="bi bi-plus-lg me-2"></i>Register New Vehicle
          </a>
        </div>
      </div>

      @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm d-flex align-items-center gap-2 mb-4 rounded-3">
          <i class="bi bi-check-circle-fill"></i>
          <div class="small fw-semibold">{{ session('success') }}</div>
          <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
        </div>
      @endif

      {{-- FLEET OVERVIEW --}}
      <div class="card border-0 shadow-sm mb-4 rounded-4 overflow-hidden">
        <div class="card-header bg-white border-bottom py-3 px-4">
          <h6 class="fw-bold mb-0">Fleet Overview</h6>
        </div>
        <div class="card-body p-4">
          <div class="row g-3">
            <div class="col-md-3">
              <div class="text-center p-3 rounded" style="background: #d1fae5;">
                <div style="font-size: 32px; font-weight: 700; color: #059669;">{{ $fleetStats['available'] }}</div>
                <div style="font-size: 12px; color: #065f46; font-weight: 600;">Available</div>
              </div>
            </div>
            <div class="col-md-3">
              <div class="text-center p-3 rounded" style="background: #dbeafe;">
                <div style="font-size: 32px; font-weight: 700; color: #2563eb;">{{ $fleetStats['in_use'] }}</div>
                <div style="font-size: 12px; color: #1e40af; font-weight: 600;">In Use</div>
              </div>
            </div>
            <div class="col-md-3">
              <div class="text-center p-3 rounded" style="background: #fef3c7;">
                <div style="font-size: 32px; font-weight: 700; color: #d97706;">{{ $fleetStats['maintenance'] }}</div>
                <div style="font-size: 12px; color: #92400e; font-weight: 600;">Maintenance</div>
              </div>
            </div>
            <div class="col-md-3">
              <div class="text-center p-3 rounded" style="background: #e2e8f0;">
                <div style="font-size: 32px; font-weight: 700; color: #475569;">{{ $fleetStats['total'] }}</div>
                <div style="font-size: 12px; color: #64748b; font-weight: 600;">Total Fleet</div>
              </div>
            </div>
          </div>
        </div>
      </div>

      {{-- SEARCH & FILTERS --}}
      <div class="card border-0 shadow-sm mb-4 rounded-4 overflow-hidden">
        <div class="card-body p-3 bg-white">
          <form method="GET" action="{{ route('staff.cars') }}" class="row g-2 align-items-center">
            <div class="col-lg-5">
              <div class="input-group input-group-sm border rounded-3 overflow-hidden bg-light-subtle">
                <span class="input-group-text bg-transparent border-0 ps-3"><i class="bi bi-search text-muted"></i></span>
                <input type="text" name="search" class="form-control border-0 bg-transparent py-2 px-2" placeholder="Search by brand, model, or plate number..." value="{{ $filters['search'] ?? '' }}">
              </div>
            </div>
            <div class="col-lg-2">
              <select name="status" class="form-select form-select-sm border rounded-3 py-2 bg-light-subtle">
                <option value="">All Fleet Statuses</option>
                <option value="available" {{ ($filters['status'] ?? '') === 'available' ? 'selected' : '' }}>Available & Ready</option>
                <option value="in_use" {{ ($filters['status'] ?? '') === 'in_use' ? 'selected' : '' }}>Currently In Use</option>
                <option value="maintenance" {{ ($filters['status'] ?? '') === 'maintenance' ? 'selected' : '' }}>Under Maintenance</option>
              </select>
            </div>
            <div class="col-lg-2">
              <select name="car_type" class="form-select form-select-sm border rounded-3 py-2 bg-light-subtle">
                <option value="">All Car Types</option>
                @php
                  $carTypeLabels = [
                    'hatchback' => 'Hatchback (Budget)',
                    'sedan' => 'Sedan (Comfort)',
                    'suv' => 'SUV (Family)',
                    'mpv' => 'MPV (Group)'
                  ];
                @endphp
                @foreach($availableCarTypes as $type)
                  <option value="{{ $type }}" {{ ($filters['car_type'] ?? '') === $type ? 'selected' : '' }}>
                    {{ $carTypeLabels[$type] ?? ucfirst($type) }}
                  </option>
                @endforeach
              </select>
            </div>
            <div class="col-lg-3 d-flex gap-2">
              <button type="submit" class="btn btn-sm btn-dark px-4 flex-fill rounded-3 py-2 fw-bold">Apply Filters</button>
              <a href="{{ route('staff.cars') }}" class="btn btn-sm btn-light border px-3 rounded-3 py-2 fw-semibold text-muted">Reset</a>
            </div>
          </form>
        </div>
      </div>

      {{-- FLEET GRID --}}
      <div class="row g-4">
        @forelse($cars as $car)
          <div class="col-md-6 col-xl-4">
            <div class="card border-0 shadow-sm h-100 car-admin-card rounded-4 overflow-hidden bg-white">
              {{-- Vehicle Image & Status Badge --}}
              <div class="position-relative">
                @if($car->image_url)
                  <img src="{{ $car->image_url }}" alt="{{ $car->brand }} {{ $car->model }}" class="card-img-top" style="height: 200px; object-fit: cover;">
                @else
                  <div class="bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                    <i class="bi bi-image text-muted opacity-25" style="font-size: 3rem;"></i>
                  </div>
                @endif
                <div class="position-absolute top-0 end-0 m-3">
                  @php
                    $statusBadgeClass = match($car->status) {
                      'available' => 'bg-success',
                      'in_use' => 'bg-warning text-dark',
                      'maintenance' => 'bg-danger',
                      default => 'bg-secondary'
                    };
                  @endphp
                  <span class="badge rounded-pill shadow-sm px-3 py-2 fw-bold text-uppercase {{ $statusBadgeClass }}" style="font-size: 10px; letter-spacing: 0.5px;">
                    {{ ucfirst($car->status) }}
                  </span>
                </div>
              </div>
              
              <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-start mb-3">
                  <div>
                    <h5 class="fw-bold mb-1 text-slate-800" style="letter-spacing: -0.3px;">{{ $car->brand }} {{ $car->model }}</h5>
                    <div class="d-flex align-items-center gap-2">
                      <span class="badge bg-light text-slate-700 border border-slate-200 fw-bold px-2 py-1" style="font-size: 11px;">{{ strtoupper($car->plate_number) }}</span>
                      <span class="text-muted small fw-medium">{{ $car->year }}</span>
                    </div>
                  </div>
                  <div class="text-end">
                    <div class="small text-muted fw-bold text-uppercase" style="font-size: 9px;">Rate/Hour</div>
                    <div class="fw-bold text-primary">RM{{ number_format($car->base_rate_per_hour, 2) }}</div>
                  </div>
                </div>

                {{-- Key Stats Row --}}
                <div class="row g-2 mb-4 bg-light rounded-3 p-2">
                  <div class="col-12">
                    <div class="p-2">
                      <div class="small text-muted mb-0" style="font-size: 9px; font-weight: 700; text-transform: uppercase;">Mileage</div>
                      <div class="fw-bold text-slate-700">{{ number_format($car->current_mileage) }} km</div>
                    </div>
                  </div>
                </div>

                {{-- Maintenance Status --}}
                @php
                  $mileageLimit = $car->service_mileage_limit ?: 5000;
                  
                  // Get last service mileage (from maintenance records) or use initial mileage
                  $lastServiceMileage = $lastServiceMileages[$car->id] ?? ($car->initial_mileage ?? 0);
                  
                  // Calculate distance since last service
                  $distanceSinceService = $car->current_mileage - $lastServiceMileage;
                  
                  // Calculate progress percentage (0-100%)
                  $progress = ($mileageLimit > 0) ? min(100, ($distanceSinceService / $mileageLimit) * 100) : 0;
                  
                  // Check if service is due
                  $isServiceDue = $distanceSinceService >= $mileageLimit;
                  
                  // Calculate remaining km to next service
                  $remainingKm = max(0, $mileageLimit - $distanceSinceService);
                  
                  // If overdue, calculate how much past the service point
                  $overdueKm = $isServiceDue ? ($distanceSinceService - $mileageLimit) : 0;
                @endphp
                <div class="mb-4">
                  <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="small fw-bold text-slate-700" style="font-size: 11px;">Service Progress</span>
                    <span class="small {{ $isServiceDue ? 'text-danger fw-bold' : 'text-muted fw-medium' }}" style="font-size: 11px;">
                      @if($isServiceDue)
                        Service due ({{ number_format($overdueKm) }} km past interval)
                      @else
                        {{ number_format($remainingKm) }} km to next service
                      @endif
                    </span>
                  </div>
                  <div class="progress" style="height: 8px; border-radius: 10px; background: #f1f5f9;">
                    <div class="progress-bar {{ $isServiceDue ? 'bg-danger' : ($progress > 85 ? 'bg-warning' : 'bg-success') }} dynamic-width" 
                         data-width="{{ min(100, $progress) }}%" style="border-radius: 10px;"></div>
                  </div>
                </div>

                {{-- Notifications & Actions --}}
                <div class="d-grid gap-2">
                  @php $openIssuesCount = $issuesCounts[$car->id] ?? 0; @endphp
                  @if($openIssuesCount > 0)
                    <a href="{{ route('staff.maintenance-issues', ['car_id' => $car->id]) }}" class="btn btn-warning-subtle btn-sm text-warning-emphasis d-flex align-items-center justify-content-center gap-2 border-0 rounded-3 py-2 fw-bold text-decoration-none">
                      <i class="bi bi-exclamation-triangle-fill"></i>
                      <span>{{ $openIssuesCount }} Reported Issue{{ $openIssuesCount > 1 ? 's' : '' }}</span>
                    </a>
                  @endif

                  <div class="d-flex gap-2">
                    <a href="{{ route('staff.cars.edit', $car->id) }}" class="btn btn-primary flex-fill fw-bold btn-sm py-2 rounded-3">
                      <i class="bi bi-pencil-square me-1"></i>Edit Profile
                    </a>
                    <a href="{{ route('staff.maintenance.index', $car->id) }}" class="btn btn-outline-secondary btn-sm px-3 rounded-3" title="Service Logs">
                      <i class="bi bi-wrench-adjustable"></i>
                    </a>
                    <button type="button" class="btn btn-outline-danger btn-sm px-3 rounded-3" data-bs-toggle="modal" data-bs-target="#deleteCarModal{{ $car->id }}">
                      <i class="bi bi-trash3"></i>
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
          
          {{-- Modern Delete Confirmation Modal --}}
          <div class="modal fade" id="deleteCarModal{{ $car->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
              <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-body text-center p-5">
                  <div class="bg-danger-subtle text-danger rounded-circle d-inline-flex p-4 mb-4">
                    <i class="bi bi-trash3 h1 mb-0"></i>
                  </div>
                  <h4 class="fw-bold text-slate-800 mb-2">Confirm Removal</h4>
                  <p class="text-muted mb-4">Are you sure you want to remove <strong>{{ $car->brand }} {{ $car->model }}</strong> from the fleet? This action cannot be undone if the vehicle has active records.</p>
                  
                  @if($car->bookings_count > 0)
                    <div class="p-3 bg-warning-subtle text-warning-emphasis rounded-3 small mb-4 text-start">
                      <i class="bi bi-info-circle-fill me-2"></i> This car is linked to <strong>{{ $car->bookings_count }}</strong> booking history records.
                    </div>
                  @endif

                  <div class="d-grid gap-2">
                    <form action="{{ route('staff.cars.destroy', $car->id) }}" method="POST">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="btn btn-danger w-100 py-3 rounded-3 fw-bold shadow-sm mb-2">Yes, Remove Vehicle</button>
                    </form>
                    <button type="button" class="btn btn-light w-100 py-3 rounded-3 fw-semibold border" data-bs-dismiss="modal">Keep in Fleet</button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        @empty
          <div class="col-12">
            <div class="card border-0 shadow-sm p-5 text-center bg-white rounded-4">
              <div class="mb-4">
                <div class="bg-light rounded-circle d-inline-flex p-4">
                  <i class="bi bi-car-front text-muted h1 mb-0 opacity-25"></i>
                </div>
              </div>
              <h5 class="fw-bold text-slate-800">No vehicles matching your criteria</h5>
              <p class="text-muted mb-4">Try adjusting your filters or search terms.</p>
              <div class="d-flex justify-content-center gap-2">
                <a href="{{ route('staff.cars') }}" class="btn btn-outline-secondary rounded-3 px-4 fw-semibold">Clear All Filters</a>
                <a href="{{ route('staff.cars.create') }}" class="btn btn-hasta rounded-3 px-4 fw-bold shadow-sm">Add New Vehicle</a>
              </div>
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
</div>
@endsection

@push('scripts')
<script>
document.querySelectorAll('.dynamic-width').forEach(el => {
  el.style.width = el.dataset.width;
});
</script>
@endpush
