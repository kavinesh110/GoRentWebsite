@extends('layouts.app')
@section('title', 'Customer Register - Hasta GoRent')

@section('content')
<div class="d-flex justify-content-center align-items-center min-vh-100" style="padding: 40px 16px 48px;">
  <div class="card border-0 shadow-soft rounded-xxl w-100" style="max-width: 600px;">
    <div class="card-body p-4 p-md-5 text-center">
      {{-- Hasta logo on top --}}
      <img src="{{ asset('images/hastalogo.jpg') }}" alt="Hasta GoRent Logo" class=" mb-3" style="max-height: 60px; width: auto;">

      <h2 class="mb-2 fw-bold" style="color:#333;">Create your customer account</h2>
      <p class="text-muted mb-4" style="font-size:14px;">Register as a UTM student or staff to start booking with Hasta GoRent.</p>

      @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show small" role="alert">
          <ul class="mb-0 text-start">
            @foreach($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
      @endif

      <form method="POST" action="{{ route('register') }}" enctype="multipart/form-data" class="mt-2 text-start">
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
              <option value="">Select role</option>
              <option value="student" {{ old('utm_role') === 'student' ? 'selected' : '' }}>Student</option>
              <option value="staff" {{ old('utm_role') === 'staff' ? 'selected' : '' }}>Staff</option>
            </select>
          </div>
          <div class="col-md-12">
            <label class="form-label">Residential College (Kolej) <span class="text-muted small">(optional)</span></label>
            <input type="text" name="college" class="form-control" placeholder="e.g., Kolej Tun Razak" value="{{ old('college') }}">
          </div>
        </div>

        <div class="row g-3 mt-1">
          <div class="col-md-4">
            <label class="form-label small">Upload IC / Passport (coming soon)</label>
            <input type="file" class="form-control" disabled>
          </div>
          <div class="col-md-4">
            <label class="form-label small">Upload UTM ID (coming soon)</label>
            <input type="file" class="form-control" disabled>
          </div>
          <div class="col-md-4">
            <label class="form-label small">Upload driving licence (coming soon)</label>
            <input type="file" class="form-control" disabled>
          </div>
        </div>

        <p class="small text-muted mt-3 mb-3">
          By registering, you agree that bookings, deposits, penalties and loyalty rewards will follow Hasta GoRent policies.
        </p>

        <button type="submit" class="btn btn-hasta w-100 btn-sm">Register as customer</button>
      </form>

      <p class="small text-muted mt-3 mb-0">
        Already have an account? <a href="{{ route('login') }}" class="text-hasta">Login here</a>
      </p>
    </div>
  </div>
</div>
@endsection