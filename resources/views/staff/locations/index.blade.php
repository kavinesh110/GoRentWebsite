@extends('layouts.app')
@section('title', 'Car Locations')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
<style>
  .fw-800 { font-weight: 800; }
  .fw-700 { font-weight: 700; }
  .fw-600 { font-weight: 600; }
  .fw-500 { font-weight: 500; }
  .text-slate-500 { color: #64748b; }
  .text-slate-600 { color: #475569; }
  .text-slate-700 { color: #334155; }
  .bg-slate-50 { background-color: #f8fafc; }
  .form-label-sm { font-size: 12px; font-weight: 700; color: #475569; margin-bottom: 6px; text-transform: uppercase; letter-spacing: 0.3px; }
</style>
@endpush

@section('content')
<div class="d-flex" style="min-height: calc(100vh - 60px);">
  @include('staff._nav')
  <div class="flex-fill bg-slate-50">
    <div class="container-fluid px-4 px-md-5 py-4">
      {{-- Header --}}
      <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
          <h1 class="h4 fw-800 mb-0 text-slate-800">Car Locations</h1>
          <p class="text-muted mb-0" style="font-size: 13px;">Manage pickup and dropoff locations for car rentals.</p>
        </div>
        <a href="{{ route('staff.locations.create') }}" class="btn btn-hasta btn-sm rounded-2 fw-700 px-3 py-2">
          <i class="bi bi-plus-lg me-1"></i>New Location
        </a>
      </div>

      @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm rounded-3 py-2 px-3 small mb-4 d-flex align-items-center gap-2">
          <i class="bi bi-check-circle-fill"></i>
          <span>{{ session('success') }}</span>
          <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" style="font-size: 10px;"></button>
        </div>
      @endif

      @if(session('error'))
        <div class="alert alert-danger border-0 shadow-sm rounded-3 py-2 px-3 small mb-4 d-flex align-items-center gap-2">
          <i class="bi bi-exclamation-triangle-fill"></i>
          <span>{{ session('error') }}</span>
          <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" style="font-size: 10px;"></button>
        </div>
      @endif

      {{-- FILTERS --}}
      <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-body p-2 px-3">
          <form method="GET" action="{{ route('staff.locations') }}" class="row g-2 align-items-center">
            <div class="col-md-3">
              <select name="type" class="form-select form-select-sm border-0 bg-light fw-600 rounded-2 py-2">
                <option value="">All Types</option>
                <option value="pickup" {{ ($filters['type'] ?? '') === 'pickup' ? 'selected' : '' }}>Pickup Only</option>
                <option value="dropoff" {{ ($filters['type'] ?? '') === 'dropoff' ? 'selected' : '' }}>Dropoff Only</option>
                <option value="both" {{ ($filters['type'] ?? '') === 'both' ? 'selected' : '' }}>Both</option>
              </select>
            </div>
            <div class="col-md-6">
              <div class="input-group input-group-sm">
                <span class="input-group-text border-0 bg-light text-muted"><i class="bi bi-search" style="font-size: 12px;"></i></span>
                <input type="text" name="search" class="form-control form-control-sm border-0 bg-light fw-500 rounded-end-2" placeholder="Search location name..." value="{{ $filters['search'] ?? '' }}">
              </div>
            </div>
            <div class="col-md-2">
              <button type="submit" class="btn btn-hasta btn-sm w-100 rounded-2 fw-700 py-1">Apply</button>
            </div>
            <div class="col-md-1">
              <a href="{{ route('staff.locations') }}" class="btn btn-sm btn-light w-100 rounded-2 fw-600 py-1" title="Clear filters">
                <i class="bi bi-x-lg"></i>
              </a>
            </div>
          </form>
        </div>
      </div>

      {{-- LOCATIONS TABLE --}}
      <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="font-size: 13px;">
              <thead class="bg-light">
                <tr>
                  <th class="ps-3 py-2 border-0 text-uppercase small fw-700 text-slate-500" style="font-size: 11px;">Location Name</th>
                  <th class="py-2 border-0 text-uppercase small fw-700 text-slate-500" style="font-size: 11px;">Type</th>
                  <th class="py-2 border-0 text-uppercase small fw-700 text-slate-500" style="font-size: 11px;">Created</th>
                  <th class="pe-3 py-2 border-0 text-uppercase small fw-700 text-slate-500 text-end" style="font-size: 11px;">Actions</th>
                </tr>
              </thead>
              <tbody class="border-top-0">
                @forelse($locations as $location)
                  <tr>
                    <td class="ps-3 py-2">
                      <div class="fw-700 text-slate-700">{{ $location->name }}</div>
                    </td>
                    <td class="py-2">
                      @php
                        $typeColors = [
                          'both' => ['bg' => '#eff6ff', 'text' => '#2563eb', 'border' => '#dbeafe'],
                          'pickup' => ['bg' => '#ecfeff', 'text' => '#0891b2', 'border' => '#cffafe'],
                          'dropoff' => ['bg' => '#f0fdf4', 'text' => '#16a34a', 'border' => '#dcfce7']
                        ];
                        $colors = $typeColors[$location->type] ?? $typeColors['both'];
                      @endphp
                      <span class="badge rounded-pill px-2 py-1 fw-700" style="background: {{ $colors['bg'] }}; color: {{ $colors['text'] }}; border: 1px solid {{ $colors['border'] }}; font-size: 10px;">
                        {{ strtoupper($location->type) }}
                      </span>
                    </td>
                    <td class="py-2">
                      <div class="fw-600 text-slate-700">{{ $location->created_at->format('d M Y') }}</div>
                    </td>
                    <td class="pe-3 py-2 text-end">
                      <div class="d-flex gap-1 justify-content-end">
                        <a href="{{ route('staff.locations.edit', $location->location_id) }}" class="btn btn-sm btn-light rounded-2 px-2 py-1" title="Edit">
                          <i class="bi bi-pencil text-slate-600" style="font-size: 12px;"></i>
                        </a>
                        <form method="POST" action="{{ route('staff.locations.destroy', $location->location_id) }}" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this location?');">
                          @csrf
                          @method('DELETE')
                          <button type="submit" class="btn btn-sm btn-light rounded-2 px-2 py-1" title="Delete">
                            <i class="bi bi-trash text-danger" style="font-size: 12px;"></i>
                          </button>
                        </form>
                      </div>
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="4" class="text-center py-4">
                      <div class="text-muted mb-2"><i class="bi bi-geo-alt fs-1"></i></div>
                      <div class="fw-600 text-slate-500">No locations found</div>
                      <small class="text-muted">Create your first location to get started</small>
                    </td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>

      {{-- PAGINATION --}}
      <div class="mt-3 d-flex justify-content-center">
        {{ $locations->links() }}
      </div>
    </div>
  </div>
</div>

<style>
  .btn-hasta { background: var(--hasta); color: white; border: none; transition: all 0.2s; }
  .btn-hasta:hover { background: var(--hasta-dark); color: white; transform: translateY(-1px); }
  .pagination {
    gap: 3px;
  }
  .page-item .page-link {
    border-radius: 4px !important;
    border: 1px solid #e2e8f0;
    padding: 4px 10px;
    color: #475569;
    font-size: 12px;
    font-weight: 600;
    transition: all 0.2s;
  }
  .page-item.active .page-link {
    background-color: var(--hasta);
    border-color: var(--hasta);
    color: white;
  }
  .page-item .page-link:hover:not(.active) {
    background-color: #f8fafc;
    border-color: #cbd5e1;
    color: var(--hasta);
    transform: translateY(-1px);
  }
</style>
@endsection
