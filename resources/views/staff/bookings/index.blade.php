@extends('layouts.app')
@section('title', 'Bookings - Staff Dashboard')

@section('content')
<div class="d-flex" style="min-height: calc(100vh - 70px);">
  @include('staff._nav')
  <div class="flex-fill" style="background: #f8fafc;">
    <div class="container-fluid px-4 px-md-5 py-4">
      
      {{-- Page Header --}}
      <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
          <h1 class="h4 fw-bold mb-1" style="color:#1e293b; letter-spacing: -0.5px;">Booking Records</h1>
          <p class="text-muted mb-0 small">Manage rental lifecycle, verify payments, and monitor active trips.</p>
        </div>
        <div class="d-flex gap-2">
          <a href="{{ route('staff.bookings.export', request()->query()) }}" class="btn btn-outline-success btn-sm px-3 rounded-3 fw-bold border-2">
            <i class="bi bi-file-earmark-excel me-2"></i>Export Excel
          </a>
        </div>
      </div>

      @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm d-flex align-items-center gap-2 mb-4 rounded-3">
          <i class="bi bi-check-circle-fill"></i>
          <div class="small fw-semibold">{{ session('success') }}</div>
          <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
        </div>
      @endif

      {{-- SEARCH & FILTERS --}}
      <div class="card border-0 shadow-sm mb-4 rounded-4 overflow-hidden">
        <div class="card-body p-3 bg-white">
          <form method="GET" action="{{ route('staff.bookings') }}" class="row g-2 align-items-center">
            <div class="col-lg-5">
              <div class="input-group input-group-sm border rounded-3 overflow-hidden bg-light-subtle">
                <span class="input-group-text bg-transparent border-0 ps-3"><i class="bi bi-search text-muted"></i></span>
                <input type="text" name="search" class="form-control border-0 bg-transparent py-2 px-2" placeholder="Customer name, email, or plate number..." value="{{ $filters['search'] ?? '' }}">
              </div>
            </div>
            <div class="col-lg-3">
              <select name="status" class="form-select form-select-sm border rounded-3 py-2 bg-light-subtle">
                <option value="">All Booking Statuses</option>
                <option value="created" {{ ($filters['status'] ?? '') === 'created' ? 'selected' : '' }}>Created / Pending</option>
                <option value="confirmed" {{ ($filters['status'] ?? '') === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                <option value="active" {{ ($filters['status'] ?? '') === 'active' ? 'selected' : '' }}>Currently Active</option>
                <option value="completed" {{ ($filters['status'] ?? '') === 'completed' ? 'selected' : '' }}>Completed</option>
                <option value="cancelled" {{ ($filters['status'] ?? '') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
              </select>
            </div>
            <div class="col-lg-4 d-flex gap-2">
              <button type="submit" class="btn btn-sm btn-dark px-4 flex-fill rounded-3 py-2 fw-bold">Filter Records</button>
              <a href="{{ route('staff.bookings') }}" class="btn btn-sm btn-light border px-3 rounded-3 py-2 fw-semibold text-muted">Reset</a>
            </div>
          </form>
        </div>
      </div>

      {{-- BOOKINGS TABLE --}}
      <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
              <thead class="bg-light-subtle">
                <tr>
                  <th class="ps-4 py-3 text-uppercase text-muted fw-bold" style="font-size: 10px; letter-spacing: 0.5px;">Reference</th>
                  <th class="py-3 text-uppercase text-muted fw-bold" style="font-size: 10px; letter-spacing: 0.5px;">Customer</th>
                  <th class="py-3 text-uppercase text-muted fw-bold" style="font-size: 10px; letter-spacing: 0.5px;">Vehicle</th>
                  <th class="py-3 text-uppercase text-muted fw-bold" style="font-size: 10px; letter-spacing: 0.5px;">Duration</th>
                  <th class="py-3 text-uppercase text-muted fw-bold" style="font-size: 10px; letter-spacing: 0.5px;">Amount</th>
                  <th class="py-3 text-uppercase text-muted fw-bold" style="font-size: 10px; letter-spacing: 0.5px;">Status</th>
                  <th class="pe-4 py-3 text-uppercase text-muted fw-bold text-end" style="font-size: 10px; letter-spacing: 0.5px;">Operation</th>
                </tr>
              </thead>
              <tbody class="border-top-0">
                @forelse($bookings as $booking)
                  <tr>
                    <td class="ps-4">
                      <div class="fw-bold text-slate-800 mb-0">#{{ $booking->booking_id }}</div>
                      <div class="text-muted x-small">{{ $booking->created_at->format('d M, Y') }}</div>
                    </td>
                    <td>
                      <div class="d-flex align-items-center gap-3">
                        <div class="bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center fw-bold shadow-sm" style="width: 32px; height: 32px; font-size: 11px;">
                          {{ substr($booking->customer->full_name ?? 'N', 0, 1) }}
                        </div>
                        <div>
                          <div class="fw-bold text-slate-700 small mb-0">{{ $booking->customer->full_name ?? 'N/A' }}</div>
                          <div class="text-muted x-small text-truncate" style="max-width: 150px;">{{ $booking->customer->email ?? '' }}</div>
                        </div>
                      </div>
                    </td>
                    <td>
                      <div class="fw-bold text-slate-700 small mb-0">{{ $booking->car->brand ?? 'N/A' }} {{ $booking->car->model ?? '' }}</div>
                      <span class="badge bg-light text-slate-600 border border-slate-200 x-small px-2">{{ $booking->car->plate_number ?? '' }}</span>
                    </td>
                    <td>
                      <div class="small text-slate-700 fw-medium">
                        {{ $booking->start_datetime?->format('d/m H:i') }}
                        <i class="bi bi-arrow-right mx-1 text-muted"></i>
                        {{ $booking->end_datetime?->format('d/m H:i') }}
                      </div>
                      <div class="text-muted x-small mt-1">{{ $booking->rental_hours ?? 0 }} Hours Total</div>
                    </td>
                    <td>
                      <div class="fw-bold text-slate-800">RM{{ number_format($booking->final_amount ?? 0, 2) }}</div>
                      @php
                        $pendingPayment = $booking->payments->where('status', 'pending')->count();
                      @endphp
                      @if($pendingPayment > 0)
                        <span class="badge bg-warning-subtle text-warning-emphasis x-small px-2" style="font-size: 9px;">
                          <i class="bi bi-clock-history me-1"></i>{{ $pendingPayment }} Pending
                        </span>
                      @endif
                    </td>
                    <td>
                      @php
                        $statusClass = match($booking->status) {
                          'completed' => 'bg-success-subtle text-success',
                          'active' => 'bg-info-subtle text-info',
                          'cancelled' => 'bg-danger-subtle text-danger',
                          'confirmed' => 'bg-primary-subtle text-primary',
                          'created' => 'bg-warning-subtle text-warning',
                          default => 'bg-secondary-subtle text-secondary'
                        };
                      @endphp
                      <span class="badge rounded-pill px-3 py-2 {{ $statusClass }} fw-bold text-uppercase" style="font-size: 9px; letter-spacing: 0.3px;">
                        {{ ucfirst($booking->status ?? 'N/A') }}
                      </span>
                    </td>
                    <td class="pe-4 text-end">
                      <a href="{{ route('staff.bookings.show', $booking->booking_id) }}" class="btn btn-sm btn-white border rounded-pill px-3 fw-bold x-small shadow-sm">
                        Details <i class="bi bi-chevron-right ms-1"></i>
                      </a>
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="7" class="text-center py-5">
                      <div class="py-4">
                        <i class="bi bi-journal-x display-4 text-muted opacity-25 d-block mb-3"></i>
                        <h6 class="text-slate-700 fw-bold">No bookings found</h6>
                        <p class="text-muted small">We couldn't find any records matching your search criteria.</p>
                        <a href="{{ route('staff.bookings') }}" class="btn btn-sm btn-light border rounded-pill px-4 mt-2">Clear All Filters</a>
                      </div>
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
</div>

<style>
  .text-slate-700 { color: #334155; }
  .text-slate-800 { color: #1e293b; }
  .x-small { font-size: 11px; }
  .bg-light-subtle { background-color: #f8fafc !important; }
  
  .table > :not(caption) > * > * {
    padding: 1.25rem 0.75rem;
    border-bottom: 1px solid #f1f5f9;
  }
  
  .btn-white {
    background: #fff;
    color: #1e293b;
    border-color: #e2e8f0;
    transition: all 0.2s;
  }
  .btn-white:hover {
    background: #f8fafc;
    border-color: #cbd5e1;
    color: var(--hasta);
  }

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
@endsection