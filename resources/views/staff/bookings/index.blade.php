@extends('layouts.app')
@section('title', 'Bookings - Staff Dashboard')

@section('content')
<div class="d-flex" style="min-height: calc(100vh - 60px);">
  @include('staff._nav')
  <div class="flex-fill" style="background: var(--bg);">
    <div class="container-custom" style="padding: 32px 24px 48px;">
  <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
    <div>
      <h1 class="h3 fw-bold mb-1" style="color:#333;">Booking Management</h1>
      <p class="text-muted mb-0" style="font-size: 14px;">Track all deposit-first bookings, payments and rental statuses.</p>
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
      <form method="GET" action="{{ route('staff.bookings') }}" class="row g-2 align-items-end">
        <div class="col-md-5">
          <label class="form-label small">Search</label>
          <input type="text" name="search" class="form-control form-control-sm" placeholder="Customer name, email, car plate..." value="{{ $filters['search'] ?? '' }}">
        </div>
        <div class="col-md-3">
          <label class="form-label small">Status</label>
          <select name="status" class="form-select form-select-sm">
            <option value="">All statuses</option>
            <option value="created" {{ ($filters['status'] ?? '') === 'created' ? 'selected' : '' }}>Created</option>
            <option value="confirmed" {{ ($filters['status'] ?? '') === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
            <option value="ongoing" {{ ($filters['status'] ?? '') === 'ongoing' ? 'selected' : '' }}>Ongoing</option>
            <option value="completed" {{ ($filters['status'] ?? '') === 'completed' ? 'selected' : '' }}>Completed</option>
            <option value="cancelled" {{ ($filters['status'] ?? '') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
          </select>
        </div>
        <div class="col-md-2">
          <button type="submit" class="btn btn-sm btn-hasta w-100">Filter</button>
        </div>
        <div class="col-md-2">
          <a href="{{ route('staff.bookings') }}" class="btn btn-sm btn-outline-secondary w-100">Clear</a>
        </div>
      </form>
    </div>
  </div>

  {{-- BOOKINGS TABLE --}}
  <div class="card border-0 shadow-soft">
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-hover mb-0">
          <thead class="table-light">
            <tr>
              <th>Booking ID</th>
              <th>Customer</th>
              <th>Car</th>
              <th>Pickup</th>
              <th>Dropoff</th>
              <th>Hours</th>
              <th>Total (RM)</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            @forelse($bookings as $booking)
              <tr>
                <td><strong>#{{ $booking->booking_id }}</strong></td>
                <td>
                  <div>{{ $booking->customer->full_name ?? 'N/A' }}</div>
                  <small class="text-muted">{{ $booking->customer->email ?? '' }}</small>
                </td>
                <td>
                  <div>{{ $booking->car->brand ?? 'N/A' }} {{ $booking->car->model ?? '' }}</div>
                  <small class="text-muted">{{ $booking->car->plate_number ?? '' }}</small>
                </td>
                <td>
                  <div>{{ $booking->start_datetime?->format('d/m/Y') ?? 'N/A' }}</div>
                  <small class="text-muted">{{ $booking->start_datetime?->format('H:i') ?? '' }}</small>
                </td>
                <td>
                  <div>{{ $booking->end_datetime?->format('d/m/Y') ?? 'N/A' }}</div>
                  <small class="text-muted">{{ $booking->end_datetime?->format('H:i') ?? '' }}</small>
                </td>
                <td>{{ $booking->rental_hours ?? 0 }}h</td>
                <td><strong>RM {{ number_format($booking->final_amount ?? 0, 2) }}</strong></td>
                <td>
                  <span class="badge 
                    {{ $booking->status === 'completed' ? 'bg-success' : ($booking->status === 'ongoing' ? 'bg-warning' : ($booking->status === 'cancelled' ? 'bg-danger' : 'bg-secondary')) }}">
                    {{ ucfirst($booking->status ?? 'N/A') }}
                  </span>
                </td>
                <td>
                  <a href="{{ route('staff.bookings.show', $booking->booking_id) }}" class="btn btn-sm btn-outline-primary">View</a>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="9" class="text-center py-4 text-muted">No bookings found.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>

  {{-- PAGINATION --}}
  <div class="mt-4">
    {{ $bookings->links() }}
  </div>
  </div>
</div>
</div>
@endsection
