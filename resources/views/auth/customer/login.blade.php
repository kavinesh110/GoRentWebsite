@extends('layouts.app')
@section('title', 'Customer Login - Hasta GoRent')

@section('content')
<div class="d-flex justify-content-center align-items-center min-vh-100" style="padding: 40px 16px 80px;">
  <div class="card border-0 shadow-soft rounded-xxl w-100" style="max-width: 480px;">
    <div class="card-body p-4 p-md-5 text-center">
      {{-- Hasta logo on top --}}
      <img src="{{ asset('images/hastalogo.jpg') }}" alt="Hasta GoRent Logo" class="mb-3" style="max-height: 60px; width: auto;">
      
      <h2 class="mb-2 fw-bold" style="color:#333;">Welcome back</h2>
      <p class="text-muted mb-4" style="font-size:14px;">Login to your Hasta GoRent account to book cars and manage your rentals.</p>

      @if($errors->any())
        <div class="alert alert-danger small">
          {{ $errors->first() }}
        </div>
      @endif

      @if(session('error'))
        <div class="alert alert-danger small">
          {{ session('error') }}
        </div>
      @endif

      <form method="POST" action="{{ route('login') }}">
        @csrf
        <input type="hidden" name="role" value="customer">

        <div class="mb-3">
          <label for="email" class="form-label">UTM Email</label>
          <input type="email" name="email" id="email" class="form-control" value="{{ old('email') }}" required autocomplete="email" placeholder="your.email@student.utm.my">
        </div>

        <div class="mb-3">
          <label for="password" class="form-label">Password</label>
          <input type="password" name="password" id="password" class="form-control" required autocomplete="current-password" placeholder="Enter your password">
        </div>

        <div class="d-flex justify-content-between align-items-center mb-3">
          <div class="form-check">
            <input class="form-check-input" type="checkbox" id="remember" disabled>
            <label class="form-check-label small" for="remember">Remember me (coming soon)</label>
          </div>
          <a href="{{ route('password.forgot') }}" class="small text-hasta text-decoration-none">Forgot password?</a>
        </div>

        <button type="submit" class="btn btn-hasta w-100 py-2 fw-semibold">Login</button>

        <p class="small text-center text-muted mt-3 mb-0">
          Don't have an account? <a href="{{ route('register') }}" class="text-hasta">Register now</a>
        </p>
      </form>
    </div>
  </div>
</div>
@endsection
