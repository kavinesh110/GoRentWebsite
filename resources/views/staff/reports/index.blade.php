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

  /* ========================================
     REPORTS NESTED SIDEBAR
     ======================================== */
  .reports-sidebar {
    width: 200px;
    background: #fff;
    border-right: 1px solid #e2e8f0;
    height: calc(100vh - 70px);
    position: sticky;
    top: 0;
    flex-shrink: 0;
    transition: width 0.25s ease;
    overflow: hidden;
    display: flex;
    flex-direction: column;
  }

  .reports-sidebar-inner {
    padding: 16px 12px;
    display: flex;
    flex-direction: column;
    height: 100%;
  }

  /* Toggle Button */
  .reports-sidebar-toggle {
    width: 24px;
    height: 24px;
    border: 1px solid #e2e8f0;
    border-radius: 6px;
    background: #fff;
    color: #94a3b8;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.2s;
    font-size: 12px;
    margin-left: auto;
    margin-bottom: 16px;
    flex-shrink: 0;
  }

  .reports-sidebar-toggle:hover {
    background: #f1f5f9;
    color: #475569;
    border-color: #cbd5e1;
  }

  /* Title */
  .reports-sidebar-title {
    font-size: 10px;
    font-weight: 700;
    color: var(--slate-400);
    text-transform: uppercase;
    letter-spacing: 0.8px;
    margin-bottom: 12px;
    padding-left: 8px;
    white-space: nowrap;
    transition: opacity 0.2s;
  }

  /* Nav Links */
  .reports-nav {
    display: flex;
    flex-direction: column;
    gap: 2px;
  }

  .reports-nav-link {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px 12px;
    border-radius: 8px;
    color: var(--slate-600);
    font-size: 13px;
    font-weight: 500;
    text-decoration: none;
    transition: all 0.15s ease;
    white-space: nowrap;
    position: relative;
  }

  .reports-nav-link i {
    font-size: 16px;
    width: 20px;
    min-width: 20px;
    text-align: center;
    color: var(--slate-400);
    transition: color 0.15s;
  }

  .reports-nav-link:hover {
    background: #f8fafc;
    color: #1e293b;
  }

  .reports-nav-link:hover i {
    color: #64748b;
  }

  .reports-nav-link.active {
    background: #fff1f1;
    color: var(--hasta);
    font-weight: 600;
  }

  .reports-nav-link.active i {
    color: var(--hasta);
  }

  /* ========================================
     REPORTS SIDEBAR - COLLAPSED STATE
     ======================================== */
  .reports-sidebar.collapsed {
    width: 56px;
  }

  .reports-sidebar.collapsed .reports-sidebar-inner {
    padding: 16px 8px;
    align-items: center;
  }

  .reports-sidebar.collapsed .reports-sidebar-toggle {
    margin: 0 auto 16px;
  }

  .reports-sidebar.collapsed .reports-sidebar-toggle i {
    transform: rotate(180deg);
  }

  .reports-sidebar.collapsed .reports-sidebar-title {
    display: none;
  }

  .reports-sidebar.collapsed .reports-nav-link {
    justify-content: center;
    padding: 10px;
  }

  .reports-sidebar.collapsed .reports-nav-link .nav-label {
    display: none;
  }

  /* Tooltip on hover (collapsed) */
  .reports-sidebar.collapsed .reports-nav-link::after {
    content: attr(data-tooltip);
    position: absolute;
    left: calc(100% + 8px);
    top: 50%;
    transform: translateY(-50%);
    background: #1e293b;
    color: #fff;
    padding: 6px 12px;
    border-radius: 6px;
    font-size: 12px;
    font-weight: 500;
    white-space: nowrap;
    opacity: 0;
    visibility: hidden;
    pointer-events: none;
    transition: opacity 0.15s, visibility 0.15s;
    z-index: 1001;
  }

  .reports-sidebar.collapsed .reports-nav-link:hover::after {
    opacity: 1;
    visibility: visible;
  }

  /* Reports Content Area */
  .reports-content {
    flex: 1;
    padding: 24px 32px;
    overflow-y: auto;
    scroll-behavior: smooth;
  }

  /* Section IDs for smooth scrolling */
  .report-section {
    scroll-margin-top: 20px;
    padding-top: 8px;
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

  /* Period Selectors */
  .period-selector {
    background: #ffffff;
    color: var(--slate-700);
    border: 1px solid #e2e8f0;
    border-radius: 6px;
    font-size: 12px;
    font-weight: 500;
    padding: 6px 12px;
    min-width: 100px;
    transition: all 0.15s ease;
    cursor: pointer;
  }

  .period-selector:hover {
    border-color: #cbd5e1;
  }

  .period-selector:focus {
    outline: none;
    border-color: var(--slate-900);
    box-shadow: 0 0 0 3px rgba(15, 23, 42, 0.1);
  }

  #monthSelector {
    min-width: 120px;
  }

  /* Responsive */
  @media (max-width: 1200px) {
    .reports-sidebar {
      display: none;
    }
    .reports-content {
      padding: 20px 24px;
    }
  }

  @media (max-width: 768px) {
    .reports-content {
      padding: 16px;
    }
    .kpi-value { font-size: 22px; }
    .month-selector { min-width: 120px; font-size: 11px; }
  }
