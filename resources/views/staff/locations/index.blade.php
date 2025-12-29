@extends('layouts.app')
@section('title', 'Car Locations - Staff Dashboard')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
@endpush

@section('content')
<div class="d-flex" style="min-height: calc(100vh - 60px);">
  @include('staff._nav')
  <div class="flex-fill" style="background: var(--bg);">
    <div class="container-fluid px-4 px-md-5" style="padding-top: 32px; padding-bottom: 48px;">
      <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
        <div>
          <h1 class="h3 fw-bold mb-1" style="color:#333;">Car Locations</h1>
          <p class="text-muted mb-0" style="font-size: 14px;">Manage pickup and dropoff locations for car rentals.</p>
        </div>
        <a href="{{ route('staff.locations.create') }}" class="btn btn-hasta">+ New Location</a>
      </div>

      @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
          {{ session('success') }}
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
      @endif

      @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
          {{ session('error') }}
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
      @endif

      {{-- FILTERS --}}
      <div class="card border-0 shadow-soft mb-4">
        <div class="card-body p-3">
          <form method="GET" action="{{ route('staff.locations') }}" class="row g-2 align-items-end">
            <div class="col-md-4">
              <label class="form-label small">Type</label>
              <select name="type" class="form-select form-select-sm">
                <option value="">All types</option>
                <option value="pickup" {{ ($filters['type'] ?? '') === 'pickup' ? 'selected' : '' }}>Pickup Only</option>
                <option value="dropoff" {{ ($filters['type'] ?? '') === 'dropoff' ? 'selected' : '' }}>Dropoff Only</option>
                <option value="both" {{ ($filters['type'] ?? '') === 'both' ? 'selected' : '' }}>Both</option>
              </select>
            </div>
            <div class="col-md-4">
              <label class="form-label small">Search</label>
              <input type="text" name="search" class="form-control form-control-sm" placeholder="Location name..." value="{{ $filters['search'] ?? '' }}">
            </div>
            <div class="col-md-2">
              <button type="submit" class="btn btn-sm btn-hasta w-100">Filter</button>
            </div>
            <div class="col-md-2">
              <a href="{{ route('staff.locations') }}" class="btn btn-sm btn-outline-secondary w-100">Clear</a>
            </div>
          </form>
        </div>
      </div>

      {{-- LOCATIONS TABLE --}}
      <div class="card border-0 shadow-soft">
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
              <thead class="table-light">
                <tr>
                  <th style="padding: 16px;">Location Name</th>
                  <th>Type</th>
                  <th>Created</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                @forelse($locations as $location)
                  <tr>
                    <td><strong>{{ $location->name }}</strong></td>
                    <td>
                      <span class="badge 
                        {{ $location->type === 'both' ? 'bg-primary' : ($location->type === 'pickup' ? 'bg-info' : 'bg-success') }}">
                        {{ ucfirst($location->type) }}
                      </span>
                    </td>
                    <td>{{ $location->created_at->format('d M Y') }}</td>
                    <td>
                      <div class="d-flex gap-1">
                        <a href="{{ route('staff.locations.edit', $location->location_id) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                        <form method="POST" action="{{ route('staff.locations.destroy', $location->location_id) }}" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this location?');">
                          @csrf
                          @method('DELETE')
                          <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                        </form>
                      </div>
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="4" class="text-center py-4 text-muted">No locations found. <a href="{{ route('staff.locations.create') }}">Create your first location</a></td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>

      {{-- PAGINATION --}}
      <div class="mt-4">
        {{ $locations->links() }}
      </div>
    </div>
  </div>
</div>
@endsection
