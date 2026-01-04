@extends('layouts.app')
@section('title', 'Vehicle Inspections - Staff Dashboard')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
<style>
  .inspection-card {
    transition: all 0.2s ease;
    border-radius: 16px;
    overflow: hidden;
  }
  .inspection-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 24px rgba(0,0,0,0.1) !important;
  }
  .inspection-card-img {
    height: 120px;
    object-fit: cover;
    width: 100%;
    background: #f0f0f0;
  }
  .type-badge {
    font-size: 10px;
    font-weight: 700;
    padding: 4px 10px;
    border-radius: 12px;
    text-transform: uppercase;
  }
  .type-before { background: #e3f2fd; color: #1565c0; }
  .type-after { background: #fff3e0; color: #e65100; }
  .status-badge {
    font-size: 10px;
    font-weight: 700;
    padding: 4px 10px;
    border-radius: 12px;
    text-transform: uppercase;
  }
  .status-pending { background: #fee2e2; color: #dc2626; }
  .status-in_progress { background: #dbeafe; color: #2563eb; }
  .status-completed { background: #d1fae5; color: #059669; }
  .stat-card {
    border-radius: 12px;
    transition: all 0.2s;
  }
  .stat-card:hover {
    transform: scale(1.02);
  }
</style>
@endpush

@section('content')
<div class="d-flex" style="min-height: calc(100vh - 60px);">
  @include('staff._nav')
  <div class="flex-fill" style="background: #f8fafc;">
    <div class="container-fluid px-4 px-md-5" style="padding-top: 32px; padding-bottom: 48px;">
      
      {{-- HEADER --}}
      <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
          <h1 class="h3 fw-bold mb-1" style="color:#1a202c;">
            <i class="bi bi-clipboard-check me-2 text-primary"></i>Vehicle Inspections
          </h1>
          <p class="text-muted mb-0" style="font-size: 14px;">
            Manage before-pickup and after-return vehicle inspections
          </p>
        </div>
      </div>

      {{-- SUCCESS MESSAGE --}}
      @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm d-flex align-items-center gap-2 mb-4">
          <i class="bi bi-check-circle-fill"></i>
          <div>{{ session('success') }}</div>
          <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
        </div>
      @endif

      {{-- STATISTICS CARDS --}}
      <div class="row g-3 mb-4">
        <div class="col-6 col-md-2">
          <div class="card border-0 shadow-sm stat-card h-100">
            <div class="card-body p-3 text-center">
              <div class="small text-muted text-uppercase fw-bold mb-1" style="font-size: 10px;">Total</div>
              <div class="h4 fw-bold mb-0">{{ $stats['total'] }}</div>
            </div>
          </div>
        </div>
        <div class="col-6 col-md-2">
          <div class="card border-0 shadow-sm stat-card h-100 border-start border-danger border-3">
            <div class="card-body p-3 text-center">
              <div class="small text-muted text-uppercase fw-bold mb-1" style="font-size: 10px;">Pending</div>
              <div class="h4 fw-bold mb-0 text-danger">{{ $stats['pending'] }}</div>
            </div>
          </div>
        </div>
        <div class="col-6 col-md-2">
          <div class="card border-0 shadow-sm stat-card h-100 border-start border-primary border-3">
            <div class="card-body p-3 text-center">
              <div class="small text-muted text-uppercase fw-bold mb-1" style="font-size: 10px;">In Progress</div>
              <div class="h4 fw-bold mb-0 text-primary">{{ $stats['in_progress'] }}</div>
            </div>
          </div>
        </div>
        <div class="col-6 col-md-2">
          <div class="card border-0 shadow-sm stat-card h-100 border-start border-success border-3">
            <div class="card-body p-3 text-center">
              <div class="small text-muted text-uppercase fw-bold mb-1" style="font-size: 10px;">Completed</div>
              <div class="h4 fw-bold mb-0 text-success">{{ $stats['completed'] }}</div>
            </div>
          </div>
        </div>
        <div class="col-6 col-md-2">
          <div class="card border-0 shadow-sm stat-card h-100 border-start border-info border-3">
            <div class="card-body p-3 text-center">
              <div class="small text-muted text-uppercase fw-bold mb-1" style="font-size: 10px;">Before Pickup</div>
              <div class="h4 fw-bold mb-0 text-info">{{ $stats['before'] }}</div>
            </div>
          </div>
        </div>
        <div class="col-6 col-md-2">
          <div class="card border-0 shadow-sm stat-card h-100 border-start border-warning border-3">
            <div class="card-body p-3 text-center">
              <div class="small text-muted text-uppercase fw-bold mb-1" style="font-size: 10px;">After Return</div>
              <div class="h4 fw-bold mb-0 text-warning">{{ $stats['after'] }}</div>
            </div>
          </div>
        </div>
      </div>

      {{-- FILTERS --}}
      <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-3">
          <form method="GET" action="{{ route('staff.inspections') }}" class="row g-2 align-items-end">
            <div class="col-md-2">
              <label class="form-label small fw-bold">Status</label>
              <select name="status" class="form-select form-select-sm">
                <option value="">All Statuses</option>
                <option value="pending" {{ ($filters['status'] ?? '') === 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="in_progress" {{ ($filters['status'] ?? '') === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                <option value="completed" {{ ($filters['status'] ?? '') === 'completed' ? 'selected' : '' }}>Completed</option>
              </select>
            </div>
            <div class="col-md-2">
              <label class="form-label small fw-bold">Type</label>
              <select name="type" class="form-select form-select-sm">
                <option value="">All Types</option>
                <option value="before" {{ ($filters['type'] ?? '') === 'before' ? 'selected' : '' }}>Before Pickup</option>
                <option value="after" {{ ($filters['type'] ?? '') === 'after' ? 'selected' : '' }}>After Return</option>
              </select>
            </div>
            <div class="col-md-2">
              <label class="form-label small fw-bold">Date</label>
              <input type="date" name="date" class="form-control form-control-sm" value="{{ $filters['date'] ?? '' }}">
            </div>
            <div class="col-md-4">
              <label class="form-label small fw-bold">Search</label>
              <input type="text" name="search" class="form-control form-control-sm" placeholder="Car, customer..." value="{{ $filters['search'] ?? '' }}">
            </div>
            <div class="col-md-2 d-flex gap-2">
              <button type="submit" class="btn btn-sm btn-hasta flex-fill">
                <i class="bi bi-search me-1"></i>Filter
              </button>
              <a href="{{ route('staff.inspections') }}" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-x-lg"></i>
              </a>
            </div>
          </form>
        </div>
      </div>

      {{-- INSPECTIONS GRID --}}
      <div class="row g-4">
        @forelse($inspections as $inspection)
          <div class="col-md-6 col-lg-4 col-xl-3">
            <div class="card border-0 shadow-sm inspection-card h-100">
              {{-- Car Image --}}
              <div class="position-relative">
                @if($inspection->car && $inspection->car->image_url)
                  <img src="{{ $inspection->car->image_url }}" alt="{{ $inspection->car->brand }} {{ $inspection->car->model }}" class="inspection-card-img">
                @else
                  <div class="inspection-card-img bg-light d-flex align-items-center justify-content-center">
                    <i class="bi bi-car-front text-muted h3 mb-0"></i>
                  </div>
                @endif
                <span class="type-badge type-{{ $inspection->type }} position-absolute" style="top: 10px; left: 10px;">
                  {{ $inspection->type === 'before' ? 'Before Pickup' : 'After Return' }}
                </span>
                <span class="status-badge status-{{ $inspection->status }} position-absolute" style="top: 10px; right: 10px;">
                  {{ ucfirst(str_replace('_', ' ', $inspection->status)) }}
                </span>
              </div>
              
              {{-- Card Body --}}
              <div class="card-body p-3">
                {{-- Car Info --}}
                @if($inspection->car)
                  <div class="d-flex align-items-center gap-2 mb-2">
                    <i class="bi bi-car-front text-muted"></i>
                    <span class="fw-bold text-dark">{{ $inspection->car->brand }} {{ $inspection->car->model }}</span>
                  </div>
                  <div class="small text-muted mb-2">
                    <i class="bi bi-credit-card me-1"></i>{{ $inspection->car->plate_number }}
                  </div>
                @endif

                {{-- Booking Info --}}
                @if($inspection->booking)
                  <div class="small text-muted mb-2">
                    <i class="bi bi-calendar-check me-1"></i>
                    Booking #{{ $inspection->booking_id }}
                  </div>
                  @if($inspection->booking->customer)
                    <div class="small text-muted mb-2">
                      <i class="bi bi-person me-1"></i>
                      {{ $inspection->booking->customer->full_name }}
                    </div>
                  @endif
                @endif

                {{-- Date Info --}}
                <div class="d-flex justify-content-between align-items-center small text-muted border-top pt-2 mt-2">
                  <div>
                    <i class="bi bi-clock me-1"></i>
                    {{ $inspection->created_at->format('d M Y') }}
                  </div>
                  @if($inspection->inspector)
                    <div>
                      <i class="bi bi-person-badge me-1"></i>
                      {{ Str::limit($inspection->inspector->name, 10) }}
                    </div>
                  @endif
                </div>
              </div>

              {{-- Card Footer Actions --}}
              <div class="card-footer bg-white border-0 p-3 pt-0">
                <div class="d-flex gap-2">
                  <a href="{{ route('staff.inspections.show', $inspection->inspection_id) }}" class="btn btn-sm btn-outline-primary flex-fill">
                    <i class="bi bi-eye me-1"></i>View
                  </a>
                  @if($inspection->status === 'pending')
                    <form method="POST" action="{{ route('staff.inspections.start', $inspection->inspection_id) }}" class="d-inline">
                      @csrf
                      <button type="submit" class="btn btn-sm btn-success">
                        <i class="bi bi-play-fill"></i> Start
                      </button>
                    </form>
                  @endif
                </div>
              </div>
            </div>
          </div>
        @empty
          <div class="col-12">
            <div class="card border-0 shadow-sm">
              <div class="card-body text-center py-5">
                <i class="bi bi-clipboard-check text-muted" style="font-size: 48px;"></i>
                <h5 class="mt-3 mb-2">No Inspections Found</h5>
                <p class="text-muted mb-0">
                  No inspections match your current filters.
                </p>
              </div>
            </div>
          </div>
        @endforelse
      </div>

      {{-- PAGINATION --}}
      @if($inspections->hasPages())
        <div class="mt-4 d-flex justify-content-center">
          {{ $inspections->appends($filters)->links() }}
        </div>
      @endif

    </div>
  </div>
</div>
@endsection
