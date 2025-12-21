{{-- Staff Navigation Sidebar --}}
<div class="staff-nav" style="background: #fff; border-right: 1px solid #e9ecef; min-height: calc(100vh - 60px); padding: 24px 0; position: sticky; top: 60px; width: 240px; flex-shrink: 0;">
  <div class="px-3">
    <a href="{{ route('staff.dashboard') }}" class="d-block text-decoration-none mb-3">
      <div class="fw-bold" style="color: var(--hasta-darker); font-size: 18px;">Staff Portal</div>
    </a>
    <nav class="nav flex-column gap-1">
      <a href="{{ route('staff.dashboard') }}" class="nav-link {{ request()->routeIs('staff.dashboard') ? 'active' : '' }}" style="color: #333; padding: 10px 16px; border-radius: 8px;">
        Dashboard
      </a>
      <a href="{{ route('staff.cars') }}" class="nav-link {{ request()->routeIs('staff.cars*') ? 'active' : '' }}" style="color: #333; padding: 10px 16px; border-radius: 8px;">
        Manage Cars
      </a>
      <a href="{{ route('staff.bookings') }}" class="nav-link {{ request()->routeIs('staff.bookings*') ? 'active' : '' }}" style="color: #333; padding: 10px 16px; border-radius: 8px;">
        Bookings
      </a>
      <a href="{{ route('staff.customers') }}" class="nav-link {{ request()->routeIs('staff.customers*') ? 'active' : '' }}" style="color: #333; padding: 10px 16px; border-radius: 8px;">
        Customers
      </a>
      <a href="{{ route('staff.activities') }}" class="nav-link {{ request()->routeIs('staff.activities*') ? 'active' : '' }}" style="color: #333; padding: 10px 16px; border-radius: 8px;">
        Activities & Promotions
      </a>
      <hr class="my-2">
      <a href="{{ route('home') }}" class="nav-link" style="color: #666; padding: 10px 16px; border-radius: 8px; font-size: 13px;">
        Back to Home
      </a>
      <button type="button" class="nav-link border-0 bg-transparent w-100 text-start" style="color: #666; padding: 10px 16px; border-radius: 8px; font-size: 13px; cursor: pointer;" data-bs-toggle="modal" data-bs-target="#logoutModal">
        Logout
      </button>
    </nav>
  </div>
</div>

<style>
  .staff-nav .nav-link {
    transition: all 0.2s;
  }
  .staff-nav .nav-link:hover {
    background: #f8f8f8;
    color: var(--hasta) !important;
  }
  .staff-nav .nav-link.active {
    background: rgba(203, 55, 55, 0.1);
    color: var(--hasta-darker) !important;
    font-weight: 600;
  }

  @media (max-width: 768px) {
    .staff-nav {
      width: 100% !important;
      position: static !important;
      border-right: none !important;
      border-bottom: 1px solid #e9ecef;
      min-height: auto !important;
    }
    .d-flex[style*="min-height"] {
      flex-direction: column !important;
    }
  }
</style>
