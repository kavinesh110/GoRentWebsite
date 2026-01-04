@extends('layouts.app')
@section('title', 'Maintenance Issues - Staff Dashboard')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
<style>
  .issue-card {
    transition: all 0.2s ease;
    border-radius: 16px;
    overflow: hidden;
  }
  .issue-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 24px rgba(0,0,0,0.1) !important;
  }
  .issue-card-img {
    height: 140px;
    object-fit: cover;
    width: 100%;
    background: #f0f0f0;
  }
  .category-badge {
    font-size: 11px;
    font-weight: 600;
    padding: 6px 12px;
    border-radius: 20px;
  }
  .category-cleanliness { background: #e3f2fd; color: #1565c0; }
  .category-lacking_facility { background: #fff3e0; color: #e65100; }
  .category-bluetooth { background: #f3e5f5; color: #7b1fa2; }
  .category-engine { background: #ffebee; color: #c62828; }
  .category-others { background: #e8f5e9; color: #2e7d32; }
  .status-badge {
    font-size: 10px;
    font-weight: 700;
    padding: 4px 10px;
    border-radius: 12px;
    text-transform: uppercase;
  }
  .status-open { background: #fee2e2; color: #dc2626; }
  .status-in_progress { background: #dbeafe; color: #2563eb; }
  .status-resolved { background: #d1fae5; color: #059669; }
  .status-closed { background: #e5e7eb; color: #6b7280; }
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
            <i class="bi bi-tools me-2 text-warning"></i>Maintenance Issues
          </h1>
          <p class="text-muted mb-0" style="font-size: 14px;">
            Support tickets flagged for maintenance attention
          </p>
        </div>
        <a href="{{ route('staff.support-tickets') }}" class="btn btn-outline-primary">
          <i class="bi bi-headset me-2"></i>View All Support Tickets
        </a>
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
        <div class="col-6 col-md-3">
          <div class="card border-0 shadow-sm stat-card h-100">
            <div class="card-body p-3 text-center">
              <div class="small text-muted text-uppercase fw-bold mb-1" style="font-size: 10px;">Total Issues</div>
              <div class="h3 fw-bold mb-0">{{ $stats['total'] }}</div>
            </div>
          </div>
        </div>
        <div class="col-6 col-md-3">
          <div class="card border-0 shadow-sm stat-card h-100 border-start border-danger border-3">
            <div class="card-body p-3 text-center">
              <div class="small text-muted text-uppercase fw-bold mb-1" style="font-size: 10px;">Open</div>
              <div class="h3 fw-bold mb-0 text-danger">{{ $stats['open'] }}</div>
            </div>
          </div>
        </div>
        <div class="col-6 col-md-3">
          <div class="card border-0 shadow-sm stat-card h-100 border-start border-primary border-3">
            <div class="card-body p-3 text-center">
              <div class="small text-muted text-uppercase fw-bold mb-1" style="font-size: 10px;">In Progress</div>
              <div class="h3 fw-bold mb-0 text-primary">{{ $stats['in_progress'] }}</div>
            </div>
          </div>
        </div>
        <div class="col-6 col-md-3">
          <div class="card border-0 shadow-sm stat-card h-100 border-start border-success border-3">
            <div class="card-body p-3 text-center">
              <div class="small text-muted text-uppercase fw-bold mb-1" style="font-size: 10px;">Resolved</div>
              <div class="h3 fw-bold mb-0 text-success">{{ $stats['resolved'] }}</div>
            </div>
          </div>
        </div>
      </div>

      {{-- FILTERS --}}
      <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-3">
          <form method="GET" action="{{ route('staff.maintenance-issues') }}" class="row g-2 align-items-end">
            <div class="col-md-2">
              <label class="form-label small fw-bold">Status</label>
              <select name="status" class="form-select form-select-sm">
                <option value="">Open & In Progress</option>
                <option value="open" {{ ($filters['status'] ?? '') === 'open' ? 'selected' : '' }}>Open Only</option>
                <option value="in_progress" {{ ($filters['status'] ?? '') === 'in_progress' ? 'selected' : '' }}>In Progress Only</option>
                <option value="resolved" {{ ($filters['status'] ?? '') === 'resolved' ? 'selected' : '' }}>Resolved</option>
                <option value="closed" {{ ($filters['status'] ?? '') === 'closed' ? 'selected' : '' }}>Closed</option>
              </select>
            </div>
            <div class="col-md-2">
              <label class="form-label small fw-bold">Category</label>
              <select name="category" class="form-select form-select-sm">
                <option value="">All Categories</option>
                <option value="cleanliness" {{ ($filters['category'] ?? '') === 'cleanliness' ? 'selected' : '' }}>Cleanliness</option>
                <option value="lacking_facility" {{ ($filters['category'] ?? '') === 'lacking_facility' ? 'selected' : '' }}>Lacking Facility</option>
                <option value="bluetooth" {{ ($filters['category'] ?? '') === 'bluetooth' ? 'selected' : '' }}>Bluetooth</option>
                <option value="engine" {{ ($filters['category'] ?? '') === 'engine' ? 'selected' : '' }}>Engine</option>
                <option value="others" {{ ($filters['category'] ?? '') === 'others' ? 'selected' : '' }}>Others</option>
              </select>
            </div>
            <div class="col-md-3">
              <label class="form-label small fw-bold">Vehicle</label>
              <select name="car_id" class="form-select form-select-sm">
                <option value="">All Vehicles</option>
                @foreach($cars as $car)
                  <option value="{{ $car->id }}" {{ ($filters['car_id'] ?? '') == $car->id ? 'selected' : '' }}>
                    {{ $car->brand }} {{ $car->model }} ({{ $car->plate_number }})
                  </option>
                @endforeach
              </select>
            </div>
            <div class="col-md-3">
              <label class="form-label small fw-bold">Search</label>
              <input type="text" name="search" class="form-control form-control-sm" placeholder="Subject, description, car..." value="{{ $filters['search'] ?? '' }}">
            </div>
            <div class="col-md-2 d-flex gap-2">
              <button type="submit" class="btn btn-sm btn-hasta flex-fill">
                <i class="bi bi-search me-1"></i>Filter
              </button>
              <a href="{{ route('staff.maintenance-issues') }}" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-x-lg"></i>
              </a>
            </div>
          </form>
        </div>
      </div>

      {{-- ISSUES GRID --}}
      <div class="row g-4">
        @forelse($issues as $issue)
          @php
            // Determine which car to show
            $car = $issue->car ?? ($issue->booking ? $issue->booking->car : null);
            $carImage = $car && $car->images && count($car->images) > 0 
                ? asset('storage/' . $car->images[0]) 
                : asset('images/car-placeholder.png');
          @endphp
          <div class="col-md-6 col-lg-4 col-xl-3">
            <div class="card border-0 shadow-sm issue-card h-100">
              {{-- Car Image --}}
              <div class="position-relative">
                <img src="{{ $carImage }}" alt="{{ $car->brand ?? 'Car' }} {{ $car->model ?? '' }}" class="issue-card-img" onerror="this.src='{{ asset('images/car-placeholder.png') }}'">
                <span class="status-badge status-{{ $issue->status }} position-absolute" style="top: 10px; right: 10px;">
                  {{ ucfirst(str_replace('_', ' ', $issue->status)) }}
                </span>
              </div>
              
              {{-- Card Body --}}
              <div class="card-body p-3">
                {{-- Car Info --}}
                @if($car)
                  <div class="d-flex align-items-center gap-2 mb-2">
                    <i class="bi bi-car-front text-muted"></i>
                    <span class="fw-bold text-dark">{{ $car->brand }} {{ $car->model }}</span>
                  </div>
                  <div class="small text-muted mb-2">
                    <i class="bi bi-credit-card me-1"></i>{{ $car->plate_number }}
                  </div>
                @else
                  <div class="small text-muted mb-2">
                    <i class="bi bi-car-front me-1"></i>No vehicle linked
                  </div>
                @endif

                {{-- Category Badge --}}
                <div class="mb-2">
                  <span class="category-badge category-{{ $issue->category }}">
                    <i class="bi bi-tag me-1"></i>{{ ucfirst(str_replace('_', ' ', $issue->category)) }}
                  </span>
                </div>

                {{-- Subject --}}
                <h6 class="fw-bold mb-2 text-dark" style="font-size: 14px;">
                  {{ \Illuminate\Support\Str::limit($issue->subject, 40) }}
                </h6>

                {{-- Description Preview --}}
                <p class="small text-muted mb-3" style="font-size: 12px; line-height: 1.5;">
                  {{ \Illuminate\Support\Str::limit($issue->description, 80) }}
                </p>

                {{-- Meta Info --}}
                <div class="d-flex justify-content-between align-items-center small text-muted border-top pt-2">
                  <div>
                    <i class="bi bi-person me-1"></i>{{ \Illuminate\Support\Str::limit($issue->name, 15) }}
                  </div>
                  <div>
                    <i class="bi bi-calendar me-1"></i>{{ $issue->created_at->format('d M Y') }}
                  </div>
                </div>
              </div>

              {{-- Card Footer Actions --}}
              <div class="card-footer bg-white border-0 p-3 pt-0">
                <div class="d-flex gap-2">
                  <a href="{{ route('staff.support-tickets.show', $issue->ticket_id) }}" class="btn btn-sm btn-outline-primary flex-fill">
                    <i class="bi bi-eye me-1"></i>View Details
                  </a>
                  @if($issue->status !== 'resolved' && $issue->status !== 'closed')
                    <form method="POST" action="{{ route('staff.maintenance-issues.resolve', $issue->ticket_id) }}" class="d-inline">
                      @csrf
                      <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Mark this issue as resolved?')">
                        <i class="bi bi-check-lg"></i>
                      </button>
                    </form>
                  @endif
                  @if($car)
                    <a href="{{ route('staff.maintenance.index', $car->id) }}" class="btn btn-sm btn-outline-warning" title="View Maintenance Records">
                      <i class="bi bi-wrench"></i>
                    </a>
                  @endif
                </div>
              </div>
            </div>
          </div>
        @empty
          <div class="col-12">
            <div class="card border-0 shadow-sm">
              <div class="card-body text-center py-5">
                <i class="bi bi-check-circle text-success" style="font-size: 48px;"></i>
                <h5 class="mt-3 mb-2">No Maintenance Issues</h5>
                <p class="text-muted mb-3">
                  @if(empty($filters['status']) || $filters['status'] === '')
                    No support tickets have been flagged for maintenance yet.
                  @else
                    No issues match your current filters.
                  @endif
                </p>
                <a href="{{ route('staff.support-tickets') }}" class="btn btn-outline-primary btn-sm">
                  <i class="bi bi-headset me-1"></i>View Support Tickets
                </a>
              </div>
            </div>
          </div>
        @endforelse
      </div>

      {{-- PAGINATION --}}
      @if($issues->hasPages())
        <div class="mt-4 d-flex justify-content-center">
          {{ $issues->appends($filters)->links() }}
        </div>
      @endif

    </div>
  </div>
</div>
@endsection
