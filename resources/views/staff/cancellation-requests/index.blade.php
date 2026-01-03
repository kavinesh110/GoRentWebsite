@extends('layouts.app')
@section('title', 'Cancellation Requests - Staff Dashboard')

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
          <h1 class="h3 fw-bold mb-1" style="color:#333;">Cancellation Requests</h1>
          <p class="text-muted mb-0" style="font-size: 14px;">Review and process customer booking cancellation requests.</p>
        </div>
      </div>

      @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
          {{ session('success') }}
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
      @endif

      {{-- Stats Cards --}}
      <div class="row g-3 mb-4">
        <div class="col-md-2">
          <div class="card border-0 shadow-soft h-100">
            <div class="card-body text-center">
              <div class="h4 fw-bold mb-1">{{ $stats['total'] }}</div>
              <div class="small text-muted">Total Requests</div>
            </div>
          </div>
        </div>
        <div class="col-md-2">
          <div class="card border-0 shadow-soft h-100 bg-warning bg-opacity-10">
            <div class="card-body text-center">
              <div class="h4 fw-bold mb-1 text-warning">{{ $stats['pending'] }}</div>
              <div class="small text-muted">Pending</div>
            </div>
          </div>
        </div>
        <div class="col-md-2">
          <div class="card border-0 shadow-soft h-100 bg-success bg-opacity-10">
            <div class="card-body text-center">
              <div class="h4 fw-bold mb-1 text-success">{{ $stats['approved'] }}</div>
              <div class="small text-muted">Approved</div>
            </div>
          </div>
        </div>
        <div class="col-md-2">
          <div class="card border-0 shadow-soft h-100 bg-info bg-opacity-10">
            <div class="card-body text-center">
              <div class="h4 fw-bold mb-1 text-info">{{ $stats['refunded'] }}</div>
              <div class="small text-muted">Refunded</div>
            </div>
          </div>
        </div>
        <div class="col-md-2">
          <div class="card border-0 shadow-soft h-100 bg-danger bg-opacity-10">
            <div class="card-body text-center">
              <div class="h4 fw-bold mb-1 text-danger">{{ $stats['rejected'] }}</div>
              <div class="small text-muted">Rejected</div>
            </div>
          </div>
        </div>
      </div>

      {{-- Filters --}}
      <div class="card border-0 shadow-soft mb-4">
        <div class="card-body p-3">
          <form method="GET" action="{{ route('staff.cancellation-requests') }}" class="row g-2 align-items-end">
            <div class="col-md-4">
              <label class="form-label small">Search</label>
              <input type="text" name="search" class="form-control form-control-sm" placeholder="Booking ID, customer name..." value="{{ $filters['search'] ?? '' }}">
            </div>
            <div class="col-md-3">
              <label class="form-label small">Status</label>
              <select name="status" class="form-select form-select-sm">
                <option value="">All Statuses</option>
                <option value="pending" {{ ($filters['status'] ?? '') === 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="approved" {{ ($filters['status'] ?? '') === 'approved' ? 'selected' : '' }}>Approved</option>
                <option value="refunded" {{ ($filters['status'] ?? '') === 'refunded' ? 'selected' : '' }}>Refunded</option>
                <option value="rejected" {{ ($filters['status'] ?? '') === 'rejected' ? 'selected' : '' }}>Rejected</option>
              </select>
            </div>
            <div class="col-md-2">
              <button type="submit" class="btn btn-sm btn-hasta w-100">Filter</button>
            </div>
            <div class="col-md-2">
              <a href="{{ route('staff.cancellation-requests') }}" class="btn btn-sm btn-outline-secondary w-100">Clear</a>
            </div>
          </form>
        </div>
      </div>

      {{-- Requests Table --}}
      <div class="card border-0 shadow-soft">
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
              <thead class="bg-light">
                <tr>
                  <th class="ps-4">Request ID</th>
                  <th>Booking</th>
                  <th>Customer</th>
                  <th>Reason</th>
                  <th>Submitted</th>
                  <th>Status</th>
                  <th class="pe-4">Action</th>
                </tr>
              </thead>
              <tbody>
                @forelse($requests as $req)
                  <tr>
                    <td class="ps-4">
                      <span class="fw-semibold">#{{ $req->request_id }}</span>
                    </td>
                    <td>
                      <a href="{{ route('staff.bookings.show', $req->booking_id) }}" class="text-decoration-none">
                        Booking #{{ $req->booking_id }}
                      </a>
                      @if($req->booking && $req->booking->car)
                        <div class="small text-muted">{{ $req->booking->car->brand }} {{ $req->booking->car->model }}</div>
                      @endif
                    </td>
                    <td>
                      @if($req->customer)
                        <div class="fw-semibold">{{ $req->customer->full_name }}</div>
                        <div class="small text-muted">{{ $req->customer->email }}</div>
                      @else
                        <span class="text-muted">N/A</span>
                      @endif
                    </td>
                    <td>
                      <span class="badge bg-secondary">{{ $req->reason_label }}</span>
                    </td>
                    <td>
                      <div>{{ $req->created_at->format('d M Y') }}</div>
                      <div class="small text-muted">{{ $req->created_at->format('H:i') }}</div>
                    </td>
                    <td>
                      <span class="badge 
                        {{ $req->status === 'pending' ? 'bg-warning text-dark' : '' }}
                        {{ $req->status === 'approved' ? 'bg-success' : '' }}
                        {{ $req->status === 'refunded' ? 'bg-info' : '' }}
                        {{ $req->status === 'rejected' ? 'bg-danger' : '' }}
                      ">
                        {{ ucfirst($req->status) }}
                      </span>
                    </td>
                    <td class="pe-4">
                      <a href="{{ route('staff.cancellation-requests.show', $req->request_id) }}" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-eye"></i> View
                      </a>
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="7" class="text-center py-5 text-muted">
                      <i class="bi bi-inbox" style="font-size: 48px;"></i>
                      <p class="mt-2 mb-0">No cancellation requests found.</p>
                    </td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>

      {{-- Pagination --}}
      <div class="mt-4">
        {{ $requests->links() }}
      </div>

    </div>
  </div>
</div>
@endsection
