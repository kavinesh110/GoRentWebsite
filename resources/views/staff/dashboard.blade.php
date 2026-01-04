@extends('layouts.app')
@section('title', 'Staff Dashboard - Hasta GoRent')

@section('content')
<div class="d-flex" style="min-height: calc(100vh - 60px);">
  @include('staff._nav')
  <div class="flex-fill" style="background: #f8fafc;">
    <div class="container-fluid px-4 px-md-5" style="padding-top: 32px; padding-bottom: 48px;">
  <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
    <div>
      <h1 class="h3 fw-bold mb-1" style="color:#1a202c;">Overview</h1>
      <p class="text-muted mb-0" style="font-size: 14.5px;">Welcome back, <strong>{{ session('auth_name') }}</strong>. Here's what's happening today.</p>
    </div>
    <div class="d-flex gap-2">
      <div class="bg-white px-3 py-2 rounded-3 shadow-sm border d-none d-sm-flex align-items-center gap-2">
        <i class="bi bi-calendar3 text-hasta"></i>
        <span class="small fw-semibold text-dark">{{ now()->format('D, d M Y') }}</span>
      </div>
    </div>
  </div>

  {{-- TOP STATS --}}
  <div class="row g-4 mb-4">
    <div class="col-6 col-md-3">
      <div class="card border-0 shadow-sm h-100 overflow-hidden">
        <div class="card-body p-4">
          <div class="d-flex align-items-center justify-content-between mb-3">
            <div class="bg-primary-subtle p-2 rounded-3">
              <i class="bi bi-car-front text-primary h4 mb-0"></i>
            </div>
            <span class="badge bg-light text-primary border fw-semibold">{{ $stats['totalCars'] }} Total</span>
          </div>
          <div class="small text-muted fw-bold text-uppercase mb-1" style="font-size: 11px; letter-spacing: 0.5px;">Active Fleet</div>
          <div class="h3 fw-bold mb-2">{{ $stats['availableCars'] }}</div>
          <div class="progress" style="height: 6px;">
            <div class="progress-bar bg-primary" style="width: {{ ($stats['totalCars'] > 0) ? ($stats['availableCars'] / $stats['totalCars'] * 100) : 0 }}%"></div>
          </div>
          <div class="small text-muted mt-2 d-flex justify-content-between">
            <span>{{ $stats['carsInUse'] }} in use</span>
            <span class="text-danger">{{ $stats['carsInMaintenance'] }} maint.</span>
          </div>
        </div>
      </div>
    </div>

    <div class="col-6 col-md-3">
      <div class="card border-0 shadow-sm h-100 overflow-hidden {{ $stats['pendingBookings'] > 0 ? 'border-start border-4 border-warning' : '' }}">
        <div class="card-body p-4">
          <div class="d-flex align-items-center justify-content-between mb-3">
            <div class="bg-warning-subtle p-2 rounded-3">
              <i class="bi bi-calendar-check text-warning h4 mb-0"></i>
            </div>
            @if($stats['pendingBookings'] > 0)
              <span class="badge bg-warning text-dark fw-bold">Action Needed</span>
            @endif
          </div>
          <div class="small text-muted fw-bold text-uppercase mb-1" style="font-size: 11px; letter-spacing: 0.5px;">Pending Bookings</div>
          <div class="h3 fw-bold mb-2 {{ $stats['pendingBookings'] > 0 ? 'text-warning' : '' }}">{{ $stats['pendingBookings'] }}</div>
          <div class="small text-muted d-flex align-items-center gap-1">
            <i class="bi bi-clock-history"></i>
            <span>{{ $stats['activeBookings'] }} active now</span>
          </div>
        </div>
      </div>
    </div>

    <div class="col-6 col-md-3">
      <div class="card border-0 shadow-sm h-100 overflow-hidden {{ $stats['pendingVerifications'] > 0 ? 'border-start border-4 border-info' : '' }}">
        <div class="card-body p-4">
          <div class="d-flex align-items-center justify-content-between mb-3">
            <div class="bg-info-subtle p-2 rounded-3">
              <i class="bi bi-person-check text-info h4 mb-0"></i>
            </div>
            @if($stats['pendingVerifications'] > 0)
              <span class="badge bg-info text-white fw-bold">Pending</span>
            @endif
          </div>
          <div class="small text-muted fw-bold text-uppercase mb-1" style="font-size: 11px; letter-spacing: 0.5px;">Account Verif.</div>
          <div class="h3 fw-bold mb-2 {{ $stats['pendingVerifications'] > 0 ? 'text-info' : '' }}">{{ $stats['pendingVerifications'] }}</div>
          <div class="small text-muted">{{ $stats['totalCustomers'] }} total customers</div>
        </div>
      </div>
    </div>

    <div class="col-6 col-md-3">
      <div class="card border-0 shadow-sm h-100 overflow-hidden">
        <div class="card-body p-4">
          <div class="d-flex align-items-center justify-content-between mb-3">
            <div class="bg-success-subtle p-2 rounded-3">
              <i class="bi bi-check2-all text-success h4 mb-0"></i>
            </div>
            <i class="bi bi-arrow-up-right text-success"></i>
          </div>
          <div class="small text-muted fw-bold text-uppercase mb-1" style="font-size: 11px; letter-spacing: 0.5px;">Completed</div>
          <div class="h3 fw-bold mb-2">{{ $stats['completedBookings'] }}</div>
          <div class="small text-muted">Success rate: 94%</div>
        </div>
      </div>
    </div>
  </div>

  {{-- REVENUE & ACTION REQUIRED --}}
  <div class="row g-4 mb-4">
    <div class="col-lg-8">
      <div class="card border-0 shadow-sm overflow-hidden" style="background: #fff;">
        <div class="card-header bg-white border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
          <h5 class="fw-bold mb-0">Financial Summary</h5>
          <span class="small text-muted">Currency: MYR</span>
        </div>
        <div class="card-body p-4">
          <div class="row g-4">
            <div class="col-md-4">
              <div class="p-3 rounded-4" style="background: linear-gradient(135deg, #fdfcfb 0%, #e2d1c3 100%);">
                <div class="small text-muted text-uppercase fw-bold mb-1" style="font-size: 10px;">Total Revenue</div>
                <div class="h4 fw-bold mb-0">RM {{ number_format($stats['totalRevenue'] ?? 0, 2) }}</div>
                <div class="small text-success mt-1"><i class="bi bi-graph-up"></i> Lifetime</div>
              </div>
            </div>
            <div class="col-md-4">
              <div class="p-3 rounded-4" style="background: linear-gradient(120deg, #f6d365 0%, #fda085 100%); color: #fff;">
                <div class="small text-white-50 text-uppercase fw-bold mb-1" style="font-size: 10px;">{{ now()->format('F') }} Revenue</div>
                <div class="h4 fw-bold mb-0">RM {{ number_format($stats['monthlyRevenue'] ?? 0, 2) }}</div>
                <div class="small text-white-50 mt-1"><i class="bi bi-calendar-event"></i> This month</div>
              </div>
            </div>
            <div class="col-md-4">
              <div class="p-3 rounded-4" style="background: linear-gradient(to right, #4facfe 0%, #00f2fe 100%); color: #fff;">
                <div class="small text-white-50 text-uppercase fw-bold mb-1" style="font-size: 10px;">Security Deposits</div>
                <div class="h4 fw-bold mb-0">RM {{ number_format($stats['totalDeposits'] ?? 0, 2) }}</div>
                <div class="small text-white-50 mt-1"><i class="bi bi-shield-lock"></i> Verified held</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-lg-4">
      @if($stats['pendingBookings'] > 0 || $stats['pendingVerifications'] > 0)
        <div class="card border-0 shadow-sm bg-hasta text-white h-100 shadow-soft" style="box-shadow: 0 10px 20px rgba(203, 55, 55, 0.2) !important;">
          <div class="card-body p-4 d-flex flex-column">
            <div class="d-flex align-items-center gap-2 mb-3">
              <i class="bi bi-lightning-fill h4 mb-0"></i>
              <h5 class="fw-bold mb-0">Urgent Tasks</h5>
            </div>
            <div class="flex-grow-1">
              @if($stats['pendingBookings'] > 0)
                <div class="d-flex align-items-center justify-content-between mb-3 bg-white bg-opacity-10 p-2 rounded-3">
                  <span class="small">{{ $stats['pendingBookings'] }} Bookings to confirm</span>
                  <a href="{{ route('staff.bookings') }}?status=created" class="btn btn-sm btn-light py-0">Review</a>
                </div>
              @endif
              @if($stats['pendingVerifications'] > 0)
                <div class="d-flex align-items-center justify-content-between bg-white bg-opacity-10 p-2 rounded-3">
                  <span class="small">{{ $stats['pendingVerifications'] }} Pending verifications</span>
                  <a href="{{ route('staff.customers') }}?verification_status=pending" class="btn btn-sm btn-light py-0">Review</a>
                </div>
              @endif
            </div>
            <div class="mt-4 pt-3 border-top border-white border-opacity-10 small">
              Complete these tasks to maintain service standards.
            </div>
          </div>
        </div>
      @else
        <div class="card border-0 shadow-sm h-100 d-flex align-items-center justify-content-center p-4 text-center bg-light">
          <div>
            <div class="bg-white rounded-circle d-inline-flex p-3 mb-3 shadow-sm">
              <i class="bi bi-check-all text-success h2 mb-0"></i>
            </div>
            <h6 class="fw-bold">You're all caught up!</h6>
            <p class="text-muted small mb-0">No urgent tasks currently require your attention.</p>
          </div>
        </div>
      @endif
    </div>
  </div>

  <div class="row g-4">
    {{-- QUICK ACTIONS GRID --}}
    <div class="col-lg-7">
      <div class="card border-0 shadow-sm h-100">
        <div class="card-header bg-white border-0 pt-4 px-4">
          <h5 class="fw-bold mb-0">Quick Operations</h5>
        </div>
        <div class="card-body p-4">
          <div class="row g-3">
            <div class="col-sm-6">
              <a href="{{ route('staff.cars.create') }}" class="text-decoration-none group">
                <div class="p-3 border rounded-4 bg-light-subtle h-100 transition-all hover-shadow d-flex align-items-center gap-3">
                  <div class="bg-white rounded-circle p-2 shadow-sm text-hasta">
                    <i class="bi bi-plus-circle h4 mb-0"></i>
                  </div>
                  <div>
                    <div class="fw-bold text-dark mb-0">Add New Car</div>
                    <div class="text-muted small">Expand the fleet</div>
                  </div>
                </div>
              </a>
            </div>
            <div class="col-sm-6">
              <a href="{{ route('staff.vouchers.create') }}" class="text-decoration-none group">
                <div class="p-3 border rounded-4 bg-light-subtle h-100 transition-all hover-shadow d-flex align-items-center gap-3">
                  <div class="bg-white rounded-circle p-2 shadow-sm text-primary">
                    <i class="bi bi-ticket-perforated h4 mb-0"></i>
                  </div>
                  <div>
                    <div class="fw-bold text-dark mb-0">Issue Voucher</div>
                    <div class="text-muted small">Promotional discounts</div>
                  </div>
                </div>
              </a>
            </div>
            <div class="col-sm-6">
              <a href="{{ route('staff.penalties') }}" class="text-decoration-none group">
                <div class="p-3 border rounded-4 bg-light-subtle h-100 transition-all hover-shadow d-flex align-items-center gap-3">
                  <div class="bg-white rounded-circle p-2 shadow-sm text-danger">
                    <i class="bi bi-exclamation-triangle h4 mb-0"></i>
                  </div>
                  <div>
                    <div class="fw-bold text-dark mb-0">Record Penalty</div>
                    <div class="text-muted small">Manage violations</div>
                  </div>
                </div>
              </a>
            </div>
            <div class="col-sm-6">
              <a href="{{ route('staff.reports') }}" class="text-decoration-none group">
                <div class="p-3 border rounded-4 bg-light-subtle h-100 transition-all hover-shadow d-flex align-items-center gap-3">
                  <div class="bg-white rounded-circle p-2 shadow-sm text-info">
                    <i class="bi bi-file-earmark-bar-graph h4 mb-0"></i>
                  </div>
                  <div>
                    <div class="fw-bold text-dark mb-0">Generate Report</div>
                    <div class="text-muted small">Financial analytics</div>
                  </div>
                </div>
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>

    {{-- SUMMARY PANELS --}}
    <div class="col-lg-5">
      {{-- Recent Bookings --}}
      <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
          <h6 class="fw-bold mb-0">Active/Recent Bookings</h6>
          <a href="{{ route('staff.bookings') }}" class="small text-decoration-none fw-semibold">Manage all</a>
        </div>
        <div class="card-body p-4 pt-2">
          @if($recentBookings->count() > 0)
            <div class="table-responsive">
              <table class="table table-borderless table-sm align-middle mb-0">
                <tbody>
                  @foreach($recentBookings as $booking)
                    <tr class="border-bottom">
                      <td class="py-3">
                        <div class="fw-bold text-dark">#{{ $booking->booking_id }}</div>
                        <div class="small text-muted">{{ $booking->customer->full_name ?? 'N/A' }}</div>
                      </td>
                      <td class="py-3">
                        <div class="small">{{ $booking->car->model ?? 'Car' }}</div>
                        <div class="small text-muted">{{ strtoupper($booking->car->plate_number ?? '') }}</div>
                      </td>
                      <td class="py-3 text-end">
                        <span class="badge rounded-pill px-3 py-2 
                          {{ $booking->status === 'created' ? 'bg-warning-subtle text-warning' : ($booking->status === 'active' ? 'bg-info-subtle text-info' : 'bg-secondary-subtle text-secondary') }}">
                          {{ ucfirst($booking->status) }}
                        </span>
                      </td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          @else
            <div class="text-center py-4 text-muted small">No recent activity.</div>
          @endif
        </div>
      </div>
    </div>
  </div>

</div>

<style>
  .transition-all { transition: all 0.3s ease; }
  .hover-shadow:hover { 
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0,0,0,0.05);
    background: #fff !important;
    border-color: var(--hasta) !important;
  }
  .shadow-sm { box-shadow: 0 .125rem .25rem rgba(0,0,0,.075)!important; }
  .bg-light-subtle { background-color: #f8fafc !important; }
  .progress { background-color: #e2e8f0; border-radius: 10px; }
  .badge { font-weight: 600; letter-spacing: 0.2px; }
  .btn-light { border: 1px solid #e2e8f0; }
  .table > :not(caption) > * > * { padding: 0.75rem 0.5rem; }
</style>
  </div>
</div>
@endsection
