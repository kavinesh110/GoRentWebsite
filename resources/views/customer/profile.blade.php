@extends('layouts.app')
@section('title', 'My Profile - Hasta GoRent')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
<style>
  :root {
    --hasta-secondary: #a02a2a;
  }
  .profile-header-gradient {
    background: linear-gradient(135deg, var(--hasta) 0%, var(--hasta-secondary) 100%);
    border-radius: 24px;
    padding: 3rem 2rem;
    color: white;
    margin-bottom: -2rem;
    position: relative;
    z-index: 1;
  }
  .profile-card {
    border-radius: 24px;
    border: none;
    box-shadow: 0 10px 30px rgba(0,0,0,0.05);
    margin-top: 1rem;
  }
  .avatar-circle {
    width: 120px;
    height: 120px;
    background: rgba(255,255,255,0.2);
    border: 4px solid rgba(255,255,255,0.3);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 3rem;
    font-weight: 700;
  }
  .stat-card {
    border-radius: 20px;
    padding: 1.5rem;
    height: 100%;
    background: #fdfdfd;
    border: 1px solid #f0f0f0;
    transition: all 0.3s ease;
  }
  .stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.05);
  }
  .document-box {
    border: 2px dashed #e9ecef;
    border-radius: 20px;
    padding: 1.5rem;
    text-align: center;
    transition: all 0.3s ease;
  }
  .document-box:hover {
    border-color: var(--hasta);
    background-color: #fdf4f4;
  }
  .btn-edit-profile {
    background: rgba(255,255,255,0.2);
    border: 1px solid rgba(255,255,255,0.4);
    color: white;
    border-radius: 12px;
    padding: 0.5rem 1.25rem;
    transition: all 0.3s ease;
  }
  .btn-edit-profile:hover {
    background: white;
    color: var(--hasta);
  }
</style>
@endpush

