@extends('layouts.app')
@section('title', 'My Profile - Hasta GoRent')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
@endpush

@section('content')
<div class="container-fluid px-4 px-md-5" style="padding-top: 32px; padding-bottom: 48px;">
  
  @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      {{ session('success') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  @endif

  @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
      <ul class="mb-0">
        @foreach($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  @endif

  {{-- PROFILE HEADER SECTION --}}
  <div class="card border-0 shadow-soft mb-4" style="background: linear-gradient(135deg, var(--hasta) 0%, #a02a2a 100%); color: white; overflow: hidden;">
    <div class="card-body p-4 p-md-5">
      <div class="row align-items-center">
        <div class="col-auto">
          <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 100px; height: 100px; background: rgba(255,255,255,0.2); font-size: 40px; font-weight: bold;">
            {{ strtoupper(substr($customer->full_name, 0, 1)) }}
          </div>
        </div>
        <div class="col">
          <h2 class="fw-bold mb-1">{{ $customer->full_name }}</h2>
          <p class="mb-2 opacity-75">{{ $customer->email }}</p>
          <div class="d-flex flex-wrap gap-2">
            <span class="badge bg-light text-dark px-3 py-2">
              {{ ucfirst($customer->utm_role ?? 'Not specified') }}
            </span>
            <span class="badge 
              {{ $customer->verification_status === 'approved' ? 'bg-success' : ($customer->verification_status === 'rejected' ? 'bg-danger' : 'bg-warning') }} px-3 py-2">
              {{ ucfirst($customer->verification_status ?? 'Pending') }} Verification
            </span>
            @if($customer->is_blacklisted)
              <span class="badge bg-danger px-3 py-2">Blacklisted</span>
            @endif
          </div>
        </div>
        <div class="col-auto">
          <button type="button" class="btn btn-light btn-sm" onclick="toggleEditMode()" id="editToggleBtn">
            <i class="bi bi-pencil"></i> Edit Profile
          </button>
        </div>
      </div>
    </div>
  </div>

  <div class="row g-4">
    {{-- LEFT COLUMN: ACCOUNT INFORMATION & DOCUMENTS --}}
    <div class="col-lg-8">
      
      {{-- ACCOUNT INFORMATION CARD --}}
      <div class="card border-0 shadow-soft mb-4">
        <div class="card-header bg-white border-0 py-3">
          <h5 class="fw-bold mb-0">Account Information</h5>
        </div>
        <div class="card-body p-4">
          
          {{-- EDIT FORM (Hidden by default) --}}
          <form method="POST" action="{{ route('customer.profile.update') }}" id="profileForm" style="display: none;">
            @csrf
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label fw-semibold">Full Name <span class="text-danger">*</span></label>
                <input type="text" name="full_name" class="form-control" value="{{ old('full_name', $customer->full_name) }}" required>
              </div>
              <div class="col-md-6">
                <label class="form-label fw-semibold">Email</label>
                <input type="email" class="form-control" value="{{ $customer->email }}" disabled>
                <small class="text-muted">Email cannot be changed</small>
              </div>
              <div class="col-md-6">
                <label class="form-label fw-semibold">Phone Number</label>
                <input type="tel" name="phone" class="form-control" value="{{ old('phone', $customer->phone ?? '') }}" placeholder="e.g., 0123456789">
              </div>
              <div class="col-md-6">
                <label class="form-label fw-semibold">UTM Role</label>
                <select name="utm_role" class="form-select">
                  <option value="">Select role</option>
                  <option value="student" {{ old('utm_role', $customer->utm_role) === 'student' ? 'selected' : '' }}>Student</option>
                  <option value="staff" {{ old('utm_role', $customer->utm_role) === 'staff' ? 'selected' : '' }}>Staff</option>
                </select>
              </div>
              <div class="col-12">
                <div class="d-flex gap-2">
                  <button type="submit" class="btn btn-hasta">Save Changes</button>
                  <button type="button" class="btn btn-outline-secondary" onclick="toggleEditMode()">Cancel</button>
                </div>
              </div>
            </div>
          </form>

          {{-- DISPLAY MODE (Default) --}}
          <div id="profileDisplay">
            <div class="row g-4">
              <div class="col-md-6">
                <div class="mb-3">
                  <div class="small text-muted mb-1">Full Name</div>
                  <div class="fw-semibold fs-6">{{ $customer->full_name }}</div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="mb-3">
                  <div class="small text-muted mb-1">Email Address</div>
                  <div class="fw-semibold fs-6">{{ $customer->email }}</div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="mb-3">
                  <div class="small text-muted mb-1">Phone Number</div>
                  <div class="fw-semibold fs-6">{{ $customer->phone ?? 'Not provided' }}</div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="mb-3">
                  <div class="small text-muted mb-1">UTM Role</div>
                  <div class="fw-semibold fs-6">{{ ucfirst($customer->utm_role ?? 'Not specified') }}</div>
                </div>
              </div>
              @if($customer->verification_status === 'pending')
                <div class="col-12">
                  <div class="alert alert-warning mb-0">
                    <small><strong>Verification Pending:</strong> Your account verification is pending. Upload your documents for faster approval.</small>
                  </div>
                </div>
              @endif
            </div>
          </div>
        </div>
      </div>

      {{-- DOCUMENTS SECTION --}}
      <div class="card border-0 shadow-soft mb-4">
        <div class="card-header bg-white border-0 py-3">
          <h5 class="fw-bold mb-0">Documents</h5>
          <small class="text-muted">Upload your identification documents for account verification</small>
        </div>
        <div class="card-body p-4">
          <form method="POST" action="{{ route('customer.profile.documents.update') }}" enctype="multipart/form-data">
            @csrf
            <div class="row g-3">
              {{-- IC / Passport --}}
              <div class="col-md-4">
                <div class="border rounded p-3 text-center h-100">
                  <div class="mb-2">
                    <i class="bi bi-file-earmark-text" style="font-size: 32px; color: var(--hasta);"></i>
                  </div>
                  <div class="small fw-semibold mb-2">IC / Passport</div>
                  @if($customer->ic_url)
                    <a href="{{ $customer->ic_url }}" target="_blank" class="btn btn-sm btn-outline-primary w-100 mb-2">
                      <i class="bi bi-eye"></i> View
                    </a>
                  @else
                    <div class="small text-muted mb-2">Not uploaded</div>
                  @endif
                  <input type="file" name="ic_document" class="form-control form-control-sm mt-2 @error('ic_document') is-invalid @enderror" accept="image/*,.pdf">
                  @error('ic_document')<div class="invalid-feedback">{{ $message }}</div>@enderror
                  <small class="text-muted d-block mt-1">JPEG, PNG, PDF. Max 5MB.</small>
                </div>
              </div>

              {{-- UTM ID --}}
              <div class="col-md-4">
                <div class="border rounded p-3 text-center h-100">
                  <div class="mb-2">
                    <i class="bi bi-card-text" style="font-size: 32px; color: var(--hasta);"></i>
                  </div>
                  <div class="small fw-semibold mb-2">UTM ID</div>
                  @if($customer->utmid_url)
                    <a href="{{ $customer->utmid_url }}" target="_blank" class="btn btn-sm btn-outline-primary w-100 mb-2">
                      <i class="bi bi-eye"></i> View
                    </a>
                  @else
                    <div class="small text-muted mb-2">Not uploaded</div>
                  @endif
                  <input type="file" name="utmid_document" class="form-control form-control-sm mt-2 @error('utmid_document') is-invalid @enderror" accept="image/*,.pdf">
                  @error('utmid_document')<div class="invalid-feedback">{{ $message }}</div>@enderror
                  <small class="text-muted d-block mt-1">JPEG, PNG, PDF. Max 5MB.</small>
                </div>
              </div>

              {{-- Driving License --}}
              <div class="col-md-4">
                <div class="border rounded p-3 text-center h-100">
                  <div class="mb-2">
                    <i class="bi bi-file-earmark-pdf" style="font-size: 32px; color: var(--hasta);"></i>
                  </div>
                  <div class="small fw-semibold mb-2">Driving License</div>
                  @if($customer->license_url)
                    <a href="{{ $customer->license_url }}" target="_blank" class="btn btn-sm btn-outline-primary w-100 mb-2">
                      <i class="bi bi-eye"></i> View
                    </a>
                  @else
                    <div class="small text-muted mb-2">Not uploaded</div>
                  @endif
                  <input type="file" name="license_document" class="form-control form-control-sm mt-2 @error('license_document') is-invalid @enderror" accept="image/*,.pdf">
                  @error('license_document')<div class="invalid-feedback">{{ $message }}</div>@enderror
                  <small class="text-muted d-block mt-1">JPEG, PNG, PDF. Max 5MB.</small>
                </div>
              </div>

              <div class="col-12 mt-2">
                <button type="submit" class="btn btn-hasta btn-sm">Save Documents</button>
              </div>
            </div>
          </form>
        </div>
      </div>

    </div>

    {{-- RIGHT COLUMN: STATISTICS & QUICK ACTIONS --}}
    <div class="col-lg-4">
      
      {{-- RENTAL STATISTICS --}}
      <div class="card border-0 shadow-soft mb-4">
        <div class="card-header bg-white border-0 py-3">
          <h5 class="fw-bold mb-0">Rental Statistics</h5>
        </div>
        <div class="card-body p-4">
          <div class="mb-4">
            <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
              <div>
                <div class="small text-muted mb-1">Deposit Balance</div>
                <div class="h4 fw-bold text-hasta mb-0">RM {{ number_format($customer->deposit_balance ?? 0, 2) }}</div>
              </div>
              <div class="text-hasta" style="font-size: 32px;">
                <i class="bi bi-wallet2"></i>
              </div>
            </div>
            <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
              <div>
                <div class="small text-muted mb-1">Total Rental Hours</div>
                <div class="h5 fw-bold mb-0">{{ $customer->total_rental_hours ?? 0 }}h</div>
              </div>
              <div class="text-muted" style="font-size: 28px;">
                <i class="bi bi-clock-history"></i>
              </div>
            </div>
            <div class="d-flex justify-content-between align-items-center">
              <div>
                <div class="small text-muted mb-1">Loyalty Stamps</div>
                <div class="h5 fw-bold mb-0">{{ $customer->total_stamps ?? 0 }}</div>
              </div>
              <div class="text-warning" style="font-size: 28px;">
                <i class="bi bi-star-fill"></i>
              </div>
            </div>
          </div>
        </div>
      </div>

      {{-- QUICK ACTIONS --}}
      <div class="card border-0 shadow-soft mb-4">
        <div class="card-header bg-white border-0 py-3">
          <h5 class="fw-bold mb-0">Quick Actions</h5>
        </div>
        <div class="card-body p-4">
          <div class="d-grid gap-2">
            <a href="{{ route('customer.bookings') }}" class="btn btn-outline-primary">
              <i class="bi bi-calendar-check"></i> My Bookings
            </a>
            <a href="{{ route('customer.loyalty-rewards') }}" class="btn btn-outline-secondary">
              <i class="bi bi-gift"></i> Loyalty Rewards
            </a>
            <a href="{{ route('home') }}" class="btn btn-outline-secondary">
              <i class="bi bi-car-front"></i> Browse Cars
            </a>
          </div>
        </div>
      </div>

      {{-- ACCOUNT STATUS --}}
      <div class="card border-0 shadow-soft">
        <div class="card-header bg-white border-0 py-3">
          <h5 class="fw-bold mb-0">Account Status</h5>
        </div>
        <div class="card-body p-4">
          <div class="mb-3">
            <div class="small text-muted mb-1">Verification</div>
            <div>
              <span class="badge 
                {{ $customer->verification_status === 'approved' ? 'bg-success' : ($customer->verification_status === 'rejected' ? 'bg-danger' : 'bg-warning') }} px-3 py-2">
                {{ ucfirst($customer->verification_status ?? 'Pending') }}
              </span>
            </div>
          </div>
          @if($customer->is_blacklisted)
            <div class="mb-3">
              <div class="small text-muted mb-1">Blacklist Status</div>
              <div>
                <span class="badge bg-danger px-3 py-2">Blacklisted</span>
                @if($customer->blacklist_reason)
                  <div class="small text-muted mt-2">{{ $customer->blacklist_reason }}</div>
                @endif
              </div>
            </div>
          @endif
          <div>
            <div class="small text-muted mb-1">Member Since</div>
            <div class="fw-semibold">{{ $customer->created_at->format('d M Y') }}</div>
          </div>
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
      btn.innerHTML = '<i class="bi bi-x-circle"></i> Cancel';
      btn.classList.remove('btn-light');
      btn.classList.add('btn-outline-light');
    } else {
      form.style.display = 'none';
      display.style.display = 'block';
      btn.innerHTML = '<i class="bi bi-pencil"></i> Edit Profile';
      btn.classList.remove('btn-outline-light');
      btn.classList.add('btn-light');
    }
  }
</script>
@endpush

@endsection
