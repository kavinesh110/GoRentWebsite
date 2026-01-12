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

      <form method="POST" action="{{ route('register') }}" class="mt-2 text-start">
        @csrf
        <input type="hidden" name="role" value="customer">

        <div class="row g-3 mb-4">
          <div class="col-12">
            <label class="form-label">UTM Email</label>
            <input type="email" name="email" class="form-control" value="{{ old('email') }}" 
                   pattern="[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.utm\.my$" 
                   placeholder="yourname@graduate.utm.my" required>
            <small class="text-muted">Must be a valid UTM email address (@*.utm.my)</small>
          </div>
          <div class="col-12">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control" required>
            <small class="text-muted">Minimum 8 characters</small>
          </div>
          <div class="col-12">
            <label class="form-label">Confirm password</label>
            <input type="password" name="password_confirmation" class="form-control" required>
          </div>
        </div>

        <div class="alert alert-info small mb-3">
          <i class="bi bi-info-circle me-2"></i>
          <strong>Note:</strong> After registration, you'll need to complete your profile (personal details and documents) before you can make bookings.
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