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

  /* KPI Cards */
  .kpi-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 20px;
  }

  .kpi-value {
    font-size: 26px;
    font-weight: 700;
    color: var(--slate-900);
    line-height: 1.2;
  }

  .kpi-label {
    font-size: 12px;
    font-weight: 600;
    color: var(--slate-500);
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 4px;
  }

  .kpi-change {
    font-size: 12px;
    margin-top: 8px;
  }

  .kpi-change.positive { color: #059669; }
  .kpi-change.negative { color: #dc2626; }
  .kpi-change.neutral { color: var(--slate-500); }

  /* Section Headers */
  .section-header {
    font-size: 18px;
    font-weight: 700;
    color: var(--slate-900);
    margin-bottom: 20px;
    padding-bottom: 12px;
    border-bottom: 2px solid var(--slate-200);
  }

  .section-header i {
    color: var(--slate-600);
    margin-right: 8px;
  }

  /* Cards */
  .report-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    margin-bottom: 20px;
  }

  .card-header-custom {
    padding: 16px 20px;
    border-bottom: 1px solid #e2e8f0;
    background: #fafbfc;
    font-weight: 600;
    font-size: 14px;
    color: var(--slate-900);
  }

  .card-body-custom {
    padding: 20px;
  }

  /* Tables */
  .report-table {
    width: 100%;
    border-collapse: collapse;
  }

  .report-table thead th {
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: var(--slate-500);
    padding: 10px 12px;
    border-bottom: 1px solid #e2e8f0;
    text-align: left;
    background: #fafbfc;
  }

  .report-table tbody td {
    padding: 12px;
    border-bottom: 1px solid #f1f5f9;
    font-size: 13px;
    color: var(--slate-700);
    vertical-align: middle;
  }

  .report-table tbody tr:hover {
    background: #fafbfc;
  }

  .report-table .text-right {
    text-align: right;
  }

  .report-table .font-medium {
    font-weight: 500;
  }

  .report-table .font-semibold {
    font-weight: 600;
    color: var(--slate-900);
  }

  /* Filter Card */
  .filter-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 16px 20px;
    margin-bottom: 24px;
  }

  .filter-label {
    font-size: 11px;
    font-weight: 600;
    color: var(--slate-600);
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 6px;
  }

  .filter-input {
    border: 1px solid #e2e8f0;
    border-radius: 6px;
    padding: 8px 12px;
    font-size: 13px;
    color: var(--slate-900);
    width: 100%;
  }

  .filter-input:focus {
    outline: none;
    border-color: var(--slate-400);
    box-shadow: 0 0 0 2px rgba(71, 85, 105, 0.1);
  }

  .btn-apply {
    background: var(--slate-900);
    color: #ffffff;
    border: none;
    border-radius: 6px;
    padding: 8px 16px;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
  }

  .btn-apply:hover {
    background: var(--slate-800);
  }

  .btn-reset {
    background: #ffffff;
    color: var(--slate-700);
    border: 1px solid #e2e8f0;
    border-radius: 6px;
    padding: 8px 16px;
    font-size: 13px;
    font-weight: 500;
    text-decoration: none;
  }

  .btn-reset:hover {
    background: #f8fafc;
    color: var(--slate-900);
  }

  /* Chart Container */
  .chart-container {
    position: relative;
    height: 250px;
  }

  .chart-container-sm {
    position: relative;
    height: 180px;
  }

  /* Progress Bar */
  .progress-bar-custom {
    height: 8px;
    background: #e2e8f0;
    border-radius: 4px;
    overflow: hidden;
  }

  .progress-fill {
    height: 100%;
    border-radius: 4px;
    transition: width 0.3s ease;
  }

  /* Badge */
  .status-badge {
    display: inline-block;
    padding: 3px 8px;
    border-radius: 4px;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
  }

  /* Nav Tabs */
  .report-tabs {
    display: flex;
    gap: 4px;
    border-bottom: 1px solid #e2e8f0;
    margin-bottom: 24px;
    padding-bottom: 0;
  }

  .report-tab {
    padding: 10px 16px;
    font-size: 13px;
    font-weight: 500;
    color: var(--slate-600);
    text-decoration: none;
    border-bottom: 2px solid transparent;
    margin-bottom: -1px;
  }

  .report-tab:hover {
    color: var(--slate-900);
  }

  .report-tab.active {
    color: var(--slate-900);
    border-bottom-color: var(--slate-900);
    font-weight: 600;
  }

  /* Revenue Filter Buttons */
  .revenue-filter-btn {
    background: #ffffff;
    color: var(--slate-600);
    border: 1px solid #e2e8f0;
    font-size: 12px;
    font-weight: 500;
    padding: 6px 14px;
    transition: all 0.15s ease;
  }

  .revenue-filter-btn:hover {
    background: #f8fafc;
    color: var(--slate-900);
    border-color: #cbd5e1;
  }

  .revenue-filter-btn.active {
    background: var(--slate-900);
    color: #ffffff;
    border-color: var(--slate-900);
  }

  .revenue-filter-btn:first-child {
    border-radius: 6px 0 0 6px;
  }

  .revenue-filter-btn:last-child {
    border-radius: 0 6px 6px 0;
  }

  .revenue-filter-btn:not(:first-child):not(:last-child) {
    border-radius: 0;
  }

  /* Month Selector */
  .month-selector {
    background: #ffffff;
    color: var(--slate-700);
    border: 1px solid #e2e8f0;
    border-radius: 6px;
    font-size: 12px;
    font-weight: 500;
    padding: 6px 12px;
    min-width: 140px;
    transition: all 0.15s ease;
    cursor: pointer;
  }

  .month-selector:hover {
    border-color: #cbd5e1;
  }

  .month-selector:focus {
    outline: none;
    border-color: var(--slate-900);
    box-shadow: 0 0 0 3px rgba(15, 23, 42, 0.1);
  }

  /* Responsive */
  @media (max-width: 768px) {
    .container-fluid { padding-left: 1rem !important; padding-right: 1rem !important; }
    .kpi-value { font-size: 22px; }
    .month-selector { min-width: 120px; font-size: 11px; }
  }
