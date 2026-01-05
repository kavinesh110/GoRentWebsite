@extends('layouts.app')
@section('title', 'Staff Dashboard - Hasta GoRent')

@push('styles')
<style>
  /* Standard Modern UI Variables */
  :root {
    --slate-50: #f8fafc;
    --slate-100: #f1f5f9;
    --slate-200: #e2e8f0;
    --slate-700: #334155;
    --slate-800: #1e293b;
  }

  .x-small { font-size: 11px; }
  .text-slate-700 { color: var(--slate-700); }
  .text-slate-800 { color: var(--slate-800); }
  .bg-light-subtle { background-color: #fcfcfd !important; }
  
  .kpi-card { position: relative; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); border: 1px solid #f1f5f9 !important; }
  .kpi-card:hover { transform: translateY(-4px); box-shadow: 0 12px 20px -5px rgba(0,0,0,0.05) !important; }
  
  .kpi-icon {
    position: relative;
    z-index: 2;
    width: 48px;
    height: 48px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 14px;
    font-size: 1.25rem;
  }

  .kpi-glow-warning {
    border-left: 4px solid var(--bs-warning) !important;
    background: linear-gradient(to right, #fff, #fffbeb) !important;
  }

  .urgent-item {
    background: #fff;
    border: 1px solid #f1f5f9;
    transition: all 0.2s ease;
  }
  .urgent-item:hover {
    background: #f8fafc;
  }

  .op-card {
    display: block;
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
  }
  .op-card > div {
    background: #fff;
    transition: all 0.2s;
  }
  .op-card:hover > div {
    background: #f8fafc;
    border-color: var(--slate-200) !important;
    transform: scale(1.02);
  }
  
  .op-icon {
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 10px;
    font-size: 1.1rem;
  }

  .btn-white {
    background: #fff;
    color: #1e293b;
    border-color: #e2e8f0;
  }
  .btn-white:hover {
    background: #f8fafc;
    border-color: #cbd5e1;
  }

  .table > :not(caption) > * > * {
    padding: 1rem 0.75rem;
    border-color: #f1f5f9;
  }
  
  @media (max-width: 768px) {
    .container-fluid { padding-left: 1.5rem !important; padding-right: 1.5rem !important; }
  }
</style>
@endpush

@section('content')
<div class="d-flex" style="min-height: calc(100vh - 70px);">
  @include('staff._nav')
  <div class="flex-fill" style="background: #f8fafc;">
    <div class="container-fluid px-4 px-md-5 py-4">
      
      {{-- Welcome & Date --}}
      <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
          <h1 class="h4 fw-bold mb-1" style="color:#1e293b; letter-spacing: -0.5px;">Business Overview</h1>
          <p class="text-muted mb-0 small">Welcome back, <strong>{{ session('auth_name') }}</strong>. Monitoring your rental operations.</p>
        </div>
        <div class="d-flex gap-2">
          <div class="bg-white px-3 py-2 rounded-3 shadow-sm border d-flex align-items-center gap-2">
            <i class="bi bi-calendar3 text-hasta"></i>
            <span class="small fw-bold text-dark">{{ now()->format('D, d M Y') }}</span>
          </div>
        </div>
      </div>

      {{-- KEY PERFORMANCE INDICATORS --}}
      <div class="row g-4 mb-4">
        {{-- Fleet Status --}}
        <div class="col-xl-3 col-sm-6">
          <div class="kpi-card bg-white p-4 rounded-4 shadow-sm border-0 h-100">
            <div class="d-flex justify-content-between align-items-start mb-3">
              <div class="kpi-icon bg-primary-subtle text-primary">
                <i class="bi bi-car-front-fill"></i>
              </div>
              <div class="text-end">
                <div class="small text-muted fw-bold text-uppercase" style="font-size: 10px;">Total Fleet</div>
                <div class="h4 fw-bold mb-0 text-slate-800">{{ $stats['totalCars'] }}</div>
              </div>
            </div>
            <div class="kpi-progress mb-2">
              <div class="progress" style="height: 6px; background: #f1f5f9;">
                @php $availabilityRate = ($stats['totalCars'] > 0) ? ($stats['availableCars'] / $stats['totalCars'] * 100) : 0; @endphp
                <div class="progress-bar bg-primary dynamic-width" data-width="{{ $availabilityRate }}%"></div>
              </div>
            </div>
            <div class="d-flex justify-content-between align-items-center mt-2">
              <span class="small text-muted fw-medium">{{ $stats['availableCars'] }} Ready to rent</span>
              <span class="badge bg-primary-subtle text-primary rounded-pill small">{{ round($availabilityRate) }}%</span>
            </div>
          </div>
        </div>

        {{-- Pending Bookings --}}
        <div class="col-xl-3 col-sm-6">
          <div class="kpi-card bg-white p-4 rounded-4 shadow-sm border-0 h-100 {{ $stats['pendingBookings'] > 0 ? 'kpi-glow-warning' : '' }}">
            <div class="d-flex justify-content-between align-items-start mb-3">
              <div class="kpi-icon bg-warning-subtle text-warning">
                <i class="bi bi-calendar-check-fill"></i>
              </div>
              <div class="text-end">
                <div class="small text-muted fw-bold text-uppercase" style="font-size: 10px;">New Requests</div>
                <div class="h4 fw-bold mb-0 {{ $stats['pendingBookings'] > 0 ? 'text-warning' : 'text-slate-800' }}">{{ $stats['pendingBookings'] }}</div>
              </div>
            </div>
            <div class="small text-muted mt-auto d-flex align-items-center gap-2">
              <i class="bi bi-activity text-warning"></i>
              <span>{{ $stats['activeBookings'] }} Active rentals currently</span>
            </div>
            <a href="{{ route('staff.bookings') }}?status=created" class="stretched-link"></a>
          </div>
        </div>

        {{-- Verifications --}}
        <div class="col-xl-3 col-sm-6">
          <div class="kpi-card bg-white p-4 rounded-4 shadow-sm border-0 h-100">
            <div class="d-flex justify-content-between align-items-start mb-3">
              <div class="kpi-icon bg-info-subtle text-info">
                <i class="bi bi-shield-check"></i>
              </div>
              <div class="text-end">
                <div class="small text-muted fw-bold text-uppercase" style="font-size: 10px;">Verifications</div>
                <div class="h4 fw-bold mb-0 text-slate-800">{{ $stats['pendingVerifications'] }}</div>
              </div>
            </div>
            <div class="small text-muted mt-auto">
              Total Customers: <span class="fw-bold text-dark">{{ $stats['totalCustomers'] }}</span>
            </div>
            <a href="{{ route('staff.customers') }}?verification_status=pending" class="stretched-link"></a>
          </div>
        </div>

        {{-- Revenue Today/Monthly --}}
        <div class="col-xl-3 col-sm-6">
          <div class="kpi-card bg-white p-4 rounded-4 shadow-sm border-0 h-100">
            <div class="d-flex justify-content-between align-items-start mb-3">
              <div class="kpi-icon bg-success-subtle text-success">
                <i class="bi bi-cash-stack"></i>
              </div>
              <div class="text-end">
                <div class="small text-muted fw-bold text-uppercase" style="font-size: 10px;">Monthly Revenue</div>
                <div class="h4 fw-bold mb-0 text-slate-800">RM{{ number_format($stats['monthlyRevenue'] ?? 0, 0) }}</div>
              </div>
            </div>
            <div class="small text-muted mt-auto d-flex justify-content-between align-items-center">
              <span>Lifetime:</span>
              <span class="fw-bold text-success">RM{{ number_format($stats['totalRevenue'] ?? 0, 0) }}</span>
            </div>
          </div>
        </div>
      </div>

      <div class="row g-4 mb-4">
        {{-- URGENT TASKS --}}
        <div class="col-lg-4">
          <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
            <div class="card-header bg-white border-0 pt-4 px-4">
              <div class="d-flex align-items-center gap-2">
                <div class="p-1 px-2 rounded-2 bg-danger text-white small fw-bold">Action</div>
                <h5 class="fw-bold mb-0 h6">Urgent Attention</h5>
              </div>
            </div>
            <div class="card-body p-4">
              @if($stats['pendingBookings'] > 0 || $stats['pendingVerifications'] > 0)
                <div class="d-grid gap-3">
                  @if($stats['pendingBookings'] > 0)
                    <div class="urgent-item p-3 rounded-3 border-start border-4 border-warning position-relative">
                      <div class="d-flex justify-content-between align-items-center">
                        <div>
                          <div class="fw-bold text-dark small">{{ $stats['pendingBookings'] }} New Booking(s)</div>
                          <div class="text-muted x-small">Awaiting confirmation</div>
                        </div>
                        <a href="{{ route('staff.bookings') }}?status=created" class="btn btn-sm btn-warning fw-bold py-1 px-3 stretched-link">View</a>
                      </div>
                    </div>
                  @endif
                  @if($stats['pendingVerifications'] > 0)
                    <div class="urgent-item p-3 rounded-3 border-start border-4 border-info position-relative">
                      <div class="d-flex justify-content-between align-items-center">
                        <div>
                          <div class="fw-bold text-dark small">{{ $stats['pendingVerifications'] }} User Verification(s)</div>
                          <div class="text-muted x-small">Awaiting ID review</div>
                        </div>
                        <a href="{{ route('staff.customers') }}?verification_status=pending" class="btn btn-sm btn-info text-white fw-bold py-1 px-3 stretched-link">View</a>
                      </div>
                    </div>
                  @endif
                </div>
              @else
                <div class="text-center py-5">
                  <div class="bg-light rounded-circle d-inline-flex p-3 mb-3">
                    <i class="bi bi-check2-all text-success h3 mb-0"></i>
                  </div>
                  <div class="fw-bold text-dark small">All caught up!</div>
                  <div class="text-muted x-small">No tasks require immediate action.</div>
                </div>
              @endif
            </div>
          </div>
        </div>

        {{-- QUICK OPERATIONS --}}
        <div class="col-lg-8">
          <div class="card border-0 shadow-sm rounded-4 h-100 bg-white overflow-hidden">
            <div class="card-header bg-white border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
              <h5 class="fw-bold mb-0 h6">Quick Operations</h5>
              <div class="small text-muted x-small">Direct actions</div>
            </div>
            <div class="card-body p-4 pt-2">
              <div class="row g-3">
                <div class="col-md-6">
                  <a href="{{ route('staff.cars.create') }}" class="op-card text-decoration-none group">
                    <div class="p-3 border rounded-4 h-100 d-flex align-items-center gap-3">
                      <div class="op-icon bg-hasta text-white shadow-sm">
                        <i class="bi bi-plus-lg"></i>
                      </div>
                      <div>
                        <div class="fw-bold text-dark mb-0 small">Add New Car</div>
                        <div class="text-muted x-small">Extend your active fleet</div>
                      </div>
                    </div>
                  </a>
                </div>
                <div class="col-md-6">
                  <a href="{{ route('staff.vouchers.create') }}" class="op-card text-decoration-none group">
                    <div class="p-3 border rounded-4 h-100 d-flex align-items-center gap-3">
                      <div class="op-icon bg-primary text-white shadow-sm">
                        <i class="bi bi-ticket-perforated"></i>
                      </div>
                      <div>
                        <div class="fw-bold text-dark mb-0 small">Issue Voucher</div>
                        <div class="text-muted x-small">Create promo discounts</div>
                      </div>
                    </div>
                  </a>
                </div>
                <div class="col-md-6">
                  <a href="{{ route('staff.penalties') }}" class="op-card text-decoration-none group">
                    <div class="p-3 border rounded-4 h-100 d-flex align-items-center gap-3">
                      <div class="op-icon bg-danger text-white shadow-sm">
                        <i class="bi bi-exclamation-triangle"></i>
                      </div>
                      <div>
                        <div class="fw-bold text-dark mb-0 small">Record Penalty</div>
                        <div class="text-muted x-small">Manage rental violations</div>
                      </div>
                    </div>
                  </a>
                </div>
                <div class="col-md-6">
                  <a href="{{ route('staff.reports') }}" class="op-card text-decoration-none group">
                    <div class="p-3 border rounded-4 h-100 d-flex align-items-center gap-3">
                      <div class="op-icon bg-info text-white shadow-sm">
                        <i class="bi bi-file-earmark-bar-graph"></i>
                      </div>
                      <div>
                        <div class="fw-bold text-dark mb-0 small">Generate Report</div>
                        <div class="text-muted x-small">Analytics and exports</div>
                      </div>
                    </div>
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="row g-4">
        {{-- RECENT ACTIVITY --}}
        <div class="col-lg-7">
          <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden">
            <div class="card-header bg-white border-bottom py-3 px-4 d-flex justify-content-between align-items-center">
              <h6 class="fw-bold mb-0">Active Bookings</h6>
              <a href="{{ route('staff.bookings') }}" class="small text-decoration-none fw-bold text-hasta">View All</a>
            </div>
            <div class="card-body p-0">
              @if($recentBookings->count() > 0)
                <div class="table-responsive">
                  <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light-subtle">
                      <tr>
                        <th class="ps-4 py-3 small text-muted text-uppercase fw-bold" style="font-size: 10px;">ID</th>
                        <th class="py-3 small text-muted text-uppercase fw-bold" style="font-size: 10px;">Customer / Car</th>
                        <th class="py-3 small text-muted text-uppercase fw-bold" style="font-size: 10px;">Status</th>
                        <th class="pe-4 text-end py-3 small text-muted text-uppercase fw-bold" style="font-size: 10px;">Action</th>
                      </tr>
                    </thead>
                    <tbody>
                      @foreach($recentBookings as $booking)
                        <tr>
                          <td class="ps-4">
                            <span class="fw-bold text-slate-700">#{{ $booking->booking_id }}</span>
                          </td>
                          <td>
                            <div class="fw-bold text-dark small mb-0">{{ $booking->customer->full_name ?? 'N/A' }}</div>
                            <div class="text-muted x-small">{{ $booking->car->model ?? 'Car' }} ({{ strtoupper($booking->car->plate_number ?? '') }})</div>
                          </td>
                          <td>
                            @php
                              $bStatusClass = match($booking->status) {
                                'created' => 'bg-warning-subtle text-warning',
                                'active' => 'bg-info-subtle text-info',
                                'completed' => 'bg-success-subtle text-success',
                                'cancelled' => 'bg-danger-subtle text-danger',
                                default => 'bg-secondary-subtle text-secondary'
                              };
                            @endphp
                            <span class="badge rounded-pill px-2 py-1 {{ $bStatusClass }} x-small" style="font-weight: 600;">
                              {{ ucfirst($booking->status) }}
                            </span>
                          </td>
                          <td class="pe-4 text-end">
                            <a href="{{ route('staff.bookings.show', $booking->booking_id) }}" class="btn btn-sm btn-white border px-3 rounded-pill x-small fw-bold">Manage</a>
                          </td>
                        </tr>
                      @endforeach
                    </tbody>
                  </table>
                </div>
              @else
                <div class="text-center py-5">
                  <p class="text-muted small mb-0">No active bookings found.</p>
                </div>
              @endif
            </div>
          </div>
        </div>

        {{-- FINANCIAL SUMMARY --}}
        <div class="col-lg-5">
          <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden">
            <div class="card-header bg-white border-bottom py-3 px-4">
              <h6 class="fw-bold mb-0">Financial Health</h6>
            </div>
            <div class="card-body p-4">
              <div class="d-grid gap-4">
                <div>
                  <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="small text-muted fw-bold">Revenue Target</span>
                    <span class="small fw-bold text-dark">75%</span>
                  </div>
                  <div class="progress" style="height: 8px; border-radius: 10px; background: #f1f5f9;">
                    <div class="progress-bar bg-success" style="width: 75%; border-radius: 10px;"></div>
                  </div>
                </div>

                <div class="p-3 rounded-4 bg-light border">
                  <div class="small text-muted fw-bold text-uppercase mb-2" style="font-size: 10px;">Secured Deposits</div>
                  <div class="d-flex align-items-center justify-content-between">
                    <div class="h5 fw-bold mb-0 text-slate-800">RM{{ number_format($stats['totalDeposits'] ?? 0, 2) }}</div>
                    <div class="badge bg-white text-dark border shadow-sm rounded-pill x-small px-2 py-1">Held in Trust</div>
                  </div>
                </div>

                <div class="d-grid gap-2">
                  <div class="d-flex align-items-center justify-content-between p-2 rounded-3 bg-light-subtle">
                    <span class="small text-muted">Daily Average</span>
                    <span class="small fw-bold">RM{{ number_format(($stats['monthlyRevenue'] ?? 0) / (now()->day ?: 1), 2) }}</span>
                  </div>
                  <div class="d-flex align-items-center justify-content-between p-2 rounded-3 bg-light-subtle">
                    <span class="small text-muted">Active Promos</span>
                    <span class="small fw-bold">0</span>
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

@push('scripts')
<script>
document.querySelectorAll('.dynamic-width').forEach(el => {
  el.style.width = el.dataset.width;
});
</script>
@endpush
