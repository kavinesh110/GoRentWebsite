@extends('layouts.app')
@section('title', 'Register - Hasta GoRent')

@section('content')
<div class="d-flex justify-content-center align-items-center min-vh-100" style="padding: 40px 16px 80px;">
  <div class="card border-0 shadow-soft rounded-xxl w-100" style="max-width: 1000px;">
    <div class="card-body p-4 p-md-5">
      <h2 class="mb-3 fw-bold" style="color:#333;">Create your Hasta GoRent account</h2>
          <p class="text-muted mb-4" style="font-size:14px;">Register as UTM customer or Hasta staff. Verification and blacklist rules follow the project requirements.</p>

          @if($errors->any())
            <div class="alert alert-danger small">
              <ul class="mb-0">
                @foreach($errors->all() as $error)
                  <li>{{ $error }}</li>
                @endforeach
              </ul>
            </div>
          @endif

          <ul class="nav nav-pills mb-4" id="roleTabs" role="tablist">
            <li class="nav-item" role="presentation">
              <button class="nav-link active" id="cust-tab" data-bs-toggle="pill" data-bs-target="#cust-pane" type="button" role="tab">Customer (UTM student / staff)</button>
            </li>
            <li class="nav-item" role="presentation">
              <button class="nav-link" id="staff-tab" data-bs-toggle="pill" data-bs-target="#staff-pane" type="button" role="tab">Hasta staff</button>
            </li>
          </ul>

          <div class="tab-content">
            {{-- CUSTOMER REGISTRATION --}}
            <div class="tab-pane fade show active" id="cust-pane" role="tabpanel">
              <form method="POST" action="{{ url('/register') }}" enctype="multipart/form-data" class="mt-2">
                @csrf
                <input type="hidden" name="role" value="customer">

                <div class="row g-3 mb-2">
                  <div class="col-md-6">
                    <label class="form-label">Full name</label>
                    <input type="text" name="full_name" class="form-control" value="{{ old('full_name') }}" required>
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">UTM email</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" required>
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">Confirm password</label>
                    <input type="password" name="password_confirmation" class="form-control" required>
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">Phone number</label>
                    <input type="text" name="phone" class="form-control" value="{{ old('phone') }}" placeholder="e.g., 0123456789">
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">UTM Role</label>
                    <select name="utm_role" class="form-control">
                      <option value="student" {{ old('utm_role') === 'student' ? 'selected' : '' }}>Student</option>
                      <option value="staff" {{ old('utm_role') === 'staff' ? 'selected' : '' }}>Staff</option>
                    </select>
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">Residential college (optional)</label>
                    <input type="text" name="college" class="form-control" placeholder="Kolej Tun Razak, etc." value="{{ old('college') }}">
                  </div>
                </div>

                <div class="row g-3 mt-1">
                  <div class="col-md-4">
                    <label class="form-label small">Upload IC / Passport (later)</label>
                    <input type="file" class="form-control" disabled>
                  </div>
                  <div class="col-md-4">
                    <label class="form-label small">Upload UTM ID (later)</label>
                    <input type="file" class="form-control" disabled>
                  </div>
                  <div class="col-md-4">
                    <label class="form-label small">Upload driving licence (later)</label>
                    <input type="file" class="form-control" disabled>
                  </div>
                </div>

                <p class="small text-muted mt-3 mb-3">
                  By registering, you agree that bookings, deposits, penalties and blacklist rules are managed according to Hasta Travels & Tours policies.
                </p>

                <button type="submit" class="btn btn-hasta w-100 py-2 fw-semibold">Register as customer</button>
              </form>
            </div>

            {{-- STAFF REGISTRATION --}}
            <div class="tab-pane fade" id="staff-pane" role="tabpanel">
              <form method="POST" action="{{ url('/register') }}" class="mt-2">
                @csrf
                <input type="hidden" name="role" value="staff">

                <div class="row g-3 mb-2">
                  <div class="col-md-6">
                    <label class="form-label">Staff name</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name') }}">
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">Work email</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email') }}">
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control">
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">Confirm password</label>
                    <input type="password" name="password_confirmation" class="form-control">
                  </div>
                </div>

                <p class="small text-muted mt-3 mb-3">
                  Staff accounts can manage cars, inspections, penalties, activities and reports as described in the system scope.
                </p>

                <button type="submit" class="btn btn-outline-dark w-100 py-2 fw-semibold">Register as staff</button>
              </form>
            </div>
          </div>

          <p class="small text-center text-muted mt-3 mb-0">
            Already have an account? <a href="{{ route('login') }}">Login here</a>
          </p>
    </div>
  </div>
</div>
@endsection
