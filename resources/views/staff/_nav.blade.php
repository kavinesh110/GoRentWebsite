{{-- Staff Navigation Sidebar --}}
<div class="staff-nav shadow-sm" style="background: #fff; border-right: 1px solid #edf2f7; min-height: calc(100vh - 60px); padding: 0; position: sticky; top: 60px; width: 260px; flex-shrink: 0; z-index: 1000;">
  {{-- Staff Profile Summary --}}
  <div class="px-4 py-4 mb-2 border-bottom bg-light-subtle">
    <div class="d-flex align-items-center gap-3">
      <div class="bg-hasta text-white rounded-circle d-flex align-items-center justify-content-center fw-bold shadow-sm" style="width: 42px; height: 42px; font-size: 18px;">
        {{ substr(session('auth_name') ?? 'S', 0, 1) }}
      </div>
      <div class="overflow-hidden">
        <div class="fw-bold text-dark text-truncate" style="font-size: 15px;">{{ session('auth_name') }}</div>
        <div class="text-muted small">Hasta Staff</div>
      </div>
    </div>
  </div>

  <div class="px-3 py-2">
    <nav class="nav flex-column gap-1">
      <div class="nav-section-label text-uppercase text-muted fw-bold mb-2 mt-2 px-3" style="font-size: 11px; letter-spacing: 0.8px;">Main Menu</div>
      
      <a href="{{ route('staff.dashboard') }}" class="nav-link d-flex align-items-center gap-3 {{ request()->routeIs('staff.dashboard') ? 'active' : '' }}">
        <i class="bi bi-speedometer2"></i>
        <span>Dashboard</span>
      </a>

      <div class="nav-section-label text-uppercase text-muted fw-bold mb-2 mt-3 px-3" style="font-size: 11px; letter-spacing: 0.8px;">Management</div>
      
      <a href="{{ route('staff.cars') }}" class="nav-link d-flex align-items-center gap-3 {{ request()->routeIs('staff.cars*') ? 'active' : '' }}">
        <i class="bi bi-car-front"></i>
        <span>Manage Cars</span>
      </a>
      <a href="{{ route('staff.bookings') }}" class="nav-link d-flex align-items-center gap-3 {{ request()->routeIs('staff.bookings*') ? 'active' : '' }}">
        <i class="bi bi-calendar-check"></i>
        <span>Bookings</span>
      </a>
      <a href="{{ route('staff.cancellation-requests') }}" class="nav-link d-flex align-items-center gap-3 {{ request()->routeIs('staff.cancellation-requests*') ? 'active' : '' }}">
        <i class="bi bi-x-circle"></i>
        <span>Cancellations</span>
      </a>
      <a href="{{ route('staff.customers') }}" class="nav-link d-flex align-items-center gap-3 {{ request()->routeIs('staff.customers*') ? 'active' : '' }}">
        <i class="bi bi-people"></i>
        <span>Customers</span>
      </a>

      <div class="nav-section-label text-uppercase text-muted fw-bold mb-2 mt-3 px-3" style="font-size: 11px; letter-spacing: 0.8px;">Marketing & Tools</div>

      <a href="{{ route('staff.activities') }}" class="nav-link d-flex align-items-center gap-3 {{ request()->routeIs('staff.activities*') ? 'active' : '' }}">
        <i class="bi bi-megaphone"></i>
        <span>Promotions</span>
      </a>
      <a href="{{ route('staff.vouchers') }}" class="nav-link d-flex align-items-center gap-3 {{ request()->routeIs('staff.vouchers*') ? 'active' : '' }}">
        <i class="bi bi-ticket-perforated"></i>
        <span>Vouchers</span>
      </a>
      <a href="{{ route('staff.feedbacks') }}" class="nav-link d-flex align-items-center gap-3 {{ request()->routeIs('staff.feedbacks*') ? 'active' : '' }}">
        <i class="bi bi-chat-square-text"></i>
        <span>Feedback</span>
      </a>
      <a href="{{ route('staff.locations') }}" class="nav-link d-flex align-items-center gap-3 {{ request()->routeIs('staff.locations*') ? 'active' : '' }}">
        <i class="bi bi-geo-alt"></i>
        <span>Locations</span>
      </a>

      <div class="nav-section-label text-uppercase text-muted fw-bold mb-2 mt-3 px-3" style="font-size: 11px; letter-spacing: 0.8px;">Operations</div>

      <a href="{{ route('staff.calendar') }}" class="nav-link d-flex align-items-center gap-3 {{ request()->routeIs('staff.calendar*') ? 'active' : '' }}">
        <i class="bi bi-calendar3"></i>
        <span>Schedule</span>
      </a>
      <a href="{{ route('staff.reports') }}" class="nav-link d-flex align-items-center gap-3 {{ request()->routeIs('staff.reports*') ? 'active' : '' }}">
        <i class="bi bi-bar-chart"></i>
        <span>Reports</span>
      </a>
      <a href="{{ route('staff.penalties') }}" class="nav-link d-flex align-items-center gap-3 {{ request()->routeIs('staff.penalties*') ? 'active' : '' }}">
        <i class="bi bi-exclamation-octagon"></i>
        <span>Penalties</span>
      </a>

      <div class="mt-4 mb-3 px-3">
        <hr class="my-2 opacity-10">
      </div>
      
      <a href="{{ route('home') }}" class="nav-link d-flex align-items-center gap-3" style="color: #718096; font-size: 14px;">
        <i class="bi bi-house"></i>
        <span>Public Website</span>
      </a>
      <button type="button" class="nav-link border-0 bg-transparent w-100 text-start d-flex align-items-center gap-3" style="color: #718096; font-size: 14px; cursor: pointer;" data-bs-toggle="modal" data-bs-target="#logoutModal">
        <i class="bi bi-box-arrow-right"></i>
        <span>Logout</span>
      </button>
    </nav>
  </div>
</div>

<style>
  .staff-nav .nav-link {
    color: #4a5568;
    padding: 10px 16px;
    border-radius: 10px;
    font-size: 14.5px;
    font-weight: 500;
    transition: all 0.25s ease;
  }
  .staff-nav .nav-link i {
    font-size: 1.1rem;
    color: #a0aec0;
    transition: all 0.25s ease;
  }
  .staff-nav .nav-link:hover {
    background: #f7fafc;
    color: var(--hasta) !important;
  }
  .staff-nav .nav-link:hover i {
    color: var(--hasta);
  }
  .staff-nav .nav-link.active {
    background: var(--hasta);
    color: #fff !important;
    box-shadow: 0 4px 12px rgba(203, 55, 55, 0.2);
  }
  .staff-nav .nav-link.active i {
    color: #fff !important;
  }

  @media (max-width: 768px) {
    .staff-nav {
      width: 100% !important;
      position: static !important;
      border-right: none !important;
      border-bottom: 1px solid #edf2f7;
      min-height: auto !important;
    }
  }
</style>
