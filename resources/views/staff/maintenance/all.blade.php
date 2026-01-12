@extends('layouts.app')
@section('title', 'Maintenance Log - Staff Dashboard')

@push('styles')
<style>
  :root {
    --slate-50: #f8fafc;
    --slate-100: #f1f5f9;
    --slate-200: #e2e8f0;
    --slate-300: #cbd5e1;
    --slate-400: #94a3b8;
    --slate-500: #64748b;
    --slate-600: #475569;
    --slate-700: #334155;
    --slate-800: #1e293b;
    --slate-900: #0f172a;
  }

  .maintenance-container {
    background: #f8fafc;
    min-height: calc(100vh - 70px);
  }

  /* Stats Cards */
  .stat-card {
    background: #fff;
    border: 1px solid var(--slate-200);
    border-radius: 10px;
    padding: 20px;
  }

  .stat-value {
    font-size: 24px;
    font-weight: 700;
    color: var(--slate-900);
  }

  .stat-label {
    font-size: 11px;
    font-weight: 600;
    color: var(--slate-500);
    text-transform: uppercase;
    letter-spacing: 0.5px;
  }

  /* Filter Card */
  .filter-card {
    background: #fff;
    border: 1px solid var(--slate-200);
    border-radius: 10px;
    padding: 16px 20px;
  }

  .filter-input {
    border: 1px solid var(--slate-200);
    border-radius: 6px;
    padding: 8px 12px;
    font-size: 13px;
    color: var(--slate-900);
    width: 100%;
  }

  .filter-input:focus {
    outline: none;
    border-color: var(--slate-400);
    box-shadow: 0 0 0 2px rgba(71, 85, 105, 0.1);
  }

  /* Table Card */
  .table-card {
    background: #fff;
    border: 1px solid var(--slate-200);
    border-radius: 10px;
    overflow: hidden;
  }

  .table-header {
    padding: 16px 20px;
    border-bottom: 1px solid var(--slate-200);
    background: var(--slate-50);
  }

  .table {
    margin: 0;
  }

  .table thead th {
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: var(--slate-500);
    padding: 12px 16px;
    border-bottom: 1px solid var(--slate-200);
    background: var(--slate-50);
  }

  .table tbody td {
    padding: 16px;
    border-bottom: 1px solid var(--slate-100);
    font-size: 13px;
    color: var(--slate-700);
    vertical-align: middle;
  }

  .table tbody tr:hover {
    background: var(--slate-50);
  }

  .table tbody tr:last-child td {
    border-bottom: none;
  }

  /* Car Badge */
  .car-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 6px 12px;
    background: var(--slate-100);
    border-radius: 6px;
    font-size: 12px;
    font-weight: 600;
    color: var(--slate-700);
  }

  .car-badge img {
    width: 32px;
    height: 32px;
    border-radius: 4px;
    object-fit: cover;
  }

  /* Action Buttons */
  .btn-action {
    padding: 6px 12px;
    font-size: 12px;
    font-weight: 500;
    border-radius: 6px;
    border: 1px solid var(--slate-200);
    background: #fff;
    color: var(--slate-600);
    transition: all 0.15s;
  }

  .btn-action:hover {
    background: var(--slate-50);
    color: var(--slate-900);
    border-color: var(--slate-300);
  }

  .btn-primary-custom {
    background: var(--slate-900);
    color: #fff;
    border-color: var(--slate-900);
  }

  .btn-primary-custom:hover {
    background: var(--slate-800);
    color: #fff;
  }

  /* Badge */
  .cost-badge {
    display: inline-block;
    padding: 4px 10px;
    background: #dcfce7;
    color: #166534;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
  }

  .mileage-badge {
    display: inline-block;
    padding: 4px 10px;
    background: var(--slate-100);
    color: var(--slate-700);
    border-radius: 6px;
    font-size: 12px;
    font-weight: 500;
  }

  /* Pagination */
  .pagination {
    gap: 4px;
  }

  .page-link {
    border-radius: 6px !important;
    padding: 8px 14px;
    font-size: 13px;
    font-weight: 500;
    color: var(--slate-600);
    border: 1px solid var(--slate-200);
  }

  .page-item.active .page-link {
    background: var(--hasta);
    border-color: var(--hasta);
  }

  .page-link:hover {
    background: var(--slate-50);
    color: var(--slate-900);
  }

  /* Empty State */
  .empty-state {
    text-align: center;
    padding: 60px 20px;
  }

  .empty-state i {
    font-size: 48px;
    color: var(--slate-300);
    margin-bottom: 16px;
  }

  @media (max-width: 768px) {
    .stat-value { font-size: 20px; }
  }
