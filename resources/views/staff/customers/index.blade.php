@extends('layouts.app')
@section('title', 'Customers - Staff Dashboard')

@section('content')
<div class="d-flex" style="min-height: calc(100vh - 70px);">
  @include('staff._nav')
  <div class="flex-fill" style="background: #f8fafc;">
    <div class="container-fluid px-4 px-md-5 py-4">
      
      {{-- Page Header --}}
      <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
          <h1 class="h4 fw-bold mb-1" style="color:#1e293b; letter-spacing: -0.5px;">Customer Database</h1>
          <p class="text-muted mb-0 small">Manage user profiles, account verifications, and rental history.</p>
        </div>
        <div class="d-flex gap-2">
          <a href="{{ route('staff.customers.export', request()->query()) }}" class="btn btn-outline-success btn-sm px-3 rounded-3 fw-bold border-2">
            <i class="bi bi-file-earmark-spreadsheet me-2"></i>Export CSV
          </a>
        </div>
      </div>

      @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm d-flex align-items-center gap-2 mb-4 rounded-3">
          <i class="bi bi-check-circle-fill"></i>
          <div class="small fw-semibold">{{ session('success') }}</div>
          <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
        </div>
      @endif

      {{-- SEARCH & FILTERS --}}
      <div class="card border-0 shadow-sm mb-4 rounded-4">
        <div class="card-body p-3 bg-white">
          <form method="GET" action="{{ route('staff.customers') }}" class="row g-2 align-items-center">
            <div class="col-md-6 col-lg-4">
              <div class="input-group input-group-sm border rounded-3 overflow-hidden bg-light-subtle">
                <span class="input-group-text bg-transparent border-0 ps-3"><i class="bi bi-search text-muted"></i></span>
                <input type="text" name="search" class="form-control border-0 bg-transparent py-2 px-2" placeholder="Find by name, email, or phone..." value="{{ $filters['search'] ?? '' }}">
              </div>
            </div>
            <div class="col-6 col-lg-3">
              <select name="verification_status" class="form-select form-select-sm border rounded-3 py-2 bg-light-subtle">
                <option value="">Verification Status</option>
                <option value="pending" {{ ($filters['verification_status'] ?? '') === 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="approved" {{ ($filters['verification_status'] ?? '') === 'approved' ? 'selected' : '' }}>Approved</option>
                <option value="rejected" {{ ($filters['verification_status'] ?? '') === 'rejected' ? 'selected' : '' }}>Rejected</option>
              </select>
            </div>
            <div class="col-6 col-lg-2">
              <select name="blacklisted" class="form-select form-select-sm border rounded-3 py-2 bg-light-subtle">
                <option value="">Access Status</option>
                <option value="0" {{ ($filters['blacklisted'] ?? '') === '0' ? 'selected' : '' }}>Active</option>
                <option value="1" {{ ($filters['blacklisted'] ?? '') === '1' ? 'selected' : '' }}>Blacklisted</option>
              </select>
            </div>
            <div class="col-md-12 col-lg-3 d-flex gap-2">
              <button type="submit" class="btn btn-sm btn-dark px-3 rounded-3 py-2 fw-bold">Apply</button>
              <a href="{{ route('staff.customers') }}" class="btn btn-sm btn-light border px-3 rounded-3 py-2 fw-semibold text-muted">Reset</a>
            </div>
          </form>
        </div>
      </div>

      {{-- CUSTOMERS TABLE --}}
      <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
              <thead class="bg-light-subtle">
                <tr>
                  <th class="ps-4 py-3 text-uppercase text-muted fw-bold" style="font-size: 10px; letter-spacing: 0.5px;">User Profile</th>
                  <th class="py-3 text-uppercase text-muted fw-bold" style="font-size: 10px; letter-spacing: 0.5px;">Contact & Loc</th>
                  <th class="py-3 text-uppercase text-muted fw-bold" style="font-size: 10px; letter-spacing: 0.5px;">Verification</th>
                  <th class="py-3 text-uppercase text-muted fw-bold" style="font-size: 10px; letter-spacing: 0.5px;">Activity</th>
                  <th class="pe-4 py-3 text-uppercase text-muted fw-bold text-end" style="font-size: 10px; letter-spacing: 0.5px;">Operation</th>
                </tr>
              </thead>
              <tbody class="border-top-0">
                @forelse($customers as $customer)
                  <tr>
                    <td class="ps-4">
                      <div class="d-flex align-items-center gap-3 py-1">
                        <div class="position-relative">
                          <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center fw-bold shadow-sm" style="width: 42px; height: 42px; font-size: 14px;">
                            {{ substr($customer->full_name ?? 'C', 0, 1) }}
                          </div>
                          @if(!$customer->is_blacklisted)
                            <span class="position-absolute bottom-0 end-0 bg-success border border-white border-2 rounded-circle" style="width: 12px; height: 12px;" title="Active Account"></span>
                          @else
                            <span class="position-absolute bottom-0 end-0 bg-danger border border-white border-2 rounded-circle" style="width: 12px; height: 12px;" title="Blacklisted"></span>
                          @endif
                        </div>
                        <div>
                          <div class="fw-bold text-slate-800 mb-0">{{ $customer->full_name }}</div>
                          <div class="text-muted x-small">Member ID: #{{ $customer->customer_id }}</div>
                        </div>
                      </div>
                    </td>
                    <td>
                      <div class="small text-slate-700 fw-medium mb-1"><i class="bi bi-envelope me-2 text-muted"></i>{{ $customer->email }}</div>
                      <div class="small text-muted mb-1"><i class="bi bi-telephone me-2"></i>{{ $customer->phone ?? '---' }}</div>
                      @if($customer->residential_college)
                        <div class="x-small text-slate-500"><i class="bi bi-geo-alt me-2"></i>{{ $customer->residential_college }}</div>
                      @endif
                    </td>
                    <td>
                      @php
                        $vStatus = $customer->verification_status ?? 'pending';
                        $vBadge = match($vStatus) {
                          'approved' => ['class' => 'bg-success-subtle text-success', 'icon' => 'bi-check-circle-fill'],
                          'rejected' => ['class' => 'bg-danger-subtle text-danger', 'icon' => 'bi-x-circle-fill'],
                          default => ['class' => 'bg-warning-subtle text-warning-emphasis', 'icon' => 'bi-clock-fill']
                        };
                      @endphp
                      <span class="badge rounded-pill border-0 px-3 py-2 {{ $vBadge['class'] }} fw-bold text-uppercase" style="font-size: 9px; letter-spacing: 0.3px;">
                        <i class="bi {{ $vBadge['icon'] }} me-1"></i>{{ ucfirst($vStatus) }}
                      </span>
                    </td>
                    <td>
                      <div class="small text-slate-700 fw-bold">{{ $customer->total_rental_hours ?? 0 }} <span class="fw-normal text-muted">Hours</span></div>
                      <div class="x-small text-muted">Last active: {{ $customer->updated_at->diffForHumans() }}</div>
                    </td>
                    <td class="pe-4 text-end">
                      <a href="{{ route('staff.customers.show', $customer->customer_id) }}" class="btn btn-sm btn-white border rounded-pill px-3 fw-bold x-small shadow-sm">
                        Manage Profile <i class="bi bi-chevron-right ms-1"></i>
                      </a>
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="5" class="text-center py-5">
                      <div class="py-4">
                        <i class="bi bi-people display-4 text-muted opacity-25 d-block mb-3"></i>
                        <h6 class="text-slate-700 fw-bold">No customers found</h6>
                        <p class="text-muted small">Your search or filter criteria didn't match any registered users.</p>
                        <a href="{{ route('staff.customers') }}" class="btn btn-sm btn-light border rounded-pill px-4 mt-2">Clear Search</a>
                      </div>
                    </td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>

      {{-- PAGINATION --}}
      <div class="mt-4 d-flex justify-content-center">
        {{ $customers->links() }}
      </div>
    </div>
  </div>
</div>

<style>
  .text-slate-700 { color: #334155; }
  .text-slate-800 { color: #1e293b; }
  .x-small { font-size: 11px; }
  .bg-light-subtle { background-color: #f8fafc !important; }
  
  .table > :not(caption) > * > * {
    padding: 1.25rem 0.75rem;
    border-bottom: 1px solid #f1f5f9;
  }
  
  .btn-white {
    background: #fff;
    color: #1e293b;
    border-color: #e2e8f0;
    transition: all 0.2s;
  }
  .btn-white:hover {
    background: #f8fafc;
    border-color: #cbd5e1;
    color: var(--hasta);
  }

  .pagination { 
    margin-bottom: 0; 
    gap: 5px;
  }
  .page-item .page-link { 
    border-radius: 10px !important; 
    padding: 8px 16px;
    color: #475569;
    border: 1px solid #e2e8f0;
    font-weight: 600;
    font-size: 13px;
    transition: all 0.2s;
    box-shadow: 0 2px 4px rgba(0,0,0,0.02);
  }
  .page-item:first-child .page-link, .page-item:last-child .page-link {
    border-radius: 10px !important;
  }
  .page-item.active .page-link { 
    background-color: var(--hasta); 
    border-color: var(--hasta); 
    color: #fff; 
    box-shadow: 0 4px 6px -1px rgba(203, 55, 55, 0.2);
  }
  .page-item .page-link:hover:not(.active) {
    background-color: #f8fafc;
    border-color: #cbd5e1;
    color: var(--hasta);
    transform: translateY(-1px);
  }
  .page-item.disabled .page-link {
    background-color: #f1f5f9;
    color: #94a3b8;
    border-color: #e2e8f0;
  }
</style>
@endsection