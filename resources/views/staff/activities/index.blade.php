@extends('layouts.app')
@section('title', 'Activities & Promotions - Staff Dashboard')

@section('content')
<div class="d-flex" style="min-height: calc(100vh - 60px);">
  @include('staff._nav')
  <div class="flex-fill" style="background: var(--bg);">
    <div class="container-custom" style="padding: 32px 24px 48px;">
      <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
        <div>
          <h1 class="h3 fw-bold mb-1" style="color:#333;">Activities & Promotions</h1>
          <p class="text-muted mb-0" style="font-size: 14px;">Manage company activities and promotional campaigns.</p>
        </div>
        <a href="{{ route('staff.activities.create') }}" class="btn btn-hasta">+ New Activity</a>
      </div>

      @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
          {{ session('success') }}
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
      @endif

      {{-- FILTERS --}}
      <div class="card border-0 shadow-soft mb-4">
        <div class="card-body p-3">
          <form method="GET" action="{{ route('staff.activities') }}" class="row g-2 align-items-end">
            <div class="col-md-4">
              <label class="form-label small">Status</label>
              <select name="status" class="form-select form-select-sm">
                <option value="">All activities</option>
                <option value="active" {{ ($filters['status'] ?? '') === 'active' ? 'selected' : '' }}>Active</option>
                <option value="expired" {{ ($filters['status'] ?? '') === 'expired' ? 'selected' : '' }}>Expired</option>
              </select>
            </div>
            <div class="col-md-4">
              <label class="form-label small">Search</label>
              <input type="text" name="search" class="form-control form-control-sm" placeholder="Title or description..." value="{{ $filters['search'] ?? '' }}">
            </div>
            <div class="col-md-2">
              <button type="submit" class="btn btn-sm btn-hasta w-100">Filter</button>
            </div>
            <div class="col-md-2">
              <a href="{{ route('staff.activities') }}" class="btn btn-sm btn-outline-secondary w-100">Clear</a>
            </div>
          </form>
        </div>
      </div>

      {{-- ACTIVITIES TABLE --}}
      <div class="card border-0 shadow-soft">
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
              <thead class="table-light">
                <tr>
                  <th style="padding: 16px;">Title</th>
                  <th>Description</th>
                  <th>Start Date</th>
                  <th>End Date</th>
                  <th>Status</th>
                  <th>Created By</th>
                  <th style="padding: 16px; text-align: right;">Actions</th>
                </tr>
              </thead>
              <tbody>
                @forelse($activities as $activity)
                  <tr>
                    <td style="padding: 16px;"><strong>{{ $activity->title }}</strong></td>
                    <td>
                      <div class="text-truncate" style="max-width: 300px;">
                        {{ Str::limit($activity->description ?? 'No description', 100) }}
                      </div>
                    </td>
                    <td>{{ $activity->start_date->format('d M Y') }}</td>
                    <td>{{ $activity->end_date->format('d M Y') }}</td>
                    <td>
                      @if($activity->end_date >= now())
                        <span class="badge bg-success">Active</span>
                      @else
                        <span class="badge bg-secondary">Expired</span>
                      @endif
                    </td>
                    <td>{{ $activity->staff->name ?? 'N/A' }}</td>
                    <td style="padding: 16px; text-align: right;">
                      <div class="d-flex gap-2 justify-content-end">
                        <a href="{{ route('staff.activities.edit', $activity->activity_id) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                        <form method="POST" action="{{ route('staff.activities.destroy', $activity->activity_id) }}" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this activity?');">
                          @csrf
                          @method('DELETE')
                          <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                        </form>
                      </div>
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="7" class="text-center py-4 text-muted">No activities found.</td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>

      {{-- PAGINATION --}}
      <div class="mt-4">
        {{ $activities->links() }}
      </div>
    </div>
  </div>
</div>
@endsection
