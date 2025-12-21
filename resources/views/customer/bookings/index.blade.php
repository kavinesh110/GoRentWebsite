@extends('layouts.app')
@section('title', 'My Bookings - Hasta GoRent')

@section('content')
<div class="container-custom" style="padding: 32px 24px 48px;">
  <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
    <div>
      <h1 class="h3 fw-bold mb-1" style="color:#333;">My Bookings</h1>
      <p class="text-muted mb-0" style="font-size: 14px;">View and manage your car rental bookings.</p>
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
      <form method="GET" action="{{ route('customer.bookings') }}" class="row g-2 align-items-end">
        <div class="col-md-4">
          <label class="form-label small">Status</label>
          <select name="status" class="form-select form-select-sm">
            <option value="">All statuses</option>
            <option value="created" {{ ($filters['status'] ?? '') === 'created' ? 'selected' : '' }}>Created</option>
            <option value="confirmed" {{ ($filters['status'] ?? '') === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
            <option value="active" {{ ($filters['status'] ?? '') === 'active' ? 'selected' : '' }}>Active</option>
            <option value="completed" {{ ($filters['status'] ?? '') === 'completed' ? 'selected' : '' }}>Completed</option>
            <option value="cancelled" {{ ($filters['status'] ?? '') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
          </select>
        </div>
        <div class="col-md-2">
          <button type="submit" class="btn btn-sm btn-hasta w-100">Filter</button>
        </div>
        <div class="col-md-2">
          <a href="{{ route('customer.bookings') }}" class="btn btn-sm btn-outline-secondary w-100">Clear</a>
        </div>
      </form>
    </div>
  </div>

  {{-- BOOKINGS GRID --}}
  <div class="row g-3">
    @forelse($bookings as $booking)
      <div class="col-md-6 col-lg-4">
        <div class="card border-0 shadow-soft h-100">
          <div class="card-body p-3">
            <div class="d-flex justify-content-between align-items-start mb-2">
              <div>
                <h6 class="fw-bold mb-0">Booking #{{ $booking->booking_id }}</h6>
                <small class="text-muted">{{ $booking->car->brand ?? 'N/A' }} {{ $booking->car->model ?? '' }}</small>
              </div>
              <span class="badge 
                {{ $booking->status === 'completed' ? 'bg-success' : ($booking->status === 'active' ? 'bg-warning' : ($booking->status === 'cancelled' ? 'bg-danger' : 'bg-secondary')) }}">
                {{ ucfirst($booking->status ?? 'N/A') }}
              </span>
            </div>
            
            @if($booking->car && $booking->car->image_url)
              <img src="{{ $booking->car->image_url }}" alt="{{ $booking->car->brand }} {{ $booking->car->model }}" class="img-fluid rounded mb-2" style="height: 150px; width: 100%; object-fit: cover;">
            @endif

            <div class="small mb-2">
              <div class="d-flex justify-content-between mb-1">
                <span class="text-muted">Pickup:</span>
                <strong>{{ $booking->start_datetime?->format('d M Y, H:i') ?? 'N/A' }}</strong>
              </div>
              <div class="d-flex justify-content-between mb-1">
                <span class="text-muted">Dropoff:</span>
                <strong>{{ $booking->end_datetime?->format('d M Y, H:i') ?? 'N/A' }}</strong>
              </div>
              <div class="d-flex justify-content-between mb-1">
                <span class="text-muted">Duration:</span>
                <strong>{{ $booking->rental_hours ?? 0 }} hours</strong>
              </div>
              <div class="d-flex justify-content-between mb-2">
                <span class="text-muted">Total:</span>
                <strong class="text-hasta">RM {{ number_format($booking->final_amount ?? 0, 2) }}</strong>
              </div>
            </div>

            <a href="{{ route('customer.bookings.show', $booking->booking_id) }}" class="btn btn-sm btn-outline-primary w-100">View Details</a>
          </div>
        </div>
      </div>
    @empty
      <div class="col-12">
        <div class="text-center py-5">
          <p class="text-muted">You haven't made any bookings yet.</p>
          <a href="{{ route('home') }}" class="btn btn-hasta mt-2">Browse Cars</a>
        </div>
      </div>
    @endforelse
  </div>

  {{-- PAGINATION --}}
  <div class="mt-4">
    {{ $bookings->links() }}
  </div>
</div>
@endsection
