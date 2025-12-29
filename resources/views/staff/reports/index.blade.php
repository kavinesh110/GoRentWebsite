@extends('layouts.app')
@section('title', 'Reports & Analytics - Staff Dashboard')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
@endpush

@section('content')
<div class="d-flex" style="min-height: calc(100vh - 60px);">
  @include('staff._nav')
  <div class="flex-fill" style="background: var(--bg);">
    <div class="container-fluid px-4 px-md-5" style="padding-top: 32px; padding-bottom: 48px;">
      <div class="mb-4">
        <h1 class="h3 fw-bold mb-1" style="color:#333;">Reports & Analytics</h1>
        <p class="text-muted mb-0" style="font-size: 14px;">Comprehensive business insights and statistics.</p>
      </div>

      {{-- DATE FILTER --}}
      <div class="card border-0 shadow-soft mb-4">
        <div class="card-body p-3">
          <form method="GET" action="{{ route('staff.reports') }}" class="row g-2 align-items-end">
            <div class="col-md-4">
              <label class="form-label small">Start Date</label>
              <input type="date" name="start_date" class="form-control form-control-sm" value="{{ $startDate }}">
            </div>
            <div class="col-md-4">
              <label class="form-label small">End Date</label>
              <input type="date" name="end_date" class="form-control form-control-sm" value="{{ $endDate }}">
            </div>
            <div class="col-md-2">
              <button type="submit" class="btn btn-sm btn-hasta w-100">Apply</button>
            </div>
            <div class="col-md-2">
              <a href="{{ route('staff.reports') }}" class="btn btn-sm btn-outline-secondary w-100">Reset</a>
            </div>
          </form>
        </div>
      </div>

      <div class="row g-3">
        {{-- MONTHLY REVENUE --}}
        <div class="col-md-6">
          <div class="card border-0 shadow-soft">
            <div class="card-body p-4">
              <h5 class="fw-bold mb-3">Monthly Revenue</h5>
              @if($monthlyRevenue->count() > 0)
                <div class="table-responsive">
                  <table class="table table-sm mb-0">
                    <thead>
                      <tr>
                        <th>Month</th>
                        <th class="text-end">Revenue (RM)</th>
                      </tr>
                    </thead>
                    <tbody>
                      @foreach($monthlyRevenue as $revenue)
                        <tr>
                          <td>{{ \Carbon\Carbon::parse($revenue->month . '-01')->format('F Y') }}</td>
                          <td class="text-end fw-semibold">{{ number_format($revenue->total, 2) }}</td>
                        </tr>
                      @endforeach
                    </tbody>
                  </table>
                </div>
              @else
                <p class="text-muted mb-0">No revenue data for selected period.</p>
              @endif
            </div>
          </div>
        </div>

        {{-- BOOKINGS BY STATUS --}}
        <div class="col-md-6">
          <div class="card border-0 shadow-soft">
            <div class="card-body p-4">
              <h5 class="fw-bold mb-3">Bookings by Status</h5>
              @if($bookingsByStatus->count() > 0)
                <div class="table-responsive">
                  <table class="table table-sm mb-0">
                    <thead>
                      <tr>
                        <th>Status</th>
                        <th class="text-end">Count</th>
                      </tr>
                    </thead>
                    <tbody>
                      @foreach($bookingsByStatus as $status => $count)
                        <tr>
                          <td><span class="badge bg-secondary">{{ ucfirst($status) }}</span></td>
                          <td class="text-end fw-semibold">{{ $count }}</td>
                        </tr>
                      @endforeach
                    </tbody>
                  </table>
                </div>
              @else
                <p class="text-muted mb-0">No bookings for selected period.</p>
              @endif
            </div>
          </div>
        </div>

        {{-- TOP CUSTOMERS --}}
        <div class="col-md-6">
          <div class="card border-0 shadow-soft">
            <div class="card-body p-4">
              <h5 class="fw-bold mb-3">Top Customers by Revenue</h5>
              @if($topCustomers->count() > 0)
                <div class="table-responsive">
                  <table class="table table-sm mb-0">
                    <thead>
                      <tr>
                        <th>Customer</th>
                        <th class="text-end">Revenue (RM)</th>
                      </tr>
                    </thead>
                    <tbody>
                      @foreach($topCustomers as $customer)
                        <tr>
                          <td>{{ $customer->full_name }}</td>
                          <td class="text-end fw-semibold">{{ number_format($customer->total_revenue ?? 0, 2) }}</td>
                        </tr>
                      @endforeach
                    </tbody>
                  </table>
                </div>
              @else
                <p class="text-muted mb-0">No customer data for selected period.</p>
              @endif
            </div>
          </div>
        </div>

        {{-- TOP CARS --}}
        <div class="col-md-6">
          <div class="card border-0 shadow-soft">
            <div class="card-body p-4">
              <h5 class="fw-bold mb-3">Top Cars by Bookings</h5>
              @if($topCars->count() > 0)
                <div class="table-responsive">
                  <table class="table table-sm mb-0">
                    <thead>
                      <tr>
                        <th>Car</th>
                        <th class="text-end">Bookings</th>
                      </tr>
                    </thead>
                    <tbody>
                      @foreach($topCars as $car)
                        <tr>
                          <td>{{ $car->plate_number }} - {{ $car->brand }} {{ $car->model }}</td>
                          <td class="text-end fw-semibold">{{ $car->bookings_count ?? 0 }}</td>
                        </tr>
                      @endforeach
                    </tbody>
                  </table>
                </div>
              @else
                <p class="text-muted mb-0">No car data for selected period.</p>
              @endif
            </div>
          </div>
        </div>

        {{-- PENALTY STATISTICS --}}
        <div class="col-md-12">
          <div class="card border-0 shadow-soft">
            <div class="card-body p-4">
              <h5 class="fw-bold mb-3">Penalty Statistics</h5>
              <div class="row g-3">
                <div class="col-md-3">
                  <div class="text-center">
                    <div class="small text-muted">Total Penalties</div>
                    <div class="h4 fw-bold">{{ $penaltyStats->total ?? 0 }}</div>
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="text-center">
                    <div class="small text-muted">Paid</div>
                    <div class="h4 fw-bold text-success">{{ $penaltyStats->paid_count ?? 0 }}</div>
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="text-center">
                    <div class="small text-muted">Pending</div>
                    <div class="h4 fw-bold text-warning">{{ $penaltyStats->pending_count ?? 0 }}</div>
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="text-center">
                    <div class="small text-muted">Total Amount (RM)</div>
                    <div class="h4 fw-bold">{{ number_format($penaltyStats->total_amount ?? 0, 2) }}</div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
