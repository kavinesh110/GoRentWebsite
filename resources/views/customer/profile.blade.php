@extends('layouts.app')
@section('title', 'My Profile - Hasta GoRent')

@section('content')
<div class="container-custom" style="padding: 32px 24px 48px; max-width: 900px;">
  <div class="mb-4">
    <h1 class="h3 fw-bold mb-1" style="color:#333;">My Profile</h1>
    <p class="text-muted mb-0" style="font-size: 14px;">Manage your account information and view your rental statistics.</p>
  </div>

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

  <div class="row g-3">
    {{-- PROFILE INFO --}}
    <div class="col-lg-8">
      <div class="card border-0 shadow-soft mb-3">
        <div class="card-body p-4">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-bold mb-0">Account Information</h5>
            <button type="button" class="btn btn-sm btn-outline-primary" onclick="toggleEditMode()" id="editToggleBtn">Edit Profile</button>
          </div>
          
          <form method="POST" action="{{ route('customer.profile.update') }}" id="profileForm" style="display: none;">
            @csrf
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label small text-muted">Full Name</label>
                <input type="text" name="full_name" class="form-control" value="{{ old('full_name', $customer->full_name) }}" required>
              </div>
              <div class="col-md-6">
                <label class="form-label small text-muted">Email</label>
                <input type="email" class="form-control" value="{{ $customer->email }}" disabled>
                <small class="text-muted">Email cannot be changed</small>
              </div>
              <div class="col-md-6">
                <label class="form-label small text-muted">Phone</label>
                <input type="tel" name="phone" class="form-control" value="{{ old('phone', $customer->phone ?? '') }}" placeholder="Enter phone number">
              </div>
              <div class="col-md-6">
                <label class="form-label small text-muted">UTM Role</label>
                <select name="utm_role" class="form-select">
                  <option value="">Select role</option>
                  <option value="student" {{ old('utm_role', $customer->utm_role) === 'student' ? 'selected' : '' }}>Student</option>
                  <option value="staff" {{ old('utm_role', $customer->utm_role) === 'staff' ? 'selected' : '' }}>Staff</option>
                </select>
              </div>
              <div class="col-12">
                <button type="submit" class="btn btn-hasta btn-sm">Save Changes</button>
                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="toggleEditMode()">Cancel</button>
              </div>
            </div>
          </form>

          <div id="profileDisplay">
            <div class="row g-3">
              <div class="col-md-6">
                <div class="small text-muted">Full Name</div>
                <div class="fw-semibold">{{ $customer->full_name }}</div>
              </div>
              <div class="col-md-6">
                <div class="small text-muted">Email</div>
                <div class="fw-semibold">{{ $customer->email }}</div>
              </div>
              <div class="col-md-6">
                <div class="small text-muted">Phone</div>
                <div class="fw-semibold">{{ $customer->phone ?? 'Not provided' }}</div>
              </div>
              <div class="col-md-6">
                <div class="small text-muted">UTM Role</div>
                <div class="fw-semibold">{{ ucfirst($customer->utm_role ?? 'Not specified') }}</div>
              </div>
            </div>
          </div>
        </div>
      </div>
            <div class="col-md-12">
              <div class="small text-muted">Verification Status</div>
              <div>
                <span class="badge 
                  {{ $customer->verification_status === 'approved' ? 'bg-success' : ($customer->verification_status === 'rejected' ? 'bg-danger' : 'bg-warning') }}">
                  {{ ucfirst($customer->verification_status ?? 'pending') }}
                </span>
                @if($customer->verification_status === 'pending')
                  <small class="text-muted d-block mt-1">Your account verification is pending. Upload your documents for faster approval.</small>
                @endif
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="card border-0 shadow-soft">
        <div class="card-body p-4">
          <h5 class="fw-bold mb-3">Documents</h5>
          <div class="row g-3">
            <div class="col-md-4">
              <div class="small text-muted mb-1">IC / Passport</div>
              @if($customer->ic_url)
                <div class="small"><a href="{{ $customer->ic_url }}" target="_blank" class="text-decoration-none">View Document</a></div>
              @else
                <div class="small text-muted">Not uploaded</div>
              @endif
            </div>
            <div class="col-md-4">
              <div class="small text-muted mb-1">UTM ID</div>
              @if($customer->utmid_url)
                <div class="small"><a href="{{ $customer->utmid_url }}" target="_blank" class="text-decoration-none">View Document</a></div>
              @else
                <div class="small text-muted">Not uploaded</div>
              @endif
            </div>
            <div class="col-md-4">
              <div class="small text-muted mb-1">Driving Licence</div>
              @if($customer->license_url)
                <div class="small"><a href="{{ $customer->license_url }}" target="_blank" class="text-decoration-none">View Document</a></div>
              @else
                <div class="small text-muted">Not uploaded</div>
              @endif
            </div>
          </div>
          <p class="small text-muted mt-3 mb-0">Document upload functionality will be available soon.</p>
        </div>
      </div>
    </div>

    {{-- STATISTICS --}}
    <div class="col-lg-4">
      <div class="card border-0 shadow-soft mb-3">
        <div class="card-body p-4">
          <h6 class="fw-bold mb-3">Rental Statistics</h6>
          <div class="small mb-2">
            <div class="d-flex justify-content-between mb-2">
              <span class="text-muted">Deposit Balance:</span>
              <strong>RM {{ number_format($customer->deposit_balance ?? 0, 2) }}</strong>
            </div>
            <div class="d-flex justify-content-between mb-2">
              <span class="text-muted">Total Hours:</span>
              <strong>{{ $customer->total_rental_hours ?? 0 }}h</strong>
            </div>
            <div class="d-flex justify-content-between mb-2">
              <span class="text-muted">Loyalty Stamps:</span>
              <strong>{{ $customer->total_stamps ?? 0 }}</strong>
            </div>
          </div>
        </div>
      </div>

      <div class="card border-0 shadow-soft">
        <div class="card-body p-4">
          <h6 class="fw-bold mb-3">Quick Actions</h6>
          <div class="d-grid gap-2">
            <a href="{{ route('customer.bookings') }}" class="btn btn-outline-primary btn-sm">View My Bookings</a>
            <a href="{{ route('customer.loyalty-rewards') }}" class="btn btn-outline-secondary btn-sm">Loyalty Rewards</a>
            <a href="{{ route('home') }}" class="btn btn-outline-secondary btn-sm">Browse Cars</a>
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
    
    if (form.style.display === 'none') {
      form.style.display = 'block';
      display.style.display = 'none';
      btn.textContent = 'Cancel Edit';
    } else {
      form.style.display = 'none';
      display.style.display = 'block';
      btn.textContent = 'Edit Profile';
    }
  }
</script>
@endpush
@endsection
