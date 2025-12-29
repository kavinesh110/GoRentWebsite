@extends('layouts.app')
@section('title', 'Edit Location - Staff Dashboard')

@section('content')
<div class="d-flex" style="min-height: calc(100vh - 60px);">
  @include('staff._nav')
  <div class="flex-fill" style="background: var(--bg);">
    <div class="container-fluid px-4 px-md-5" style="padding-top: 32px; padding-bottom: 48px;">
      <div class="mb-4">
        <a href="{{ route('staff.locations') }}" class="text-decoration-none small">&larr; Back to locations</a>
        <h1 class="h3 fw-bold mt-2 mb-1" style="color:#333;">Edit Location: {{ $location->name }}</h1>
        <p class="text-muted mb-0" style="font-size: 14px;">Update location details.</p>
      </div>

      <div class="card border-0 shadow-soft">
        <div class="card-body p-4">
          <form method="POST" action="{{ route('staff.locations.update', $location->location_id) }}">
            @csrf
            @method('PUT')

            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label">Location Name <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control" value="{{ old('name', $location->name) }}" required>
                @error('name')<div class="text-danger small">{{ $message }}</div>@enderror
              </div>
              <div class="col-md-6">
                <label class="form-label">Location Type <span class="text-danger">*</span></label>
                <select name="type" class="form-select" required>
                  <option value="pickup" {{ old('type', $location->type) === 'pickup' ? 'selected' : '' }}>Pickup Only</option>
                  <option value="dropoff" {{ old('type', $location->type) === 'dropoff' ? 'selected' : '' }}>Dropoff Only</option>
                  <option value="both" {{ old('type', $location->type) === 'both' ? 'selected' : '' }}>Both Pickup & Dropoff</option>
                </select>
                @error('type')<div class="text-danger small">{{ $message }}</div>@enderror
              </div>
              <div class="col-12 mt-4">
                <button type="submit" class="btn btn-hasta">Update Location</button>
                <a href="{{ route('staff.locations') }}" class="btn btn-outline-secondary ms-2">Cancel</a>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
