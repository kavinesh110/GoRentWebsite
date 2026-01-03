@extends('layouts.app')
@section('title', 'Bookings - Staff Dashboard')

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
          <h1 class="h3 fw-bold mb-1" style="color:#1a202c;">Booking Management</h1>
          <p class="text-muted mb-0" style="font-size: 14.5px;">Monitor active rentals, track payments, and manage reservations.</p>
        </div>
        <div class="d-flex gap-2">
          <a href="{{ route('staff.bookings.export', request()->query()) }}" class="btn btn-outline-success btn-sm shadow-sm px-3">
            <i class="bi bi-file-earmark-excel me-2"></i>Export Data
          </a>
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
      <form method="GET" action="{{ route('staff.bookings') }}" class="row g-3 align-items-end">
        <div class="col-md-5">
          <label class="form-label small fw-bold text-muted text-uppercase" style="font-size: 10px;">Search Bookings</label>
          <div class="input-group input-group-sm">
            <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
            <input type="text" name="search" class="form-control border-start-0 ps-0" placeholder="Customer name, email, car plate..." value="{{ $filters['search'] ?? '' }}">
          </div>
        </div>
        <div class="col-md-3">
          <label class="form-label small fw-bold text-muted text-uppercase" style="font-size: 10px;">Rental Status</label>
          <select name="status" class="form-select form-select-sm">
            <option value="">All statuses</option>
            <option value="created" {{ ($filters['status'] ?? '') === 'created' ? 'selected' : '' }}>Created</option>
            <option value="confirmed" {{ ($filters['status'] ?? '') === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
            <option value="ongoing" {{ ($filters['status'] ?? '') === 'ongoing' ? 'selected' : '' }}>Ongoing</option>
            <option value="completed" {{ ($filters['status'] ?? '') === 'completed' ? 'selected' : '' }}>Completed</option>
            <option value="cancelled" {{ ($filters['status'] ?? '') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
          </select>
        </div>
        <div class="col-md-4">
          <div class="d-flex gap-2">
            <button type="submit" class="btn btn-sm btn-dark px-4 flex-fill">Apply Filters</button>
            <a href="{{ route('staff.bookings') }}" class="btn btn-sm btn-light border px-4">Reset</a>
          </div>
        </div>
      </form>
    </div>
  </div>

  {{-- BOOKINGS TABLE --}}
  <div class="card border-0 shadow-sm overflow-hidden">
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="bg-light">
            <tr>
              <th class="ps-4 py-3 text-uppercase text-muted fw-bold" style="font-size: 11px;">Booking Info</th>
              <th class="py-3 text-uppercase text-muted fw-bold" style="font-size: 11px;">Customer</th>
              <th class="py-3 text-uppercase text-muted fw-bold" style="font-size: 11px;">Vehicle Details</th>
              <th class="py-3 text-uppercase text-muted fw-bold" style="font-size: 11px;">Rental Period</th>
              <th class="py-3 text-uppercase text-muted fw-bold" style="font-size: 11px;">Financials</th>
              <th class="py-3 text-uppercase text-muted fw-bold" style="font-size: 11px;">Status</th>
              <th class="pe-4 py-3 text-uppercase text-muted fw-bold text-end" style="font-size: 11px;">Action</th>
            </tr>
          </thead>
          <tbody>
            @forelse($bookings as $booking)
              <tr class="transition-all hover-bg-light">
                <td class="ps-4">
                  <span class="fw-bold text-dark">#{{ $booking->booking_id }}</span>
                  <div class="small text-muted">{{ $booking->created_at->format('d M, Y') }}</div>
                </td>
                <td>
                  <div class="d-flex align-items-center gap-2">
                    <div class="bg-light rounded-circle d-flex align-items-center justify-content-center fw-bold text-primary" style="width: 32px; height: 32px; font-size: 12px;">
                      {{ substr($booking->customer->full_name ?? 'N', 0, 1) }}
                    </div>
                    <div>
                      <div class="fw-semibold text-dark">{{ $booking->customer->full_name ?? 'N/A' }}</div>
                      <div class="small text-muted">{{ $booking->customer->email ?? '' }}</div>
                    </div>
                  </div>
                </td>
                <td>
                  <div class="fw-semibold text-dark">{{ $booking->car->brand ?? 'N/A' }} {{ $booking->car->model ?? '' }}</div>
                  <span class="badge bg-light text-dark border small">{{ $booking->car->plate_number ?? '' }}</span>
                </td>
                <td>
                  <div class="small">
                    <span class="text-muted">From:</span> {{ $booking->start_datetime?->format('d/m/y H:i') ?? 'N/A' }}
                  </div>
                  <div class="small">
                    <span class="text-muted">To:</span> {{ $booking->end_datetime?->format('d/m/y H:i') ?? 'N/A' }}
                  </div>
                </td>
                <td>
                  <div class="fw-bold text-dark">RM {{ number_format($booking->final_amount ?? 0, 2) }}</div>
                  <div class="small text-muted">{{ $booking->rental_hours ?? 0 }} hours</div>
                </td>
                <td>
                  @php
                    $statusClasses = [
                      'completed' => 'bg-success-subtle text-success border-success-subtle',
                      'ongoing' => 'bg-info-subtle text-info border-info-subtle',
                      'cancelled' => 'bg-danger-subtle text-danger border-danger-subtle',
                      'confirmed' => 'bg-primary-subtle text-primary border-primary-subtle',
                      'created' => 'bg-warning-subtle text-warning border-warning-subtle'
                    ];
                    $statusClass = $statusClasses[$booking->status] ?? 'bg-secondary-subtle text-secondary border-secondary-subtle';
                  @endphp
                  <span class="badge rounded-pill border px-3 py-2 {{ $statusClass }}">
                    {{ ucfirst($booking->status ?? 'N/A') }}
                  </span>
                </td>
                <td class="pe-4 text-end">
                  <a href="{{ route('staff.bookings.show', $booking->booking_id) }}" class="btn btn-sm btn-white border shadow-sm fw-semibold">
                    Manage <i class="bi bi-chevron-right ms-1 small"></i>
                  </a>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="7" class="text-center py-5 text-muted">
                  <i class="bi bi-inbox display-4 d-block mb-3 opacity-25"></i>
                  No bookings matching your criteria were found.
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
    {{ $bookings->links() }}
  </div>
  </div>
</div>

<style>
  .hover-bg-light:hover { background-color: #f8fafc !important; }
  .btn-white { background: #fff; color: #1a202c; }
  .btn-white:hover { background: #f8fafc; color: var(--hasta); }
  .table > :not(caption) > * > * { border-bottom-width: 1px; border-color: #f1f5f9; }
</style>
</div>
@endsection
