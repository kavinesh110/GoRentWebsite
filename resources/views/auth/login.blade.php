@extends('layouts.app')
@section('title', 'Login - Hasta GoRent')

@section('content')
<div class="d-flex justify-content-center align-items-center min-vh-100" style="padding: 40px 16px 80px;">
  <div class="card border-0 shadow-soft rounded-xxl w-100" style="max-width: 880px;">
    <div class="card-body p-4 p-md-5">
      <h2 class="mb-3 fw-bold" style="color:#333;">Welcome back</h2>
          <p class="text-muted mb-4" style="font-size:14px;">Login as UTM customer or Hasta staff to manage bookings and cars.</p>

          @if($errors->any())
            <div class="alert alert-danger small">
              {{ $errors->first() }}
            </div>
          @endif

          <form method="POST" action="{{ url('/login') }}">
            @csrf

            <div class="mb-3">
              <label class="form-label small fw-semibold text-uppercase">Login as</label>
              <div class="d-flex gap-2">
                <label class="btn btn-sm btn-outline-danger flex-fill mb-0">
                  <input type="radio" name="role" value="customer" autocomplete="off" {{ old('role','customer') === 'customer' ? 'checked' : '' }}> Customer (UTM student/staff)
                </label>
                <label class="btn btn-sm btn-outline-secondary flex-fill mb-0">
                  <input type="radio" name="role" value="staff" autocomplete="off" {{ old('role') === 'staff' ? 'checked' : '' }}> Hasta staff
                </label>
              </div>
            </div>

            <div class="mb-3">
              <label for="email" class="form-label">UTM / work email</label>
              <input type="email" name="email" id="email" class="form-control" value="{{ old('email') }}" required autocomplete="email">
            </div>

            <div class="mb-3">
              <label for="password" class="form-label">Password</label>
              <input type="password" name="password" id="password" class="form-control" required autocomplete="current-password">
            </div>

            <div class="d-flex justify-content-between align-items-center mb-3">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" id="remember" disabled>
                <label class="form-check-label small" for="remember">Remember me (coming soon)</label>
              </div>
            </div>

            <button type="submit" class="btn btn-hasta w-100 py-2 fw-semibold">Login</button>

            <p class="small text-center text-muted mt-3 mb-0">
              Don't have an account? <a href="{{ route('register') }}">Register now</a>
            </p>
          </form>
    </div>
  </div>
</div>
@endsection