</style>
@endpush

@section('content')
<div class="d-flex reports-container">
  @include('staff._nav')
  <div class="flex-fill">
    <div class="container-fluid px-4 px-md-5 py-4">
      
      {{-- Header --}}
      <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div>
          <h1 class="h4 fw-bold mb-1" style="color: var(--slate-900);">Business Reports</h1>
          <p class="text-muted mb-0" style="font-size: 13px;">
            Reporting period: {{ \Carbon\Carbon::parse($startDate)->format('M d, Y') }} — {{ \Carbon\Carbon::parse($endDate)->format('M d, Y') }}
            ({{ $daysDiff }} days)
          </p>
        </div>
      </div>

      {{-- Date Filter --}}
      <div class="filter-card">
        <form method="GET" action="{{ route('staff.reports') }}" class="row g-3 align-items-end">
          <div class="col-md-2">
            <div class="filter-label">Quick Filters</div>
            <select name="quick_filter" class="filter-input" onchange="applyQuickFilter(this.value)">
              <option value="">Custom Range</option>
              <option value="last_month" {{ request('quick_filter') === 'last_month' ? 'selected' : '' }}>Last Month</option>
              <option value="last_3_months" {{ request('quick_filter') === 'last_3_months' ? 'selected' : '' }}>Last 3 Months</option>
              <option value="last_6_months" {{ request('quick_filter') === 'last_6_months' ? 'selected' : '' }}>Last 6 Months</option>
              <option value="last_12_months" {{ request('quick_filter') === 'last_12_months' ? 'selected' : '' }}>Last 12 Months</option>
              <option value="this_year" {{ request('quick_filter') === 'this_year' ? 'selected' : '' }}>This Year</option>
              <option value="last_year" {{ request('quick_filter') === 'last_year' ? 'selected' : '' }}>Last Year</option>
            </select>
          </div>
          <div class="col-md-3">
            <div class="filter-label">Start Date</div>
            <input type="date" name="start_date" id="start_date" class="filter-input" value="{{ $startDate }}">
          </div>
          <div class="col-md-3">
            <div class="filter-label">End Date</div>
            <input type="date" name="end_date" id="end_date" class="filter-input" value="{{ $endDate }}">
          </div>
          <div class="col-md-4 d-flex gap-2 justify-content-md-end">
            <button type="submit" class="btn-apply">Apply Filter</button>
            <a href="{{ route('staff.reports') }}" class="btn-reset">Reset</a>
          </div>
        </form>
      </div>
      
      <script>
      function applyQuickFilter(value) {
        const today = new Date();
        const startInput = document.getElementById('start_date');
        const endInput = document.getElementById('end_date');
        
        if (!value) return;
        
        let startDate, endDate;
        
        switch(value) {
          case 'last_month':
            startDate = new Date(today.getFullYear(), today.getMonth() - 1, 1);
            endDate = new Date(today.getFullYear(), today.getMonth(), 0);
            break;
          case 'last_3_months':
            startDate = new Date(today.getFullYear(), today.getMonth() - 3, 1);
            endDate = new Date(today);
            break;
          case 'last_6_months':
            startDate = new Date(today.getFullYear(), today.getMonth() - 6, 1);
            endDate = new Date(today);
            break;
          case 'last_12_months':
            startDate = new Date(today.getFullYear(), today.getMonth() - 12, 1);
            endDate = new Date(today);
            break;
          case 'this_year':
            startDate = new Date(today.getFullYear(), 0, 1);
            endDate = new Date(today);
            break;
          case 'last_year':
            startDate = new Date(today.getFullYear() - 1, 0, 1);
            endDate = new Date(today.getFullYear() - 1, 11, 31);
            break;
          default:
            return;
        }
        
        startInput.value = startDate.toISOString().split('T')[0];
        endInput.value = endDate.toISOString().split('T')[0];
        
        // Auto-submit form
        startInput.closest('form').submit();
      }
      </script>

      {{-- ============================================== --}}
      {{-- EXECUTIVE SUMMARY - KEY METRICS --}}
      {{-- ============================================== --}}
      <div class="section-header"><i class="bi bi-speedometer2"></i>Executive Summary</div>
      
      <div class="row g-3 mb-4">
        {{-- Total Revenue --}}
        <div class="col-xl-3 col-md-6">
          <div class="kpi-card">
            <div class="kpi-label">Total Revenue</div>
            <div class="kpi-value">RM{{ number_format($totalRevenue, 0) }}</div>
            <div class="kpi-change {{ $revenueChange >= 0 ? 'positive' : 'negative' }}">
              <i class="bi bi-arrow-{{ $revenueChange >= 0 ? 'up' : 'down' }}"></i>
              {{ abs(round($revenueChange, 1)) }}% vs previous period
            </div>
          </div>
        </div>

        {{-- Total Bookings --}}
        <div class="col-xl-3 col-md-6">
          <div class="kpi-card">
            <div class="kpi-label">Total Bookings</div>
            <div class="kpi-value">{{ number_format($totalBookings) }}</div>
            <div class="kpi-change {{ $bookingsChange >= 0 ? 'positive' : 'negative' }}">
              <i class="bi bi-arrow-{{ $bookingsChange >= 0 ? 'up' : 'down' }}"></i>
              {{ abs(round($bookingsChange, 1)) }}% vs previous period
            </div>
          </div>
        </div>

        {{-- Average Booking Value --}}
        <div class="col-xl-3 col-md-6">
          <div class="kpi-card">
            <div class="kpi-label">Avg. Booking Value</div>
            <div class="kpi-value">RM{{ number_format($avgBookingValue, 0) }}</div>
            <div class="kpi-change neutral">
              Avg. duration: {{ round($avgRentalDuration, 1) }} hours
            </div>
          </div>
        </div>

        {{-- Completion Rate --}}
        <div class="col-xl-3 col-md-6">
          <div class="kpi-card">
            <div class="kpi-label">Completion Rate</div>
            <div class="kpi-value">{{ round($completionRate, 1) }}%</div>
            <div class="kpi-change neutral">
              {{ $completedBookings }} completed / {{ $cancelledBookings }} cancelled
            </div>
          </div>
        </div>
      </div>

      {{-- Second Row KPIs --}}
      <div class="row g-3 mb-5">
        {{-- Active Customers --}}
        <div class="col-xl-3 col-md-6">
          <div class="kpi-card">
            <div class="kpi-label">Active Customers</div>
            <div class="kpi-value">{{ number_format($activeCustomers) }}</div>
            <div class="kpi-change neutral">
              {{ $newCustomers }} new in period
            </div>
          </div>
        </div>

        {{-- Fleet Utilization --}}
        <div class="col-xl-3 col-md-6">
          <div class="kpi-card">
            <div class="kpi-label">Fleet Status</div>
            <div class="kpi-value">{{ $availableCars }}/{{ $totalCars }}</div>
            <div class="kpi-change neutral">
              {{ $carsInUse }} in use, {{ $carsInMaintenance }} maintenance
            </div>
          </div>
        </div>

        {{-- Deposits --}}
        <div class="col-xl-3 col-md-6">
          <div class="kpi-card">
            <div class="kpi-label">Deposits Collected</div>
            <div class="kpi-value">RM{{ number_format($totalDeposits, 0) }}</div>
            <div class="kpi-change neutral">
              Refunded: RM{{ number_format($totalRefunds, 0) }}
            </div>
          </div>
        </div>

        {{-- Penalties --}}
        <div class="col-xl-3 col-md-6">
          <div class="kpi-card">
            <div class="kpi-label">Penalties Issued</div>
            <div class="kpi-value">{{ $penaltyStats->total ?? 0 }}</div>
            <div class="kpi-change neutral">
              RM{{ number_format($penaltyStats->total_amount ?? 0, 0) }} total value
            </div>
          </div>
        </div>
      </div>

      {{-- ============================================== --}}
      {{-- REVENUE & FINANCIAL REPORTS --}}
      {{-- ============================================== --}}
      <div class="section-header"><i class="bi bi-currency-dollar"></i>Revenue & Financial</div>

      {{-- Revenue Chart with Filter --}}
      <div class="row g-4 mb-4">
        <div class="col-12">
          <div class="report-card">
            <div class="card-header-custom d-flex justify-content-between align-items-center flex-wrap gap-2">
              <span>Revenue Trend</span>
              <div class="d-flex align-items-center gap-2">
                <select id="monthSelector" class="month-selector">
                  <option value="">All Months</option>
                  @foreach($availableMonths as $month)
                    <option value="{{ $month['monthKey'] }}">{{ $month['label'] }}</option>
                  @endforeach
                </select>
                <div class="btn-group" role="group" aria-label="Revenue period filter">
                  <button type="button" class="btn btn-sm revenue-filter-btn active" data-period="daily">Daily</button>
                  <button type="button" class="btn btn-sm revenue-filter-btn" data-period="weekly">Weekly</button>
                  <button type="button" class="btn btn-sm revenue-filter-btn" data-period="monthly">Monthly</button>
                </div>
              </div>
            </div>
            <div class="card-body-custom">
              <div style="height: 320px;">
                <canvas id="revenueFilterChart"></canvas>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="row g-4 mb-4">
        {{-- Revenue Trend Chart (smaller) --}}
        <div class="col-lg-8">
          <div class="report-card">
            <div class="card-header-custom">Revenue by Period</div>
            <div class="card-body-custom">
              <div class="chart-container">
                <canvas id="revenueTrendChart"></canvas>
              </div>
            </div>
          </div>
        </div>

        {{-- Payment Methods --}}
        <div class="col-lg-4">
          <div class="report-card">
            <div class="card-header-custom">Payment Methods</div>
            <div class="card-body-custom">
              <div class="chart-container-sm">
                <canvas id="paymentMethodsChart"></canvas>
              </div>
              <div class="mt-3">
                @foreach($paymentMethods as $method)
                <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                  <span style="font-size: 13px; color: var(--slate-700);">{{ ucfirst(str_replace('_', ' ', $method->payment_method)) }}</span>
                  <span style="font-size: 13px; font-weight: 600;">RM{{ number_format($method->total, 0) }}</span>
                </div>
                @endforeach
              </div>
            </div>
          </div>
        </div>
      </div>

      {{-- Revenue by Car --}}
      <div class="row g-4 mb-5">
        <div class="col-12">
          <div class="report-card">
            <div class="card-header-custom">Revenue by Vehicle</div>
            <div class="card-body-custom p-0">
              <div class="table-responsive">
                <table class="report-table">
                  <thead>
                    <tr>
                      <th style="width: 40px;">#</th>
                      <th>Vehicle</th>
                      <th>Plate Number</th>
                      <th class="text-right">Bookings</th>
                      <th class="text-right">Revenue</th>
                      <th class="text-right">Avg/Booking</th>
                    </tr>
                  </thead>
                  <tbody>
                    @forelse($revenueByCar as $index => $car)
                    <tr>
                      <td class="font-medium">{{ $index + 1 }}</td>
                      <td class="font-semibold">{{ $car->brand }} {{ $car->model }}</td>
                      <td style="color: var(--slate-500);">{{ strtoupper($car->plate_number) }}</td>
                      <td class="text-right">{{ $car->booking_count }}</td>
                      <td class="text-right font-semibold">RM{{ number_format($car->total_revenue, 2) }}</td>
                      <td class="text-right">RM{{ $car->booking_count > 0 ? number_format($car->total_revenue / $car->booking_count, 2) : '0.00' }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center py-4" style="color: var(--slate-500);">No revenue data</td></tr>
                    @endforelse
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>

      {{-- ============================================== --}}
      {{-- BOOKING & OPERATIONS REPORTS --}}
      {{-- ============================================== --}}
      <div class="section-header"><i class="bi bi-calendar-check"></i>Booking & Operations</div>

      <div class="row g-4 mb-4">
        {{-- Bookings Over Time --}}
        <div class="col-lg-8">
          <div class="report-card">
            <div class="card-header-custom">Booking Volume</div>
            <div class="card-body-custom">
              <div class="chart-container">
                <canvas id="bookingVolumeChart"></canvas>
              </div>
            </div>
          </div>
        </div>

        {{-- Booking Status --}}
        <div class="col-lg-4">
          <div class="report-card">
            <div class="card-header-custom">Status Breakdown</div>
            <div class="card-body-custom">
              <div class="chart-container-sm">
                <canvas id="bookingStatusChart"></canvas>
              </div>
              <div class="mt-3">
                @php
                  $statusColors = [
                    'created' => '#fef3c7',
                    'verified' => '#dbeafe',
                    'confirmed' => '#e0e7ff',
                    'active' => '#cffafe',
                    'completed' => '#d1fae5',
                    'deposit_returned' => '#d1fae5',
                    'cancelled' => '#fee2e2'
                  ];
                @endphp
                @foreach($bookingsByStatus as $status => $count)
                <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                  <span style="font-size: 13px; color: var(--slate-700);">{{ ucfirst(str_replace('_', ' ', $status)) }}</span>
                  <span style="font-size: 13px; font-weight: 600;">{{ $count }}</span>
                </div>
                @endforeach
              </div>
            </div>
          </div>
        </div>
      </div>

      {{-- Peak Days --}}
      <div class="row g-4 mb-5">
        <div class="col-lg-6">
          <div class="report-card">
            <div class="card-header-custom">Bookings by Day of Week</div>
            <div class="card-body-custom">
              <div class="chart-container-sm">
                <canvas id="bookingsByDayChart"></canvas>
              </div>
            </div>
          </div>
        </div>

        <div class="col-lg-6">
          <div class="report-card">
            <div class="card-header-custom">Operational Metrics</div>
            <div class="card-body-custom">
              <div class="row g-3">
                <div class="col-6">
                  <div class="p-3 rounded" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                    <div style="font-size: 11px; color: var(--slate-500); text-transform: uppercase; margin-bottom: 4px;">Completion Rate</div>
                    <div style="font-size: 24px; font-weight: 700; color: var(--slate-900);">{{ round($completionRate, 1) }}%</div>
                  </div>
                </div>
                <div class="col-6">
                  <div class="p-3 rounded" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                    <div style="font-size: 11px; color: var(--slate-500); text-transform: uppercase; margin-bottom: 4px;">Cancellation Rate</div>
                    <div style="font-size: 24px; font-weight: 700; color: #dc2626;">{{ round($cancellationRate, 1) }}%</div>
                  </div>
                </div>
                <div class="col-6">
                  <div class="p-3 rounded" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                    <div style="font-size: 11px; color: var(--slate-500); text-transform: uppercase; margin-bottom: 4px;">Avg Duration</div>
                    <div style="font-size: 24px; font-weight: 700; color: var(--slate-900);">{{ round($avgRentalDuration, 1) }}h</div>
                  </div>
                </div>
                <div class="col-6">
                  <div class="p-3 rounded" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                    <div style="font-size: 11px; color: var(--slate-500); text-transform: uppercase; margin-bottom: 4px;">Avg Value</div>
                    <div style="font-size: 24px; font-weight: 700; color: var(--slate-900);">RM{{ number_format($avgBookingValue, 0) }}</div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      {{-- ============================================== --}}
      {{-- VEHICLE PERFORMANCE REPORTS --}}
      {{-- ============================================== --}}
      <div class="section-header"><i class="bi bi-car-front"></i>Vehicle Performance</div>

      <div class="row g-4 mb-4">
        {{-- Most Rented Cars --}}
        <div class="col-lg-6">
          <div class="report-card">
            <div class="card-header-custom">Most Rented Vehicles</div>
            <div class="card-body-custom p-0">
              <div class="table-responsive">
                <table class="report-table">
                  <thead>
                    <tr>
                      <th>Vehicle</th>
                      <th class="text-right">Bookings</th>
                      <th class="text-right">Hours</th>
                      <th class="text-right">Revenue</th>
                    </tr>
                  </thead>
                  <tbody>
                    @forelse($mostRentedCars->take(5) as $car)
                    <tr>
                      <td>
                        <div class="font-semibold">{{ $car->brand }} {{ $car->model }}</div>
                        <div style="font-size: 11px; color: var(--slate-500);">{{ strtoupper($car->plate_number) }}</div>
                      </td>
                      <td class="text-right font-semibold">{{ $car->booking_count }}</td>
                      <td class="text-right">{{ number_format($car->total_hours) }}h</td>
                      <td class="text-right font-semibold">RM{{ number_format($car->total_revenue, 0) }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="text-center py-4" style="color: var(--slate-500);">No data</td></tr>
                    @endforelse
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>

        {{-- Least Utilized Cars --}}
        <div class="col-lg-6">
          <div class="report-card">
            <div class="card-header-custom">Least Utilized Vehicles</div>
            <div class="card-body-custom p-0">
              <div class="table-responsive">
                <table class="report-table">
                  <thead>
                    <tr>
                      <th>Vehicle</th>
                      <th class="text-right">Bookings</th>
                      <th class="text-right">Hours</th>
                      <th class="text-right">Revenue</th>
                    </tr>
                  </thead>
                  <tbody>
                    @forelse($leastUtilizedCars->take(5) as $car)
                    <tr>
                      <td>
                        <div class="font-semibold">{{ $car->brand }} {{ $car->model }}</div>
                        <div style="font-size: 11px; color: var(--slate-500);">{{ strtoupper($car->plate_number) }}</div>
                      </td>
                      <td class="text-right">{{ $car->booking_count }}</td>
                      <td class="text-right">{{ number_format($car->total_hours) }}h</td>
                      <td class="text-right">RM{{ number_format($car->total_revenue, 0) }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="text-center py-4" style="color: var(--slate-500);">No data</td></tr>
                    @endforelse
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>

      {{-- ============================================== --}}
      {{-- CUSTOMER REPORTS --}}
      {{-- ============================================== --}}
      <div class="section-header"><i class="bi bi-people"></i>Customer Analytics</div>

      <div class="row g-4 mb-4">
        {{-- Customer Overview --}}
        <div class="col-lg-4">
          <div class="report-card h-100">
            <div class="card-header-custom">Customer Overview</div>
            <div class="card-body-custom">
              <div class="mb-4">
                <div style="font-size: 11px; color: var(--slate-500); text-transform: uppercase; margin-bottom: 4px;">Total Customers</div>
                <div style="font-size: 28px; font-weight: 700; color: var(--slate-900);">{{ number_format($totalCustomers) }}</div>
              </div>
              <div class="row g-3">
                <div class="col-6">
                  <div class="p-3 rounded" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                    <div style="font-size: 11px; color: var(--slate-500); text-transform: uppercase;">New</div>
                    <div style="font-size: 20px; font-weight: 700; color: #059669;">{{ $newCustomers }}</div>
                  </div>
                </div>
                <div class="col-6">
                  <div class="p-3 rounded" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                    <div style="font-size: 11px; color: var(--slate-500); text-transform: uppercase;">Active</div>
                    <div style="font-size: 20px; font-weight: 700; color: #2563eb;">{{ $activeCustomers }}</div>
                  </div>
                </div>
                <div class="col-6">
                  <div class="p-3 rounded" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                    <div style="font-size: 11px; color: var(--slate-500); text-transform: uppercase;">Returning</div>
                    <div style="font-size: 20px; font-weight: 700; color: #7c3aed;">{{ $returningCustomers }}</div>
                  </div>
                </div>
                <div class="col-6">
                  <div class="p-3 rounded" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                    <div style="font-size: 11px; color: var(--slate-500); text-transform: uppercase;">Repeat Rate</div>
                    <div style="font-size: 20px; font-weight: 700; color: var(--slate-900);">{{ round($repeatBookingRate, 1) }}%</div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        {{-- Top Customers by Revenue --}}
        <div class="col-lg-4">
          <div class="report-card h-100">
            <div class="card-header-custom">Top Customers (Revenue)</div>
            <div class="card-body-custom p-0">
              <div class="table-responsive">
                <table class="report-table">
                  <thead>
                    <tr>
                      <th>Customer</th>
                      <th class="text-right">Revenue</th>
                    </tr>
                  </thead>
                  <tbody>
                    @forelse($topCustomersByRevenue->take(5) as $customer)
                    <tr>
                      <td>
                        <div class="font-medium" style="font-size: 13px;">{{ $customer->full_name }}</div>
                        <div style="font-size: 11px; color: var(--slate-500);">{{ $customer->booking_count }} bookings</div>
                      </td>
                      <td class="text-right font-semibold">RM{{ number_format($customer->total_revenue, 0) }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="2" class="text-center py-4" style="color: var(--slate-500);">No data</td></tr>
                    @endforelse
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>

        {{-- Top Customers by Bookings --}}
        <div class="col-lg-4">
          <div class="report-card h-100">
            <div class="card-header-custom">Top Customers (Bookings)</div>
            <div class="card-body-custom p-0">
              <div class="table-responsive">
                <table class="report-table">
                  <thead>
                    <tr>
                      <th>Customer</th>
                      <th class="text-right">Bookings</th>
                    </tr>
                  </thead>
                  <tbody>
                    @forelse($topCustomersByBookings->take(5) as $customer)
                    <tr>
                      <td>
                        <div class="font-medium" style="font-size: 13px;">{{ $customer->full_name }}</div>
                        <div style="font-size: 11px; color: var(--slate-500);">RM{{ number_format($customer->total_revenue, 0) }}</div>
                      </td>
                      <td class="text-right font-semibold">{{ $customer->booking_count }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="2" class="text-center py-4" style="color: var(--slate-500);">No data</td></tr>
                    @endforelse
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>

      {{-- ============================================== --}}
      {{-- PENALTIES & DISCOUNTS --}}
      {{-- ============================================== --}}
      <div class="section-header"><i class="bi bi-exclamation-triangle"></i>Penalties & Discounts</div>

      <div class="row g-4 mb-4">
        {{-- Penalty Overview --}}
        <div class="col-lg-6">
          <div class="report-card">
            <div class="card-header-custom">Penalty Statistics</div>
            <div class="card-body-custom">
              <div class="row g-3 mb-4">
                <div class="col-4">
                  <div class="text-center p-3 rounded" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                    <div style="font-size: 24px; font-weight: 700; color: var(--slate-900);">{{ $penaltyStats->total ?? 0 }}</div>
                    <div style="font-size: 11px; color: var(--slate-500); text-transform: uppercase;">Total</div>
                  </div>
                </div>
                <div class="col-4">
                  <div class="text-center p-3 rounded" style="background: #d1fae5;">
                    <div style="font-size: 24px; font-weight: 700; color: #059669;">{{ $penaltyStats->paid_count ?? 0 }}</div>
                    <div style="font-size: 11px; color: #065f46; text-transform: uppercase;">Paid</div>
                  </div>
                </div>
                <div class="col-4">
                  <div class="text-center p-3 rounded" style="background: #fee2e2;">
                    <div style="font-size: 24px; font-weight: 700; color: #dc2626;">{{ $penaltyStats->pending_count ?? 0 }}</div>
                    <div style="font-size: 11px; color: #991b1b; text-transform: uppercase;">Pending</div>
                  </div>
                </div>
              </div>

              <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                <span style="font-size: 13px; color: var(--slate-700);">Total Penalty Value</span>
                <span style="font-size: 14px; font-weight: 600;">RM{{ number_format($penaltyStats->total_amount ?? 0, 2) }}</span>
              </div>
              <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                <span style="font-size: 13px; color: var(--slate-700);">Amount Collected</span>
                <span style="font-size: 14px; font-weight: 600; color: #059669;">RM{{ number_format($penaltyStats->paid_amount ?? 0, 2) }}</span>
              </div>
              <div class="d-flex justify-content-between align-items-center py-2">
                <span style="font-size: 13px; color: var(--slate-700);">Outstanding</span>
                <span style="font-size: 14px; font-weight: 600; color: #dc2626;">RM{{ number_format(($penaltyStats->total_amount ?? 0) - ($penaltyStats->paid_amount ?? 0), 2) }}</span>
              </div>
            </div>
          </div>
        </div>

        {{-- Penalty by Type + Discount Usage --}}
        <div class="col-lg-6">
          <div class="report-card">
            <div class="card-header-custom">Penalty Breakdown & Discounts</div>
            <div class="card-body-custom">
              <div class="mb-4">
                <div style="font-size: 12px; font-weight: 600; color: var(--slate-700); margin-bottom: 12px;">Penalties by Type</div>
                @forelse($penaltyByType as $penalty)
                <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                  <span style="font-size: 13px; color: var(--slate-700);">{{ ucfirst($penalty->penalty_type) }}</span>
                  <div>
                    <span style="font-size: 12px; color: var(--slate-500); margin-right: 8px;">{{ $penalty->count }}x</span>
                    <span style="font-size: 13px; font-weight: 600;">RM{{ number_format($penalty->total, 2) }}</span>
                  </div>
                </div>
                @empty
                <div class="text-center py-3" style="color: var(--slate-500); font-size: 13px;">No penalties in this period</div>
                @endforelse
              </div>

              <div>
                <div style="font-size: 12px; font-weight: 600; color: var(--slate-700); margin-bottom: 12px;">Discount Usage</div>
                <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                  <span style="font-size: 13px; color: var(--slate-700);">Vouchers Used</span>
                  <div>
                    <span style="font-size: 12px; color: var(--slate-500); margin-right: 8px;">{{ $discountUsage->voucher_used_count ?? 0 }}x</span>
                    <span style="font-size: 13px; font-weight: 600;">RM{{ number_format($discountUsage->total_voucher_discount ?? 0, 2) }}</span>
                  </div>
                </div>
                <div class="d-flex justify-content-between align-items-center py-2">
                  <span style="font-size: 13px; color: var(--slate-700);">Promos Applied</span>
                  <div>
                    <span style="font-size: 12px; color: var(--slate-500); margin-right: 8px;">{{ $discountUsage->promo_used_count ?? 0 }}x</span>
                    <span style="font-size: 13px; font-weight: 600;">RM{{ number_format($discountUsage->total_promo_discount ?? 0, 2) }}</span>
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
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
// Wait for DOM to be ready
document.addEventListener('DOMContentLoaded', function() {
// Chart defaults
Chart.defaults.font.family = '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif';
Chart.defaults.plugins.legend.labels.usePointStyle = true;

// ==========================================
// FILTERABLE REVENUE CHART (Daily/Weekly/Monthly)
// ==========================================
const revenueDataSets = {
  daily: {!! json_encode($dailyRevenueData ?? []) !!},
  weekly: {!! json_encode($weeklyRevenueData ?? []) !!},
  monthly: {!! json_encode($monthlyRevenueData ?? []) !!}
};

// Check if canvas element exists
const revenueFilterCanvas = document.getElementById('revenueFilterChart');
if (!revenueFilterCanvas) {
  console.error('Revenue Filter Chart canvas not found');
  return;
}

const revenueFilterCtx = revenueFilterCanvas.getContext('2d');
// Ensure we have data before initializing chart
const initialDailyData = revenueDataSets.daily && revenueDataSets.daily.length > 0 
  ? revenueDataSets.daily 
  : [{ label: 'No Data', value: 0 }];

let revenueFilterChart = new Chart(revenueFilterCtx, {
  type: 'bar',
  data: {
    labels: initialDailyData.map(d => d.label),
    datasets: [{
      label: 'Revenue',
      data: initialDailyData.map(d => d.value),
      backgroundColor: '#475569',
      borderRadius: 4,
      borderSkipped: false
    }]
  },
  options: {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
      legend: { display: false },
      tooltip: {
        backgroundColor: '#1e293b',
        padding: 12,
        titleFont: { size: 13, weight: '600' },
        bodyFont: { size: 13 },
        callbacks: {
          label: ctx => 'RM ' + ctx.parsed.y.toLocaleString()
        }
      }
    },
    scales: {
      y: {
        beginAtZero: true,
        grid: { color: '#f1f5f9' },
        ticks: {
          color: '#64748b',
          font: { size: 11 },
          callback: v => 'RM ' + v.toLocaleString()
        }
      },
      x: {
        grid: { display: false },
        ticks: { color: '#64748b', font: { size: 10 }, maxRotation: 45, minRotation: 0 }
      }
    }
  }
});

// Function to generate all days in a month
function generateAllDaysInMonth(monthKey) {
  const [year, month] = monthKey.split('-').map(Number);
  const daysInMonth = new Date(year, month, 0).getDate(); // Get number of days in month
  const result = [];
  
  // Get existing data for this month
  const existingData = revenueDataSets.daily.filter(d => d.monthKey === monthKey);
  const dataMap = {};
  existingData.forEach(d => {
    const day = parseInt(d.date.split('-')[2]);
    dataMap[day] = d.value;
  });
  
  // Generate all days (1 to daysInMonth)
  for (let day = 1; day <= daysInMonth; day++) {
    const date = new Date(year, month - 1, day);
    const dateStr = `${year}-${String(month).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
    result.push({
      label: date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' }),
      value: dataMap[day] || 0,
      monthKey: monthKey,
      date: dateStr
    });
  }
  
  return result;
}

// Function to get all weeks in a month
function getAllWeeksInMonth(monthKey) {
  const [year, month] = monthKey.split('-').map(Number);
  const firstDay = new Date(year, month - 1, 1);
  const lastDay = new Date(year, month, 0); // Last day of the month
  
  // Find all weeks that overlap with this month
  const result = [];
  let currentDate = new Date(firstDay);
  
  // Go to the start of the week containing the first day
  const dayOfWeek = currentDate.getDay();
  currentDate.setDate(currentDate.getDate() - dayOfWeek);
  
  // Iterate through weeks until we pass the last day of the month
  while (currentDate <= lastDay || (currentDate.getMonth() === month - 1 && currentDate.getFullYear() === year)) {
    const weekEnd = new Date(currentDate);
    weekEnd.setDate(weekEnd.getDate() + 6);
    
    // Check if this week overlaps with the selected month
    if (weekEnd >= firstDay && currentDate <= lastDay) {
      const weekStartStr = currentDate.toISOString().split('T')[0];
      const weekEndDate = weekEnd > lastDay ? lastDay : weekEnd;
      const weekEndStr = weekEndDate.toISOString().split('T')[0];
      
      // Try to find existing data for this week
      const existingWeek = revenueDataSets.weekly.find(w => {
        const wStart = new Date(w.weekStart);
        const wEnd = new Date(w.weekEnd);
        // Check if weeks overlap
        return (wStart <= weekEndDate && wEnd >= currentDate);
      });
      
      const weekLabel = currentDate.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
      result.push({
        label: `Week ${getWeekNumber(currentDate)} (${weekLabel})`,
        value: existingWeek ? existingWeek.value : 0,
        monthKey: monthKey,
        weekStart: weekStartStr,
        weekEnd: weekEndStr
      });
    }
    
    // Move to next week
    currentDate.setDate(currentDate.getDate() + 7);
    
    // Stop if we've passed the month
    if (currentDate > lastDay && currentDate.getMonth() !== month - 1) break;
  }
  
  return result;
}

// Helper to get week number
function getWeekNumber(date) {
  const d = new Date(Date.UTC(date.getFullYear(), date.getMonth(), date.getDate()));
  const dayNum = d.getUTCDay() || 7;
  d.setUTCDate(d.getUTCDate() + 4 - dayNum);
  const yearStart = new Date(Date.UTC(d.getUTCFullYear(), 0, 1));
  return Math.ceil((((d - yearStart) / 86400000) + 1) / 7);
}

// Function to update chart with data
function updateRevenueChart(data) {
  revenueFilterChart.data.labels = data.map(d => d.label);
  revenueFilterChart.data.datasets[0].data = data.map(d => d.value);
  revenueFilterChart.update('active');
}

// Get current period and month selection
function getCurrentPeriod() {
  const activeBtn = document.querySelector('.revenue-filter-btn.active');
  return activeBtn ? activeBtn.dataset.period : 'daily';
}

function getCurrentMonth() {
  const monthSelector = document.getElementById('monthSelector');
  return monthSelector ? monthSelector.value || '' : '';
}

// Function to apply current filters
function applyFilters() {
  const period = getCurrentPeriod();
  const selectedMonth = getCurrentMonth();
  
  let data;
  
  if (period === 'monthly') {
    // Monthly view shows all months in the date range (from backend)
    data = revenueDataSets.monthly;
    // If no data, show empty array (don't generate fake months)
    if (!data || data.length === 0) {
      data = [];
    }
  } else if (period === 'daily') {
    // Daily view: Show all days in selected month
    if (selectedMonth) {
      data = generateAllDaysInMonth(selectedMonth);
    } else {
      data = revenueDataSets.daily;
    }
  } else if (period === 'weekly') {
    // Weekly view: Show all weeks in selected month
    if (selectedMonth) {
      data = getAllWeeksInMonth(selectedMonth);
    } else {
      data = revenueDataSets.weekly;
    }
  }
  
  updateRevenueChart(data);
}

// Month selector handler
const monthSelector = document.getElementById('monthSelector');
if (monthSelector) {
  monthSelector.addEventListener('change', function() {
    applyFilters();
  });
}

// Revenue filter button handlers
document.querySelectorAll('.revenue-filter-btn').forEach(btn => {
  btn.addEventListener('click', function() {
    // Update active state
    document.querySelectorAll('.revenue-filter-btn').forEach(b => b.classList.remove('active'));
    this.classList.add('active');
    
    // Get selected period
    const period = this.dataset.period;
    
    // Show/hide month selector
    const monthSelectorEl = document.getElementById('monthSelector');
    if (monthSelectorEl) {
      if (period === 'monthly') {
        // For monthly, hide selector since we always show all months
        monthSelectorEl.style.display = 'none';
        monthSelectorEl.value = ''; // Reset selection
      } else {
        // Show for daily/weekly
        monthSelectorEl.style.display = 'block';
      }
    }
    
    // Apply filters with new period
    applyFilters();
  });
});

// Revenue Trend Chart (smaller one)
const revenueTrendCanvas = document.getElementById('revenueTrendChart');
if (revenueTrendCanvas) {
  const revenueTrendCtx = revenueTrendCanvas.getContext('2d');
  const trendData = revenueDataSets.daily && revenueDataSets.daily.length > 0 
    ? revenueDataSets.daily 
    : [{ label: 'No Data', value: 0 }];

  new Chart(revenueTrendCtx, {
  type: 'line',
  data: {
    labels: trendData.map(d => d.label),
    datasets: [{
      label: 'Revenue',
      data: trendData.map(d => d.value),
      borderColor: '#475569',
      backgroundColor: 'rgba(71, 85, 105, 0.1)',
      borderWidth: 2,
      fill: true,
      tension: 0.3,
      pointRadius: 3,
      pointHoverRadius: 5,
      pointBackgroundColor: '#475569',
      pointBorderColor: '#ffffff',
      pointBorderWidth: 2
    }]
  },
  options: {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
      legend: { display: false },
      tooltip: {
        backgroundColor: '#1e293b',
        padding: 10,
        callbacks: {
          label: ctx => 'RM ' + ctx.parsed.y.toLocaleString()
        }
      }
    },
    scales: {
      y: {
        beginAtZero: true,
        grid: { color: '#f1f5f9' },
        ticks: {
          color: '#64748b',
          font: { size: 11 },
          callback: v => 'RM ' + v.toLocaleString()
        }
      },
      x: {
        grid: { display: false },
        ticks: { color: '#64748b', font: { size: 10 }, maxRotation: 45 }
      }
    }
  }
});

// Payment Methods Chart
const paymentMethodsCtx = document.getElementById('paymentMethodsChart').getContext('2d');
const paymentMethodsData = {!! json_encode($paymentMethods) !!};
const hasPaymentData = paymentMethodsData && paymentMethodsData.length > 0 && paymentMethodsData.some(p => parseFloat(p.total) > 0);

if (hasPaymentData) {
  new Chart(paymentMethodsCtx, {
    type: 'doughnut',
    data: {
      labels: paymentMethodsData.map(p => p.payment_method.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase())),
      datasets: [{
        data: paymentMethodsData.map(p => parseFloat(p.total)),
        backgroundColor: ['#475569', '#64748b', '#94a3b8', '#cbd5e1', '#e2e8f0'],
        borderWidth: 0
      }]
    },
  options: {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
      legend: { display: false },
      tooltip: {
        callbacks: {
          label: ctx => ctx.label + ': RM ' + ctx.parsed.toLocaleString()
        }
      }
    }
    });
  } else {
    // Show empty state for payment methods chart
    new Chart(paymentMethodsCtx, {
      type: 'doughnut',
      data: {
        labels: ['No Data'],
        datasets: [{
          data: [1],
          backgroundColor: ['#e2e8f0'],
          borderWidth: 0
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: { display: false },
          tooltip: { enabled: false }
        }
      }
    });
  }
}

// Booking Volume Chart
const bookingVolumeCanvas = document.getElementById('bookingVolumeChart');
if (bookingVolumeCanvas) {
  const bookingVolumeCtx = bookingVolumeCanvas.getContext('2d');
  const dailyBookingsData = {!! json_encode($dailyBookingsData ?? []) !!};
  if (dailyBookingsData && dailyBookingsData.length > 0) {
    new Chart(bookingVolumeCtx, {
    type: 'bar',
    data: {
      labels: dailyBookingsData.map(d => d.date),
      datasets: [{
        label: 'Bookings',
        data: dailyBookingsData.map(d => d.count),
        backgroundColor: '#475569',
        borderRadius: 4
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: { legend: { display: false } },
      scales: {
        y: {
          beginAtZero: true,
          grid: { color: '#f1f5f9' },
          ticks: { color: '#64748b', font: { size: 11 }, stepSize: 1 }
        },
        x: {
          grid: { display: false },
          ticks: { color: '#64748b', font: { size: 10 }, maxRotation: 45 }
        }
      }
    }
  });
}

// Booking Status Chart
const bookingStatusCtx = document.getElementById('bookingStatusChart').getContext('2d');
const bookingsByStatus = {!! json_encode($bookingsByStatus) !!};
const statusColors = {
  'created': '#fef3c7',
  'verified': '#dbeafe',
  'confirmed': '#e0e7ff',
  'active': '#cffafe',
  'completed': '#d1fae5',
  'deposit_returned': '#bbf7d0',
  'cancelled': '#fee2e2'
};
new Chart(bookingStatusCtx, {
  type: 'doughnut',
  data: {
    labels: Object.keys(bookingsByStatus).map(s => s.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase())),
    datasets: [{
      data: Object.values(bookingsByStatus),
      backgroundColor: Object.keys(bookingsByStatus).map(s => statusColors[s] || '#e2e8f0'),
      borderWidth: 0
    }]
  },
  options: {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
      legend: { display: false },
      tooltip: {
        callbacks: {
          label: ctx => ctx.label + ': ' + ctx.parsed
        }
      }
    }
    });
  }
}

// Bookings by Day Chart
const bookingsByDayCanvas = document.getElementById('bookingsByDayChart');
if (bookingsByDayCanvas) {
  const bookingsByDayCtx = bookingsByDayCanvas.getContext('2d');
  const bookingsByDayData = {!! json_encode($bookingsByDayOfWeek ?? []) !!};
  
  if (bookingsByDayData && bookingsByDayData.length > 0) {
    new Chart(bookingsByDayCtx, {
  type: 'bar',
  data: {
    labels: bookingsByDayData.map(d => d.day_name),
    datasets: [{
      label: 'Bookings',
      data: bookingsByDayData.map(d => d.count),
      backgroundColor: '#475569',
      borderRadius: 4
    }]
  },
  options: {
    responsive: true,
    maintainAspectRatio: false,
    plugins: { legend: { display: false } },
    scales: {
      y: {
        beginAtZero: true,
        grid: { color: '#f1f5f9' },
        ticks: { color: '#64748b', font: { size: 11 }, stepSize: 1 }
      },
      x: {
        grid: { display: false },
        ticks: { color: '#64748b', font: { size: 11 } }
      }
    }
  }
  });
  }
}

}); // End of DOMContentLoaded
</script>
@endpush
