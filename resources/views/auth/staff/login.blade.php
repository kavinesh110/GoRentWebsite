@extends('layouts.app')
@section('title', 'Staff Login - Hasta GoRent')

@section('content')
<div class="d-flex justify-content-center align-items-center min-vh-100" style="padding: 40px 16px 80px;">
  <div class="card border-0 shadow-soft rounded-xxl w-100" style="max-width: 480px;">
    <div class="card-body p-4 p-md-5 text-center">
      {{-- Hasta logo on top --}}
      <img src="{{ asset('images/hastalogo.jpg') }}" alt="Hasta GoRent Logo" class="mb-3" style="max-height: 60px; width: auto;">

      <div class="mb-3">
        <h2 class="mb-2 fw-bold" style="color:#333;">Staff Portal</h2>
        <p class="text-muted mb-0" style="font-size:14px;">For authorized Hasta Travels & Tours staff only</p>
      </div>

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

      <form method="POST" action="{{ route('staff.login') }}">
        @csrf
        <input type="hidden" name="role" value="staff">

        <div class="mb-3">
          <label for="email" class="form-label">Work Email</label>
          <input type="email" name="email" id="email" class="form-control" value="{{ old('email') }}" required autocomplete="email" placeholder="staff@hasta.com">
        </div>

        <div class="mb-3">
          <label for="password" class="form-label">Password</label>
          <input type="password" name="password" id="password" class="form-control" required autocomplete="current-password" placeholder="Enter your password">
        </div>

        <div class="alert alert-info small mb-3" style="background: #f8f9fa; border-color: #dee2e6;">
          <strong>Note:</strong> This portal is restricted to authorized Hasta staff members only.
        </div>

        <button type="submit" class="btn btn-hasta w-100 py-2 fw-semibold">Login to Staff Portal</button>
      </form>
    </div>
  </div>
</div>
@endsection
