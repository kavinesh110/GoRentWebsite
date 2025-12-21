@extends('layouts.app')
@section('title', 'Customers - Staff Dashboard')

@section('content')
<div class="d-flex" style="min-height: calc(100vh - 60px);">
  @include('staff._nav')
  <div class="flex-fill" style="background: var(--bg);">
    <div class="container-custom" style="padding: 32px 24px 48px;">
  <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
    <div>
      <h1 class="h3 fw-bold mb-1" style="color:#333;">Customer Management</h1>
      <p class="text-muted mb-0" style="font-size: 14px;">Verify accounts, manage blacklist and view customer rental history.</p>
    </div>
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
      <form method="GET" action="{{ route('staff.customers') }}" class="row g-2 align-items-end">
        <div class="col-md-4">
          <label class="form-label small">Search</label>
          <input type="text" name="search" class="form-control form-control-sm" placeholder="Name, email..." value="{{ $filters['search'] ?? '' }}">
        </div>
        <div class="col-md-3">
          <label class="form-label small">Verification</label>
          <select name="verification_status" class="form-select form-select-sm">
            <option value="">All</option>
            <option value="pending" {{ ($filters['verification_status'] ?? '') === 'pending' ? 'selected' : '' }}>Pending</option>
            <option value="approved" {{ ($filters['verification_status'] ?? '') === 'approved' ? 'selected' : '' }}>Approved</option>
            <option value="rejected" {{ ($filters['verification_status'] ?? '') === 'rejected' ? 'selected' : '' }}>Rejected</option>
          </select>
        </div>
        <div class="col-md-2">
          <label class="form-label small">Blacklist</label>
          <select name="blacklisted" class="form-select form-select-sm">
            <option value="">All</option>
            <option value="1" {{ ($filters['blacklisted'] ?? '') === '1' ? 'selected' : '' }}>Blacklisted</option>
            <option value="0" {{ ($filters['blacklisted'] ?? '') === '0' ? 'selected' : '' }}>Not Blacklisted</option>
          </select>
        </div>
        <div class="col-md-1">
          <button type="submit" class="btn btn-sm btn-hasta w-100">Filter</button>
        </div>
        <div class="col-md-2">
          <a href="{{ route('staff.customers') }}" class="btn btn-sm btn-outline-secondary w-100">Clear</a>
        </div>
      </form>
    </div>
  </div>

  {{-- CUSTOMERS TABLE --}}
  <div class="card border-0 shadow-soft">
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-hover mb-0">
          <thead class="table-light">
            <tr>
              <th>Customer ID</th>
              <th>Name</th>
              <th>Email</th>
              <th>Phone</th>
              <th>Verification</th>
              <th>Blacklist</th>
              <th>Total Hours</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            @forelse($customers as $customer)
              <tr>
                <td><strong>#{{ $customer->customer_id }}</strong></td>
                <td>{{ $customer->full_name }}</td>
                <td>{{ $customer->email }}</td>
                <td>{{ $customer->phone ?? 'N/A' }}</td>
                <td>
                  <span class="badge 
                    {{ $customer->verification_status === 'approved' ? 'bg-success' : ($customer->verification_status === 'rejected' ? 'bg-danger' : 'bg-warning') }}">
                    {{ ucfirst($customer->verification_status ?? 'pending') }}
                  </span>
                </td>
                <td>
                  @if($customer->is_blacklisted)
                    <span class="badge bg-danger">Blacklisted</span>
                  @else
                    <span class="badge bg-success">Active</span>
                  @endif
                </td>
                <td>{{ $customer->total_rental_hours ?? 0 }}h</td>
                <td>
                  <a href="{{ route('staff.customers.show', $customer->customer_id) }}" class="btn btn-sm btn-outline-primary">View</a>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="8" class="text-center py-4 text-muted">No customers found.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>

  {{-- PAGINATION --}}
  <div class="mt-4">
    {{ $customers->links() }}
  </div>
  </div>
</div>
</div>
@endsection
