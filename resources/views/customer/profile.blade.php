@extends('layouts.app')
@section('title', 'My Profile - Hasta GoRent')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
<style>
  /* ===== PROFILE PAGE STYLES ===== */
  .profile-container {
    width: 100%;
    padding: 32px 24px 60px;
  }
  @media (min-width: 768px) {
    .profile-container {
      padding: 32px 40px 60px;
    }
  }
  @media (min-width: 1200px) {
    .profile-container {
      padding: 32px 60px 60px;
    }
  }
  
  /* Profile Header */
  .profile-header {
    background: linear-gradient(135deg, var(--hasta) 0%, #a02a2a 100%);
    border-radius: 20px;
    padding: 28px;
    color: white;
    margin-bottom: 24px;
  }
  .profile-avatar {
    width: 80px;
    height: 80px;
    background: rgba(255,255,255,0.2);
    border: 3px solid rgba(255,255,255,0.4);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    font-weight: 700;
    flex-shrink: 0;
  }
  .profile-name {
    font-size: 22px;
    font-weight: 700;
    margin-bottom: 4px;
  }
  .profile-email {
    font-size: 14px;
    opacity: 0.85;
    margin-bottom: 12px;
  }
  .profile-badges {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
  }
  .profile-badge {
    background: rgba(255,255,255,0.2);
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
  }
  .profile-badge.verified { background: #28a745; }
  .profile-badge.pending { background: #ffc107; color: #333; }
  .profile-badge.rejected { background: #dc3545; }
  
  /* Section Cards */
  .section-card {
    background: #fff;
    border-radius: 16px;
    border: 1px solid #eee;
    margin-bottom: 20px;
    overflow: hidden;
  }
  .section-header {
    padding: 20px 24px;
    border-bottom: 1px solid #f0f0f0;
    display: flex;
    justify-content: space-between;
    align-items: center;
  }
  .section-title {
    font-size: 16px;
    font-weight: 700;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 10px;
  }
  .section-title i {
    color: var(--hasta);
  }
  .section-body {
    padding: 24px;
  }
  
  /* Info Grid */
  .info-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 20px;
  }
  .info-item {
    display: flex;
    flex-direction: column;
  }
  .info-label {
    font-size: 11px;
    font-weight: 600;
    color: #999;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 6px;
  }
  .info-value {
    font-size: 15px;
    font-weight: 600;
    color: #333;
  }
  .info-value.empty {
    color: #bbb;
    font-style: italic;
  }
  
  /* Stats Row */
  .stats-row {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 16px;
    margin-bottom: 20px;
  }
  .stat-box {
    background: #fff;
    border: 1px solid #eee;
    border-radius: 14px;
    padding: 20px 16px;
    text-align: center;
  }
  .stat-value {
    font-size: 24px;
    font-weight: 700;
    color: var(--hasta);
    margin-bottom: 4px;
  }
  .stat-label {
    font-size: 12px;
    color: #888;
    font-weight: 500;
  }
  
  /* Document Grid */
  .doc-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 16px;
  }
  .doc-item {
    border: 2px dashed #e0e0e0;
    border-radius: 14px;
    padding: 20px 16px;
    text-align: center;
    transition: all 0.2s;
  }
  .doc-item:hover {
    border-color: var(--hasta);
    background: #fdf6f6;
  }
  .doc-item.uploaded {
    border-style: solid;
    border-color: #28a745;
    background: #f0fff4;
  }
  .doc-icon {
    font-size: 28px;
    color: var(--hasta);
    margin-bottom: 10px;
  }
  .doc-item.uploaded .doc-icon {
    color: #28a745;
  }
  .doc-name {
    font-size: 13px;
    font-weight: 600;
    margin-bottom: 10px;
  }
  .doc-status {
    font-size: 11px;
    color: #888;
    margin-bottom: 10px;
  }
  .doc-item.uploaded .doc-status {
    color: #28a745;
  }
  
  /* Form Styles */
  .form-group {
    margin-bottom: 20px;
  }
  .form-label-custom {
    font-size: 12px;
    font-weight: 600;
    color: #666;
    margin-bottom: 8px;
    display: block;
  }
  .form-input {
    width: 100%;
    padding: 14px 16px;
    border: 1px solid #e0e0e0;
    border-radius: 10px;
    font-size: 15px;
    transition: all 0.2s;
  }
  .form-input:focus {
    outline: none;
    border-color: var(--hasta);
    box-shadow: 0 0 0 3px rgba(203, 55, 55, 0.1);
  }
  .form-input:disabled {
    background: #f8f9fa;
    color: #999;
  }
  
  /* Buttons */
  .btn-primary-custom {
    background: var(--hasta);
    color: #fff;
    border: none;
    padding: 14px 28px;
    border-radius: 10px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
  }
  .btn-primary-custom:hover {
    background: #a82c2c;
    transform: translateY(-1px);
  }
  .btn-secondary-custom {
    background: #f0f0f0;
    color: #333;
    border: none;
    padding: 14px 28px;
    border-radius: 10px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
  }
  .btn-secondary-custom:hover {
    background: #e0e0e0;
  }
  .btn-sm-custom {
    padding: 8px 16px;
    font-size: 12px;
    border-radius: 8px;
  }
  
  
  /* Alerts */
  .alert-custom {
    padding: 16px 20px;
    border-radius: 12px;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 12px;
  }
  .alert-success-custom {
    background: #d4edda;
    color: #155724;
  }
  .alert-danger-custom {
    background: #f8d7da;
    color: #721c24;
  }
  
  /* Mobile Responsive */
  @media (max-width: 767px) {
    .profile-container {
      padding: 20px 16px 40px;
    }
    .profile-header {
      padding: 24px 20px;
      border-radius: 16px;
    }
    .profile-avatar {
      width: 64px;
      height: 64px;
      font-size: 1.5rem;
    }
    .profile-name {
      font-size: 20px;
    }
    .profile-email {
      font-size: 13px;
    }
    .profile-badge {
      font-size: 11px;
      padding: 5px 10px;
    }
    .section-header {
      padding: 16px 20px;
    }
    .section-body {
      padding: 20px;
    }
    .info-grid {
      grid-template-columns: 1fr;
      gap: 16px;
    }
    .stats-row {
      grid-template-columns: 1fr;
      gap: 12px;
    }
    .stat-box {
      padding: 16px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      text-align: left;
    }
    .stat-value {
      font-size: 20px;
      margin-bottom: 0;
      order: 2;
    }
    .stat-label {
      order: 1;
    }
    .doc-grid {
      grid-template-columns: 1fr;
      gap: 12px;
    }
    .doc-item {
      display: flex;
      align-items: center;
      text-align: left;
      padding: 16px;
      gap: 16px;
    }
    .doc-icon {
      font-size: 24px;
      margin-bottom: 0;
    }
    .doc-info {
      flex: 1;
    }
    .doc-name {
      margin-bottom: 4px;
    }
    .doc-status {
      margin-bottom: 0;
    }
    .doc-actions {
      display: flex;
      flex-direction: column;
      gap: 8px;
    }
    .btn-primary-custom,
    .btn-secondary-custom {
      width: 100%;
    }
    .button-row {
      flex-direction: column;
      gap: 12px;
    }
  }
</style>
@endpush

@section('content')
{{-- MOBILE BACK BUTTON --}}
<div class="d-md-none" style="background: var(--hasta-darker); padding: 12px 16px;">
  <a href="{{ route('home') }}" class="text-white text-decoration-none d-inline-flex align-items-center gap-2" style="font-size: 14px; font-weight: 500;">
    <i class="bi bi-arrow-left"></i> Back
  </a>
</div>

<div class="profile-container">
  {{-- Alerts --}}
  @if(session('success'))
    <div class="alert-custom alert-success-custom">
      <i class="bi bi-check-circle-fill"></i>
      <span>{{ session('success') }}</span>
    </div>
  @endif
  @if($errors->any())
    <div class="alert-custom alert-danger-custom">
      <i class="bi bi-exclamation-circle-fill"></i>
      <span>{{ $errors->first() }}</span>
    </div>
  @endif

  {{-- PROFILE HEADER --}}
  <div class="profile-header">
    <div class="d-flex align-items-center gap-3 gap-md-4">
      <div class="profile-avatar">
        {{ strtoupper(substr($customer->full_name, 0, 1)) }}
      </div>
      <div class="flex-grow-1">
        <div class="profile-name">{{ $customer->full_name }}</div>
        <div class="profile-email"><i class="bi bi-envelope me-1"></i> {{ $customer->email }}</div>
        <div class="profile-badges">
          <span class="profile-badge">
            <i class="bi bi-person-badge me-1"></i> {{ ucfirst($customer->utm_role ?? 'Customer') }}
          </span>
          <span class="profile-badge {{ $customer->verification_status === 'approved' ? 'verified' : ($customer->verification_status === 'rejected' ? 'rejected' : 'pending') }}">
            <i class="bi bi-patch-check me-1"></i> {{ ucfirst($customer->verification_status ?? 'Pending') }}
          </span>
          @if($customer->is_blacklisted)
            <span class="profile-badge" style="background: #333;">Blacklisted</span>
          @endif
        </div>
      </div>
    </div>
  </div>

  {{-- STATS ROW --}}
  <div class="stats-row">
    <div class="stat-box">
      <div class="stat-label">Deposit Balance</div>
      <div class="stat-value">RM {{ number_format($customer->deposit_balance ?? 0, 2) }}</div>
    </div>
    <div class="stat-box">
      <div class="stat-label">Total Rentals</div>
      <div class="stat-value">{{ $customer->bookings->count() }}</div>
    </div>
    <div class="stat-box">
      <div class="stat-label">Loyalty Stamps</div>
      <div class="stat-value">{{ $customer->total_stamps ?? 0 }} <i class="bi bi-star-fill" style="font-size: 16px;"></i></div>
      @php
        $currentHours = $customer->total_rental_hours ?? 0;
        $hoursTowardsNextStamp = $currentHours % 9;
        $percentComplete = ($hoursTowardsNextStamp / 9) * 100;
      @endphp
      <div class="progress mt-2" style="height: 4px; border-radius: 4px;">
        <div class="progress-bar bg-warning" role="progressbar" style="width: {{ $percentComplete }}%;" aria-valuenow="{{ $percentComplete }}" aria-valuemin="0" aria-valuemax="100"></div>
      </div>
      <div class="text-center mt-1" style="font-size: 10px; color: #666;">
        {{ $hoursTowardsNextStamp }}/9h to next
      </div>
    </div>
  </div>

  {{-- PERSONAL INFORMATION --}}
  <div class="section-card">
    <div class="section-header">
      <h2 class="section-title"><i class="bi bi-person"></i> Personal Information</h2>
      <button type="button" class="btn-secondary-custom btn-sm-custom" onclick="toggleEditProfile()" id="editProfileBtn">
        <i class="bi bi-pencil"></i> Edit
      </button>
    </div>
    <div class="section-body">
      {{-- Display Mode --}}
      <div id="profileDisplay">
        <div class="info-grid">
          <div class="info-item">
            <span class="info-label">Full Name</span>
            <span class="info-value">{{ $customer->full_name }}</span>
          </div>
          <div class="info-item">
            <span class="info-label">Email Address</span>
            <span class="info-value">{{ $customer->email }}</span>
          </div>
          <div class="info-item">
            <span class="info-label">Phone Number</span>
            <span class="info-value {{ !$customer->phone ? 'empty' : '' }}">{{ $customer->phone ?? 'Not provided' }}</span>
          </div>
          <div class="info-item">
            <span class="info-label">UTM Role</span>
            <span class="info-value">{{ ucfirst($customer->utm_role ?? 'Not specified') }}</span>
          </div>
          <div class="info-item">
            <span class="info-label">Residential College (Kolej)</span>
            <span class="info-value {{ !$customer->college_name ? 'empty' : '' }}">{{ $customer->college_name ?? 'Not provided' }}</span>
          </div>
        </div>
      </div>
      
      {{-- Edit Mode --}}
      <form method="POST" action="{{ route('customer.profile.update') }}" id="profileForm" style="display: none;">
        @csrf
        <div class="info-grid">
          <div class="form-group" style="margin-bottom: 0;">
            <label class="form-label-custom">Full Name</label>
            <input type="text" name="full_name" class="form-input" value="{{ old('full_name', $customer->full_name) }}" required>
          </div>
          <div class="form-group" style="margin-bottom: 0;">
            <label class="form-label-custom">Email Address</label>
            <input type="email" class="form-input" value="{{ $customer->email }}" disabled>
            <small class="text-muted" style="font-size: 11px;">Email cannot be changed</small>
          </div>
          <div class="form-group" style="margin-bottom: 0;">
            <label class="form-label-custom">Phone Number</label>
            <input type="tel" name="phone" class="form-input" value="{{ old('phone', $customer->phone ?? '') }}" placeholder="e.g., 0123456789">
          </div>
          <div class="form-group" style="margin-bottom: 0;">
            <label class="form-label-custom">UTM Role</label>
            <select name="utm_role" class="form-input">
              <option value="">Select role</option>
              <option value="student" {{ old('utm_role', $customer->utm_role) === 'student' ? 'selected' : '' }}>Student</option>
              <option value="staff" {{ old('utm_role', $customer->utm_role) === 'staff' ? 'selected' : '' }}>Staff</option>
            </select>
          </div>
          <div class="form-group" style="margin-bottom: 0;">
            <label class="form-label-custom">Residential College (Kolej)</label>
            <input type="text" name="college_name" class="form-input" value="{{ old('college_name', $customer->college_name ?? '') }}" placeholder="e.g., Kolej Tun Razak">
          </div>
        </div>
        <div class="d-flex gap-3 mt-4 button-row">
          <button type="submit" class="btn-primary-custom">Save Changes</button>
          <button type="button" class="btn-secondary-custom" onclick="toggleEditProfile()">Cancel</button>
        </div>
      </form>
    </div>
  </div>

  {{-- IDENTITY DOCUMENTS --}}
  <div class="section-card">
    <div class="section-header">
      <h2 class="section-title"><i class="bi bi-file-earmark-person"></i> Identity Documents</h2>
    </div>
    <div class="section-body">
      <p class="text-muted small mb-4">Upload clear copies of your documents for verification. All three documents are required.</p>
      
      <form method="POST" action="{{ route('customer.profile.documents.update') }}" enctype="multipart/form-data">
        @csrf
        
        {{-- Desktop Grid --}}
        <div class="doc-grid d-none d-md-grid">
          {{-- IC / Passport --}}
          <div class="doc-item {{ $customer->ic_url ? 'uploaded' : '' }}">
            <i class="bi bi-person-vcard doc-icon"></i>
            <div class="doc-name">IC / Passport</div>
            <div class="doc-status">{{ $customer->ic_url ? 'Uploaded' : 'Not uploaded' }}</div>
            @if($customer->ic_url)
              <a href="{{ $customer->ic_url }}" target="_blank" class="btn-secondary-custom btn-sm-custom d-inline-block mb-2">
                <i class="bi bi-eye"></i> View
              </a>
            @endif
            <input type="file" name="ic_document" class="form-control form-control-sm" accept="image/*,.pdf" style="font-size: 12px;">
          </div>
          
          {{-- UTM ID --}}
          <div class="doc-item {{ $customer->utmid_url ? 'uploaded' : '' }}">
            <i class="bi bi-credit-card doc-icon"></i>
            <div class="doc-name">UTM Student/Staff ID</div>
            <div class="doc-status">{{ $customer->utmid_url ? 'Uploaded' : 'Not uploaded' }}</div>
            @if($customer->utmid_url)
              <a href="{{ $customer->utmid_url }}" target="_blank" class="btn-secondary-custom btn-sm-custom d-inline-block mb-2">
                <i class="bi bi-eye"></i> View
              </a>
            @endif
            <input type="file" name="utmid_document" class="form-control form-control-sm" accept="image/*,.pdf" style="font-size: 12px;">
          </div>
          
          {{-- Driving License --}}
          <div class="doc-item {{ $customer->license_url ? 'uploaded' : '' }}">
            <i class="bi bi-car-front doc-icon"></i>
            <div class="doc-name">Driving License</div>
            <div class="doc-status">{{ $customer->license_url ? 'Uploaded' : 'Not uploaded' }}</div>
            @if($customer->license_url)
              <a href="{{ $customer->license_url }}" target="_blank" class="btn-secondary-custom btn-sm-custom d-inline-block mb-2">
                <i class="bi bi-eye"></i> View
              </a>
            @endif
            <input type="file" name="license_document" class="form-control form-control-sm" accept="image/*,.pdf" style="font-size: 12px;">
          </div>
        </div>
        
        {{-- Mobile List --}}
        <div class="doc-grid d-md-none">
          {{-- IC / Passport --}}
          <div class="doc-item {{ $customer->ic_url ? 'uploaded' : '' }}">
            <i class="bi bi-person-vcard doc-icon"></i>
            <div class="doc-info">
              <div class="doc-name">IC / Passport</div>
              <div class="doc-status">{{ $customer->ic_url ? 'Uploaded' : 'Not uploaded' }}</div>
            </div>
            <div class="doc-actions">
              @if($customer->ic_url)
                <a href="{{ $customer->ic_url }}" target="_blank" class="btn-secondary-custom btn-sm-custom">View</a>
              @endif
              <label class="btn-secondary-custom btn-sm-custom" style="cursor: pointer; margin: 0;">
                <i class="bi bi-upload"></i>
                <input type="file" name="ic_document" accept="image/*,.pdf" style="display: none;">
              </label>
            </div>
          </div>
          
          {{-- UTM ID --}}
          <div class="doc-item {{ $customer->utmid_url ? 'uploaded' : '' }}">
            <i class="bi bi-credit-card doc-icon"></i>
            <div class="doc-info">
              <div class="doc-name">UTM ID</div>
              <div class="doc-status">{{ $customer->utmid_url ? 'Uploaded' : 'Not uploaded' }}</div>
            </div>
            <div class="doc-actions">
              @if($customer->utmid_url)
                <a href="{{ $customer->utmid_url }}" target="_blank" class="btn-secondary-custom btn-sm-custom">View</a>
              @endif
              <label class="btn-secondary-custom btn-sm-custom" style="cursor: pointer; margin: 0;">
                <i class="bi bi-upload"></i>
                <input type="file" name="utmid_document" accept="image/*,.pdf" style="display: none;">
              </label>
            </div>
          </div>
          
          {{-- Driving License --}}
          <div class="doc-item {{ $customer->license_url ? 'uploaded' : '' }}">
            <i class="bi bi-car-front doc-icon"></i>
            <div class="doc-info">
              <div class="doc-name">Driving License</div>
              <div class="doc-status">{{ $customer->license_url ? 'Uploaded' : 'Not uploaded' }}</div>
            </div>
            <div class="doc-actions">
              @if($customer->license_url)
                <a href="{{ $customer->license_url }}" target="_blank" class="btn-secondary-custom btn-sm-custom">View</a>
              @endif
              <label class="btn-secondary-custom btn-sm-custom" style="cursor: pointer; margin: 0;">
                <i class="bi bi-upload"></i>
                <input type="file" name="license_document" accept="image/*,.pdf" style="display: none;">
              </label>
            </div>
          </div>
        </div>
        
        <div class="text-center mt-4">
          <button type="submit" class="btn-primary-custom">
            <i class="bi bi-cloud-upload me-2"></i> Upload Documents
          </button>
        </div>
      </form>
    </div>
  </div>

  {{-- CHANGE PASSWORD --}}
  <div class="section-card">
    <div class="section-header">
      <h2 class="section-title"><i class="bi bi-shield-lock"></i> Change Password</h2>
    </div>
    <div class="section-body">
      <form method="POST" action="{{ route('customer.profile.password.update') }}" id="passwordForm">
        @csrf
        <div class="info-grid">
          <div class="form-group" style="margin-bottom: 0;">
            <label class="form-label-custom">Current Password</label>
            <input type="password" name="current_password" class="form-input" placeholder="Enter current password" required>
          </div>
          <div class="form-group" style="margin-bottom: 0;">
            <label class="form-label-custom">New Password</label>
            <input type="password" name="new_password" class="form-input" placeholder="Enter new password" required minlength="8">
          </div>
          <div class="form-group" style="margin-bottom: 0;">
            <label class="form-label-custom">Confirm New Password</label>
            <input type="password" name="new_password_confirmation" class="form-input" placeholder="Confirm new password" required>
          </div>
        </div>
        <div class="mt-4">
          <button type="submit" class="btn-primary-custom">Update Password</button>
        </div>
      </form>
    </div>
  </div>

</div>

@push('scripts')
<script>
  function toggleEditProfile() {
    const form = document.getElementById('profileForm');
    const display = document.getElementById('profileDisplay');
    const btn = document.getElementById('editProfileBtn');
    
    if (form.style.display === 'none' || form.style.display === '') {
      form.style.display = 'block';
      display.style.display = 'none';
      btn.innerHTML = '<i class="bi bi-x"></i> Cancel';
    } else {
      form.style.display = 'none';
      display.style.display = 'block';
      btn.innerHTML = '<i class="bi bi-pencil"></i> Edit';
    }
  }
</script>
@endpush
@endsection
