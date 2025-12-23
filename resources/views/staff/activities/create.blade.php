@extends('layouts.app')
@section('title', 'Create Activity - Staff Dashboard')

@section('content')
<div class="d-flex" style="min-height: calc(100vh - 60px);">
  @include('staff._nav')
  <div class="flex-fill" style="background: var(--bg);">
    <div class="container-custom" style="padding: 32px 24px 48px; max-width: 900px;">
      <div class="mb-4">
        <a href="{{ route('staff.activities') }}" class="text-decoration-none small">&larr; Back to activities</a>
        <h1 class="h3 fw-bold mt-2 mb-1" style="color:#333;">Create New Activity</h1>
        <p class="text-muted mb-0" style="font-size: 14px;">Add a new company activity or promotional campaign.</p>
      </div>

      <div class="card border-0 shadow-soft">
      <div class="card-body p-4">
        <form method="POST" action="{{ route('staff.activities.store') }}" enctype="multipart/form-data">
            @csrf

            <div class="row g-3">
              <div class="col-12">
                <label class="form-label">Title <span class="text-danger">*</span></label>
                <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title') }}" required>
                @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
              <div class="col-12">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="4" placeholder="Enter activity description...">{{ old('description') }}</textarea>
                @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
              <div class="col-md-6">
                <label class="form-label">Start Date <span class="text-danger">*</span></label>
                <input type="date" name="start_date" class="form-control @error('start_date') is-invalid @enderror" value="{{ old('start_date') }}" required>
                @error('start_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
              <div class="col-md-6">
                <label class="form-label">End Date <span class="text-danger">*</span></label>
                <input type="date" name="end_date" class="form-control @error('end_date') is-invalid @enderror" value="{{ old('end_date') }}" required>
                @error('end_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
              <div class="col-12">
                <label class="form-label">Hero Image (optional)</label>
                <input type="file" name="activity_image" class="form-control @error('activity_image') is-invalid @enderror" accept="image/*">
                <div class="form-text">This image will appear in the homepage hero slider for this activity.</div>
                @error('activity_image')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
            </div>

            <div class="mt-4 d-flex gap-2">
              <button type="submit" class="btn btn-hasta">Create Activity</button>
              <a href="{{ route('staff.activities') }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
