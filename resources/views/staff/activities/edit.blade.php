@extends('layouts.app')
@section('title', 'Edit Activity - Staff Dashboard')

@section('content')
<div class="d-flex" style="min-height: calc(100vh - 60px);">
  @include('staff._nav')
  <div class="flex-fill" style="background: var(--bg);">
    <div class="container-custom" style="padding: 32px 24px 48px; max-width: 900px;">
      <div class="mb-4">
        <a href="{{ route('staff.activities') }}" class="text-decoration-none small">&larr; Back to activities</a>
        <h1 class="h3 fw-bold mt-2 mb-1" style="color:#333;">Edit Activity: {{ $activity->title }}</h1>
        <p class="text-muted mb-0" style="font-size: 14px;">Update activity details and dates.</p>
      </div>

      <div class="card border-0 shadow-soft">
      <div class="card-body p-4">
        <form method="POST" action="{{ route('staff.activities.update', $activity->activity_id) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="row g-3">
              <div class="col-12">
                <label class="form-label">Title <span class="text-danger">*</span></label>
                <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title', $activity->title) }}" required>
                @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
              <div class="col-12">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="4" placeholder="Enter activity description...">{{ old('description', $activity->description) }}</textarea>
                @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
              <div class="col-md-6">
                <label class="form-label">Start Date <span class="text-danger">*</span></label>
                <input type="date" name="start_date" class="form-control @error('start_date') is-invalid @enderror" value="{{ old('start_date', $activity->start_date->format('Y-m-d')) }}" required>
                @error('start_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
              <div class="col-md-6">
                <label class="form-label">End Date <span class="text-danger">*</span></label>
                <input type="date" name="end_date" class="form-control @error('end_date') is-invalid @enderror" value="{{ old('end_date', $activity->end_date->format('Y-m-d')) }}" required>
                @error('end_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
              <div class="col-12">
                <label class="form-label">Hero Image (optional)</label>
                @if($activity->image_url)
                  <div class="mb-2">
                    <img src="{{ $activity->image_url }}" alt="Current hero image" style="max-height: 140px; border-radius: 12px;">
                  </div>
                @endif
                <input type="file" name="activity_image" class="form-control @error('activity_image') is-invalid @enderror" accept="image/*">
                <div class="form-text">Upload a new image to replace the current one.</div>
                @error('activity_image')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
            </div>

            <div class="mt-4 d-flex gap-2">
              <button type="submit" class="btn btn-hasta">Update Activity</button>
              <a href="{{ route('staff.activities') }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
