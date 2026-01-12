{{-- Staff Navigation Sidebar --}}
<div class="staff-nav" id="mainSidebar">
  {{-- Collapse Button --}}
  <button class="sidebar-toggle" id="mainSidebarToggle" title="Collapse sidebar">
    <i class="bi bi-chevron-left"></i>
  </button>

  {{-- Staff Profile Summary --}}
  <div class="sidebar-profile">
    <div class="profile-avatar bg-hasta text-white rounded-3 d-flex align-items-center justify-content-center fw-bold shadow-sm">
      {{ substr(session('auth_name') ?? 'S', 0, 1) }}
    </div>
    <div class="profile-info">
      <div class="fw-bold text-dark text-truncate" style="font-size: 14px;">{{ session('auth_name') }}</div>
      <div class="text-muted" style="font-size: 11px; font-weight: 600; text-transform: uppercase;">Staff Portal</div>
    </div>
  </div>

  <div class="sidebar-menu">
    <nav class="nav flex-column">
      {{-- SECTION: CORE --}}
      <div class="nav-section-label">Main Menu</div>
      
      <a href="{{ route('staff.dashboard') }}" class="nav-link {{ request()->routeIs('staff.dashboard') ? 'active' : '' }}" data-tooltip="Dashboard">
        <i class="bi bi-grid-1x2-fill"></i>
        <span class="nav-text">Dashboard</span>
      </a>

      {{-- SECTION: OPERATIONS --}}
      <div class="nav-section-label">Operations</div>
      
      <a href="{{ route('staff.cars') }}" class="nav-link {{ request()->routeIs('staff.cars*') ? 'active' : '' }}" data-tooltip="Fleet Management">
        <i class="bi bi-car-front-fill"></i>
        <span class="nav-text">Fleet Management</span>
      </a>
      <a href="{{ route('staff.bookings') }}" class="nav-link {{ request()->routeIs('staff.bookings*') ? 'active' : '' }}" data-tooltip="Booking Records">
        <i class="bi bi-journal-check"></i>
        <span class="nav-text">Booking Records</span>
      </a>
      <a href="{{ route('staff.cancellation-requests') }}" class="nav-link {{ request()->routeIs('staff.cancellation-requests*') ? 'active' : '' }}" data-tooltip="Cancellations">
        <i class="bi bi-x-octagon-fill"></i>
        <span class="nav-text">Cancellations</span>
      </a>
      <a href="{{ route('staff.customers') }}" class="nav-link {{ request()->routeIs('staff.customers*') ? 'active' : '' }}" data-tooltip="Customer Database">
        <i class="bi bi-people-fill"></i>
        <span class="nav-text">Customer Database</span>
      </a>

      {{-- SECTION: GROWTH --}}
      <div class="nav-section-label">Marketing & Growth</div>

      <a href="{{ route('staff.activities') }}" class="nav-link {{ request()->routeIs('staff.activities*') ? 'active' : '' }}" data-tooltip="Company Schedule">
        <i class="bi bi-calendar-event"></i>
        <span class="nav-text">Company Schedule</span>
      </a>
      <a href="{{ route('staff.vouchers') }}" class="nav-link {{ request()->routeIs('staff.vouchers*') ? 'active' : '' }}" data-tooltip="Voucher & Promotion">
        <i class="bi bi-ticket-perforated-fill"></i>
        <span class="nav-text">Voucher & Promotion</span>
      </a>
      <a href="{{ route('staff.feedbacks') }}" class="nav-link {{ request()->routeIs('staff.feedbacks*') ? 'active' : '' }}" data-tooltip="Reviews & Ratings">
        <i class="bi bi-star-fill"></i>
        <span class="nav-text">Reviews & Ratings</span>
      </a>

      {{-- SECTION: SUPPORT --}}
      <div class="nav-section-label">Service & Maintenance</div>

      <a href="{{ route('staff.support-tickets') }}" class="nav-link {{ request()->routeIs('staff.support-tickets*') ? 'active' : '' }}" data-tooltip="Support Center">
        <i class="bi bi-headset"></i>
        <span class="nav-text">Support Center</span>
      </a>
      <a href="{{ route('staff.maintenance') }}" class="nav-link {{ request()->routeIs('staff.maintenance') || request()->routeIs('staff.maintenance.index') ? 'active' : '' }}" data-tooltip="Maintenance Log">
        <i class="bi bi-tools"></i>
        <span class="nav-text">Maintenance Log</span>
      </a>
      <a href="{{ route('staff.penalties') }}" class="nav-link {{ request()->routeIs('staff.penalties*') ? 'active' : '' }}" data-tooltip="Penalties Ledger">
        <i class="bi bi-exclamation-triangle-fill"></i>
        <span class="nav-text">Penalties Ledger</span>
      </a>

      {{-- SECTION: SYSTEM --}}
      <div class="nav-section-label">Configuration</div>

      <a href="{{ route('staff.locations') }}" class="nav-link {{ request()->routeIs('staff.locations*') ? 'active' : '' }}" data-tooltip="Service Locations">
        <i class="bi bi-geo-alt-fill"></i>
        <span class="nav-text">Service Locations</span>
      </a>
      <a href="{{ route('staff.reports') }}" class="nav-link {{ request()->routeIs('staff.reports*') ? 'active' : '' }}" data-tooltip="Business Reports">
        <i class="bi bi-bar-chart-line-fill"></i>
        <span class="nav-text">Business Reports</span>
      </a>

      <div class="nav-divider"></div>
      
      <a href="{{ route('home') }}" class="nav-link secondary-link" data-tooltip="Back to Website">
        <i class="bi bi-arrow-left-circle"></i>
        <span class="nav-text">Back to Website</span>
      </a>
      <button type="button" class="nav-link secondary-link border-0 bg-transparent w-100 text-start" data-bs-toggle="modal" data-bs-target="#logoutModal" data-tooltip="Secure Logout">
        <i class="bi bi-power"></i>
        <span class="nav-text">Secure Logout</span>
      </button>
    </nav>
  </div>