@section('content')
<div class="container-fluid px-4 px-md-5" style="padding-top: 32px; padding-bottom: 60px;">
  
  @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
      <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  @endif

  {{-- PROFILE HEADER SECTION --}}
  <div class="profile-header-gradient shadow-lg mb-5">
    <div class="row align-items-center">
      <div class="col-auto">
        <div class="avatar-circle">
          {{ strtoupper(substr($customer->full_name, 0, 1)) }}
        </div>
      </div>
      <div class="col mt-3 mt-md-0">
        <h1 class="fw-bold mb-1">{{ $customer->full_name }}</h1>
        <p class="mb-3 opacity-75 d-flex align-items-center gap-2">
          <i class="bi bi-envelope"></i> {{ $customer->email }}
        </p>
        <div class="d-flex flex-wrap gap-2">
          <span class="badge bg-light text-dark rounded-pill px-3 py-2">
            <i class="bi bi-person-badge me-1"></i> {{ ucfirst($customer->utm_role ?? 'Customer') }}
          </span>
          <span class="badge rounded-pill px-3 py-2 
            {{ $customer->verification_status === 'approved' ? 'bg-success' : ($customer->verification_status === 'rejected' ? 'bg-danger' : 'bg-warning text-dark') }}">
            <i class="bi bi-patch-check me-1"></i> {{ ucfirst($customer->verification_status ?? 'Pending') }} Verification
          </span>
          @if($customer->is_blacklisted)
            <span class="badge bg-dark rounded-pill px-3 py-2">Blacklisted</span>
          @endif
        </div>
      </div>
      <div class="col-auto mt-4 mt-md-0 text-md-end">
        <button type="button" class="btn btn-edit-profile" onclick="toggleEditMode()" id="editToggleBtn">
          <i class="bi bi-pencil-square"></i> Edit Profile
        </button>
      </div>
    </div>
  </div>

  <div class="row g-4 mt-2">
    {{-- LEFT COLUMN --}}
    <div class="col-lg-8">
      
      {{-- INFO SECTION --}}
      <div class="card profile-card mb-4">
        <div class="card-body p-4 p-md-5">
          <div class="d-flex align-items-center justify-content-between mb-4 pb-2 border-bottom">
            <h5 class="fw-bold mb-0">Personal Information</h5>
          </div>
          
          {{-- EDIT FORM --}}
          <form method="POST" action="{{ route('customer.profile.update') }}" id="profileForm" style="display: none;">
            @csrf
            <div class="row g-4">
              <div class="col-md-6">
                <label class="form-label fw-bold small text-muted text-uppercase">Full Name</label>
                <input type="text" name="full_name" class="form-control border-0 bg-light p-3" value="{{ old('full_name', $customer->full_name) }}" required>
              </div>
              <div class="col-md-6">
                <label class="form-label fw-bold small text-muted text-uppercase">Phone Number</label>
                <input type="tel" name="phone" class="form-control border-0 bg-light p-3" value="{{ old('phone', $customer->phone ?? '') }}" placeholder="e.g., 0123456789">
              </div>
              <div class="col-md-6">
                <label class="form-label fw-bold small text-muted text-uppercase">UTM Role</label>
                <select name="utm_role" class="form-select border-0 bg-light p-3">
                  <option value="">Select role</option>
                  <option value="student" {{ old('utm_role', $customer->utm_role) === 'student' ? 'selected' : '' }}>Student</option>
                  <option value="staff" {{ old('utm_role', $customer->utm_role) === 'staff' ? 'selected' : '' }}>Staff</option>
                </select>
              </div>
              <div class="col-12 mt-4">
                <div class="d-flex gap-3">
                  <button type="submit" class="btn btn-hasta px-4 py-2">Save Changes</button>
                  <button type="button" class="btn btn-outline-secondary px-4 py-2" onclick="toggleEditMode()">Cancel</button>
                </div>
              </div>
            </div>
          </form>

          {{-- DISPLAY VIEW --}}
          <div id="profileDisplay">
            <div class="row g-4">
              <div class="col-md-6">
                <div class="small text-muted text-uppercase fw-bold mb-1" style="font-size: 11px; letter-spacing: 1px;">Full Name</div>
                <div class="h6 mb-0">{{ $customer->full_name }}</div>
              </div>
              <div class="col-md-6">
                <div class="small text-muted text-uppercase fw-bold mb-1" style="font-size: 11px; letter-spacing: 1px;">Phone Number</div>
                <div class="h6 mb-0">{{ $customer->phone ?? '--' }}</div>
              </div>
              <div class="col-md-6">
                <div class="small text-muted text-uppercase fw-bold mb-1" style="font-size: 11px; letter-spacing: 1px;">UTM Role</div>
                <div class="h6 mb-0">{{ ucfirst($customer->utm_role ?? 'Not specified') }}</div>
              </div>
              <div class="col-md-6">
                <div class="small text-muted text-uppercase fw-bold mb-1" style="font-size: 11px; letter-spacing: 1px;">Account Status</div>
                <div class="h6 mb-0 text-success">Active</div>
              </div>
            </div>
          </div>
        </div>
      </div>

      {{-- DOCUMENTS SECTION --}}
      <div class="card profile-card">
        <div class="card-body p-4 p-md-5">
          <div class="d-flex align-items-center justify-content-between mb-4 pb-2 border-bottom">
            <h5 class="fw-bold mb-0">Identity Verification</h5>
          </div>
          <p class="text-muted small mb-4">Please ensure all uploaded documents are clear and valid for verification.</p>

          <form method="POST" action="{{ route('customer.profile.documents.update') }}" enctype="multipart/form-data">
            @csrf
            <div class="row g-4">
              <div class="col-md-4">
                <div class="document-box">
                  <i class="bi bi-person-bounding-box h1 text-hasta mb-3 d-block"></i>
                  <h6 class="fw-bold small mb-3">IC / Passport</h6>
                  @if($customer->ic_url)
                    <a href="{{ $customer->ic_url }}" target="_blank" class="btn btn-sm btn-light rounded-pill px-3 mb-3">
                      <i class="bi bi-eye"></i> View File
                    </a>
                  @endif
                  <input type="file" name="ic_document" class="form-control form-control-sm" accept="image/*,.pdf">
                </div>
              </div>
              <div class="col-md-4">
                <div class="document-box">
                  <i class="bi bi-credit-card h1 text-hasta mb-3 d-block"></i>
                  <h6 class="fw-bold small mb-3">UTM Student/Staff ID</h6>
                  @if($customer->utmid_url)
                    <a href="{{ $customer->utmid_url }}" target="_blank" class="btn btn-sm btn-light rounded-pill px-3 mb-3">
                      <i class="bi bi-eye"></i> View File
                    </a>
                  @endif
                  <input type="file" name="utmid_document" class="form-control form-control-sm" accept="image/*,.pdf">
                </div>
              </div>
              <div class="col-md-4">
                <div class="document-box">
                  <i class="bi bi-car-front h1 text-hasta mb-3 d-block"></i>
                  <h6 class="fw-bold small mb-3">Driving License</h6>
                  @if($customer->license_url)
                    <a href="{{ $customer->license_url }}" target="_blank" class="btn btn-sm btn-light rounded-pill px-3 mb-3">
                      <i class="bi bi-eye"></i> View File
                    </a>
                  @endif
                  <input type="file" name="license_document" class="form-control form-control-sm" accept="image/*,.pdf">
                </div>
              </div>
              <div class="col-12 mt-4 text-center">
                <button type="submit" class="btn btn-hasta px-5">Upload All Documents</button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>

    {{-- RIGHT COLUMN --}}
    <div class="col-lg-4">
      {{-- STATS SECTION --}}
      <div class="row g-3 mb-4">
        <div class="col-12">
          <div class="stat-card">
            <div class="d-flex align-items-center justify-content-between mb-2">
              <div class="text-muted small fw-bold text-uppercase" style="letter-spacing: 1px;">Deposit Balance</div>
              <div class="bg-light rounded p-2 text-hasta"><i class="bi bi-wallet2 h5 mb-0"></i></div>
            </div>
            <div class="h3 fw-bold mb-0">RM {{ number_format($customer->deposit_balance ?? 0, 2) }}</div>
            <div class="small text-muted mt-2">Refundable upon completion</div>
          </div>
        </div>
        <div class="col-6">
          <div class="stat-card">
            <div class="text-muted small fw-bold text-uppercase mb-2" style="letter-spacing: 1px;">Rentals</div>
            <div class="h4 fw-bold mb-0">{{ $customer->bookings->count() }}</div>
          </div>
        </div>
        <div class="col-6">
          <div class="stat-card">
            <div class="text-muted small fw-bold text-uppercase mb-2" style="letter-spacing: 1px;">Stamps</div>
            <div class="h4 fw-bold mb-0 text-warning">{{ $customer->total_stamps ?? 0 }} <i class="bi bi-star-fill small"></i></div>
          </div>
        </div>
      </div>

      {{-- QUICK LINKS --}}
      <div class="card profile-card">
        <div class="card-body p-4">
          <h6 class="fw-bold mb-4">Account Dashboard</h6>
          <div class="list-group list-group-flush">
            <a href="{{ route('customer.bookings') }}" class="list-group-item list-group-item-action border-0 px-0 d-flex justify-content-between align-items-center py-3">
              <span><i class="bi bi-calendar3 me-3 text-hasta"></i> My Bookings</span>
              <i class="bi bi-chevron-right small text-muted"></i>
            </a>
            <a href="{{ route('customer.loyalty-rewards') }}" class="list-group-item list-group-item-action border-0 px-0 d-flex justify-content-between align-items-center py-3">
              <span><i class="bi bi-gift me-3 text-hasta"></i> Loyalty Rewards</span>
              <i class="bi bi-chevron-right small text-muted"></i>
            </a>
            <a href="{{ route('home') }}" class="list-group-item list-group-item-action border-0 px-0 d-flex justify-content-between align-items-center py-3">
              <span><i class="bi bi-plus-circle me-3 text-hasta"></i> Rent a Car</span>
              <i class="bi bi-chevron-right small text-muted"></i>
            </a>
          </div>
        </div>
      </div>

      {{-- HELP & SUPPORT --}}
      <div class="card profile-card mt-4 bg-light">
        <div class="card-body p-4 text-center">
          <i class="bi bi-headset h1 text-hasta mb-3 d-block"></i>
          <h6 class="fw-bold mb-2">Need Help?</h6>
          <p class="text-muted small mb-0">Contact our support team for any assistance regarding your account or rentals.</p>
        </div>
      </div>
    </div>
  </div>
</div>

@push('scripts')
<script>
  function toggleEditMode() {
    const form = document.getElementById('profileForm');
    const display = document.getElementById('profileDisplay');
    const btn = document.getElementById('editToggleBtn');
    
    if (form.style.display === 'none' || form.style.display === '') {
      form.style.display = 'block';
      display.style.display = 'none';
      btn.innerHTML = '<i class="bi bi-x-circle me-2"></i> Cancel Edit';
      btn.classList.add('bg-white', 'text-hasta');
    } else {
      form.style.display = 'none';
      display.style.display = 'block';
      btn.innerHTML = '<i class="bi bi-pencil-square me-2"></i> Edit Profile';
      btn.classList.remove('bg-white', 'text-hasta');
    }
  }
</script>
@endpush
@endsection
