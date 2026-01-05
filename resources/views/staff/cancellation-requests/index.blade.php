@extends('layouts.app')
@section('title', 'Cancellation Requests')

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
          <h1 class="h4 fw-800 mb-0 text-slate-800">Cancellation Requests</h1>
          <p class="text-muted mb-0" style="font-size: 13px;">Review and process customer booking cancellation requests.</p>
        </div>
      </div>

      @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm rounded-3 py-2 px-3 small mb-4 d-flex align-items-center gap-2">
          <i class="bi bi-check-circle-fill"></i>
          <span>{{ session('success') }}</span>
          <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" style="font-size: 10px;"></button>
        </div>
      @endif

      {{-- Stats Cards --}}
      <div class="row g-3 mb-4">
        <div class="col-md-2">
          <div class="card border-0 shadow-sm rounded-3 h-100">
            <div class="card-body p-3 text-center">
              <div class="text-slate-500 small fw-700 text-uppercase mb-1" style="font-size: 10px; letter-spacing: 0.5px;">Total</div>
              <div class="h4 fw-800 mb-0 text-slate-700">{{ $stats['total'] }}</div>
            </div>
          </div>
        </div>
        <div class="col-md-2">
          <div class="card border-0 shadow-sm rounded-3 h-100">
            <div class="card-body p-3 text-center">
              <div class="text-slate-500 small fw-700 text-uppercase mb-1" style="font-size: 10px; letter-spacing: 0.5px;">Pending</div>
              <div class="h4 fw-800 mb-0 text-warning">{{ $stats['pending'] }}</div>
            </div>
          </div>
        </div>
        <div class="col-md-2">
          <div class="card border-0 shadow-sm rounded-3 h-100">
            <div class="card-body p-3 text-center">
              <div class="text-slate-500 small fw-700 text-uppercase mb-1" style="font-size: 10px; letter-spacing: 0.5px;">Approved</div>
              <div class="h4 fw-800 mb-0 text-success">{{ $stats['approved'] }}</div>
            </div>
          </div>
        </div>
        <div class="col-md-2">
          <div class="card border-0 shadow-sm rounded-3 h-100">
            <div class="card-body p-3 text-center">
              <div class="text-slate-500 small fw-700 text-uppercase mb-1" style="font-size: 10px; letter-spacing: 0.5px;">Refunded</div>
              <div class="h4 fw-800 mb-0 text-info">{{ $stats['refunded'] }}</div>
            </div>
          </div>
        </div>
        <div class="col-md-2">
          <div class="card border-0 shadow-sm rounded-3 h-100">
            <div class="card-body p-3 text-center">
              <div class="text-slate-500 small fw-700 text-uppercase mb-1" style="font-size: 10px; letter-spacing: 0.5px;">Rejected</div>
              <div class="h4 fw-800 mb-0 text-danger">{{ $stats['rejected'] }}</div>
            </div>
          </div>
        </div>
      </div>

      {{-- Filters --}}
      <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-body p-2 px-3">
          <form method="GET" action="{{ route('staff.cancellation-requests') }}" class="row g-2 align-items-center">
            <div class="col-md-5">
              <div class="input-group input-group-sm">
                <span class="input-group-text border-0 bg-light text-muted"><i class="bi bi-search" style="font-size: 12px;"></i></span>
                <input type="text" name="search" class="form-control form-control-sm border-0 bg-light fw-500 rounded-end-2" placeholder="Search booking ID or customer name..." value="{{ $filters['search'] ?? '' }}">
              </div>
            </div>
            <div class="col-md-3">
              <select name="status" class="form-select form-select-sm border-0 bg-light fw-600 rounded-2 py-2">
                <option value="">All Statuses</option>
                <option value="pending" {{ ($filters['status'] ?? '') === 'pending' ? 'selected' : '' }}>PENDING</option>
                <option value="approved" {{ ($filters['status'] ?? '') === 'approved' ? 'selected' : '' }}>APPROVED</option>
                <option value="refunded" {{ ($filters['status'] ?? '') === 'refunded' ? 'selected' : '' }}>REFUNDED</option>
                <option value="rejected" {{ ($filters['status'] ?? '') === 'rejected' ? 'selected' : '' }}>REJECTED</option>
              </select>
            </div>
            <div class="col-md-2">
              <button type="submit" class="btn btn-hasta btn-sm w-100 rounded-2 fw-700 py-1">Apply</button>
            </div>
            <div class="col-md-2">
              <a href="{{ route('staff.cancellation-requests') }}" class="btn btn-sm btn-light w-100 rounded-2 fw-600 py-1">
                <i class="bi bi-x-lg me-1"></i>Clear
              </a>
            </div>
          </form>
        </div>
      </div>

      {{-- Requests Table --}}
      <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="font-size: 13px;">
              <thead class="bg-light">
                <tr>
                  <th class="ps-3 py-2 border-0 text-uppercase small fw-700 text-slate-500" style="font-size: 11px;">Request ID</th>
                  <th class="py-2 border-0 text-uppercase small fw-700 text-slate-500" style="font-size: 11px;">Booking</th>
                  <th class="py-2 border-0 text-uppercase small fw-700 text-slate-500" style="font-size: 11px;">Customer</th>
                  <th class="py-2 border-0 text-uppercase small fw-700 text-slate-500" style="font-size: 11px;">Reason</th>
                  <th class="py-2 border-0 text-uppercase small fw-700 text-slate-500" style="font-size: 11px;">Submitted</th>
                  <th class="py-2 border-0 text-uppercase small fw-700 text-slate-500" style="font-size: 11px;">Status</th>
                  <th class="pe-3 py-2 border-0 text-uppercase small fw-700 text-slate-500 text-end" style="font-size: 11px;">Action</th>
                </tr>
              </thead>
              <tbody class="border-top-0">
                @forelse($requests as $req)
                  <tr>
                    <td class="ps-3 py-2">
                      <div class="fw-700 text-slate-700">#{{ $req->request_id }}</div>
                    </td>
                    <td class="py-2">
                      <a href="{{ route('staff.bookings.show', $req->booking_id) }}" class="text-decoration-none fw-700 text-primary">
                        #{{ $req->booking_id }}
                      </a>
                      @if($req->booking && $req->booking->car)
                        <div class="text-slate-500" style="font-size: 11px;">{{ $req->booking->car->brand }} {{ $req->booking->car->model }}</div>
                      @endif
                    </td>
                    <td class="py-2">
                      @if($req->customer)
                        <div class="fw-700 text-slate-700">{{ $req->customer->full_name }}</div>
                        <div class="text-slate-500" style="font-size: 11px;">{{ $req->customer->email }}</div>
                      @else
                        <span class="text-muted small">N/A</span>
                      @endif
                    </td>
                    <td class="py-2">
                      <span class="badge bg-light text-slate-600 border rounded-pill px-2 py-1 fw-700" style="font-size: 10px;">
                        {{ $req->reason_label }}
                      </span>
                    </td>
                    <td class="py-2">
                      <div class="fw-600 text-slate-700">{{ $req->created_at->format('d M Y') }}</div>
                      <div class="text-slate-400" style="font-size: 11px;">{{ $req->created_at->format('H:i') }}</div>
                    </td>
                    <td class="py-2">
                      @php
                        $statusColors = [
                          'pending' => ['bg' => '#fffbeb', 'text' => '#d97706', 'border' => '#fef3c7'],
                          'approved' => ['bg' => '#f0fdf4', 'text' => '#16a34a', 'border' => '#dcfce7'],
                          'refunded' => ['bg' => '#eff6ff', 'text' => '#2563eb', 'border' => '#dbeafe'],
                          'rejected' => ['bg' => '#fee2e2', 'text' => '#dc2626', 'border' => '#fecaca']
                        ];
                        $colors = $statusColors[$req->status] ?? $statusColors['pending'];
                      @endphp
                      <span class="badge rounded-pill px-2 py-1 fw-700" style="background: {{ $colors['bg'] }}; color: {{ $colors['text'] }}; border: 1px solid {{ $colors['border'] }}; font-size: 10px;">
                        {{ strtoupper($req->status) }}
                      </span>
                    </td>
                    <td class="pe-3 py-2 text-end">
                      <a href="{{ route('staff.cancellation-requests.show', $req->request_id) }}" class="btn btn-sm btn-light rounded-2 px-2 py-1" title="View Details">
                        <i class="bi bi-eye text-slate-600" style="font-size: 12px;"></i>
                      </a>
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="7" class="text-center py-4">
                      <div class="text-muted mb-2"><i class="bi bi-inbox fs-1"></i></div>
                      <div class="fw-600 text-slate-500">No cancellation requests found</div>
                      <small class="text-muted">Adjust your filters to see more results</small>
                    </td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>

      {{-- Pagination --}}
      <div class="mt-3 d-flex justify-content-center">
        {{ $requests->links() }}
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
