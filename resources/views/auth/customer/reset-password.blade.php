@extends('layouts.app')
@section('title', 'Reset Password - Hasta GoRent')

@section('content')
<div class="d-flex justify-content-center align-items-center min-vh-100" style="padding: 40px 16px 80px;">
  <div class="card border-0 shadow-soft rounded-xxl w-100" style="max-width: 480px;">
    <div class="card-body p-4 p-md-5 text-center">
      {{-- Hasta logo on top --}}
      <img src="{{ asset('images/hastalogo.jpg') }}" alt="Hasta GoRent Logo" class="mb-3" style="max-height: 60px; width: auto;">
      
      <h2 class="mb-2 fw-bold" style="color:#333;">Reset Password</h2>
      <p class="text-muted mb-4" style="font-size:14px;">Enter your new password below.</p>

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

      <form method="POST" action="{{ route('password.update') }}">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">
        <input type="hidden" name="email" value="{{ $email }}">

        <div class="mb-3">
          <label for="email_display" class="form-label text-start w-100">Email</label>
          <input type="email" id="email_display" class="form-control" value="{{ $email }}" disabled>
        </div>

        <div class="mb-3">
          <label for="password" class="form-label text-start w-100">New Password</label>
          <input type="password" name="password" id="password" class="form-control" required autocomplete="new-password" placeholder="Enter new password" minlength="8">
          <small class="text-muted text-start d-block mt-1">Minimum 8 characters</small>
        </div>

        <div class="mb-4">
          <label for="password_confirmation" class="form-label text-start w-100">Confirm New Password</label>
          <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" required autocomplete="new-password" placeholder="Confirm new password">
        </div>

        <button type="submit" class="btn btn-hasta w-100 py-2 fw-semibold mb-3">Reset Password</button>

        <p class="small text-center text-muted mb-0">
          <a href="{{ route('login') }}" class="text-hasta">Back to login</a>
        </p>
      </form>
    </div>
  </div>
</div>
@endsection
