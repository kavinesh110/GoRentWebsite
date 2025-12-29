@extends('layouts.app')
@section('title', 'Penalties - Staff Dashboard')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
@endpush

@section('content')
<div class="d-flex" style="min-height: calc(100vh - 60px);">
  @include('staff._nav')
  <div class="flex-fill" style="background: var(--bg);">
    <div class="container-fluid px-4 px-md-5" style="padding-top: 32px; padding-bottom: 48px;">
      <div class="mb-4">
        <h1 class="h3 fw-bold mb-1" style="color:#333;">Penalty Management</h1>
        <p class="text-muted mb-0" style="font-size: 14px;">Track and manage all penalties across bookings.</p>
      </div>

      {{-- STATISTICS --}}
      <div class="row g-3 mb-4">
        <div class="col-md-3">
          <div class="card border-0 shadow-soft">
            <div class="card-body text-center">
              <div class="small text-muted">Total Penalties</div>
              <div class="h4 fw-bold mb-0">{{ $stats['total'] }}</div>
            </div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="card border-0 shadow-soft">
            <div class="card-body text-center">
              <div class="small text-muted">Paid</div>
              <div class="h4 fw-bold text-success mb-0">{{ $stats['paid'] }}</div>
            </div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="card border-0 shadow-soft">
            <div class="card-body text-center">
              <div class="small text-muted">Pending</div>
              <div class="h4 fw-bold text-warning mb-0">{{ $stats['pending'] }}</div>
            </div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="card border-0 shadow-soft">
            <div class="card-body text-center">
              <div class="small text-muted">Total Amount</div>
              <div class="h4 fw-bold mb-0">RM {{ number_format($stats['total_amount'], 2) }}</div>
            </div>
          </div>
        </div>
      </div>

      {{-- FILTERS --}}
      <div class="card border-0 shadow-soft mb-4">
        <div class="card-body p-3">
          <form method="GET" action="{{ route('staff.penalties') }}" class="row g-2 align-items-end">
            <div class="col-md-3">
              <label class="form-label small">Status</label>
              <select name="status" class="form-select form-select-sm">
                <option value="">All statuses</option>
                <option value="pending" {{ ($filters['status'] ?? '') === 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="paid" {{ ($filters['status'] ?? '') === 'paid' ? 'selected' : '' }}>Paid</option>
              </select>
            </div>
            <div class="col-md-3">
              <label class="form-label small">Type</label>
              <select name="type" class="form-select form-select-sm">
                <option value="">All types</option>
                <option value="late_return" {{ ($filters['type'] ?? '') === 'late_return' ? 'selected' : '' }}>Late Return</option>
                <option value="fuel_issue" {{ ($filters['type'] ?? '') === 'fuel_issue' ? 'selected' : '' }}>Fuel Issue</option>
                <option value="damage" {{ ($filters['type'] ?? '') === 'damage' ? 'selected' : '' }}>Damage</option>
                <option value="accident" {{ ($filters['type'] ?? '') === 'accident' ? 'selected' : '' }}>Accident</option>
                <option value="other" {{ ($filters['type'] ?? '') === 'other' ? 'selected' : '' }}>Other</option>
              </select>
            </div>
            <div class="col-md-4">
              <label class="form-label small">Search</label>
              <input type="text" name="search" class="form-control form-control-sm" placeholder="Customer name, email, plate..." value="{{ $filters['search'] ?? '' }}">
            </div>
            <div class="col-md-2">
              <button type="submit" class="btn btn-sm btn-hasta w-100">Filter</button>
            </div>
          </form>
        </div>
      </div>

      {{-- PENALTIES TABLE --}}
      <div class="card border-0 shadow-soft">
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
              <thead class="table-light">
                <tr>
                  <th style="padding: 16px;">Booking</th>
                  <th>Customer</th>
                  <th>Car</th>
                  <th>Type</th>
                  <th>Amount (RM)</th>
                  <th>Status</th>
                  <th>Paid Amount</th>
                  <th>Date</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                @forelse($penalties as $penalty)
                  <tr>
                    <td>
                      <a href="{{ route('staff.bookings.show', $penalty->booking_id) }}" class="text-decoration-none">
                        #{{ $penalty->booking_id }}
                      </a>
                    </td>
                    <td>
                      <div>{{ $penalty->booking->customer->full_name ?? 'N/A' }}</div>
                      <small class="text-muted">{{ $penalty->booking->customer->email ?? '' }}</small>
                    </td>
                    <td>{{ $penalty->booking->car->plate_number ?? 'N/A' }}</td>
                    <td><span class="badge bg-secondary">{{ ucfirst(str_replace('_', ' ', $penalty->penalty_type)) }}</span></td>
                    <td><strong>RM {{ number_format($penalty->amount, 2) }}</strong></td>
                    <td>
                      <span class="badge {{ $penalty->status === 'paid' ? 'bg-success' : 'bg-warning' }}">
                        {{ ucfirst($penalty->status) }}
                      </span>
                    </td>
                    <td>
                      @php
                        $paidAmount = $penalty->payments->where('status', 'verified')->sum('amount');
                      @endphp
                      <span class="{{ $paidAmount >= $penalty->amount ? 'text-success' : 'text-warning' }}">
                        RM {{ number_format($paidAmount, 2) }}
                      </span>
                    </td>
                    <td>{{ $penalty->created_at->format('d M Y') }}</td>
                    <td>
                      <a href="{{ route('staff.bookings.show', $penalty->booking_id) }}" class="btn btn-sm btn-outline-primary">View Booking</a>
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="9" class="text-center py-4 text-muted">No penalties found.</td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>

      {{-- PAGINATION --}}
      <div class="mt-4">
        {{ $penalties->links() }}
      </div>
    </div>
  </div>
</div>
@endsection