</style>
@endpush

@section('content')
<div class="d-flex maintenance-container">
  @include('staff._nav')
  <div class="flex-fill">
    <div class="container-fluid px-4 px-md-5 py-4">
      
      {{-- Page Header --}}
      <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
          <h1 class="h4 fw-bold mb-1" style="color: var(--slate-900);">Maintenance Log</h1>
          <p class="text-muted mb-0" style="font-size: 13px;">Service records for all vehicles in the fleet</p>
        </div>
        <div class="d-flex gap-2">
          <a href="{{ route('staff.maintenance-issues') }}" class="btn btn-action">
            <i class="bi bi-exclamation-triangle me-1"></i>Issues
          </a>
        </div>
      </div>

      @if(session('success'))
        <div class="alert alert-success border-0 d-flex align-items-center gap-2 mb-4" style="background: #dcfce7; color: #166534;">
          <i class="bi bi-check-circle-fill"></i>
          <span style="font-size: 13px; font-weight: 500;">{{ session('success') }}</span>
          <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" style="font-size: 10px;"></button>
        </div>
      @endif

      {{-- Stats --}}
      <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
          <div class="stat-card">
            <div class="stat-label mb-1">Total Records</div>
            <div class="stat-value">{{ number_format($stats['total_records']) }}</div>
          </div>
        </div>
        <div class="col-6 col-md-3">
          <div class="stat-card">
            <div class="stat-label mb-1">Total Expenditure</div>
            <div class="stat-value">RM{{ number_format($stats['total_cost'], 0) }}</div>
          </div>
        </div>
        <div class="col-6 col-md-3">
          <div class="stat-card">
            <div class="stat-label mb-1">This Month</div>
            <div class="stat-value">{{ $stats['this_month'] }}</div>
          </div>
        </div>
        <div class="col-6 col-md-3">
          <div class="stat-card">
            <div class="stat-label mb-1">This Month Cost</div>
            <div class="stat-value">RM{{ number_format($stats['this_month_cost'], 0) }}</div>
          </div>
        </div>
      </div>

      {{-- Filters --}}
      <div class="filter-card mb-4">
        <form method="GET" action="{{ route('staff.maintenance') }}" class="row g-3 align-items-end">
          <div class="col-md-3">
            <label class="form-label small fw-semibold text-muted">Vehicle</label>
            <select name="car_id" class="filter-input">
              <option value="">All Vehicles</option>
              @foreach($cars as $car)
                <option value="{{ $car->id }}" {{ ($filters['car_id'] ?? '') == $car->id ? 'selected' : '' }}>
                  {{ $car->brand }} {{ $car->model }} ({{ strtoupper($car->plate_number) }})
                </option>
              @endforeach
            </select>
          </div>
          <div class="col-md-2">
            <label class="form-label small fw-semibold text-muted">From Date</label>
            <input type="date" name="start_date" class="filter-input" value="{{ $filters['start_date'] ?? '' }}">
          </div>
          <div class="col-md-2">
            <label class="form-label small fw-semibold text-muted">To Date</label>
            <input type="date" name="end_date" class="filter-input" value="{{ $filters['end_date'] ?? '' }}">
          </div>
          <div class="col-md-3">
            <label class="form-label small fw-semibold text-muted">Search</label>
            <input type="text" name="search" class="filter-input" placeholder="Search description, car..." value="{{ $filters['search'] ?? '' }}">
          </div>
          <div class="col-md-2 d-flex gap-2">
            <button type="submit" class="btn btn-action btn-primary-custom flex-fill">Filter</button>
            <a href="{{ route('staff.maintenance') }}" class="btn btn-action px-3"><i class="bi bi-arrow-clockwise"></i></a>
          </div>
        </form>
      </div>

      {{-- Records Table --}}
      <div class="table-card">
        <div class="table-header d-flex justify-content-between align-items-center">
          <h6 class="fw-bold mb-0" style="color: var(--slate-900);">Service Records</h6>
          <span class="badge" style="background: var(--slate-100); color: var(--slate-600); font-size: 12px; padding: 6px 12px;">
            {{ $records->total() }} records
          </span>
        </div>
        <div class="table-responsive">
          <table class="table">
            <thead>
              <tr>
                <th>Date</th>
                <th>Vehicle</th>
                <th>Description</th>
                <th>Mileage</th>
                <th>Cost</th>
                <th class="text-end">Actions</th>
              </tr>
            </thead>
            <tbody>
              @forelse($records as $record)
                <tr>
                  <td>
                    <div class="fw-semibold" style="color: var(--slate-900);">{{ $record->service_date->format('d M Y') }}</div>
                    <div class="text-muted" style="font-size: 11px;">{{ $record->service_date->diffForHumans() }}</div>
                  </td>
                  <td>
                    @if($record->car)
                      <a href="{{ route('staff.maintenance.index', $record->car->id) }}" class="car-badge text-decoration-none">
                        @if($record->car->images && count($record->car->images) > 0)
                          <img src="{{ asset('storage/' . $record->car->images[0]) }}" alt="{{ $record->car->brand }}">
                        @else
                          <div style="width: 32px; height: 32px; background: var(--slate-200); border-radius: 4px; display: flex; align-items: center; justify-content: center;">
                            <i class="bi bi-car-front text-muted" style="font-size: 14px;"></i>
                          </div>
                        @endif
                        <div>
                          <div>{{ $record->car->brand }} {{ $record->car->model }}</div>
                          <div style="font-size: 10px; color: var(--slate-500);">{{ strtoupper($record->car->plate_number) }}</div>
                        </div>
                      </a>
                    @else
                      <span class="text-muted">Vehicle removed</span>
                    @endif
                  </td>
                  <td>
                    <div style="max-width: 300px;">
                      {{ \Illuminate\Support\Str::limit($record->description ?: 'Routine maintenance', 80) }}
                    </div>
                  </td>
                  <td>
                    <span class="mileage-badge">{{ number_format($record->mileage_at_service) }} km</span>
                  </td>
                  <td>
                    @if($record->cost > 0)
                      <span class="cost-badge">RM{{ number_format($record->cost, 2) }}</span>
                    @else
                      <span class="text-muted">—</span>
                    @endif
                  </td>
                  <td class="text-end">
                    <div class="d-flex gap-2 justify-content-end">
                      @if($record->car)
                        <a href="{{ route('staff.maintenance.edit', [$record->car->id, $record->maintenance_id]) }}" class="btn btn-action" title="Edit">
                          <i class="bi bi-pencil"></i>
                        </a>
                        <form method="POST" action="{{ route('staff.maintenance.destroy', [$record->car->id, $record->maintenance_id]) }}" class="d-inline" onsubmit="return confirm('Delete this service record?');">
                          @csrf
                          @method('DELETE')
                          <button type="submit" class="btn btn-action text-danger" title="Delete">
                            <i class="bi bi-trash"></i>
                          </button>
                        </form>
                      @endif
                    </div>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="6">
                    <div class="empty-state">
                      <i class="bi bi-wrench-adjustable d-block"></i>
                      <h6 class="fw-bold" style="color: var(--slate-700);">No maintenance records</h6>
                      <p class="text-muted small mb-3">No service records found matching your filters.</p>
                      <a href="{{ route('staff.cars') }}" class="btn btn-action btn-primary-custom">
                        <i class="bi bi-car-front me-1"></i>Go to Fleet
                      </a>
                    </div>
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>

      {{-- Pagination --}}
      @if($records->hasPages())
        <div class="mt-4 d-flex justify-content-center">
          {{ $records->links() }}
        </div>
      @endif

    </div>
  </div>
</div>
@endsection
