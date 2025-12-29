@extends('layouts.app')
@section('title', 'Dashboard - Hasta GoRent')

@section('content')
<div class="container-fluid px-4 px-md-5" style="padding-top: 32px; padding-bottom: 48px;">
  <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
    <div>
      <h1 class="h3 fw-bold mb-1" style="color:#333;">Welcome back, {{ $customer->full_name }}!</h1>
      <p class="text-muted mb-0" style="font-size: 14px;">Manage your bookings and track your rental activity.</p>
    </div>
  </div>

  {{-- STATS CARDS --}}
  <div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
      <div class="card border-0 shadow-soft h-100">
        <div class="card-body py-3 px-3 text-center">
          <div class="small text-muted text-uppercase mb-1">Total Bookings</div>
          <div class="h4 fw-bold mb-0">{{ $totalBookings ?? 0 }}</div>
        </div>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="card border-0 shadow-soft h-100">
        <div class="card-body py-3 px-3 text-center">
          <div class="small text-muted text-uppercase mb-1">Rental Hours</div>
          <div class="h4 fw-bold mb-0">{{ $customer->total_rental_hours ?? 0 }}h</div>
        </div>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="card border-0 shadow-soft h-100">
        <div class="card-body py-3 px-3 text-center">
          <div class="small text-muted text-uppercase mb-1">Deposit Balance</div>
          <div class="h4 fw-bold mb-0 text-hasta">RM {{ number_format($customer->deposit_balance ?? 0, 2) }}</div>
        </div>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="card border-0 shadow-soft h-100">
        <div class="card-body py-3 px-3 text-center">
          <div class="small text-muted text-uppercase mb-1">Loyalty Stamps</div>
          <div class="h4 fw-bold mb-0">{{ $customer->total_stamps ?? 0 }}</div>
        </div>
      </div>
    </div>
  </div>

  {{-- QUICK ACTIONS --}}
  <div class="row g-3 mb-4">
    <div class="col-md-6 col-lg-3">
      <a href="{{ route('customer.bookings') }}" class="card border-0 shadow-soft text-decoration-none h-100">
        <div class="card-body p-3 text-center">
          <div class="mb-2">
            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color: var(--hasta);">
              <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
              <circle cx="8.5" cy="7" r="4"></circle>
              <polyline points="17 11 19 13 23 9"></polyline>
            </svg>
          </div>
          <div class="fw-semibold">My Bookings</div>
          <div class="small text-muted">View all bookings</div>
        </div>
      </a>
    </div>
    <div class="col-md-6 col-lg-3">
      <a href="{{ route('home') }}" class="card border-0 shadow-soft text-decoration-none h-100">
        <div class="card-body p-3 text-center">
          <div class="mb-2">
            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color: var(--hasta);">
              <path d="M5 17h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2z"/>
              <circle cx="7" cy="17" r="2"/>
              <circle cx="17" cy="17" r="2"/>
            </svg>
          </div>
          <div class="fw-semibold">Book a Car</div>
          <div class="small text-muted">Browse available cars</div>
        </div>
      </a>
    </div>
    <div class="col-md-6 col-lg-3">
      <a href="{{ route('customer.loyalty-rewards') }}" class="card border-0 shadow-soft text-decoration-none h-100">
        <div class="card-body p-3 text-center">
          <div class="mb-2">
            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color: var(--hasta);">
              <path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"></path>
              <line x1="7" y1="7" x2="7.01" y2="7"></line>
            </svg>
          </div>
          <div class="fw-semibold">Loyalty Rewards</div>
          <div class="small text-muted">Redeem vouchers</div>
        </div>
      </a>
    </div>
    <div class="col-md-6 col-lg-3">
      <a href="{{ route('customer.profile') }}" class="card border-0 shadow-soft text-decoration-none h-100">
        <div class="card-body p-3 text-center">
          <div class="mb-2">
            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color: var(--hasta);">
              <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
              <circle cx="12" cy="7" r="4"></circle>
            </svg>
          </div>
          <div class="fw-semibold">My Profile</div>
          <div class="small text-muted">Manage account</div>
        </div>
      </a>
    </div>
  </div>

  {{-- RECENT BOOKINGS --}}
  <div class="card border-0 shadow-soft">
    <div class="card-body p-4">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold mb-0">Recent Bookings</h5>
        <a href="{{ route('customer.bookings') }}" class="small text-decoration-none">View all &rarr;</a>
      </div>
      
      @if($recentBookings->count() > 0)
        <div class="table-responsive">
          <table class="table table-sm align-middle mb-0">
            <thead>
              <tr>
                <th>Booking ID</th>
                <th>Car</th>
                <th>Pickup Date</th>
                <th>Status</th>
                <th>Amount</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              @foreach($recentBookings as $booking)
                <tr>
                  <td><strong>#{{ $booking->booking_id }}</strong></td>
                  <td>
                    <div>{{ $booking->car->brand ?? 'N/A' }} {{ $booking->car->model ?? '' }}</div>
                    <small class="text-muted">{{ $booking->rental_hours ?? 0 }} hours</small>
                  </td>
                  <td>
                    <div>{{ $booking->start_datetime?->format('d M Y') ?? 'N/A' }}</div>
                    <small class="text-muted">{{ $booking->start_datetime?->format('H:i') ?? '' }}</small>
                  </td>
                  <td>
                    <span class="badge 
                      {{ $booking->status === 'completed' ? 'bg-success' : ($booking->status === 'active' ? 'bg-warning' : ($booking->status === 'cancelled' ? 'bg-danger' : 'bg-secondary')) }}">
                      {{ ucfirst($booking->status ?? 'N/A') }}
                    </span>
                  </td>
                  <td><strong>RM {{ number_format($booking->final_amount ?? 0, 2) }}</strong></td>
                  <td>
                    <a href="{{ route('customer.bookings.show', $booking->booking_id) }}" class="btn btn-sm btn-outline-primary">View</a>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      @else
        <div class="text-center py-4">
          <p class="text-muted mb-3">You haven't made any bookings yet.</p>
          <a href="{{ route('home') }}" class="btn btn-hasta">Browse Cars</a>
        </div>
      @endif
    </div>
  </div>
</div>
@endsection
