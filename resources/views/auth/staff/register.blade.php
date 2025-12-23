@extends('layouts.app')
@section('title', 'Staff Register - Hasta GoRent')

@section('content')
<div class="d-flex justify-content-center align-items-center min-vh-100" style="padding: 40px 16px 48px;">
  <div class="card border-0 shadow-soft rounded-xxl w-100" style="max-width: 600px;">
    <div class="card-body p-4 p-md-5 text-center">
      {{-- Hasta logo on top --}}
      <img src="{{ asset('images/hastalogo.jpg') }}" alt="Hasta GoRent Logo" class="mb-3" style="max-height: 60px; width: auto;">

      <h2 class="mb-2 fw-bold" style="color:#333;">Create staff account</h2>
      <p class="text-muted mb-4" style="font-size:14px;">Register a Hasta Travels & Tours staff account to manage cars, bookings and customers.</p>

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
        <input type="hidden" name="role" value="staff">

        <div class="row g-3 mb-2">
          <div class="col-md-6">
            <label class="form-label">Staff name</label>
            <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
          </div>
          <div class="col-md-6">
            <label class="form-label">Work email</label>
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
        </div>

        <p class="small text-muted mt-3 mb-3">
          Staff accounts can manage cars, inspections, penalties, activities and reports as described in the system scope.
        </p>

        <button type="submit" class="btn btn-hasta w-100 btn-sm">Register as staff</button>
      </form>

      <p class="small text-muted mt-3 mb-0">
        Already have a staff account? <a href="{{ route('staff.login') }}" class="text-hasta">Login here</a>
      </p>
    </div>
  </div>
</div>
@endsection

