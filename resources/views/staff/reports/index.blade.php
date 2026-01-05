@extends('layouts.app')
@section('title', 'Business Reports - Staff Dashboard')

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

  .reports-container {
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
    margin-bottom: 24px;
  }

  .chart-title {
    font-size: 16px;
    font-weight: 600;
    color: var(--slate-900);
    margin-bottom: 20px;
    padding-bottom: 12px;
    border-bottom: 1px solid #e2e8f0;
  }

  .filter-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 24px;
  }

  .form-label {
    font-size: 12px;
    font-weight: 600;
    color: var(--slate-700);
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 8px;
  }

  .form-control, .form-select {
    border: 1px solid #e2e8f0;
    border-radius: 6px;
    padding: 8px 12px;
    font-size: 14px;
    color: var(--slate-900);
  }

  .form-control:focus, .form-select:focus {
    border-color: #cbd5e1;
    box-shadow: 0 0 0 3px rgba(71, 85, 105, 0.1);
  }

  .btn-filter {
    background: var(--slate-900);
    color: #ffffff;
    border: none;
    border-radius: 6px;
    padding: 8px 20px;
    font-size: 13px;
    font-weight: 600;
    transition: all 0.2s;
  }

  .btn-filter:hover {
    background: var(--slate-800);
  }

  .btn-reset {
    background: #ffffff;
    color: var(--slate-700);
    border: 1px solid #e2e8f0;
    border-radius: 6px;
    padding: 8px 20px;
    font-size: 13px;
    font-weight: 500;
    transition: all 0.2s;
  }

  .btn-reset:hover {
    background: #f8fafc;
    border-color: #cbd5e1;
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

  @media (max-width: 768px) {
    .container-fluid { padding-left: 1rem !important; padding-right: 1rem !important; }
    .stat-value { font-size: 24px; }
  }
</style>
@endpush

@section('content')
<div class="d-flex reports-container">
  @include('staff._nav')
  <div class="flex-fill">
    <div class="container-fluid px-4 px-md-5 py-5">
      
      {{-- Header --}}
      <div class="d-flex flex-wrap justify-content-between align-items-center mb-5">
        <div>
          <h1 class="h3 fw-bold mb-1" style="color: var(--slate-900);">Business Reports</h1>
          <p class="text-muted mb-0" style="font-size: 14px;">Comprehensive analytics and insights</p>
        </div>
      </div>

      {{-- Date Filter --}}
      <div class="filter-card">
        <form method="GET" action="{{ route('staff.reports') }}" class="row g-3 align-items-end">
          <div class="col-md-4">
            <label class="form-label">Start Date</label>
            <input type="date" name="start_date" class="form-control" value="{{ $startDate }}">
          </div>
          <div class="col-md-4">
            <label class="form-label">End Date</label>
            <input type="date" name="end_date" class="form-control" value="{{ $endDate }}">
          </div>
          <div class="col-md-4 d-flex gap-2">
            <button type="submit" class="btn-filter flex-fill">Apply Filter</button>
            <a href="{{ route('staff.reports') }}" class="btn-reset">Reset</a>
          </div>
        </form>
      </div>

      {{-- Key Metrics --}}
      <div class="row g-3 mb-4">
        <div class="col-xl-3 col-md-6">
          <div class="stat-card">
            <div class="stat-label">Total Revenue</div>
            <div class="stat-value">RM{{ number_format($totalRevenue ?? 0, 0) }}</div>
            <div class="stat-change">
              <span>Deposits: RM{{ number_format($totalDeposits ?? 0, 0) }}</span>
            </div>
          </div>
        </div>

        <div class="col-xl-3 col-md-6">
          <div class="stat-card">
            <div class="stat-label">Total Bookings</div>
            <div class="stat-value">{{ number_format($totalBookings ?? 0, 0) }}</div>
            <div class="stat-change">
              <span>Avg Value: RM{{ number_format($avgBookingValue ?? 0, 0) }}</span>
            </div>
          </div>
        </div>

        <div class="col-xl-3 col-md-6">
          <div class="stat-card">
            <div class="stat-label">Total Penalties</div>
            <div class="stat-value">{{ $penaltyStats->total ?? 0 }}</div>
            <div class="stat-change">
              <span>Amount: RM{{ number_format($penaltyStats->total_amount ?? 0, 0) }}</span>
            </div>
          </div>
        </div>

        <div class="col-xl-3 col-md-6">
          <div class="stat-card">
            <div class="stat-label">Penalties Paid</div>
            <div class="stat-value">{{ $penaltyStats->paid_count ?? 0 }}</div>
            <div class="stat-change">
              <span>Pending: {{ $penaltyStats->pending_count ?? 0 }}</span>
            </div>
          </div>
        </div>
      </div>

      {{-- Charts Row 1 --}}
      <div class="row g-4 mb-4">
        {{-- Monthly Revenue --}}
        <div class="col-lg-8">
          <div class="chart-card">
            <div class="chart-title">Monthly Revenue Trend</div>
            <canvas id="monthlyRevenueChart" height="80"></canvas>
          </div>
        </div>

        {{-- Booking Status Distribution --}}
        <div class="col-lg-4">
          <div class="chart-card">
            <div class="chart-title">Booking Status Distribution</div>
            <canvas id="bookingStatusChart" height="200"></canvas>
          </div>
        </div>
      </div>

      {{-- Charts Row 2 --}}
      <div class="row g-4 mb-4">
        {{-- Daily Revenue --}}
        <div class="col-lg-8">
          <div class="chart-card">
            <div class="chart-title">Daily Revenue</div>
            <canvas id="dailyRevenueChart" height="80"></canvas>
          </div>
        </div>

        {{-- Penalty Status --}}
        <div class="col-lg-4">
          <div class="chart-card">
            <div class="chart-title">Penalty Status</div>
            <canvas id="penaltyStatusChart" height="200"></canvas>
          </div>
        </div>
      </div>

      {{-- Top Customers & Cars --}}
      <div class="row g-4 mb-4">
        {{-- Top Customers --}}
        <div class="col-lg-6">
          <div class="table-card">
            <div class="table-header">
              <h6 class="table-title mb-0">Top Customers by Revenue</h6>
            </div>
            <div class="table-responsive">
              <table class="table">
                <thead>
                  <tr>
                    <th>Customer</th>
                    <th class="text-end">Revenue (RM)</th>
                  </tr>
                </thead>
                <tbody>
                  @forelse($topCustomers as $customer)
                    <tr>
                      <td>
                        <div style="font-weight: 500; color: var(--slate-900);">{{ $customer->full_name }}</div>
                        <div style="font-size: 12px; color: var(--slate-500);">{{ $customer->email }}</div>
                      </td>
                      <td class="text-end" style="font-weight: 600; color: var(--slate-900);">
                        {{ number_format($customer->total_revenue ?? 0, 2) }}
                      </td>
                    </tr>
                  @empty
                    <tr>
                      <td colspan="2" class="text-center py-4 text-muted">No customer data</td>
                    </tr>
                  @endforelse
                </tbody>
              </table>
            </div>
          </div>
        </div>

        {{-- Top Cars --}}
        <div class="col-lg-6">
          <div class="table-card">
            <div class="table-header">
              <h6 class="table-title mb-0">Top Cars by Bookings</h6>
            </div>
            <div class="table-responsive">
              <table class="table">
                <thead>
                  <tr>
                    <th>Vehicle</th>
                    <th class="text-end">Bookings</th>
                  </tr>
                </thead>
                <tbody>
                  @forelse($topCars as $car)
                    <tr>
                      <td>
                        <div style="font-weight: 500; color: var(--slate-900);">{{ $car->brand }} {{ $car->model }}</div>
                        <div style="font-size: 12px; color: var(--slate-500);">{{ strtoupper($car->plate_number) }}</div>
                      </td>
                      <td class="text-end" style="font-weight: 600; color: var(--slate-900);">
                        {{ $car->bookings_count ?? 0 }}
                      </td>
                    </tr>
                  @empty
                    <tr>
                      <td colspan="2" class="text-center py-4 text-muted">No car data</td>
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
// Monthly Revenue Chart
const monthlyRevenueCtx = document.getElementById('monthlyRevenueChart').getContext('2d');
const monthlyRevenueData = {!! json_encode($monthlyRevenue) !!};
new Chart(monthlyRevenueCtx, {
  type: 'line',
  data: {
    labels: monthlyRevenueData.map(r => {
      const date = new Date(r.month + '-01');
      return date.toLocaleDateString('en-US', { month: 'short', year: 'numeric' });
    }),
    datasets: [{
      label: 'Revenue (RM)',
      data: monthlyRevenueData.map(r => parseFloat(r.total)),
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
const bookingStatusCtx = document.getElementById('bookingStatusChart').getContext('2d');
const bookingStatusData = {!! json_encode($bookingsByStatus) !!};
new Chart(bookingStatusCtx, {
  type: 'doughnut',
  data: {
    labels: Object.keys(bookingStatusData).map(s => s.charAt(0).toUpperCase() + s.slice(1)),
    datasets: [{
      data: Object.values(bookingStatusData),
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

// Daily Revenue Chart
const dailyRevenueCtx = document.getElementById('dailyRevenueChart').getContext('2d');
const dailyRevenueData = {!! json_encode($dailyRevenueData) !!};
new Chart(dailyRevenueCtx, {
  type: 'bar',
  data: {
    labels: dailyRevenueData.map(d => d.date),
    datasets: [{
      label: 'Revenue (RM)',
      data: dailyRevenueData.map(d => d.revenue),
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
          font: { size: 10 },
          maxRotation: 45,
          minRotation: 45
        }
      }
    }
  }
});

// Penalty Status Chart
const penaltyStatusCtx = document.getElementById('penaltyStatusChart').getContext('2d');
const penaltyStats = {!! json_encode($penaltyStats) !!};
new Chart(penaltyStatusCtx, {
  type: 'doughnut',
  data: {
    labels: ['Paid', 'Pending'],
    datasets: [{
      data: [
        penaltyStats.paid_count || 0,
        penaltyStats.pending_count || 0
      ],
      backgroundColor: [
        '#d1fae5',
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
