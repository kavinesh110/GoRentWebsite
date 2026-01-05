@extends('layouts.app')
@section('title', 'Staff Dashboard - Hasta GoRent')

@push('styles')
<style>
  :root {
    --slate-50: #f8fafc;
    --slate-100: #f1f5f9;
    --slate-200: #e2e8f0;
    --slate-300: #cbd5e1;
    --slate-400: #94a3b8;
    --slate-500: #64748b;
    --slate-600: #475569;
    --slate-700: #334155;
    --slate-800: #1e293b;
    --slate-900: #0f172a;
  }

  .dashboard-container {
    background: #f8fafc;
    min-height: calc(100vh - 70px);
  }

  .stat-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 20px;
    transition: all 0.2s ease;
  }

  .stat-card:hover {
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
    border-color: #cbd5e1;
  }

  .stat-value {
    font-size: 28px;
    font-weight: 700;
    color: var(--slate-900);
    line-height: 1.2;
    margin: 8px 0 4px;
  }

  .stat-label {
    font-size: 12px;
    font-weight: 600;
    color: var(--slate-500);
    text-transform: uppercase;
    letter-spacing: 0.5px;
  }

  .stat-change {
    font-size: 13px;
    color: var(--slate-500);
    margin-top: 8px;
  }

  .chart-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 24px;
    margin-bottom: 0;
  }

  .chart-title {
    font-size: 16px;
    font-weight: 600;
    color: var(--slate-900);
    margin-bottom: 20px;
    padding-bottom: 12px;
    border-bottom: 1px solid #e2e8f0;
  }

  .table-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    overflow: hidden;
  }

  .table-header {
    padding: 16px 20px;
    border-bottom: 1px solid #e2e8f0;
    background: #fafbfc;
  }

  .table-title {
    font-size: 14px;
    font-weight: 600;
    color: var(--slate-900);
    margin: 0;
  }

  .table {
    margin: 0;
  }

  .table thead th {
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: var(--slate-500);
    padding: 12px 20px;
    border-bottom: 1px solid #e2e8f0;
    background: #fafbfc;
  }

  .table tbody td {
    padding: 16px 20px;
    border-bottom: 1px solid #f1f5f9;
    font-size: 14px;
    color: var(--slate-700);
    vertical-align: middle;
  }

  .table tbody tr:hover {
    background: #fafbfc;
  }

  .status-badge {
    display: inline-block;
    padding: 4px 10px;
    border-radius: 4px;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.3px;
  }

  .status-created { background: #fef3c7; color: #92400e; }
  .status-verified { background: #dbeafe; color: #1e40af; }
  .status-active { background: #d1fae5; color: #065f46; }
  .status-completed { background: #d1fae5; color: #065f46; }
  .status-cancelled { background: #fee2e2; color: #991b1b; }

  .btn-minimal {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    color: var(--slate-700);
    font-size: 13px;
    font-weight: 500;
    padding: 6px 14px;
    border-radius: 6px;
    transition: all 0.2s;
  }

  .btn-minimal:hover {
    background: #f8fafc;
    border-color: #cbd5e1;
    color: var(--slate-900);
  }

  .icon-wrapper {
    width: 40px;
    height: 40px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: var(--slate-100);
    color: var(--slate-600);
    font-size: 18px;
  }

  @media (max-width: 768px) {
    .container-fluid { padding-left: 1rem !important; padding-right: 1rem !important; }
    .stat-value { font-size: 24px; }
  }
</style>
@endpush

@section('content')
<div class="d-flex dashboard-container">
  @include('staff._nav')
  <div class="flex-fill">
    <div class="container-fluid px-4 px-md-5 py-5">
      
      {{-- Header --}}
      <div class="d-flex flex-wrap justify-content-between align-items-center mb-5">
        <div>
          <h1 class="h3 fw-bold mb-1" style="color: var(--slate-900);">Dashboard</h1>
          <p class="text-muted mb-0" style="font-size: 14px;">Welcome back, {{ session('auth_name') }}</p>
        </div>
        <div class="d-flex align-items-center gap-2 text-muted" style="font-size: 13px;">
          <i class="bi bi-calendar3"></i>
          <span>{{ now()->format('F d, Y') }}</span>
        </div>
      </div>

      {{-- Key Metrics --}}
      <div class="row g-3 mb-4">
        <div class="col-xl-3 col-md-6">
          <div class="stat-card">
            <div class="d-flex justify-content-between align-items-start mb-3">
              <div class="icon-wrapper">
                <i class="bi bi-car-front"></i>
              </div>
            </div>
            <div class="stat-label">Total Fleet</div>
            <div class="stat-value">{{ $stats['totalCars'] }}</div>
            <div class="stat-change">
              <span class="text-muted">{{ $stats['availableCars'] }} available</span>
            </div>
          </div>
        </div>

        <div class="col-xl-3 col-md-6">
          <div class="stat-card">
            <div class="d-flex justify-content-between align-items-start mb-3">
              <div class="icon-wrapper">
                <i class="bi bi-calendar-check"></i>
              </div>
            </div>
            <div class="stat-label">Active Bookings</div>
            <div class="stat-value">{{ $stats['activeBookings'] }}</div>
            <div class="stat-change">
              <span class="text-muted">{{ $stats['pendingBookings'] }} pending</span>
            </div>
          </div>
        </div>

        <div class="col-xl-3 col-md-6">
          <div class="stat-card">
            <div class="d-flex justify-content-between align-items-start mb-3">
              <div class="icon-wrapper">
                <i class="bi bi-people"></i>
              </div>
            </div>
            <div class="stat-label">Total Customers</div>
            <div class="stat-value">{{ $stats['totalCustomers'] }}</div>
            <div class="stat-change">
              <span class="text-muted">{{ $stats['pendingVerifications'] }} pending verification</span>
            </div>
          </div>
        </div>

        <div class="col-xl-3 col-md-6">
          <div class="stat-card">
            <div class="d-flex justify-content-between align-items-start mb-3">
              <div class="icon-wrapper">
                <i class="bi bi-cash-stack"></i>
              </div>
            </div>
            <div class="stat-label">Monthly Revenue</div>
            <div class="stat-value">RM{{ number_format($stats['monthlyRevenue'] ?? 0, 0) }}</div>
            <div class="stat-change">
              <span class="text-muted">Total: RM{{ number_format($stats['totalRevenue'] ?? 0, 0) }}</span>
            </div>
          </div>
        </div>
      </div>

      {{-- Charts Row 1: Booking Status Distribution and Fleet Utilization --}}
      <div class="row g-4 mb-0">
        {{-- Booking Status Distribution --}}
        <div class="col-lg-6">
          <div class="chart-card">
            <div class="chart-title">Booking Status Distribution</div>
            <canvas id="statusChart" height="25"></canvas>
          </div>
        </div>

        {{-- Fleet Utilization --}}
        <div class="col-lg-6">
          <div class="chart-card">
            <div class="chart-title">Fleet Utilization</div>
            <canvas id="fleetChart" height="25"></canvas>
          </div>
        </div>
      </div>

      {{-- Charts Row 2: Revenue Trend and Daily Bookings --}}
      <div class="row g-4 mb-4">
        {{-- Revenue Trend --}}
        <div class="col-lg-6">
          <div class="chart-card">
            <div class="chart-title">Revenue Trend (Last 6 Months)</div>
            <canvas id="revenueChart" height="80"></canvas>
          </div>
        </div>

        {{-- Daily Bookings --}}
        <div class="col-lg-6">
          <div class="chart-card">
            <div class="chart-title">Daily Bookings (Last 30 Days)</div>
            <canvas id="dailyBookingsChart" height="80"></canvas>
          </div>
        </div>
      </div>

      {{-- Recent Activity --}}
      <div class="row g-4">
        <div class="col-12">
          <div class="table-card">
            <div class="table-header d-flex justify-content-between align-items-center">
              <h6 class="table-title mb-0">Recent Bookings</h6>
              <a href="{{ route('staff.bookings') }}" class="text-decoration-none" style="font-size: 13px; color: var(--slate-600);">View All</a>
            </div>
            <div class="table-responsive">
              <table class="table">
                <thead>
                  <tr>
                    <th>Booking ID</th>
                    <th>Customer</th>
                    <th>Vehicle</th>
                    <th>Status</th>
                    <th>Created</th>
                    <th class="text-end">Action</th>
                  </tr>
                </thead>
                <tbody>
                  @forelse($recentBookings as $booking)
                    <tr>
                      <td>
                        <span class="fw-semibold" style="color: var(--slate-900);">#{{ $booking->booking_id }}</span>
                      </td>
                      <td>
                        <div style="font-weight: 500; color: var(--slate-900);">{{ $booking->customer->full_name ?? 'N/A' }}</div>
                        <div style="font-size: 12px; color: var(--slate-500);">{{ $booking->customer->email ?? '' }}</div>
                      </td>
                      <td>
                        <div style="font-weight: 500; color: var(--slate-700);">{{ $booking->car->brand ?? 'N/A' }} {{ $booking->car->model ?? '' }}</div>
                        <div style="font-size: 12px; color: var(--slate-500);">{{ strtoupper($booking->car->plate_number ?? '') }}</div>
                      </td>
                      <td>
                        <span class="status-badge status-{{ $booking->status }}">
                          {{ ucfirst($booking->status) }}
                        </span>
                      </td>
                      <td style="color: var(--slate-500); font-size: 13px;">
                        {{ $booking->created_at->format('M d, Y') }}
                      </td>
                      <td class="text-end">
                        <a href="{{ route('staff.bookings.show', $booking->booking_id) }}" class="btn-minimal">View</a>
                      </td>
                    </tr>
                  @empty
                    <tr>
                      <td colspan="6" class="text-center py-4 text-muted">No recent bookings</td>
                    </tr>
                  @endforelse
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
// Revenue Trend Chart
const revenueCtx = document.getElementById('revenueChart').getContext('2d');
new Chart(revenueCtx, {
  type: 'line',
  data: {
    labels: {!! json_encode(array_column($monthlyRevenueData, 'month')) !!},
    datasets: [{
      label: 'Revenue (RM)',
      data: {!! json_encode(array_column($monthlyRevenueData, 'revenue')) !!},
      borderColor: '#475569',
      backgroundColor: 'rgba(71, 85, 105, 0.1)',
      borderWidth: 2,
      fill: true,
      tension: 0.4,
      pointRadius: 4,
      pointHoverRadius: 6,
      pointBackgroundColor: '#475569',
      pointBorderColor: '#ffffff',
      pointBorderWidth: 2
    }]
  },
  options: {
    responsive: true,
    maintainAspectRatio: true,
    plugins: {
      legend: {
        display: false
      },
      tooltip: {
        backgroundColor: '#1e293b',
        padding: 12,
        titleFont: { size: 13, weight: '600' },
        bodyFont: { size: 13 },
        displayColors: false,
        callbacks: {
          label: function(context) {
            return 'RM ' + context.parsed.y.toLocaleString();
          }
        }
      }
    },
    scales: {
      y: {
        beginAtZero: true,
        grid: {
          color: '#f1f5f9',
          borderColor: '#e2e8f0'
        },
        ticks: {
          color: '#64748b',
          font: { size: 11 },
          callback: function(value) {
            return 'RM ' + value.toLocaleString();
          }
        }
      },
      x: {
        grid: {
          display: false
        },
        ticks: {
          color: '#64748b',
          font: { size: 11 }
        }
      }
    }
  }
});

// Booking Status Distribution Chart
const statusCtx = document.getElementById('statusChart').getContext('2d');
const statusData = {!! json_encode($bookingStatusData) !!};
new Chart(statusCtx, {
  type: 'doughnut',
  data: {
    labels: Object.keys(statusData).map(s => s.charAt(0).toUpperCase() + s.slice(1)),
    datasets: [{
      data: Object.values(statusData),
      backgroundColor: [
        '#fef3c7',
        '#dbeafe',
        '#d1fae5',
        '#fee2e2',
        '#e2e8f0'
      ],
      borderWidth: 0,
      hoverOffset: 4
    }]
  },
  options: {
    responsive: true,
    maintainAspectRatio: true,
    plugins: {
      legend: {
        position: 'bottom',
        labels: {
          padding: 12,
          font: { size: 12 },
          color: '#64748b',
          usePointStyle: true
        }
      },
      tooltip: {
        backgroundColor: '#1e293b',
        padding: 12,
        titleFont: { size: 13, weight: '600' },
        bodyFont: { size: 13 },
        callbacks: {
          label: function(context) {
            const total = context.dataset.data.reduce((a, b) => a + b, 0);
            const percentage = total > 0 ? ((context.parsed / total) * 100).toFixed(1) : 0;
            return context.label + ': ' + context.parsed + ' (' + percentage + '%)';
          }
        }
      }
    }
  }
});

// Daily Bookings Chart
const dailyCtx = document.getElementById('dailyBookingsChart').getContext('2d');
new Chart(dailyCtx, {
  type: 'bar',
  data: {
    labels: {!! json_encode(array_column($dailyBookingsData, 'date')) !!},
    datasets: [{
      label: 'Bookings',
      data: {!! json_encode(array_column($dailyBookingsData, 'count')) !!},
      backgroundColor: '#475569',
      borderRadius: 4,
      borderSkipped: false
    }]
  },
  options: {
    responsive: true,
    maintainAspectRatio: true,
    plugins: {
      legend: {
        display: false
      },
      tooltip: {
        backgroundColor: '#1e293b',
        padding: 12,
        titleFont: { size: 13, weight: '600' },
        bodyFont: { size: 13 },
        displayColors: false
      }
    },
    scales: {
      y: {
        beginAtZero: true,
        grid: {
          color: '#f1f5f9',
          borderColor: '#e2e8f0'
        },
        ticks: {
          color: '#64748b',
          font: { size: 11 },
          stepSize: 1
        }
      },
      x: {
        grid: {
          display: false
        },
        ticks: {
          color: '#64748b',
          font: { size: 10 },
          maxRotation: 45,
          minRotation: 45
        }
      }
    }
  }
});

// Fleet Utilization Chart
const fleetCtx = document.getElementById('fleetChart').getContext('2d');
const fleetData = {!! json_encode($fleetUtilization) !!};
new Chart(fleetCtx, {
  type: 'doughnut',
  data: {
    labels: ['Available', 'In Use', 'Maintenance'],
    datasets: [{
      data: [fleetData.available, fleetData.in_use, fleetData.maintenance],
      backgroundColor: [
        '#d1fae5',
        '#dbeafe',
        '#fee2e2'
      ],
      borderWidth: 0,
      hoverOffset: 4
    }]
  },
  options: {
    responsive: true,
    maintainAspectRatio: true,
    plugins: {
      legend: {
        position: 'bottom',
        labels: {
          padding: 12,
          font: { size: 12 },
          color: '#64748b',
          usePointStyle: true
        }
      },
      tooltip: {
        backgroundColor: '#1e293b',
        padding: 12,
        titleFont: { size: 13, weight: '600' },
        bodyFont: { size: 13 },
        callbacks: {
          label: function(context) {
            const total = context.dataset.data.reduce((a, b) => a + b, 0);
            const percentage = total > 0 ? ((context.parsed / total) * 100).toFixed(1) : 0;
            return context.label + ': ' + context.parsed + ' (' + percentage + '%)';
          }
        }
      }
    }
  }
});
</script>
@endpush
