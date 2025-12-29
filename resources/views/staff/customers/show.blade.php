@extends('layouts.app')
@section('title', 'Customer Details - Staff Dashboard')

@section('content')
<div class="d-flex" style="min-height: calc(100vh - 60px);">
  @include('staff._nav')
  <div class="flex-fill" style="background: var(--bg);">
    <div class="container-fluid px-4 px-md-5" style="padding-top: 32px; padding-bottom: 48px;">
  <div class="mb-4">
    <a href="{{ route('staff.customers') }}" class="text-decoration-none small">&larr; Back to customers</a>
    <h1 class="h3 fw-bold mt-2 mb-1" style="color:#333;">Customer #{{ $customer->customer_id }}: {{ $customer->full_name }}</h1>
  </div>

  @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      {{ session('success') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  @endif

  <div class="row g-3">
    {{-- CUSTOMER INFO --}}
    <div class="col-lg-8">
      <div class="card border-0 shadow-soft mb-3">
        <div class="card-body p-4">
          <h5 class="fw-bold mb-3">Customer Information</h5>
          <div class="row g-3">
            <div class="col-md-6">
              <div class="small text-muted">Full Name</div>
              <div class="fw-semibold">{{ $customer->full_name }}</div>
            </div>
            <div class="col-md-6">
              <div class="small text-muted">Email</div>
              <div class="fw-semibold">{{ $customer->email }}</div>
            </div>
            <div class="col-md-6">
              <div class="small text-muted">Phone</div>
              <div class="fw-semibold">{{ $customer->phone ?? 'N/A' }}</div>
            </div>
            <div class="col-md-6">
              <div class="small text-muted">UTM Role</div>
              <div class="fw-semibold">{{ $customer->utm_role ?? 'N/A' }}</div>
            </div>
            <div class="col-md-6">
              <div class="small text-muted">Verification Status</div>
              <div>
                <span class="badge 
                  {{ $customer->verification_status === 'approved' ? 'bg-success' : ($customer->verification_status === 'rejected' ? 'bg-danger' : 'bg-warning') }}">
                  {{ ucfirst($customer->verification_status ?? 'pending') }}
                </span>
                @if($customer->verified_at)
                  <small class="text-muted d-block">Verified on {{ $customer->verified_at->format('d M Y') }}</small>
                @endif
              </div>
            </div>
            <div class="col-md-6">
              <div class="small text-muted">Blacklist Status</div>
              <div>
                @if($customer->is_blacklisted)
                  <span class="badge bg-danger">Blacklisted</span>
                  @if($customer->blacklist_reason)
                    <small class="text-muted d-block">Reason: {{ $customer->blacklist_reason }}</small>
                  @endif
                  @if($customer->blacklist_since)
                    <small class="text-muted d-block">Since: {{ $customer->blacklist_since->format('d M Y') }}</small>
                  @endif
                @else
                  <span class="badge bg-success">Active</span>
                @endif
              </div>
            </div>
            <div class="col-md-6">
              <div class="small text-muted">Deposit Balance</div>
              <div class="fw-semibold">RM {{ number_format($customer->deposit_balance ?? 0, 2) }}</div>
            </div>
            <div class="col-md-6">
              <div class="small text-muted">Total Rental Hours</div>
              <div class="fw-semibold">{{ $customer->total_rental_hours ?? 0 }} hours</div>
            </div>
            <div class="col-md-6">
              <div class="small text-muted">Loyalty Stamps</div>
              <div class="fw-semibold">{{ $customer->total_stamps ?? 0 }} stamps</div>
            </div>
          </div>
        </div>
      </div>

      <div class="card border-0 shadow-soft">
        <div class="card-body p-4">
          <h5 class="fw-bold mb-3">Booking History</h5>
          @if($customer->bookings && $customer->bookings->count() > 0)
            <div class="table-responsive">
              <table class="table table-sm">
                <thead>
                  <tr>
                    <th>Booking ID</th>
                    <th>Car</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Amount</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($customer->bookings->take(10) as $booking)
                    <tr>
                      <td>#{{ $booking->booking_id }}</td>
                      <td>{{ $booking->car->brand ?? 'N/A' }} {{ $booking->car->model ?? '' }}</td>
                      <td>{{ $booking->start_datetime?->format('d M Y') ?? 'N/A' }}</td>
                      <td><span class="badge bg-secondary">{{ ucfirst($booking->status ?? 'N/A') }}</span></td>
                      <td>RM {{ number_format($booking->final_amount ?? 0, 2) }}</td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          @else
            <p class="text-muted mb-0">No bookings yet.</p>
          @endif
        </div>
      </div>
    </div>

    {{-- ACTIONS --}}
    <div class="col-lg-4">
      <div class="card border-0 shadow-soft mb-3">
        <div class="card-body p-4">
          <h5 class="fw-bold mb-3">Verification</h5>
          <form method="POST" action="{{ route('staff.customers.verify', $customer->customer_id) }}">
            @csrf
            @method('PATCH')
            <div class="mb-3">
              <select name="verification_status" class="form-select" required>
                <option value="pending" {{ $customer->verification_status === 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="approved" {{ $customer->verification_status === 'approved' ? 'selected' : '' }}>Approved</option>
                <option value="rejected" {{ $customer->verification_status === 'rejected' ? 'selected' : '' }}>Rejected</option>
              </select>
            </div>
            <button type="submit" class="btn btn-hasta w-100">Update Verification</button>
          </form>
        </div>
      </div>

      <div class="card border-0 shadow-soft">
        <div class="card-body p-4">
          <h5 class="fw-bold mb-3">Blacklist Management</h5>
          <form method="POST" action="{{ route('staff.customers.blacklist', $customer->customer_id) }}">
            @csrf
            @method('PATCH')
            <div class="mb-3">
              <select name="is_blacklisted" class="form-select" required>
                <option value="0" {{ !$customer->is_blacklisted ? 'selected' : '' }}>Not Blacklisted</option>
                <option value="1" {{ $customer->is_blacklisted ? 'selected' : '' }}>Blacklisted</option>
              </select>
            </div>
            <div class="mb-3">
              <label class="form-label small">Blacklist Reason (if blacklisting)</label>
              <textarea name="blacklist_reason" class="form-control" rows="3" placeholder="Reason for blacklisting...">{{ $customer->blacklist_reason }}</textarea>
            </div>
            <button type="submit" class="btn btn-outline-danger w-100">Update Blacklist</button>
          </form>
        </div>
      </div>
    </div>
  </div>
  </div>
</div>
</div>
@endsection
