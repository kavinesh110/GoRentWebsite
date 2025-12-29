@extends('layouts.app')
@section('title', 'Staff Dashboard - Hasta GoRent')

@section('content')
<div class="d-flex" style="min-height: calc(100vh - 60px);">
  @include('staff._nav')
  <div class="flex-fill" style="background: var(--bg);">
    <div class="container-fluid px-4 px-md-5" style="padding-top: 32px; padding-bottom: 48px;">
  <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
    <div>
      <h1 class="h3 fw-bold mb-1" style="color:#333;">Hasta Staff Dashboard</h1>
      <p class="text-muted mb-0" style="font-size: 14px;">Monitor cars, bookings and customers for Hasta Travels &amp; Tours at UTM.</p>
    </div>
    <div class="text-end small text-muted">
      Logged in as <strong>{{ session('auth_name') }}</strong>
    </div>
  </div>

  {{-- TOP STATS --}}
  <div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
      <div class="card border-0 shadow-soft h-100">
        <div class="card-body py-3 px-3">
          <div class="small text-muted text-uppercase mb-1">Total cars</div>
          <div class="h4 fw-bold mb-0">{{ $stats['totalCars'] }}</div>
          <div class="small text-muted mt-1">
            <span class="text-success">{{ $stats['availableCars'] }} available</span> • 
            <span class="text-warning">{{ $stats['carsInUse'] }} in use</span> • 
            <span class="text-danger">{{ $stats['carsInMaintenance'] }} maintenance</span>
          </div>
        </div>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="card border-0 shadow-soft h-100 {{ $stats['pendingBookings'] > 0 ? 'border-warning' : '' }}">
        <div class="card-body py-3 px-3">
          <div class="small text-muted text-uppercase mb-1">Pending bookings</div>
          <div class="h4 fw-bold mb-0 {{ $stats['pendingBookings'] > 0 ? 'text-warning' : '' }}">{{ $stats['pendingBookings'] }}</div>
          <div class="small text-muted mt-1">{{ $stats['activeBookings'] }} active</div>
        </div>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="card border-0 shadow-soft h-100 {{ $stats['pendingVerifications'] > 0 ? 'border-info' : '' }}">
        <div class="card-body py-3 px-3">
          <div class="small text-muted text-uppercase mb-1">Pending verifications</div>
          <div class="h4 fw-bold mb-0 {{ $stats['pendingVerifications'] > 0 ? 'text-info' : '' }}">{{ $stats['pendingVerifications'] }}</div>
          <div class="small text-muted mt-1">{{ $stats['totalCustomers'] }} total customers</div>
        </div>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="card border-0 shadow-soft h-100">
        <div class="card-body py-3 px-3">
          <div class="small text-muted text-uppercase mb-1">Completed bookings</div>
          <div class="h4 fw-bold mb-0">{{ $stats['completedBookings'] }}</div>
          <div class="small text-muted mt-1">{{ $stats['blacklistedCustomers'] }} blacklisted</div>
        </div>
      </div>
    </div>
  </div>

  {{-- REVENUE STATS --}}
  <div class="row g-3 mb-4">
    <div class="col-md-4">
      <div class="card border-0 shadow-soft h-100" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
        <div class="card-body py-3 px-3">
          <div class="small text-white-50 text-uppercase mb-1">Total Revenue</div>
          <div class="h4 fw-bold mb-0">RM {{ number_format($stats['totalRevenue'] ?? 0, 2) }}</div>
          <div class="small text-white-50 mt-1">All verified rental payments</div>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card border-0 shadow-soft h-100" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white;">
        <div class="card-body py-3 px-3">
          <div class="small text-white-50 text-uppercase mb-1">This Month Revenue</div>
          <div class="h4 fw-bold mb-0">RM {{ number_format($stats['monthlyRevenue'] ?? 0, 2) }}</div>
          <div class="small text-white-50 mt-1">{{ now()->format('F Y') }}</div>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card border-0 shadow-soft h-100" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color: white;">
        <div class="card-body py-3 px-3">
          <div class="small text-white-50 text-uppercase mb-1">Total Deposits</div>
          <div class="h4 fw-bold mb-0">RM {{ number_format($stats['totalDeposits'] ?? 0, 2) }}</div>
          <div class="small text-white-50 mt-1">Verified deposit payments</div>
        </div>
      </div>
    </div>
  </div>

  <div class="row g-3">
    {{-- PENDING ACTIONS --}}
    @if($stats['pendingBookings'] > 0 || $stats['pendingVerifications'] > 0)
    <div class="col-12">
      <div class="alert alert-info d-flex justify-content-between align-items-center">
        <div>
          <strong>Action Required:</strong>
          @if($stats['pendingBookings'] > 0)
            <span>{{ $stats['pendingBookings'] }} booking(s) awaiting confirmation</span>
          @endif
          @if($stats['pendingBookings'] > 0 && $stats['pendingVerifications'] > 0)
            <span> • </span>
          @endif
          @if($stats['pendingVerifications'] > 0)
            <span>{{ $stats['pendingVerifications'] }} customer(s) awaiting verification</span>
          @endif
        </div>
        <div>
          @if($stats['pendingBookings'] > 0)
            <a href="{{ route('staff.bookings') }}?status=created" class="btn btn-sm btn-light">View Bookings</a>
          @endif
          @if($stats['pendingVerifications'] > 0)
            <a href="{{ route('staff.customers') }}?verification_status=pending" class="btn btn-sm btn-light">View Customers</a>
          @endif
        </div>
      </div>
    </div>
    @endif

    {{-- QUICK ACTIONS --}}
    <div class="col-lg-7">
      <div class="card border-0 shadow-soft h-100">
        <div class="card-body p-3 p-md-4">
          <h5 class="fw-bold mb-3">Quick actions</h5>
          <div class="row g-2">
            <div class="col-sm-6">
              <a href="{{ route('staff.cars') }}" class="btn w-100 text-start border bg-white d-flex align-items-center justify-content-between">
                <div>
                  <div class="fw-semibold">Manage cars</div>
                  <div class="small text-muted">Update status, mileage, locations &amp; photos.</div>
                </div>
                <span class="ms-2">&rarr;</span>
              </a>
            </div>
            <div class="col-sm-6">
              <a href="{{ route('staff.bookings') }}" class="btn w-100 text-start border bg-white d-flex align-items-center justify-content-between">
                <div>
                  <div class="fw-semibold">View bookings</div>
                  <div class="small text-muted">Track deposit-first bookings &amp; payments.</div>
                </div>
                <span class="ms-2">&rarr;</span>
              </a>
            </div>
            <div class="col-sm-6">
              <a href="{{ route('staff.customers') }}" class="btn w-100 text-start border bg-white d-flex align-items-center justify-content-between">
                <div>
                  <div class="fw-semibold">Manage customers</div>
                  <div class="small text-muted">Verify accounts, manage blacklist &amp; view history.</div>
                </div>
                <span class="ms-2">&rarr;</span>
              </a>
            </div>
            <div class="col-sm-6">
              <a href="{{ route('staff.customers') }}" class="btn w-100 text-start border bg-white d-flex align-items-center justify-content-between">
                <div>
                  <div class="fw-semibold">Manage customers</div>
                  <div class="small text-muted">Verify accounts, manage blacklist &amp; view history.</div>
                </div>
                <span class="ms-2">&rarr;</span>
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>

    {{-- SUMMARY PANELS --}}
    <div class="col-lg-5">
      {{-- Recent Bookings --}}
      @if($recentBookings->count() > 0)
      <div class="card border-0 shadow-soft mb-3">
        <div class="card-body p-3 p-md-4">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="fw-bold mb-0">Recent Bookings</h6>
            <a href="{{ route('staff.bookings') }}" class="small text-decoration-none">View all &rarr;</a>
          </div>
          <div class="list-group list-group-flush">
            @foreach($recentBookings as $booking)
              <div class="list-group-item px-0 py-2 border-0 border-bottom">
                <div class="d-flex justify-content-between align-items-start">
                  <div class="flex-grow-1">
                    <div class="fw-semibold small">#{{ $booking->booking_id }}</div>
                    <div class="small text-muted">{{ $booking->car->brand ?? 'N/A' }} {{ $booking->car->model ?? '' }}</div>
                    <div class="small text-muted">{{ $booking->customer->full_name ?? 'N/A' }}</div>
                  </div>
                  <span class="badge 
                    {{ $booking->status === 'created' ? 'bg-warning' : ($booking->status === 'active' ? 'bg-info' : 'bg-secondary') }}">
                    {{ ucfirst($booking->status) }}
                  </span>
                </div>
              </div>
            @endforeach
          </div>
        </div>
      </div>
      @endif

      {{-- Pending Verifications --}}
      @if($pendingCustomers->count() > 0)
      <div class="card border-0 shadow-soft mb-3">
        <div class="card-body p-3 p-md-4">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="fw-bold mb-0">Pending Verifications</h6>
            <a href="{{ route('staff.customers') }}?verification_status=pending" class="small text-decoration-none">View all &rarr;</a>
          </div>
          <div class="list-group list-group-flush">
            @foreach($pendingCustomers as $customer)
              <div class="list-group-item px-0 py-2 border-0 border-bottom">
                <div class="d-flex justify-content-between align-items-start">
                  <div class="flex-grow-1">
                    <div class="fw-semibold small">{{ $customer->full_name }}</div>
                    <div class="small text-muted">{{ $customer->email }}</div>
                    <div class="small text-muted">{{ $customer->created_at->diffForHumans() }}</div>
                  </div>
                  <a href="{{ route('staff.customers.show', $customer->customer_id) }}" class="btn btn-sm btn-outline-primary">Verify</a>
                </div>
              </div>
            @endforeach
          </div>
        </div>
      </div>
      @endif

      <div class="card border-0 shadow-soft">
        <div class="card-body p-3 p-md-4">
          <h6 class="fw-bold mb-2">Today at a glance</h6>
          <ul class="list-unstyled small mb-0">
            <li class="d-flex justify-content-between mb-1">
              <span>Cars available</span>
              <span class="fw-semibold">{{ $stats['availableCars'] }}</span>
            </li>
            <li class="d-flex justify-content-between mb-1">
              <span>Active bookings</span>
              <span class="fw-semibold">{{ $stats['activeBookings'] }}</span>
            </li>
            <li class="d-flex justify-content-between mb-1">
              <span>Total fleet size</span>
              <span class="fw-semibold">{{ $stats['totalCars'] }}</span>
            </li>
            <li class="d-flex justify-content-between mb-1">
              <span>Completed bookings</span>
              <span class="fw-semibold">{{ $stats['completedBookings'] }}</span>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>
  </div>
</div>
@endsection
