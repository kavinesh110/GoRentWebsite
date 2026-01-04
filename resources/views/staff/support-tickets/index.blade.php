@extends('layouts.app')
@section('title', 'Support Tickets - Staff Dashboard')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
@endpush

@section('content')
<div class="d-flex" style="min-height: calc(100vh - 60px);">
  @include('staff._nav')
  <div class="flex-fill" style="background: #f8fafc;">
    <div class="container-fluid px-4 px-md-5" style="padding-top: 32px; padding-bottom: 48px;">
      <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
        <div>
          <h1 class="h3 fw-bold mb-1" style="color:#1a202c;">Customer Support</h1>
          <p class="text-muted mb-0" style="font-size: 14.5px;">Manage customer service requests, complaints, and inquiries.</p>
        </div>
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
          <form method="GET" action="{{ route('staff.support-tickets') }}" class="row g-3 align-items-end">
            <div class="col-md-4">
              <label class="form-label small fw-bold text-muted text-uppercase" style="font-size: 10px;">Search Tickets</label>
              <div class="input-group input-group-sm">
                <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                <input type="text" name="search" class="form-control border-start-0 ps-0" placeholder="Subject, customer, email..." value="{{ $filters['search'] ?? '' }}">
              </div>
            </div>
            <div class="col-md-3">
              <label class="form-label small fw-bold text-muted text-uppercase" style="font-size: 10px;">Status</label>
              <select name="status" class="form-select form-select-sm">
                <option value="">All Statuses</option>
                <option value="open" {{ ($filters['status'] ?? '') === 'open' ? 'selected' : '' }}>Open</option>
                <option value="in_progress" {{ ($filters['status'] ?? '') === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                <option value="resolved" {{ ($filters['status'] ?? '') === 'resolved' ? 'selected' : '' }}>Resolved</option>
                <option value="closed" {{ ($filters['status'] ?? '') === 'closed' ? 'selected' : '' }}>Closed</option>
              </select>
            </div>
            <div class="col-md-3">
              <label class="form-label small fw-bold text-muted text-uppercase" style="font-size: 10px;">Category</label>
              <select name="category" class="form-select form-select-sm">
                <option value="">All Categories</option>
                <option value="cleanliness" {{ ($filters['category'] ?? '') === 'cleanliness' ? 'selected' : '' }}>Cleanliness</option>
                <option value="lacking_facility" {{ ($filters['category'] ?? '') === 'lacking_facility' ? 'selected' : '' }}>Lacking Facility</option>
                <option value="bluetooth" {{ ($filters['category'] ?? '') === 'bluetooth' ? 'selected' : '' }}>Bluetooth</option>
                <option value="engine" {{ ($filters['category'] ?? '') === 'engine' ? 'selected' : '' }}>Engine</option>
                <option value="others" {{ ($filters['category'] ?? '') === 'others' ? 'selected' : '' }}>Others</option>
              </select>
            </div>
            <div class="col-md-2">
              <div class="d-flex gap-2">
                <button type="submit" class="btn btn-sm btn-dark px-4 flex-fill">Filter</button>
                <a href="{{ route('staff.support-tickets') }}" class="btn btn-sm btn-light border px-4">Reset</a>
              </div>
            </div>
          </form>
        </div>
      </div>

      {{-- TICKETS TABLE --}}
      <div class="card border-0 shadow-sm overflow-hidden">
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
              <thead class="bg-light">
                <tr>
                  <th class="ps-4 py-3 text-uppercase text-muted fw-bold" style="font-size: 11px;">Ticket #</th>
                  <th class="py-3 text-uppercase text-muted fw-bold" style="font-size: 11px;">Customer</th>
                  <th class="py-3 text-uppercase text-muted fw-bold" style="font-size: 11px;">Subject</th>
                  <th class="py-3 text-uppercase text-muted fw-bold" style="font-size: 11px;">Category</th>
                  <th class="py-3 text-uppercase text-muted fw-bold" style="font-size: 11px;">Status</th>
                  <th class="py-3 text-uppercase text-muted fw-bold" style="font-size: 11px;">Assigned To</th>
                  <th class="py-3 text-uppercase text-muted fw-bold" style="font-size: 11px;">Created</th>
                  <th class="pe-4 py-3 text-uppercase text-muted fw-bold text-end" style="font-size: 11px;">Action</th>
                </tr>
              </thead>
              <tbody>
                @forelse($tickets as $ticket)
                  <tr class="transition-all hover-bg-light">
                    <td class="ps-4">
                      <span class="fw-bold text-dark">#{{ $ticket->ticket_id }}</span>
                      @if($ticket->flagged_for_maintenance)
                        <span class="badge bg-warning text-dark ms-1" title="Flagged for Maintenance">
                          <i class="bi bi-tools"></i>
                        </span>
                      @endif
                    </td>
                    <td>
                      <div class="fw-semibold text-dark">{{ $ticket->name }}</div>
                      <div class="small text-muted">{{ $ticket->email }}</div>
                    </td>
                    <td>
                      <div class="fw-medium text-dark">{{ Str::limit($ticket->subject, 50) }}</div>
                    </td>
                    <td>
                      <span class="badge bg-light text-dark border px-2 py-1">
                        {{ ucfirst(str_replace('_', ' ', $ticket->category)) }}
                      </span>
                    </td>
                    <td>
                      @php
                        $statusClasses = [
                          'open' => 'bg-primary-subtle text-primary border-primary-subtle',
                          'in_progress' => 'bg-info-subtle text-info border-info-subtle',
                          'resolved' => 'bg-success-subtle text-success border-success-subtle',
                          'closed' => 'bg-secondary-subtle text-secondary border-secondary-subtle'
                        ];
                        $statusClass = $statusClasses[$ticket->status] ?? 'bg-light text-dark border';
                      @endphp
                      <span class="badge rounded-pill border px-3 py-2 {{ $statusClass }}">
                        {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                      </span>
                    </td>
                    <td>
                      <div class="small text-muted">{{ $ticket->assignedStaff->name ?? 'Unassigned' }}</div>
                    </td>
                    <td>
                      <div class="small text-muted">{{ $ticket->created_at->format('d M Y') }}</div>
                      <div class="small text-muted">{{ $ticket->created_at->format('H:i') }}</div>
                    </td>
                    <td class="pe-4 text-end">
                      <a href="{{ route('staff.support-tickets.show', $ticket->ticket_id) }}" class="btn btn-sm btn-white border shadow-sm fw-semibold">
                        Review <i class="bi bi-chevron-right ms-1 small"></i>
                      </a>
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="8" class="text-center py-5 text-muted">
                      <i class="bi bi-headset display-4 d-block mb-3 opacity-25"></i>
                      No support tickets found.
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
        {{ $tickets->links() }}
      </div>
    </div>
  </div>
</div>

<style>
  .hover-bg-light:hover { background-color: #f8fafc !important; }
  .btn-white { background: #fff; color: #1a202c; }
  .btn-white:hover { background: #f8fafc; color: var(--hasta); }
</style>
@endsection
