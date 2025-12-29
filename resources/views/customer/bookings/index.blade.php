@extends('layouts.app')
@section('title', 'My Bookings - Hasta GoRent')

@section('content')
<div class="container-fluid px-4 px-md-5" style="padding-top: 32px; padding-bottom: 48px;">
  <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
    <div>
      <h1 class="h3 fw-bold mb-1" style="color:#333;">My Bookings</h1>
      <p class="text-muted mb-0" style="font-size: 14px;">Track and manage your car rental reservations.</p>
    </div>
    <div>
      <a href="{{ route('home') }}" class="btn btn-hasta">
        <i class="bi bi-plus-lg"></i> New Booking
      </a>
    </div>
  </div>

  @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
      <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  @endif

  {{-- FILTERS --}}
  <div class="card border-0 shadow-soft mb-4">
    <div class="card-body p-4">
      <form method="GET" action="{{ route('customer.bookings') }}" class="row g-3 align-items-end">
        <div class="col-md-4 col-lg-3">
          <label class="form-label fw-semibold small">Filter by Status</label>
          <select name="status" class="form-select border-0 bg-light">
            <option value="">All statuses</option>
            <option value="created" {{ ($filters['status'] ?? '') === 'created' ? 'selected' : '' }}>Created</option>
            <option value="confirmed" {{ ($filters['status'] ?? '') === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
            <option value="active" {{ ($filters['status'] ?? '') === 'active' ? 'selected' : '' }}>Active</option>
            <option value="completed" {{ ($filters['status'] ?? '') === 'completed' ? 'selected' : '' }}>Completed</option>
            <option value="cancelled" {{ ($filters['status'] ?? '') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
          </select>
        </div>
        <div class="col-md-3 col-lg-2">
          <button type="submit" class="btn btn-hasta w-100">Apply Filter</button>
        </div>
        @if(request('status'))
          <div class="col-md-3 col-lg-2">
            <a href="{{ route('customer.bookings') }}" class="btn btn-outline-secondary w-100">Clear</a>
          </div>
        @endif
      </form>
    </div>
  </div>

  {{-- BOOKINGS LIST --}}
  <div class="row g-4">
    @forelse($bookings as $booking)
      <div class="col-12">
        <div class="card border-0 shadow-soft overflow-hidden">
          <div class="row g-0">
            {{-- Car Image (optional/hidden on mobile if preferred, but let's keep it stylish) --}}
            <div class="col-md-3 col-lg-2 bg-light d-flex align-items-center justify-content-center p-3">
              @if($booking->car && $booking->car->image_url)
                <img src="{{ $booking->car->image_url }}" alt="{{ $booking->car->brand }} {{ $booking->car->model }}" class="img-fluid rounded" style="max-height: 120px; object-fit: contain;">
              @else
                <div class="text-muted text-center">
                  <i class="bi bi-car-front" style="font-size: 48px;"></i>
                </div>
              @endif
            </div>
            
            <div class="col-md-9 col-lg-10">
              <div class="card-body p-4">
                <div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-3">
                  <div>
                    <div class="d-flex align-items-center gap-2 mb-1">
                      <h5 class="fw-bold mb-0">Booking #{{ $booking->booking_id }}</h5>
                      <span class="badge rounded-pill 
                        {{ $booking->status === 'completed' ? 'bg-success-light text-success' : 
                           ($booking->status === 'active' ? 'bg-warning-light text-warning-dark' : 
                           ($booking->status === 'cancelled' ? 'bg-danger-light text-danger' : 
                           'bg-light text-muted')) }} px-3">
                        {{ ucfirst($booking->status ?? 'N/A') }}
                      </span>
                    </div>
                    <div class="text-muted">{{ $booking->car->brand ?? 'N/A' }} {{ $booking->car->model ?? '' }} ({{ $booking->car->plate_number ?? 'N/A' }})</div>
                  </div>
                  <div class="text-md-end">
                    <div class="small text-muted mb-1">Total Amount</div>
                    <div class="h5 fw-bold text-hasta mb-0">RM {{ number_format($booking->final_amount ?? 0, 2) }}</div>
                  </div>
                </div>

                <div class="row g-3 mb-4">
                  <div class="col-sm-6 col-md-4">
                    <div class="d-flex align-items-center gap-2">
                      <div class="bg-light rounded p-2 text-primary">
                        <i class="bi bi-calendar-event"></i>
                      </div>
                      <div>
                        <div class="small text-muted" style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px;">Pickup</div>
                        <div class="fw-semibold small">{{ $booking->start_datetime?->format('d M Y, H:i') ?? 'N/A' }}</div>
                      </div>
                    </div>
                  </div>
                  <div class="col-sm-6 col-md-4">
                    <div class="d-flex align-items-center gap-2">
                      <div class="bg-light rounded p-2 text-danger">
                        <i class="bi bi-calendar-check"></i>
                      </div>
                      <div>
                        <div class="small text-muted" style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px;">Dropoff</div>
                        <div class="fw-semibold small">{{ $booking->end_datetime?->format('d M Y, H:i') ?? 'N/A' }}</div>
                      </div>
                    </div>
                  </div>
                  <div class="col-sm-6 col-md-4">
                    <div class="d-flex align-items-center gap-2">
                      <div class="bg-light rounded p-2 text-info">
                        <i class="bi bi-clock"></i>
                      </div>
                      <div>
                        <div class="small text-muted" style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px;">Duration</div>
                        <div class="fw-semibold small">{{ $booking->rental_hours ?? 0 }} Hours</div>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="d-flex justify-content-end">
                  <a href="{{ route('customer.bookings.show', $booking->booking_id) }}" class="btn btn-sm btn-outline-hasta px-4">
                    Manage Booking <i class="bi bi-arrow-right ms-1"></i>
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    @empty
      <div class="col-12">
        <div class="card border-0 shadow-soft py-5">
          <div class="card-body text-center py-5">
            <div class="mb-3 text-muted">
              <i class="bi bi-calendar-x" style="font-size: 64px;"></i>
            </div>
            <h5 class="fw-bold">No Bookings Found</h5>
            <p class="text-muted">You haven't made any car rental bookings yet.</p>
            <a href="{{ route('home') }}" class="btn btn-hasta mt-2 px-4">Browse Available Cars</a>
          </div>
        </div>
      </div>
    @endforelse
  </div>

  {{-- PAGINATION --}}
  <div class="mt-5 d-flex justify-content-center">
    {{ $bookings->links() }}
  </div>
</div>

<style>
  .bg-success-light { background-color: #d1e7dd; }
  .bg-warning-light { background-color: #fff3cd; }
  .bg-danger-light { background-color: #f8d7da; }
  .text-warning-dark { color: #856404; }
  .btn-outline-hasta {
    color: var(--hasta);
    border-color: var(--hasta);
  }
  .btn-outline-hasta:hover {
    background-color: var(--hasta);
    color: white;
  }
</style>
@endsection
