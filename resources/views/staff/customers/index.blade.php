@extends('layouts.app')
@section('title', 'Customers - Staff Dashboard')

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
          <h1 class="h3 fw-bold mb-1" style="color:#1a202c;">Customer Database</h1>
          <p class="text-muted mb-0" style="font-size: 14.5px;">Manage user accounts, verification statuses, and membership details.</p>
        </div>
        <div class="d-flex gap-2">
          <a href="{{ route('staff.customers.export', request()->query()) }}" class="btn btn-outline-success btn-sm shadow-sm px-3">
            <i class="bi bi-file-earmark-excel me-2"></i>Export List
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
      <form method="GET" action="{{ route('staff.customers') }}" class="row g-3 align-items-end">
        <div class="col-md-4">
          <label class="form-label small fw-bold text-muted text-uppercase" style="font-size: 10px;">Find Customer</label>
          <div class="input-group input-group-sm">
            <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
            <input type="text" name="search" class="form-control border-start-0 ps-0" placeholder="Name, email, phone..." value="{{ $filters['search'] ?? '' }}">
          </div>
        </div>
        <div class="col-md-2">
          <label class="form-label small fw-bold text-muted text-uppercase" style="font-size: 10px;">Verification</label>
          <select name="verification_status" class="form-select form-select-sm">
            <option value="">All Statuses</option>
            <option value="pending" {{ ($filters['verification_status'] ?? '') === 'pending' ? 'selected' : '' }}>Pending</option>
            <option value="approved" {{ ($filters['verification_status'] ?? '') === 'approved' ? 'selected' : '' }}>Approved</option>
            <option value="rejected" {{ ($filters['verification_status'] ?? '') === 'rejected' ? 'selected' : '' }}>Rejected</option>
          </select>
        </div>
        <div class="col-md-2">
          <label class="form-label small fw-bold text-muted text-uppercase" style="font-size: 10px;">Account Type</label>
          <select name="blacklisted" class="form-select form-select-sm">
            <option value="">All Accounts</option>
            <option value="1" {{ ($filters['blacklisted'] ?? '') === '1' ? 'selected' : '' }}>Blacklisted</option>
            <option value="0" {{ ($filters['blacklisted'] ?? '') === '0' ? 'selected' : '' }}>Active Only</option>
          </select>
        </div>
        <div class="col-md-4">
          <div class="d-flex gap-2">
            <button type="submit" class="btn btn-sm btn-dark px-4 flex-fill">Filter Results</button>
            <a href="{{ route('staff.customers') }}" class="btn btn-sm btn-light border px-4">Reset</a>
          </div>
        </div>
      </form>
    </div>
  </div>

  {{-- CUSTOMERS TABLE --}}
  <div class="card border-0 shadow-sm overflow-hidden">
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="bg-light">
            <tr>
              <th class="ps-4 py-3 text-uppercase text-muted fw-bold" style="font-size: 11px;">Identity</th>
              <th class="py-3 text-uppercase text-muted fw-bold" style="font-size: 11px;">Contact Info</th>
              <th class="py-3 text-uppercase text-muted fw-bold" style="font-size: 11px;">Verification</th>
              <th class="py-3 text-uppercase text-muted fw-bold" style="font-size: 11px;">Status</th>
              <th class="py-3 text-uppercase text-muted fw-bold" style="font-size: 11px;">Rental Volume</th>
              <th class="pe-4 py-3 text-uppercase text-muted fw-bold text-end" style="font-size: 11px;">Action</th>
            </tr>
          </thead>
          <tbody>
            @forelse($customers as $customer)
              <tr class="transition-all hover-bg-light">
                <td class="ps-4">
                  <div class="d-flex align-items-center gap-3 py-1">
                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center fw-bold shadow-sm" style="width: 38px; height: 38px;">
                      {{ substr($customer->full_name ?? 'C', 0, 1) }}
                    </div>
                    <div>
                      <div class="fw-bold text-dark">{{ $customer->full_name }}</div>
                      <div class="small text-muted">ID: #{{ $customer->customer_id }}</div>
                    </div>
                  </div>
                </td>
                <td>
                  <div class="small text-dark fw-medium"><i class="bi bi-envelope me-2"></i>{{ $customer->email }}</div>
                  <div class="small text-muted"><i class="bi bi-telephone me-2"></i>{{ $customer->phone ?? 'Not provided' }}</div>
                </td>
                <td>
                  @php
                    $vStatus = $customer->verification_status ?? 'pending';
                    $vClass = [
                      'approved' => 'bg-success-subtle text-success border-success-subtle',
                      'rejected' => 'bg-danger-subtle text-danger border-danger-subtle',
                      'pending' => 'bg-warning-subtle text-warning border-warning-subtle'
                    ][$vStatus];
                  @endphp
                  <span class="badge rounded-pill border px-3 py-2 {{ $vClass }}">
                    {{ ucfirst($vStatus) }}
                  </span>
                </td>
                <td>
                  @if($customer->is_blacklisted)
                    <span class="badge bg-danger rounded-pill px-3 py-2"><i class="bi bi-slash-circle me-1"></i>Blacklisted</span>
                  @else
                    <span class="badge bg-success rounded-pill px-3 py-2"><i class="bi bi-check-circle me-1"></i>Active</span>
                  @endif
                </td>
                <td>
                  <div class="fw-bold text-dark">{{ $customer->total_rental_hours ?? 0 }}h</div>
                  <div class="small text-muted">Total usage</div>
                </td>
                <td class="pe-4 text-end">
                  <a href="{{ route('staff.customers.show', $customer->customer_id) }}" class="btn btn-sm btn-white border shadow-sm fw-semibold">
                    View Profile <i class="bi bi-chevron-right ms-1 small"></i>
                  </a>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="6" class="text-center py-5 text-muted">
                  <i class="bi bi-people display-4 d-block mb-3 opacity-25"></i>
                  No customers found in the database.
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
    {{ $customers->links() }}
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
    {{ $customers->links() }}
  </div>
  </div>
</div>
</div>
@endsection
