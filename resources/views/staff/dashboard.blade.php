@extends('layouts.app')
@section('title', 'Staff Dashboard - Hasta GoRent')

@section('content')
<div class="d-flex" style="min-height: calc(100vh - 60px);">
  @include('staff._nav')
  <div class="flex-fill" style="background: var(--bg);">
    <div class="container-custom" style="padding: 32px 24px 48px;">
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
        </div>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="card border-0 shadow-soft h-100">
        <div class="card-body py-3 px-3">
          <div class="small text-muted text-uppercase mb-1">Available now</div>
          <div class="h4 fw-bold mb-0">{{ $stats['availableCars'] }}</div>
        </div>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="card border-0 shadow-soft h-100">
        <div class="card-body py-3 px-3">
          <div class="small text-muted text-uppercase mb-1">Active bookings</div>
          <div class="h4 fw-bold mb-0">{{ $stats['activeBookings'] }}</div>
        </div>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="card border-0 shadow-soft h-100">
        <div class="card-body py-3 px-3">
          <div class="small text-muted text-uppercase mb-1">Registered customers</div>
          <div class="h4 fw-bold mb-0">{{ $stats['totalCustomers'] }}</div>
        </div>
      </div>
    </div>
  </div>

  <div class="row g-3">
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
      <div class="card border-0 shadow-soft mb-3">
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
          </ul>
        </div>
      </div>

      <div class="card border-0 shadow-soft">
        <div class="card-body p-3 p-md-4">
          <h6 class="fw-bold mb-2">Coming soon</h6>
          <p class="small text-muted mb-1">This staff area will later include:</p>
          <ul class="small text-muted mb-0">
            <li>Blacklist management &amp; verification queue</li>
            <li>College-based booking analytics</li>
            <li>Loyalty voucher configuration</li>
          </ul>
        </div>
      </div>
    </div>
  </div>
  </div>
</div>
@endsection