</div>

<style>
  /* ========================================
     MAIN SIDEBAR - EXPANDED STATE
     ======================================== */
  .staff-nav {
    background: #fff;
    border-right: 1px solid #e2e8f0;
    height: calc(100vh - 70px);
    position: fixed;
    top: 70px;
    left: 0;
    width: 250px;
    z-index: 1000;
    transition: width 0.25s ease;
    overflow-y: auto;
    overflow-x: hidden;
    display: flex;
    flex-direction: column;
  }

  /* Toggle Button */
  .sidebar-toggle {
    position: absolute;
    top: 20px;
    right: 12px;
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
    z-index: 10;
    font-size: 12px;
  }

  .sidebar-toggle:hover {
    background: #f1f5f9;
    color: #475569;
    border-color: #cbd5e1;
  }

  /* Profile Section */
  .sidebar-profile {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 20px 16px;
    border-bottom: 1px solid #f1f5f9;
  }

  .profile-avatar {
    width: 38px;
    height: 38px;
    min-width: 38px;
    font-size: 15px;
    flex-shrink: 0;
  }

  .profile-info {
    overflow: hidden;
    transition: opacity 0.2s, width 0.2s;
  }

  /* Menu Section */
  .sidebar-menu {
    padding: 12px;
    flex: 1;
  }

  .sidebar-menu .nav {
    gap: 2px;
  }

  .nav-section-label {
    text-transform: uppercase;
    color: #94a3b8;
    font-weight: 700;
    margin: 16px 0 6px 12px;
    font-size: 10px;
    letter-spacing: 0.8px;
    white-space: nowrap;
    transition: opacity 0.2s;
  }

  .nav-section-label:first-child {
    margin-top: 4px;
  }

  .nav-link {
    color: #64748b;
    padding: 10px 12px;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 10px;
    transition: all 0.15s ease;
    text-decoration: none;
    white-space: nowrap;
    position: relative;
  }

  .nav-link i {
    font-size: 16px;
    width: 20px;
    min-width: 20px;
    text-align: center;
    color: #94a3b8;
    transition: color 0.15s;
  }

  .nav-link .nav-text {
    transition: opacity 0.2s;
  }

  .nav-link:hover {
    background: #f8fafc;
    color: #1e293b;
  }

  .nav-link:hover i {
    color: #64748b;
  }

  .nav-link.active {
    background: #fff1f1;
    color: var(--hasta);
    font-weight: 600;
  }

  .nav-link.active i {
    color: var(--hasta);
  }

  .secondary-link {
    color: #94a3b8 !important;
    font-size: 12px;
  }

  .secondary-link:hover {
    background: #f8fafc !important;
    color: #64748b !important;
  }

  .nav-divider {
    height: 1px;
    background: #f1f5f9;
    margin: 12px 0;
  }

  /* ========================================
     MAIN SIDEBAR - COLLAPSED STATE
     ======================================== */
  .staff-nav.collapsed {
    width: 64px;
  }

  .staff-nav.collapsed .sidebar-toggle {
    position: relative;
    top: 0;
    right: 0;
    margin: 16px auto 8px;
  }

  .staff-nav.collapsed .sidebar-toggle i {
    transform: rotate(180deg);
  }

  .staff-nav.collapsed .sidebar-profile {
    flex-direction: column;
    padding: 0 12px 16px;
    border-bottom: 1px solid #f1f5f9;
  }

  .staff-nav.collapsed .profile-info {
    display: none;
  }

  .staff-nav.collapsed .sidebar-menu {
    padding: 8px;
  }

  .staff-nav.collapsed .nav-section-label {
    display: none;
  }

  .staff-nav.collapsed .nav-link {
    justify-content: center;
    padding: 10px;
    margin: 2px 0;
  }

  .staff-nav.collapsed .nav-link .nav-text {
    display: none;
  }

  .staff-nav.collapsed .nav-divider {
    margin: 8px 0;
  }

  /* Tooltip on hover (collapsed only) */
  .staff-nav.collapsed .nav-link::after {
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

  .staff-nav.collapsed .nav-link:hover::after {
    opacity: 1;
    visibility: visible;
  }

  /* ========================================
     CONTENT AREA ADJUSTMENTS
     ======================================== */
  .d-flex:has(.staff-nav) {
    height: calc(100vh - 70px);
    overflow: hidden;
  }

  .d-flex > .flex-fill {
    margin-left: 250px;
    height: 100%;
    overflow-y: auto;
    overflow-x: hidden;
    width: calc(100% - 250px);
    transition: margin-left 0.25s ease, width 0.25s ease;
  }

  .d-flex:has(.staff-nav.collapsed) > .flex-fill {
    margin-left: 64px;
    width: calc(100% - 64px);
  }

  .d-flex > .flex-fill > .container-fluid {
    padding-bottom: 80px !important;
  }

  /* Scrollbar */
  .staff-nav::-webkit-scrollbar {
    width: 4px;
  }
  .staff-nav::-webkit-scrollbar-track {
    background: transparent;
  }
  .staff-nav::-webkit-scrollbar-thumb {
    background: #e2e8f0;
    border-radius: 2px;
  }
  .staff-nav::-webkit-scrollbar-thumb:hover {
    background: #cbd5e1;
  }

  /* ========================================
     MOBILE RESPONSIVE
     ======================================== */
  @media (max-width: 768px) {
    .staff-nav {
      width: 100% !important;
      position: static !important;
      height: auto !important;
      border-right: none !important;
      border-bottom: 1px solid #e2e8f0;
    }
    .staff-nav.collapsed {
      width: 100% !important;
    }
    .staff-nav .sidebar-toggle {
      display: none;
    }
    .staff-nav.collapsed .profile-info,
    .staff-nav.collapsed .nav-section-label,
    .staff-nav.collapsed .nav-link .nav-text {
      display: block !important;
    }
    .staff-nav.collapsed .sidebar-profile {
      flex-direction: row;
      padding: 16px;
    }
    .staff-nav.collapsed .nav-link {
      justify-content: flex-start;
      padding: 10px 12px;
    }
    .d-flex > .flex-fill {
      margin-left: 0 !important;
      width: 100% !important;
      height: auto !important;
    }
  }
</style>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    // Hide footer on staff pages
    const footer = document.querySelector('footer');
    if (footer && document.querySelector('.staff-nav')) {
      footer.style.display = 'none';
    }

    // Sidebar toggle
    const mainSidebar = document.getElementById('mainSidebar');
    const mainToggle = document.getElementById('mainSidebarToggle');
    
    // Restore saved state
    if (localStorage.getItem('mainSidebarCollapsed') === 'true') {
      mainSidebar.classList.add('collapsed');
    }

    if (mainToggle) {
      mainToggle.addEventListener('click', function() {
        mainSidebar.classList.toggle('collapsed');
        localStorage.setItem('mainSidebarCollapsed', mainSidebar.classList.contains('collapsed'));
      });
    }
  });
</script>
