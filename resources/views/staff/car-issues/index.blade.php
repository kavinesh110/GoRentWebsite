@extends('layouts.app')
@section('title', 'Maintenance Issues - Staff Dashboard')

@push('styles')
<style>
  .text-slate-700 { color: #334155; }
  .text-slate-800 { color: #1e293b; }
  .x-small { font-size: 11px; }
  .bg-light-subtle { background-color: #f8fafc !important; }
  .bg-slate-100 { background-color: #f1f5f9; }
  
  .issue-card {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    border: 1px solid #f1f5f9 !important;
  }
  .issue-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1) !important;
  }
  
  .line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
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
          <h1 class="h4 fw-bold mb-1" style="color:#1e293b; letter-spacing: -0.5px;">Maintenance Issues</h1>
          <p class="text-muted mb-0 small">Support tickets flagged for vehicle maintenance and cleanliness attention.</p>
        </div>
        <a href="{{ route('staff.support-tickets') }}" class="btn btn-outline-primary btn-sm px-3 rounded-3 fw-bold border-2">
          <i class="bi bi-headset me-2"></i>All Support Tickets
        </a>
      </div>

      {{-- KPI Stats --}}
      <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
          <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
            <div class="card-body p-3 text-center">
              <div class="text-muted fw-bold text-uppercase x-small mb-1">Total Issues</div>
              <div class="h4 fw-bold mb-0 text-slate-800">{{ $stats['total'] }}</div>
            </div>
          </div>
        </div>
        <div class="col-6 col-md-3">
          <div class="card border-0 shadow-sm rounded-4 h-100 bg-white border-start border-4 border-danger">
            <div class="card-body p-3 text-center">
              <div class="text-danger fw-bold text-uppercase x-small mb-1">Open</div>
              <div class="h4 fw-bold mb-0 text-danger">{{ $stats['open'] }}</div>
            </div>
          </div>
        </div>
        <div class="col-6 col-md-3">
          <div class="card border-0 shadow-sm rounded-4 h-100 bg-white border-start border-4 border-info">
            <div class="card-body p-3 text-center">
              <div class="text-info fw-bold text-uppercase x-small mb-1">In Progress</div>
              <div class="h4 fw-bold mb-0 text-info">{{ $stats['in_progress'] }}</div>
            </div>
          </div>
        </div>
        <div class="col-6 col-md-3">
          <div class="card border-0 shadow-sm rounded-4 h-100 bg-white border-start border-4 border-success">
            <div class="card-body p-3 text-center">
              <div class="text-success fw-bold text-uppercase x-small mb-1">Resolved</div>
              <div class="h4 fw-bold mb-0 text-success">{{ $stats['resolved'] }}</div>
            </div>
          </div>
        </div>
      </div>

      {{-- FILTERS --}}
      <div class="card border-0 shadow-sm mb-4 rounded-4 overflow-hidden">
        <div class="card-body p-3 bg-white">
          <form method="GET" action="{{ route('staff.maintenance-issues') }}" class="row g-2 align-items-center">
            <div class="col-lg-3">
              <select name="status" class="form-select form-select-sm border rounded-3 py-2 bg-light-subtle">
                <option value="">Status: Open & Active</option>
                <option value="open" {{ ($filters['status'] ?? '') === 'open' ? 'selected' : '' }}>Open Only</option>
                <option value="in_progress" {{ ($filters['status'] ?? '') === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                <option value="resolved" {{ ($filters['status'] ?? '') === 'resolved' ? 'selected' : '' }}>Resolved</option>
                <option value="closed" {{ ($filters['status'] ?? '') === 'closed' ? 'selected' : '' }}>Closed</option>
              </select>
            </div>
            <div class="col-lg-3">
              <select name="category" class="form-select form-select-sm border rounded-3 py-2 bg-light-subtle">
                <option value="">All Categories</option>
                <option value="cleanliness" {{ ($filters['category'] ?? '') === 'cleanliness' ? 'selected' : '' }}>Cleanliness</option>
                <option value="lacking_facility" {{ ($filters['category'] ?? '') === 'lacking_facility' ? 'selected' : '' }}>Lacking Facility</option>
                <option value="bluetooth" {{ ($filters['category'] ?? '') === 'bluetooth' ? 'selected' : '' }}>Bluetooth</option>
                <option value="engine" {{ ($filters['category'] ?? '') === 'engine' ? 'selected' : '' }}>Engine</option>
                <option value="others" {{ ($filters['category'] ?? '') === 'others' ? 'selected' : '' }}>Others</option>
              </select>
            </div>
            <div class="col-lg-4">
              <div class="input-group input-group-sm border rounded-3 overflow-hidden bg-light-subtle">
                <span class="input-group-text bg-transparent border-0"><i class="bi bi-search text-muted"></i></span>
                <input type="text" name="search" class="form-control border-0 bg-transparent py-2 px-2" placeholder="Search description, car..." value="{{ $filters['search'] ?? '' }}">
              </div>
            </div>
            <div class="col-lg-2 d-flex gap-2">
              <button type="submit" class="btn btn-sm btn-dark px-4 flex-fill rounded-3 py-2 fw-bold">Filter</button>
              <a href="{{ route('staff.maintenance-issues') }}" class="btn btn-sm btn-light border px-2 rounded-3 py-2 text-muted"><i class="bi bi-arrow-clockwise"></i></a>
            </div>
          </form>
        </div>
      </div>

      {{-- ISSUES GRID --}}
      <div class="row g-4">
        @forelse($issues as $issue)
          @php
            $car = $issue->car ?? ($issue->booking ? $issue->booking->car : null);
            $carImage = $car && $car->images && count($car->images) > 0 
                ? asset('storage/' . $car->images[0]) 
                : asset('images/car-placeholder.png');
          @endphp
          <div class="col-md-6 col-xl-4">
            <div class="card border-0 shadow-sm h-100 rounded-4 overflow-hidden bg-white issue-card">
              {{-- Header Image/Status --}}
              <div class="position-relative">
                <img src="{{ $carImage }}" alt="Car" class="w-100" style="height: 160px; object-fit: cover;" data-fallback="{{ asset('images/car-placeholder.png') }}" onerror="this.src=this.dataset.fallback;this.onerror=null;">
                <div class="position-absolute top-0 end-0 m-3">
                  @php
                    $statusClass = match($issue->status) {
                      'open' => 'bg-danger',
                      'in_progress' => 'bg-info',
                      'resolved' => 'bg-success',
                      default => 'bg-secondary'
                    };
                  @endphp
                  <span class="badge rounded-pill shadow-sm px-3 py-2 fw-bold text-uppercase {{ $statusClass }}" style="font-size: 9px; letter-spacing: 0.5px;">
                    {{ ucfirst(str_replace('_', ' ', $issue->status)) }}
                  </span>
                </div>
              </div>
              
              <div class="card-body p-4">
                {{-- Vehicle Header --}}
                <div class="d-flex align-items-center gap-3 mb-3 pb-3 border-bottom">
                  <div class="bg-light rounded-3 p-2 text-primary">
                    <i class="bi bi-car-front-fill h5 mb-0"></i>
                  </div>
                  <div>
                    <div class="fw-bold text-slate-800 small">{{ $car->brand ?? 'Unknown' }} {{ $car->model ?? 'Vehicle' }}</div>
                    <div class="text-muted x-small fw-bold">{{ strtoupper($car->plate_number ?? '---') }}</div>
                  </div>
                </div>

                {{-- Category & Subject --}}
                <div class="mb-3">
                  <span class="badge rounded-pill bg-slate-100 text-slate-700 border border-slate-200 x-small px-2 py-1 mb-2">
                    <i class="bi bi-tag-fill me-1 text-muted"></i>{{ ucfirst(str_replace('_', ' ', $issue->category)) }}
                  </span>
                  <h6 class="fw-bold text-slate-800 mb-2">{{ \Illuminate\Support\Str::limit($issue->subject, 50) }}</h6>
                  <p class="text-muted x-small line-clamp-2 mb-0">{{ $issue->description }}</p>
                </div>

                {{-- Reporter Meta --}}
                <div class="d-flex justify-content-between align-items-center bg-light rounded-3 p-2 px-3 mb-4">
                  <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-person-circle text-muted"></i>
                    <span class="x-small fw-bold text-slate-700">{{ \Illuminate\Support\Str::limit($issue->name, 15) }}</span>
                  </div>
                  <div class="x-small text-muted">
                    <i class="bi bi-calendar-event me-1"></i>{{ $issue->created_at->format('d M Y') }}
                  </div>
                </div>

                {{-- Actions --}}
                <div class="d-grid gap-2">
                  <div class="d-flex gap-2">
                    <a href="{{ route('staff.support-tickets.show', $issue->ticket_id) }}" class="btn btn-primary flex-fill fw-bold btn-sm py-2 rounded-3">
                      <i class="bi bi-eye me-1"></i>Investigate
                    </a>
                    @if($issue->status !== 'resolved' && $issue->status !== 'closed')
                      <form method="POST" action="{{ route('staff.maintenance-issues.resolve', $issue->ticket_id) }}" class="flex-shrink-0">
                        @csrf
                        <button type="submit" class="btn btn-success btn-sm px-3 py-2 rounded-3" onclick="return confirm('Mark this issue as resolved?')">
                          <i class="bi bi-check-lg"></i>
                        </button>
                      </form>
                    @endif
                    @if($car)
                      <a href="{{ route('staff.maintenance.index', $car->id) }}" class="btn btn-outline-secondary btn-sm px-3 py-2 rounded-3" title="Service Logs">
                        <i class="bi bi-wrench-adjustable"></i>
                      </a>
                    @endif
                  </div>
                </div>
              </div>
            </div>
          </div>
        @empty
          <div class="col-12">
            <div class="card border-0 shadow-sm p-5 text-center bg-white rounded-4">
              <div class="bg-light rounded-circle d-inline-flex p-4 mb-4">
                <i class="bi bi-check2-circle text-success h1 mb-0 opacity-25"></i>
              </div>
              <h5 class="fw-bold text-slate-800">Clear of Maintenance Issues</h5>
              <p class="text-muted small">All reported car issues have been addressed or none found.</p>
            </div>
          </div>
        @endforelse
      </div>

      {{-- PAGINATION --}}
      @if($issues->hasPages())
        <div class="mt-5 d-flex justify-content-center">
          {{ $issues->appends($filters)->links() }}
        </div>
      @endif

    </div>
  </div>
</div>
@endsection
