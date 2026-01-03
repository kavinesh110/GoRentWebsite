@extends('layouts.app')
@section('title', 'Customer Details - Staff Dashboard')

@section('content')
<div class="d-flex" style="min-height: calc(100vh - 60px);">
  @include('staff._nav')
  <div class="flex-fill" style="background: #f8fafc;">
    <div class="container-fluid px-4 px-md-5" style="padding-top: 32px; padding-bottom: 48px;">
  <div class="mb-4">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb mb-2">
        <li class="breadcrumb-item"><a href="{{ route('staff.customers') }}" class="text-decoration-none">Customers</a></li>
        <li class="breadcrumb-item active">#{{ $customer->customer_id }}</li>
      </ol>
    </nav>
    <div class="d-flex align-items-center gap-3">
      <h1 class="h3 fw-bold mb-0" style="color:#1a202c;">{{ $customer->full_name }}</h1>
      @if($customer->is_blacklisted)
        <span class="badge bg-danger rounded-pill px-3 py-2 shadow-sm"><i class="bi bi-slash-circle me-1"></i>Blacklisted</span>
      @else
        <span class="badge bg-success rounded-pill px-3 py-2 shadow-sm"><i class="bi bi-check-circle me-1"></i>Active Account</span>
      @endif
    </div>
  </div>

  @if(session('success'))
    <div class="alert alert-success border-0 shadow-sm d-flex align-items-center gap-2 mb-4">
      <i class="bi bi-check-circle-fill"></i>
      <div>{{ session('success') }}</div>
      <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
    </div>
  @endif

  <div class="row g-4">
    {{-- CUSTOMER INFO --}}
    <div class="col-lg-8">
      <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white border-0 pt-4 px-4">
          <h5 class="fw-bold mb-0"><i class="bi bi-person-badge me-2 text-primary"></i>Profile Details</h5>
        </div>
        <div class="card-body p-4 pt-2">
          <div class="row g-4">
            <div class="col-md-6">
              <label class="x-small">Full Name</label>
              <div class="fw-bold text-dark">{{ $customer->full_name }}</div>
            </div>
            <div class="col-md-6">
              <label class="x-small">Email Address</label>
              <div class="fw-bold text-dark">{{ $customer->email }}</div>
            </div>
            <div class="col-md-6">
              <label class="x-small">Phone Number</label>
              <div class="fw-bold text-dark">{{ $customer->phone ?? 'Not provided' }}</div>
            </div>
            <div class="col-md-6">
              <label class="x-small">UTM Affiliation</label>
              <div class="fw-bold text-dark">
                <span class="badge bg-light text-dark border">{{ strtoupper($customer->utm_role ?? 'General') }}</span>
              </div>
            </div>
            <div class="col-md-6">
              <label class="x-small">Verification</label>
              <div>
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
                @if($customer->verified_at)
                  <div class="x-small text-muted mt-1"><i class="bi bi-calendar-check me-1"></i>Verified on {{ $customer->verified_at->format('d M Y') }}</div>
                @endif
              </div>
            </div>
            <div class="col-md-6">
              <label class="x-small">Residential College (Kolej)</label>
              <div class="fw-bold text-dark">{{ $customer->college_name ?? 'Not provided' }}</div>
            </div>
          </div>
        </div>
      </div>

      {{-- STATS GRID --}}
      <div class="row g-3 mb-4">
        <div class="col-md-4">
          <div class="card border-0 shadow-sm bg-white p-3 h-100">
            <div class="small text-muted fw-bold text-uppercase mb-1" style="font-size: 10px;">Deposit Balance</div>
            <div class="h4 fw-bold mb-0 text-primary">RM {{ number_format($customer->deposit_balance ?? 0, 2) }}</div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card border-0 shadow-sm bg-white p-3 h-100">
            <div class="small text-muted fw-bold text-uppercase mb-1" style="font-size: 10px;">Rental Volume</div>
            <div class="h4 fw-bold mb-0 text-dark">{{ $customer->total_rental_hours ?? 0 }} <span class="small fw-normal">Hours</span></div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card border-0 shadow-sm bg-white p-3 h-100">
            <div class="small text-muted fw-bold text-uppercase mb-1" style="font-size: 10px;">Loyalty Progress</div>
            <div class="h4 fw-bold mb-0 text-success">{{ $customer->total_stamps ?? 0 }} <span class="small fw-normal">Stamps</span></div>
          </div>
        </div>
      </div>

      <div class="card border-0 shadow-sm overflow-hidden">
        <div class="card-header bg-white border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
          <h5 class="fw-bold mb-0"><i class="bi bi-clock-history me-2 text-dark"></i>Recent Bookings</h5>
        </div>
        <div class="card-body p-0">
          @if($customer->bookings && $customer->bookings->count() > 0)
            <div class="table-responsive">
              <table class="table table-hover align-middle mb-0 small">
                <thead class="bg-light">
                  <tr>
                    <th class="ps-4">Booking ID</th>
                    <th>Vehicle</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th class="text-end pe-4">Amount</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($customer->bookings->take(10) as $booking)
                    <tr>
                      <td class="ps-4 fw-bold text-dark">#{{ $booking->booking_id }}</td>
                      <td>{{ $booking->car->brand ?? 'N/A' }} {{ $booking->car->model ?? '' }}</td>
                      <td class="text-muted">{{ $booking->start_datetime?->format('d M Y') ?? 'N/A' }}</td>
                      <td>
                        <span class="badge rounded-pill bg-light text-dark border px-3">
                          {{ ucfirst($booking->status ?? 'N/A') }}
                        </span>
                      </td>
                      <td class="text-end pe-4 fw-bold">RM {{ number_format($booking->final_amount ?? 0, 2) }}</td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          @else
            <div class="text-center py-5">
              <i class="bi bi-calendar-x text-muted display-6 d-block mb-2 opacity-25"></i>
              <p class="text-muted mb-0">This customer has no rental history yet.</p>
            </div>
          @endif
        </div>
      </div>
    </div>

    {{-- ACTIONS --}}
    <div class="col-lg-4">
      {{-- Verification Card --}}
      <div class="card border-0 shadow-sm mb-4 shadow-soft">
        <div class="card-header bg-dark text-white border-0 py-3 px-4">
          <h6 class="fw-bold mb-0">Identity Verification</h6>
        </div>
        <div class="card-body p-4">
          <form method="POST" action="{{ route('staff.customers.verify', $customer->customer_id) }}">
            @csrf
            @method('PATCH')
            <div class="mb-3">
              <label class="x-small">Current Status</label>
              <select name="verification_status" class="form-select border-2 shadow-none" required>
                <option value="pending" {{ $customer->verification_status === 'pending' ? 'selected' : '' }}>Pending Approval</option>
                <option value="approved" {{ $customer->verification_status === 'approved' ? 'selected' : '' }}>Verified / Approved</option>
                <option value="rejected" {{ $customer->verification_status === 'rejected' ? 'selected' : '' }}>Rejected</option>
              </select>
            </div>
            <button type="submit" class="btn btn-hasta w-100 py-2 fw-bold shadow-sm">
              <i class="bi bi-shield-check me-2"></i>Update Verification
            </button>
          </form>
        </div>
      </div>

      {{-- Blacklist Management --}}
      <div class="card border-0 shadow-sm border-danger-subtle">
        <div class="card-header bg-danger-subtle text-danger border-0 py-3 px-4">
          <h6 class="fw-bold mb-0">Blacklist Control</h6>
        </div>
        <div class="card-body p-4 pt-3">
          <form method="POST" action="{{ route('staff.customers.blacklist', $customer->customer_id) }}">
            @csrf
            @method('PATCH')
            <div class="mb-3">
              <label class="x-small">Account Access</label>
              <select name="is_blacklisted" class="form-select border-danger-subtle shadow-none" required>
                <option value="0" {{ !$customer->is_blacklisted ? 'selected' : '' }}>Active Access</option>
                <option value="1" {{ $customer->is_blacklisted ? 'selected' : '' }}>Restrict Access (Blacklist)</option>
              </select>
            </div>
            <div class="mb-3">
              <label class="x-small">Incident Description / Reason</label>
              <textarea name="blacklist_reason" class="form-control border-danger-subtle" rows="3" placeholder="Provide details for blacklisting...">{{ $customer->blacklist_reason }}</textarea>
            </div>
            <button type="submit" class="btn btn-outline-danger w-100 fw-bold">
              Update Restrictions
            </button>
          </form>
          @if($customer->is_blacklisted && $customer->blacklist_since)
            <div class="mt-3 x-small text-danger text-center fw-bold">
              <i class="bi bi-info-circle me-1"></i> Restricted since {{ $customer->blacklist_since->format('d M Y, H:i') }}
            </div>
          @endif
        </div>
      </div>
    </div>
  </div>
  </div>
</div>

<style>
  .shadow-soft { box-shadow: 0 10px 20px rgba(0,0,0,0.05) !important; }
  .x-small { font-size: 10px; font-weight: 700; text-transform: uppercase; color: #718096; display: block; margin-bottom: 4px; }
  .btn-hasta { background-color: var(--hasta); color: #fff; border: none; }
  .btn-hasta:hover { background-color: var(--hasta-darker); color: #fff; }
</style>
  </div>
</div>
</div>
@endsection