</style>
@endpush

@section('content')
<div class="d-flex reports-container">
  @include('staff._nav')
  <div class="flex-fill d-flex">
    
    {{-- Nested Reports Sidebar --}}
    <div class="reports-sidebar" id="reportsSidebar">
      <div class="reports-sidebar-inner">
        <button class="reports-sidebar-toggle" id="reportsSidebarToggle" title="Collapse">
          <i class="bi bi-chevron-left"></i>
        </button>
        
        <div class="reports-sidebar-title">Sections</div>
        
        <nav class="reports-nav">
          <a href="#section-summary" class="reports-nav-link active" data-section="section-summary" data-tooltip="Executive Summary">
            <i class="bi bi-speedometer2"></i>
            <span class="nav-label">Summary</span>
          </a>
          <a href="#section-revenue" class="reports-nav-link" data-section="section-revenue" data-tooltip="Revenue & Financial">
            <i class="bi bi-currency-dollar"></i>
            <span class="nav-label">Revenue</span>
          </a>
          <a href="#section-bookings" class="reports-nav-link" data-section="section-bookings" data-tooltip="Booking & Operations">
            <i class="bi bi-calendar-check"></i>
            <span class="nav-label">Bookings</span>
          </a>
          <a href="#section-vehicles" class="reports-nav-link" data-section="section-vehicles" data-tooltip="Vehicle Performance">
            <i class="bi bi-car-front"></i>
            <span class="nav-label">Vehicles</span>
          </a>
          <a href="#section-customers" class="reports-nav-link" data-section="section-customers" data-tooltip="Customer Analytics">
            <i class="bi bi-people"></i>
            <span class="nav-label">Customers</span>
          </a>
          <a href="#section-penalties" class="reports-nav-link" data-section="section-penalties" data-tooltip="Penalties & Discounts">
            <i class="bi bi-exclamation-triangle"></i>
            <span class="nav-label">Penalties</span>
          </a>
        </nav>
      </div>
    </div>

    {{-- Reports Content --}}
    <div class="reports-content" id="reportsContent">
      
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
      <div id="section-summary" class="report-section">
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
      </div>

      {{-- ============================================== --}}
      {{-- REVENUE & FINANCIAL REPORTS --}}
      {{-- ============================================== --}}
      <div id="section-revenue" class="report-section">
        <div class="section-header"><i class="bi bi-currency-dollar"></i>Revenue & Financial</div>

        {{-- Revenue Chart with Filter --}}
        <div class="row g-4 mb-4">
          <div class="col-12">
            <div class="report-card">
              <div class="card-header-custom d-flex justify-content-between align-items-center flex-wrap gap-2">
                <span>Revenue Trend</span>
                <div class="d-flex align-items-center gap-2 flex-wrap">
                  {{-- Year Selector (for Monthly/Daily view) --}}
                  <select id="yearSelector" class="period-selector">
                    @php
                      $currentYear = date('Y');
                      $startYear = $currentYear - 3;
                    @endphp
                    @for($y = $currentYear; $y >= $startYear; $y--)
                      <option value="{{ $y }}" {{ $y == $currentYear ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                  </select>
                  {{-- Month Selector (for Daily view) --}}
                  <select id="monthSelector" class="period-selector" style="display: none;">
                    <option value="01">January</option>
                    <option value="02">February</option>
                    <option value="03">March</option>
                    <option value="04">April</option>
                    <option value="05">May</option>
                    <option value="06">June</option>
                    <option value="07">July</option>
                    <option value="08">August</option>
                    <option value="09">September</option>
                    <option value="10">October</option>
                    <option value="11">November</option>
                    <option value="12" selected>December</option>
                  </select>
                  <div class="btn-group" role="group" aria-label="Revenue period filter">
                    <button type="button" class="btn btn-sm revenue-filter-btn" data-period="yearly">Yearly</button>
                    <button type="button" class="btn btn-sm revenue-filter-btn active" data-period="monthly">Monthly</button>
                    <button type="button" class="btn btn-sm revenue-filter-btn" data-period="daily">Daily</button>
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
      </div>

      {{-- ============================================== --}}
      {{-- BOOKING & OPERATIONS REPORTS --}}
      {{-- ============================================== --}}
      <div id="section-bookings" class="report-section">
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
      </div>

      {{-- ============================================== --}}
      {{-- VEHICLE PERFORMANCE REPORTS --}}
      {{-- ============================================== --}}
      <div id="section-vehicles" class="report-section">
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
      </div>

      {{-- ============================================== --}}
      {{-- CUSTOMER REPORTS --}}
      {{-- ============================================== --}}
      <div id="section-customers" class="report-section">
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
      </div>

      {{-- ============================================== --}}
      {{-- PENALTIES & DISCOUNTS --}}
      {{-- ============================================== --}}
      <div id="section-penalties" class="report-section">
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
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
// Wait for DOM to be ready
document.addEventListener('DOMContentLoaded', function() {

// ==========================================
// REPORTS SIDEBAR NAVIGATION
// ==========================================
const reportsSidebar = document.getElementById('reportsSidebar');
const reportsSidebarToggle = document.getElementById('reportsSidebarToggle');
const reportsContent = document.getElementById('reportsContent');
const navLinks = document.querySelectorAll('.reports-nav-link');

// Restore saved state
if (reportsSidebar && localStorage.getItem('reportsSidebarCollapsed') === 'true') {
  reportsSidebar.classList.add('collapsed');
}

// Toggle reports sidebar
if (reportsSidebarToggle && reportsSidebar) {
  reportsSidebarToggle.addEventListener('click', function() {
    reportsSidebar.classList.toggle('collapsed');
    localStorage.setItem('reportsSidebarCollapsed', reportsSidebar.classList.contains('collapsed'));
  });
}

// Smooth scroll navigation with active state
navLinks.forEach(link => {
  link.addEventListener('click', function(e) {
    e.preventDefault();
    const targetId = this.getAttribute('href').substring(1);
    const targetSection = document.getElementById(targetId);
    
    if (targetSection && reportsContent) {
      // Remove active from all links
      navLinks.forEach(l => l.classList.remove('active'));
      // Add active to clicked link
      this.classList.add('active');
      
      // Scroll to section
      targetSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
  });
});

// Update active link on scroll
if (reportsContent) {
  reportsContent.addEventListener('scroll', function() {
    const sections = document.querySelectorAll('.report-section');
    let currentSection = '';
    
    sections.forEach(section => {
      const sectionTop = section.offsetTop - reportsContent.offsetTop;
      const sectionHeight = section.offsetHeight;
      const scrollTop = reportsContent.scrollTop;
      
      if (scrollTop >= sectionTop - 100) {
        currentSection = section.getAttribute('id');
      }
    });
    
    navLinks.forEach(link => {
      link.classList.remove('active');
      if (link.getAttribute('data-section') === currentSection) {
        link.classList.add('active');
      }
    });
  });
}

// Chart defaults
Chart.defaults.font.family = '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif';
Chart.defaults.plugins.legend.labels.usePointStyle = true;

// ==========================================
// FILTERABLE REVENUE CHART (Yearly/Monthly/Daily)
// ==========================================
const revenueDataSets = {
  daily: {!! json_encode($dailyRevenueData ?? []) !!},
  monthly: {!! json_encode($monthlyRevenueData ?? []) !!}
};

// Check if canvas element exists
const revenueFilterCanvas = document.getElementById('revenueFilterChart');
if (!revenueFilterCanvas) {
  console.error('Revenue Filter Chart canvas not found');
  return;
}

const revenueFilterCtx = revenueFilterCanvas.getContext('2d');

// Initialize with monthly data for current year
const currentYear = new Date().getFullYear();
const initialMonthlyData = getMonthlyDataForYear(currentYear);

let revenueFilterChart = new Chart(revenueFilterCtx, {
  type: 'bar',
  data: {
    labels: initialMonthlyData.map(d => d.label),
    datasets: [{
      label: 'Revenue',
      data: initialMonthlyData.map(d => d.value),
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

// Get yearly data (aggregate by year)
function getYearlyData() {
  const yearlyMap = {};
  
  // Aggregate monthly data by year
  (revenueDataSets.monthly || []).forEach(m => {
    const year = m.label.split(' ')[1]; // Get year from "Jan 2024" format
    if (!yearlyMap[year]) {
      yearlyMap[year] = 0;
    }
    yearlyMap[year] += m.value;
  });
  
  // Convert to array and sort by year
  return Object.entries(yearlyMap)
    .map(([year, value]) => ({ label: year, value: value }))
    .sort((a, b) => a.label.localeCompare(b.label));
}

// Get monthly data for a specific year
function getMonthlyDataForYear(year) {
  const monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
  const result = [];
  
  // Create data for all 12 months
  monthNames.forEach((monthName, index) => {
    const monthKey = `${year}-${String(index + 1).padStart(2, '0')}`;
    const monthLabel = `${monthName} ${year}`;
    
    // Find matching data from backend
    const existingData = (revenueDataSets.monthly || []).find(m => 
      m.label === monthLabel || m.monthKey === monthKey
    );
    
    result.push({
      label: monthName,
      value: existingData ? existingData.value : 0,
      monthKey: monthKey
    });
  });
  
  return result;
}

// Get daily data for a specific month
function getDailyDataForMonth(year, month) {
  const monthKey = `${year}-${month}`;
  const daysInMonth = new Date(year, parseInt(month), 0).getDate();
  const result = [];
  
  // Create a map of existing daily data for this month
  const dailyMap = {};
  (revenueDataSets.daily || []).forEach(d => {
    if (d.monthKey === monthKey) {
      const day = parseInt(d.date.split('-')[2]);
      dailyMap[day] = d.value;
    }
  });
  
  // Generate all days in the month
  for (let day = 1; day <= daysInMonth; day++) {
    result.push({
      label: `${day}`,
      value: dailyMap[day] || 0,
      date: `${year}-${month}-${String(day).padStart(2, '0')}`
    });
  }
  
  return result;
}

function updateRevenueChart(data) {
  if (!data || data.length === 0) {
    data = [{ label: 'No Data', value: 0 }];
  }
  revenueFilterChart.data.labels = data.map(d => d.label);
  revenueFilterChart.data.datasets[0].data = data.map(d => d.value);
  revenueFilterChart.update('active');
}

function getCurrentPeriod() {
  const activeBtn = document.querySelector('.revenue-filter-btn.active');
  return activeBtn ? activeBtn.dataset.period : 'monthly';
}

function applyFilters() {
  const period = getCurrentPeriod();
  const yearSelector = document.getElementById('yearSelector');
  const monthSelector = document.getElementById('monthSelector');
  const selectedYear = yearSelector ? yearSelector.value : new Date().getFullYear();
  const selectedMonth = monthSelector ? monthSelector.value : '01';
  
  let data;
  
  if (period === 'yearly') {
    data = getYearlyData();
  } else if (period === 'monthly') {
    data = getMonthlyDataForYear(selectedYear);
  } else if (period === 'daily') {
    data = getDailyDataForMonth(selectedYear, selectedMonth);
  }
  
  updateRevenueChart(data);
}

// Year selector change handler
const yearSelector = document.getElementById('yearSelector');
if (yearSelector) {
  yearSelector.addEventListener('change', function() {
    applyFilters();
  });
}

// Month selector change handler
const monthSelector = document.getElementById('monthSelector');
if (monthSelector) {
  // Set to current month
  monthSelector.value = String(new Date().getMonth() + 1).padStart(2, '0');
  
  monthSelector.addEventListener('change', function() {
    applyFilters();
  });
}

// Period button handlers
document.querySelectorAll('.revenue-filter-btn').forEach(btn => {
  btn.addEventListener('click', function() {
    document.querySelectorAll('.revenue-filter-btn').forEach(b => b.classList.remove('active'));
    this.classList.add('active');
    
    const period = this.dataset.period;
    const yearSelectorEl = document.getElementById('yearSelector');
    const monthSelectorEl = document.getElementById('monthSelector');
    
    if (period === 'yearly') {
      // Hide both selectors for yearly view
      if (yearSelectorEl) yearSelectorEl.style.display = 'none';
      if (monthSelectorEl) monthSelectorEl.style.display = 'none';
    } else if (period === 'monthly') {
      // Show year selector only
      if (yearSelectorEl) yearSelectorEl.style.display = 'block';
      if (monthSelectorEl) monthSelectorEl.style.display = 'none';
    } else if (period === 'daily') {
      // Show both selectors
      if (yearSelectorEl) yearSelectorEl.style.display = 'block';
      if (monthSelectorEl) monthSelectorEl.style.display = 'block';
    }
    
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
      }
    });
  } else {
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
    }
  });
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
