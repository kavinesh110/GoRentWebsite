@extends('layouts.app')
@section('title', 'Forgot Password - Hasta GoRent')

@section('content')
<div class="d-flex justify-content-center align-items-center min-vh-100" style="padding: 40px 16px 80px;">
  <div class="card border-0 shadow-soft rounded-xxl w-100" style="max-width: 480px;">
    <div class="card-body p-4 p-md-5 text-center">
      {{-- Hasta logo on top --}}
      <img src="{{ asset('images/hastalogo.jpg') }}" alt="Hasta GoRent Logo" class="mb-3" style="max-height: 60px; width: auto;">
      
      <h2 class="mb-2 fw-bold" style="color:#333;">Forgot Password</h2>
      <p class="text-muted mb-4" style="font-size:14px;">Enter your email address and we'll help you reset your password.</p>

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

      @if(session('success'))
        <div class="alert alert-success small">
          {{ session('success') }}
        </div>
      @endif

      <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <div class="mb-4">
          <label for="email" class="form-label text-start w-100">UTM Email</label>
          <input type="email" name="email" id="email" class="form-control" value="{{ old('email') }}" required autocomplete="email" placeholder="your.email@student.utm.my">
          <small class="text-muted text-start d-block mt-2">We'll send you a password reset link if this email exists in our system.</small>
        </div>

        <button type="submit" class="btn btn-hasta w-100 py-2 fw-semibold mb-3">Send Reset Link</button>

        <p class="small text-center text-muted mb-0">
          Remember your password? <a href="{{ route('login') }}" class="text-hasta">Back to login</a>
        </p>
      </form>
    </div>
  </div>
</div>
@endsection
