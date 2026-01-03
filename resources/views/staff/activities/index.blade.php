@extends('layouts.app')
@section('title', 'Activities & Promotions - Staff Dashboard')

@section('content')
<div class="d-flex" style="min-height: calc(100vh - 60px);">
  @include('staff._nav')
  <div class="flex-fill" style="background: #f8fafc;">
    <div class="container-fluid px-4 px-md-5" style="padding-top: 32px; padding-bottom: 48px;">
      <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
        <div>
          <h1 class="h3 fw-bold mb-1" style="color:#1a202c;">Marketing & Promotions</h1>
          <p class="text-muted mb-0" style="font-size: 14.5px;">Manage company activities, banners, and promotional campaigns.</p>
        </div>
        <a href="{{ route('staff.activities.create') }}" class="btn btn-hasta shadow-sm px-4">
          <i class="bi bi-plus-lg me-2"></i>New Activity
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
          <form method="GET" action="{{ route('staff.activities') }}" class="row g-3 align-items-end">
            <div class="col-md-3">
              <label class="form-label small fw-bold text-muted text-uppercase" style="font-size: 10px;">Filter Status</label>
              <select name="status" class="form-select form-select-sm">
                <option value="">All activities</option>
                <option value="active" {{ ($filters['status'] ?? '') === 'active' ? 'selected' : '' }}>Active Only</option>
                <option value="expired" {{ ($filters['status'] ?? '') === 'expired' ? 'selected' : '' }}>Expired</option>
              </select>
            </div>
            <div class="col-md-5">
              <label class="form-label small fw-bold text-muted text-uppercase" style="font-size: 10px;">Search Campaigns</label>
              <div class="input-group input-group-sm">
                <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                <input type="text" name="search" class="form-control border-start-0 ps-0" placeholder="Title or description..." value="{{ $filters['search'] ?? '' }}">
              </div>
            </div>
            <div class="col-md-4">
              <div class="d-flex gap-2">
                <button type="submit" class="btn btn-sm btn-dark px-4 flex-fill">Apply Filters</button>
                <a href="{{ route('staff.activities') }}" class="btn btn-sm btn-light border px-4">Reset</a>
              </div>
            </div>
          </form>
        </div>
      </div>

      {{-- ACTIVITIES TABLE --}}
      <div class="card border-0 shadow-sm overflow-hidden">
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
              <thead class="bg-light">
                <tr>
                  <th class="ps-4 py-3 text-uppercase text-muted fw-bold" style="font-size: 11px;">Promotion Title</th>
                  <th class="py-3 text-uppercase text-muted fw-bold" style="font-size: 11px;">Campaign Duration</th>
                  <th class="py-3 text-uppercase text-muted fw-bold" style="font-size: 11px;">Status</th>
                  <th class="py-3 text-uppercase text-muted fw-bold" style="font-size: 11px;">Admin</th>
                  <th class="pe-4 py-3 text-uppercase text-muted fw-bold text-end" style="font-size: 11px;">Actions</th>
                </tr>
              </thead>
              <tbody>
                @forelse($activities as $activity)
                  <tr class="transition-all hover-bg-light">
                    <td class="ps-4">
                      <div class="fw-bold text-dark">{{ $activity->title }}</div>
                      <div class="small text-muted text-truncate" style="max-width: 350px;">
                        {{ Str::limit($activity->description ?? 'No description', 80) }}
                      </div>
                    </td>
                    <td>
                      <div class="small fw-semibold text-dark"><i class="bi bi-calendar-check me-2 text-muted"></i>{{ $activity->start_date->format('d M Y') }}</div>
                      <div class="small text-muted"><i class="bi bi-calendar-x me-2 text-muted"></i>{{ $activity->end_date->format('d M Y') }}</div>
                    </td>
                    <td>
                      @if($activity->end_date >= now())
                        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-2">Active</span>
                      @else
                        <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle rounded-pill px-3 py-2">Expired</span>
                      @endif
                    </td>
                    <td>
                      <div class="small fw-medium text-dark">{{ $activity->staff->name ?? 'System' }}</div>
                    </td>
                    <td class="pe-4 text-end">
                      <div class="d-flex gap-2 justify-content-end">
                        <a href="{{ route('staff.activities.edit', $activity->activity_id) }}" class="btn btn-sm btn-white border shadow-sm px-3 fw-semibold">Edit</a>
                        <form method="POST" action="{{ route('staff.activities.destroy', $activity->activity_id) }}" class="d-inline" onsubmit="return confirm('Delete this campaign?');">
                          @csrf
                          @method('DELETE')
                          <button type="submit" class="btn btn-sm btn-outline-danger shadow-sm px-2">
                            <i class="bi bi-trash"></i>
                          </button>
                        </form>
                      </div>
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="5" class="text-center py-5 text-muted">
                      <i class="bi bi-megaphone display-4 d-block mb-3 opacity-25"></i>
                      No promotional activities found.
                    </td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>

      {{-- PAGINATION --}}
      <div class="mt-4 d-flex justify-content-center">
        {{ $activities->links() }}
      </div>
    </div>
  </div>
</div>

<style>
  .hover-bg-light:hover { background-color: #f8fafc !important; }
  .btn-white { background: #fff; color: #1a202c; }
  .btn-white:hover { background: #f8fafc; color: var(--hasta); }
</style>

      {{-- PAGINATION --}}
      <div class="mt-4">
        {{ $activities->links() }}
      </div>
    </div>
  </div>
</div>
@endsection
