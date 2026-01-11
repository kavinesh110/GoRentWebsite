{{-- Staff Navigation Sidebar --}}
<div class="staff-nav" style="background: #fff; border-right: 1px solid #edf2f7; height: calc(100vh - 70px); padding: 0; position: fixed; top: 70px; left: 0; width: 260px; flex-shrink: 0; z-index: 1000; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); overflow-y: auto; overflow-x: hidden;">
  {{-- Staff Profile Summary (Simplified) --}}
  <div class="px-4 py-4 mb-2">
    <div class="d-flex align-items-center gap-3">
      <div class="bg-hasta text-white rounded-3 d-flex align-items-center justify-content-center fw-bold shadow-sm" style="width: 40px; height: 40px; font-size: 16px;">
        {{ substr(session('auth_name') ?? 'S', 0, 1) }}
      </div>
      <div class="overflow-hidden">
        <div class="fw-bold text-dark text-truncate" style="font-size: 14px; letter-spacing: -0.3px;">{{ session('auth_name') }}</div>
        <div class="text-muted" style="font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">Staff Portal</div>
      </div>
    </div>
  </div>

  <div class="px-3">
    <nav class="nav flex-column gap-1">
      {{-- SECTION: CORE --}}
      <div class="nav-section-label">Main Menu</div>
      
      <a href="{{ route('staff.dashboard') }}" class="nav-link {{ request()->routeIs('staff.dashboard') ? 'active' : '' }}">
        <i class="bi bi-grid-1x2-fill"></i>
        <span>Dashboard</span>
      </a>

      {{-- SECTION: OPERATIONS --}}
      <div class="nav-section-label">Operations</div>
      
      <a href="{{ route('staff.cars') }}" class="nav-link {{ request()->routeIs('staff.cars*') ? 'active' : '' }}">
        <i class="bi bi-car-front-fill"></i>
        <span>Fleet Management</span>
      </a>
      <a href="{{ route('staff.bookings') }}" class="nav-link {{ request()->routeIs('staff.bookings*') ? 'active' : '' }}">
        <i class="bi bi-journal-check"></i>
        <span>Booking Records</span>
      </a>
      <a href="{{ route('staff.cancellation-requests') }}" class="nav-link {{ request()->routeIs('staff.cancellation-requests*') ? 'active' : '' }}">
        <i class="bi bi-x-octagon-fill"></i>
        <span>Cancellations</span>
      </a>
      <a href="{{ route('staff.customers') }}" class="nav-link {{ request()->routeIs('staff.customers*') ? 'active' : '' }}">
        <i class="bi bi-people-fill"></i>
        <span>Customer Database</span>
      </a>

      {{-- SECTION: GROWTH --}}
      <div class="nav-section-label">Marketing & Growth</div>

      <a href="{{ route('staff.activities') }}" class="nav-link {{ request()->routeIs('staff.activities*') ? 'active' : '' }}">
        <i class="bi bi-calendar-event"></i>
        <span>Company Schedule</span>
      </a>
      <a href="{{ route('staff.vouchers') }}" class="nav-link {{ request()->routeIs('staff.vouchers*') ? 'active' : '' }}">
        <i class="bi bi-ticket-perforated-fill"></i>
        <span>Voucher & Promotion</span>
      </a>
      <a href="{{ route('staff.feedbacks') }}" class="nav-link {{ request()->routeIs('staff.feedbacks*') ? 'active' : '' }}">
        <i class="bi bi-star-fill"></i>
        <span>Reviews & Ratings</span>
      </a>

      {{-- SECTION: SUPPORT --}}
      <div class="nav-section-label">Service & Maintenance</div>

      <a href="{{ route('staff.support-tickets') }}" class="nav-link {{ request()->routeIs('staff.support-tickets*') ? 'active' : '' }}">
        <i class="bi bi-headset"></i>
        <span>Support Center</span>
      </a>
      <a href="{{ route('staff.maintenance-issues') }}" class="nav-link {{ request()->routeIs('staff.maintenance-issues*') ? 'active' : '' }}">
        <i class="bi bi-tools"></i>
        <span>Maintenance Log</span>
      </a>
      <a href="{{ route('staff.penalties') }}" class="nav-link {{ request()->routeIs('staff.penalties*') ? 'active' : '' }}">
        <i class="bi bi-exclamation-triangle-fill"></i>
        <span>Penalties Ledger</span>
      </a>

      {{-- SECTION: SYSTEM --}}
      <div class="nav-section-label">Configuration</div>

      <a href="{{ route('staff.locations') }}" class="nav-link {{ request()->routeIs('staff.locations*') ? 'active' : '' }}">
        <i class="bi bi-geo-alt-fill"></i>
        <span>Service Locations</span>
      </a>
      <a href="{{ route('staff.reports') }}" class="nav-link {{ request()->routeIs('staff.reports*') ? 'active' : '' }}">
        <i class="bi bi-bar-chart-line-fill"></i>
        <span>Business Reports</span>
      </a>

      <div class="my-3 px-3">
        <hr class="m-0 opacity-10">
      </div>
      
      <a href="{{ route('home') }}" class="nav-link secondary-link">
        <i class="bi bi-arrow-left-circle"></i>
        <span>Back to Website</span>
      </a>
      <button type="button" class="nav-link secondary-link border-0 bg-transparent w-100 text-start" data-bs-toggle="modal" data-bs-target="#logoutModal">
        <i class="bi bi-power"></i>
        <span>Secure Logout</span>
      </button>
    </nav>
  </div>
