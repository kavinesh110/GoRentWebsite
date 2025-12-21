@extends('layouts.app')
@section('title', 'Registration Successful - Hasta GoRent')

@section('content')
<div class="d-flex justify-content-center align-items-center min-vh-100" style="padding: 40px 16px 80px;">
  <div class="card border-0 shadow-soft rounded-xxl w-100" style="max-width: 500px;">
    <div class="card-body p-4 p-md-5 text-center">
      <div class="mb-4">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 80px; height: 80px; color: var(--hasta); margin: 0 auto;">
          <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
          <polyline points="22 4 12 14.01 9 11.01"></polyline>
        </svg>
      </div>
      
      <h2 class="mb-3 fw-bold" style="color:#333;">Registration Successful!</h2>
      <p class="text-muted mb-4" style="font-size:16px; line-height:1.6;">
        Welcome to Hasta GoRent! Your account has been created successfully. You can now start browsing and booking cars.
      </p>
      
      @if(session('auth_role') === 'customer')
        <div class="alert alert-info small mb-4" style="text-align: left;">
          <strong>Next Steps:</strong>
          <ul class="mb-0 mt-2" style="padding-left: 20px;">
            <li>Your account verification is pending</li>
            <li>You can browse and view cars</li>
            <li>Upload your documents later for verification</li>
          </ul>
        </div>
      @endif
      
      <a href="{{ route('home') }}" class="btn btn-hasta w-100 py-3 fw-semibold" style="font-size: 16px;">
        Go to Homepage
      </a>
    </div>
  </div>
</div>
@endsection
