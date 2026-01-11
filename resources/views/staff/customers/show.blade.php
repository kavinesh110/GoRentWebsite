@extends('layouts.app')
@section('title', 'Customer Details - Staff Dashboard')

@section('content')
<div class="d-flex" style="min-height: calc(100vh - 60px);">
  @include('staff._nav')
  <div class="flex-fill" style="background: #f8fafc;">
    <div class="container-fluid px-4 px-md-5" style="padding-top: 32px; padding-bottom: 48px;">
      
      {{-- Header --}}
      <div class="mb-4">
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb mb-2 small">
            <li class="breadcrumb-item"><a href="{{ route('staff.customers') }}" class="text-decoration-none text-muted">Customers</a></li>
            <li class="breadcrumb-item active text-dark">#{{ $customer->customer_id }}</li>
          </ol>
        </nav>
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
          <div class="d-flex align-items-center gap-3">
            <div class="customer-avatar">
              {{ strtoupper(substr($customer->full_name ?? $customer->email, 0, 1)) }}
            </div>
            <div>
              <h1 class="h4 fw-bold mb-1" style="color:#1a202c;">{{ $customer->full_name ?? 'Unnamed Customer' }}</h1>
              <div class="text-muted small">{{ $customer->email }}</div>
            </div>
          </div>
          @php
            // Single status with clear priority: Blacklisted > Rejected > Pending > Verified
            if ($customer->is_blacklisted) {
                $statusLabel = 'Blacklisted';
                $statusClass = 'bg-danger';
                $statusIcon = 'slash-circle';
            } elseif (($customer->verification_status ?? 'pending') === 'rejected') {
                $statusLabel = 'Rejected';
                $statusClass = 'bg-danger';
                $statusIcon = 'x-circle';
            } elseif (($customer->verification_status ?? 'pending') === 'pending') {
                $statusLabel = 'Pending Verification';
                $statusClass = 'bg-warning text-dark';
                $statusIcon = 'hourglass-split';
            } else {
                $statusLabel = 'Verified';
                $statusClass = 'bg-success';
                $statusIcon = 'patch-check-fill';
            }
          @endphp
          <span class="badge {{ $statusClass }} px-3 py-2">
            <i class="bi bi-{{ $statusIcon }} me-1"></i>{{ $statusLabel }}
          </span>
        </div>
      </div>

      @if(session('success'))
        <div class="alert alert-success border-0 d-flex align-items-center gap-2 mb-4">
          <i class="bi bi-check-circle-fill"></i>
          <div>{{ session('success') }}</div>
          <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
        </div>
      @endif

      <div class="row g-4">
        {{-- Left Column - Customer Info --}}
        <div class="col-lg-8">
          {{-- Quick Stats --}}
          <div class="row g-3 mb-4">
            <div class="col-sm-4">
              <div class="stat-card">
                <div class="stat-icon bg-primary-subtle text-primary">
                  <i class="bi bi-wallet2"></i>
                </div>
                <div>
                  <div class="stat-label">Deposit Balance</div>
                  <div class="stat-value">RM {{ number_format($customer->deposit_balance ?? 0, 2) }}</div>
                </div>
              </div>
            </div>
            <div class="col-sm-4">
              <div class="stat-card">
                <div class="stat-icon bg-success-subtle text-success">
                  <i class="bi bi-clock-history"></i>
                </div>
                <div>
                  <div class="stat-label">Rental Hours</div>
                  <div class="stat-value">{{ $customer->total_rental_hours ?? 0 }}h</div>
                </div>
              </div>
            </div>
            <div class="col-sm-4">
              <div class="stat-card">
                <div class="stat-icon bg-warning-subtle text-warning">
                  <i class="bi bi-star-fill"></i>
                </div>
                <div>
                  <div class="stat-label">Loyalty Stamps</div>
                  <div class="stat-value">{{ $customer->total_stamps ?? 0 }}</div>
                </div>
              </div>
            </div>
          </div>

          {{-- Profile Information --}}
          <div class="content-card mb-4">
            <div class="content-card-header">
              <h6 class="fw-bold mb-0"><i class="bi bi-person me-2 text-primary"></i>Profile Information</h6>
            </div>
            <div class="content-card-body">
              <div class="row g-4">
                <div class="col-sm-6">
                  <div class="info-item">
                    <label>Full Name</label>
                    <div>{{ $customer->full_name ?? 'Not provided' }}</div>
                  </div>
                </div>
                <div class="col-sm-6">
                  <div class="info-item">
                    <label>Email</label>
                    <div>{{ $customer->email }}</div>
                  </div>
                </div>
                <div class="col-sm-6">
                  <div class="info-item">
                    <label>Phone</label>
                    <div>{{ $customer->phone ?? 'Not provided' }}</div>
                  </div>
                </div>
                <div class="col-sm-6">
                  <div class="info-item">
                    <label>UTM Role</label>
                    <div>{{ ucfirst($customer->utm_role ?? 'Not specified') }}</div>
                  </div>
                </div>
                <div class="col-sm-6">
                  <div class="info-item">
                    <label>College</label>
                    <div>{{ $customer->college_name ?? 'Not provided' }}</div>
                  </div>
                </div>
                <div class="col-sm-6">
                  <div class="info-item">
                    <label>Member Since</label>
                    <div>{{ $customer->created_at?->format('d M Y') ?? 'N/A' }}</div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          {{-- Recent Bookings --}}
          <div class="content-card">
            <div class="content-card-header d-flex justify-content-between align-items-center">
              <h6 class="fw-bold mb-0"><i class="bi bi-calendar-check me-2 text-primary"></i>Recent Bookings</h6>
              <a href="{{ route('staff.bookings') }}?customer_id={{ $customer->customer_id }}" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="content-card-body p-0">
              @if($customer->bookings && $customer->bookings->count() > 0)
                <div class="table-responsive">
                  <table class="table table-hover align-middle mb-0">
                    <thead>
                      <tr>
                        <th class="ps-4">ID</th>
                        <th>Vehicle</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th class="text-end pe-4">Amount</th>
                      </tr>
                    </thead>
                    <tbody>
                      @foreach($customer->bookings->take(5) as $booking)
                        <tr>
                          <td class="ps-4">
                            <a href="{{ route('staff.bookings.show', $booking->booking_id) }}" class="fw-semibold text-primary text-decoration-none">
                              #{{ $booking->booking_id }}
                            </a>
                          </td>
                          <td class="text-muted">{{ $booking->car->brand ?? '' }} {{ $booking->car->model ?? '' }}</td>
                          <td class="text-muted">{{ $booking->start_datetime?->format('d M Y') ?? 'N/A' }}</td>
                          <td>
                            @php
                              $statusColors = [
                                'created' => 'secondary',
                                'verified' => 'info',
                                'confirmed' => 'primary',
                                'active' => 'success',
                                'completed' => 'success',
                                'cancelled' => 'danger'
                              ];
                              $color = $statusColors[$booking->status] ?? 'secondary';
                            @endphp
                            <span class="badge bg-{{ $color }}-subtle text-{{ $color }} px-2 py-1">{{ ucfirst($booking->status ?? 'N/A') }}</span>
                          </td>
                          <td class="text-end pe-4 fw-semibold">RM {{ number_format($booking->final_amount ?? 0, 2) }}</td>
                        </tr>
                      @endforeach
                    </tbody>
                  </table>
                </div>
              @else
                <div class="text-center py-5 text-muted">
                  <i class="bi bi-calendar-x display-6 d-block mb-2 opacity-25"></i>
                  <p class="mb-0">No bookings yet</p>
                </div>
              @endif
            </div>
          </div>
        </div>

        {{-- Right Column - Actions --}}
        <div class="col-lg-4">
          {{-- Identity Documents --}}
          @php
            $hasIc = !empty($customer->getRawOriginal('ic_url'));
            $hasUtmid = !empty($customer->getRawOriginal('utmid_url'));
            $hasLicense = !empty($customer->getRawOriginal('license_url'));
            $hasAllDocuments = $hasIc && $hasUtmid && $hasLicense;
          @endphp
          
          <div class="content-card mb-4">
            <div class="content-card-header">
              <h6 class="fw-bold mb-0"><i class="bi bi-file-earmark-text me-2 text-primary"></i>Identity Documents</h6>
            </div>
            <div class="content-card-body">
              <div class="doc-list">
                {{-- IC/Passport --}}
                <div class="doc-item {{ $hasIc ? 'uploaded' : '' }}">
                  <div class="doc-info">
                    <i class="bi bi-{{ $hasIc ? 'check-circle-fill text-success' : 'x-circle text-muted' }}"></i>
                    <span>IC / Passport</span>
                  </div>
                  @if($hasIc)
                    <a href="{{ $customer->ic_url }}" target="_blank" class="btn btn-sm btn-outline-primary">View</a>
                  @else
                    <span class="badge bg-warning-subtle text-warning-emphasis">Missing</span>
                  @endif
                </div>
                
                {{-- UTM ID --}}
                <div class="doc-item {{ $hasUtmid ? 'uploaded' : '' }}">
                  <div class="doc-info">
                    <i class="bi bi-{{ $hasUtmid ? 'check-circle-fill text-success' : 'x-circle text-muted' }}"></i>
                    <span>UTM ID</span>
                  </div>
                  @if($hasUtmid)
                    <a href="{{ $customer->utmid_url }}" target="_blank" class="btn btn-sm btn-outline-primary">View</a>
                  @else
                    <span class="badge bg-warning-subtle text-warning-emphasis">Missing</span>
                  @endif
                </div>
                
                {{-- Driving License --}}
                <div class="doc-item {{ $hasLicense ? 'uploaded' : '' }}">
                  <div class="doc-info">
                    <i class="bi bi-{{ $hasLicense ? 'check-circle-fill text-success' : 'x-circle text-muted' }}"></i>
                    <span>Driving License</span>
                  </div>
                  @if($hasLicense)
                    <a href="{{ $customer->license_url }}" target="_blank" class="btn btn-sm btn-outline-primary">View</a>
                  @else
                    <span class="badge bg-warning-subtle text-warning-emphasis">Missing</span>
                  @endif
                </div>
              </div>
              
              @if(!$hasAllDocuments)
                <div class="alert alert-warning small mb-0 mt-3">
                  <i class="bi bi-exclamation-triangle me-1"></i>
                  Documents incomplete. Customer needs to upload missing documents.
                </div>
              @endif
            </div>
          </div>

          {{-- Verification --}}
          <div class="content-card mb-4">
            <div class="content-card-header">
              <h6 class="fw-bold mb-0"><i class="bi bi-shield-check me-2 text-primary"></i>Verification</h6>
            </div>
            <div class="content-card-body">
              <form method="POST" action="{{ route('staff.customers.verify', $customer->customer_id) }}">
                @csrf
                @method('PATCH')
                
                <div class="mb-3">
                  <label class="form-label small fw-semibold">Status</label>
                  <select name="verification_status" class="form-select" {{ !$hasAllDocuments ? 'disabled' : '' }}>
                    <option value="pending" {{ ($customer->verification_status ?? 'pending') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ $customer->verification_status === 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="rejected" {{ $customer->verification_status === 'rejected' ? 'selected' : '' }}>Rejected</option>
                  </select>
                </div>
                
                <button type="submit" class="btn btn-primary w-100" {{ !$hasAllDocuments ? 'disabled' : '' }}>
                  <i class="bi bi-check-lg me-1"></i> Update Status
                </button>
                
                @if(!$hasAllDocuments)
                  <div class="text-muted small text-center mt-2">
                    <i class="bi bi-info-circle me-1"></i>Upload all documents first
                  </div>
                @endif
              </form>
            </div>
          </div>

          {{-- Account Access --}}
          <div class="content-card border-danger-subtle">
            <div class="content-card-header">
              <h6 class="fw-bold mb-0"><i class="bi bi-shield-exclamation me-2 text-danger"></i>Account Access</h6>
            </div>
            <div class="content-card-body">
              <form method="POST" action="{{ route('staff.customers.blacklist', $customer->customer_id) }}">
                @csrf
                @method('PATCH')
                
                <div class="mb-3">
                  <label class="form-label small fw-semibold">Access Status</label>
                  <select name="is_blacklisted" class="form-select">
                    <option value="0" {{ !$customer->is_blacklisted ? 'selected' : '' }}>Active</option>
                    <option value="1" {{ $customer->is_blacklisted ? 'selected' : '' }}>Blacklisted</option>
                  </select>
                </div>
                
                <div class="mb-3">
                  <label class="form-label small fw-semibold">Reason (if blacklisting)</label>
                  <textarea name="blacklist_reason" class="form-control" rows="2" placeholder="Optional...">{{ $customer->blacklist_reason }}</textarea>
                </div>
                
                <button type="submit" class="btn btn-outline-danger w-100">
                  <i class="bi bi-shield me-1"></i> Update Access
                </button>
                
                @if($customer->is_blacklisted && $customer->blacklist_since)
                  <div class="text-danger small text-center mt-2">
                    Blacklisted since {{ $customer->blacklist_since->format('d M Y') }}
                  </div>
                @endif
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<style>
  /* Avatar */
  .customer-avatar {
    width: 56px;
    height: 56px;
    background: linear-gradient(135deg, var(--hasta) 0%, #a02a2a 100%);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    font-size: 22px;
    font-weight: 700;
  }
  
  /* Stat Cards */
  .stat-card {
    background: #fff;
    border: 1px solid #e9ecef;
    border-radius: 12px;
    padding: 16px;
    display: flex;
    align-items: center;
    gap: 14px;
  }
  .stat-icon {
    width: 44px;
    height: 44px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
  }
  .stat-label {
    font-size: 11px;
    font-weight: 600;
    color: #6c757d;
    text-transform: uppercase;
    letter-spacing: 0.3px;
    margin-bottom: 2px;
  }
  .stat-value {
    font-size: 18px;
    font-weight: 700;
    color: #1a202c;
  }
  
  /* Content Cards */
  .content-card {
    background: #fff;
    border: 1px solid #e9ecef;
    border-radius: 12px;
    overflow: hidden;
  }
  .content-card-header {
    padding: 16px 20px;
    border-bottom: 1px solid #f0f0f0;
    background: #fafbfc;
  }
  .content-card-body {
    padding: 20px;
  }
  .content-card-body.p-0 {
    padding: 0;
  }
  
  /* Info Items */
  .info-item label {
    font-size: 11px;
    font-weight: 600;
    color: #6c757d;
    text-transform: uppercase;
    letter-spacing: 0.3px;
    margin-bottom: 4px;
    display: block;
  }
  .info-item > div {
    font-size: 14px;
    font-weight: 500;
    color: #1a202c;
  }
  
  /* Table Styling */
  .table thead th {
    font-size: 10px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: #6c757d;
    border-bottom: 1px solid #e9ecef;
    padding: 12px 8px;
    background: #fafbfc;
  }
  .table tbody td {
    font-size: 13px;
    padding: 14px 8px;
    border-bottom: 1px solid #f5f5f5;
  }
  .table tbody tr:last-child td {
    border-bottom: none;
  }
  
  /* Document List */
  .doc-list {
    display: flex;
    flex-direction: column;
    gap: 10px;
  }
  .doc-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 12px;
    background: #f8f9fa;
    border-radius: 8px;
    border: 1px solid #e9ecef;
  }
  .doc-item.uploaded {
    background: #f0fff4;
    border-color: #c3e6cb;
  }
  .doc-info {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 13px;
    font-weight: 500;
  }
  .doc-info i {
    font-size: 16px;
  }
  
  /* Form Controls */
  .form-select, .form-control {
    font-size: 13px;
    border-radius: 8px;
  }
  .form-label {
    margin-bottom: 6px;
  }
  
  /* Buttons */
  .btn-primary {
    background: var(--hasta);
    border-color: var(--hasta);
  }
  .btn-primary:hover {
    background: #a02a2a;
    border-color: #a02a2a;
  }
  .btn-outline-primary {
    color: var(--hasta);
    border-color: var(--hasta);
  }
  .btn-outline-primary:hover {
    background: var(--hasta);
    border-color: var(--hasta);
  }
</style>
@endsection