</div>

<style>
  .staff-nav .nav-section-label {
    text-transform: uppercase;
    color: #94a3b8;
    font-weight: 700;
    margin-bottom: 4px;
    margin-top: 12px;
    padding: 0 16px;
    font-size: 10px;
    letter-spacing: 1px;
  }
  .staff-nav .nav-link {
    color: #64748b;
    padding: 10px 16px;
    border-radius: 8px;
    font-size: 13.5px;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 12px;
    transition: all 0.2s ease;
    border: 1px solid transparent;
  }
  .staff-nav .nav-link i {
    font-size: 1.1rem;
    width: 20px;
    text-align: center;
    transition: all 0.2s ease;
  }
  .staff-nav .nav-link:hover {
    background: #f8fafc;
    color: var(--hasta) !important;
    border-color: #f1f5f9;
  }
  .staff-nav .nav-link.active {
    background: #fff1f1;
    color: var(--hasta) !important;
    font-weight: 600;
    border-color: #fee2e2;
  }
  .staff-nav .nav-link.active i {
    color: var(--hasta);
  }
  .staff-nav .secondary-link {
    font-size: 13px;
    color: #94a3b8;
  }
  .staff-nav .secondary-link:hover {
    color: #475569 !important;
    background: #f1f5f9;
  }

  /* Smooth scrolling for sidebar */
  .staff-nav {
    scrollbar-width: thin;
    scrollbar-color: #cbd5e1 #f8fafc;
  }
  .staff-nav::-webkit-scrollbar {
    width: 6px;
  }
  .staff-nav::-webkit-scrollbar-track {
    background: #f8fafc;
  }
  .staff-nav::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 3px;
  }
  .staff-nav::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
  }

  /* Main content area - independent scrolling */
  .d-flex:has(.staff-nav) {
    min-height: auto !important;
    height: calc(100vh - 70px);
    overflow: hidden;
    position: relative;
  }
  .d-flex > .flex-fill {
    margin-left: 260px;
    height: 100%;
    overflow-y: auto;
    overflow-x: hidden;
    position: relative;
    width: calc(100% - 260px);
  }
  .d-flex > .flex-fill > .container-fluid {
    padding-bottom: 100px !important;
    padding-right: 1.5rem !important;
    padding-left: 1.5rem !important;
    box-sizing: border-box;
  }
  @media (min-width: 768px) {
    .d-flex > .flex-fill > .container-fluid {
      padding-right: 2rem !important;
      padding-left: 2rem !important;
    }
  }
  .d-flex > .flex-fill::-webkit-scrollbar {
    width: 8px;
  }
  .d-flex > .flex-fill::-webkit-scrollbar-track {
    background: #f8fafc;
  }
  .d-flex > .flex-fill::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 4px;
  }
  .d-flex > .flex-fill::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
  }

  @media (max-width: 768px) {
    .staff-nav {
      width: 100% !important;
      position: static !important;
      border-right: none !important;
      border-bottom: 1px solid #edf2f7;
      height: auto !important;
      overflow-y: visible !important;
    }
    .d-flex > .flex-fill {
      margin-left: 0 !important;
      width: 100% !important;
      height: auto !important;
      overflow-y: visible !important;
      overflow-x: hidden !important;
    }
  }
</style>

<script>
  // Hide footer on staff pages
  document.addEventListener('DOMContentLoaded', function() {
    const footer = document.querySelector('footer');
    if (footer && document.querySelector('.staff-nav')) {
      footer.style.display = 'none';
    }
  });
</script>
