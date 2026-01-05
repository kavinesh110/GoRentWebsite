@extends('layouts.app')
@section('title', 'Inspection #' . $inspection->inspection_id . ' - Staff Dashboard')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
<style>
  .photo-thumbnail {
    width: 100%;
    height: 150px;
    object-fit: cover;
    border-radius: 12px;
    cursor: pointer;
    transition: transform 0.2s;
  }
  .photo-thumbnail:hover {
    transform: scale(1.05);
  }
  .condition-badge {
    font-size: 12px;
    padding: 6px 12px;
    border-radius: 8px;
  }
</style>
@endpush

@section('content')
<div class="d-flex" style="min-height: calc(100vh - 60px);">
  @include('staff._nav')
  <div class="flex-fill" style="background: #f8fafc;">
    <div class="container-fluid px-4 px-md-5" style="padding-top: 32px; padding-bottom: 48px;">
      
      {{-- HEADER --}}
      <div class="mb-4">
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb mb-2">
            <li class="breadcrumb-item"><a href="{{ route('staff.inspections') }}" class="text-decoration-none">Inspections</a></li>
            <li class="breadcrumb-item active">#{{ $inspection->inspection_id }}</li>
          </ol>
        </nav>
        <div class="d-flex align-items-center gap-3 flex-wrap">
          <h1 class="h3 fw-bold mb-0" style="color:#1a202c;">
            Inspection #{{ $inspection->inspection_id }}
          </h1>
          <span class="badge rounded-pill px-3 py-2 {{ $inspection->type === 'before' ? 'bg-info text-white' : 'bg-warning text-dark' }}">
            {{ $inspection->type === 'before' ? 'Before Pickup' : 'After Return' }}
          </span>
          <span class="badge rounded-pill px-3 py-2 {{ $inspection->status_badge_class }}">
            {{ ucfirst(str_replace('_', ' ', $inspection->status)) }}
          </span>
        </div>
      </div>

      @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm d-flex align-items-center gap-2 mb-4">
          <i class="bi bi-check-circle-fill"></i>
          <div>{{ session('success') }}</div>
          <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
        </div>
      @endif

      @if($errors->any())
        <div class="alert alert-danger border-0 shadow-sm mb-4">
          <strong>Please fix the following errors:</strong>
          <ul class="mb-0 mt-2">
            @foreach($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      <div class="row g-4">
        {{-- LEFT: DETAILS --}}
        <div class="col-lg-8">
          {{-- Car & Booking Info --}}
          <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-0 pt-4 px-4">
              <h5 class="fw-bold mb-0"><i class="bi bi-car-front me-2 text-primary"></i>Vehicle & Booking</h5>
            </div>
            <div class="card-body p-4">
              <div class="row g-4">
                @if($inspection->car)
                  <div class="col-md-6">
                    <div class="border rounded-4 overflow-hidden">
                      @if($inspection->car->image_url)
                        <img src="{{ $inspection->car->image_url }}" alt="{{ $inspection->car->brand }} {{ $inspection->car->model }}" class="w-100" style="height: 180px; object-fit: cover;">
                      @else
                        <div class="bg-light d-flex align-items-center justify-content-center" style="height: 180px;">
                          <i class="bi bi-car-front display-4 text-muted"></i>
                        </div>
                      @endif
                      <div class="p-3">
                        <div class="fw-bold text-dark">{{ $inspection->car->brand }} {{ $inspection->car->model }}</div>
                        <div class="small text-muted">Plate: {{ $inspection->car->plate_number }}</div>
                        <div class="small text-muted">Current Mileage: {{ number_format($inspection->car->current_mileage) }} km</div>
                      </div>
                    </div>
                  </div>
                @endif
                @if($inspection->booking)
                  <div class="col-md-6">
                    <div class="p-3 bg-light rounded-4 h-100">
                      <div class="small text-muted fw-bold text-uppercase mb-2" style="font-size: 10px;">Booking Details</div>
                      <div class="mb-2">
                        <a href="{{ route('staff.bookings.show', $inspection->booking_id) }}" class="fw-bold text-primary text-decoration-none">
                          Booking #{{ $inspection->booking_id }}
                        </a>
                      </div>
                      @if($inspection->booking->customer)
                        <div class="mb-2">
                          <i class="bi bi-person me-1 text-muted"></i>
                          {{ $inspection->booking->customer->full_name }}
                        </div>
                        <div class="mb-2 small text-muted">
                          <i class="bi bi-envelope me-1"></i>{{ $inspection->booking->customer->email }}
                        </div>
                        <div class="small text-muted">
                          <i class="bi bi-telephone me-1"></i>{{ $inspection->booking->customer->phone }}
                        </div>
                      @endif
                      <div class="mt-3 pt-3 border-top">
                        <div class="small text-muted">
                          <i class="bi bi-calendar me-1"></i>
                          {{ $inspection->booking->start_datetime->format('d M Y, H:i') }} - 
                          {{ $inspection->booking->end_datetime->format('d M Y, H:i') }}
                        </div>
                      </div>
                    </div>
                  </div>
                @endif
              </div>
            </div>
          </div>

          {{-- Inspection Form (if pending or in progress) --}}
          @if($inspection->status !== 'completed')
            <div class="card border-0 shadow-sm mb-4 border-start border-primary border-4">
              <div class="card-header bg-white border-0 pt-4 px-4">
                <h5 class="fw-bold mb-0"><i class="bi bi-clipboard-check me-2 text-primary"></i>Complete Inspection</h5>
              </div>
              <div class="card-body p-4">
                <form method="POST" action="{{ route('staff.inspections.complete', $inspection->inspection_id) }}" enctype="multipart/form-data">
                  @csrf
                  
                  <div class="row g-3">
                    <div class="col-md-6">
                      <label class="form-label fw-bold">Exterior Condition <span class="text-danger">*</span></label>
                      <textarea name="exterior_condition" class="form-control" rows="3" required placeholder="Describe the exterior condition (body, paint, lights, etc.)">{{ old('exterior_condition', $inspection->exterior_condition) }}</textarea>
                    </div>
                    <div class="col-md-6">
                      <label class="form-label fw-bold">Interior Condition <span class="text-danger">*</span></label>
                      <textarea name="interior_condition" class="form-control" rows="3" required placeholder="Describe the interior condition (seats, dashboard, cleanliness, etc.)">{{ old('interior_condition', $inspection->interior_condition) }}</textarea>
                    </div>
                    <div class="col-md-6">
                      <label class="form-label fw-bold">Engine Condition</label>
                      <textarea name="engine_condition" class="form-control" rows="3" placeholder="Describe engine condition (optional)">{{ old('engine_condition', $inspection->engine_condition) }}</textarea>
                    </div>
                    <div class="col-md-6">
                      <label class="form-label fw-bold">Damages Found</label>
                      <textarea name="damages_found" class="form-control" rows="3" placeholder="List any damages found (scratches, dents, etc.)">{{ old('damages_found', $inspection->damages_found) }}</textarea>
                    </div>
                    <div class="col-md-4">
                      <label class="form-label fw-bold">Fuel Level <span class="text-danger">*</span></label>
                      <div class="input-group">
                        <input type="number" name="fuel_level" class="form-control" min="0" max="100" required value="{{ old('fuel_level', $inspection->fuel_level ?? 100) }}" placeholder="0-100">
                        <span class="input-group-text">%</span>
                      </div>
                    </div>
                    <div class="col-md-4">
                      <label class="form-label fw-bold">Mileage Reading <span class="text-danger">*</span></label>
                      <div class="input-group">
                        <input type="number" name="mileage_reading" class="form-control" min="0" required value="{{ old('mileage_reading', $inspection->mileage_reading ?? $inspection->car->current_mileage) }}" placeholder="Current mileage">
                        <span class="input-group-text">km</span>
                      </div>
                    </div>
                    <div class="col-md-4">
                      <label class="form-label fw-bold">Inspection Photos</label>
                      <input type="file" name="photos[]" class="form-control" accept="image/*" multiple>
                      <small class="text-muted">Max 10 photos, 5MB each</small>
                    </div>
                    <div class="col-12">
                      <label class="form-label fw-bold">Additional Notes</label>
                      <textarea name="notes" class="form-control" rows="2" placeholder="Any additional notes or observations">{{ old('notes', $inspection->notes) }}</textarea>
                    </div>
                    <div class="col-12">
                      <button type="submit" class="btn btn-hasta btn-lg px-5">
                        <i class="bi bi-check-circle me-2"></i>Complete Inspection
                      </button>
                    </div>
                  </div>
                </form>
              </div>
            </div>
          @else
            {{-- Completed Inspection Details --}}
            <div class="card border-0 shadow-sm mb-4">
              <div class="card-header bg-success-subtle border-0 pt-4 px-4">
                <h5 class="fw-bold mb-0 text-success"><i class="bi bi-check-circle me-2"></i>Inspection Details</h5>
              </div>
              <div class="card-body p-4">
                <div class="row g-4">
                  <div class="col-md-6">
                    <div class="small text-muted fw-bold text-uppercase mb-1" style="font-size: 10px;">Exterior Condition</div>
                    <div class="p-3 bg-light rounded-3">{{ $inspection->exterior_condition ?: 'N/A' }}</div>
                  </div>
                  <div class="col-md-6">
                    <div class="small text-muted fw-bold text-uppercase mb-1" style="font-size: 10px;">Interior Condition</div>
                    <div class="p-3 bg-light rounded-3">{{ $inspection->interior_condition ?: 'N/A' }}</div>
                  </div>
                  <div class="col-md-6">
                    <div class="small text-muted fw-bold text-uppercase mb-1" style="font-size: 10px;">Engine Condition</div>
                    <div class="p-3 bg-light rounded-3">{{ $inspection->engine_condition ?: 'Not inspected' }}</div>
                  </div>
                  <div class="col-md-6">
                    <div class="small text-muted fw-bold text-uppercase mb-1" style="font-size: 10px;">Damages Found</div>
                    <div class="p-3 bg-light rounded-3">{{ $inspection->damages_found ?: 'No damages found' }}</div>
                  </div>
                  <div class="col-md-3">
                    <div class="small text-muted fw-bold text-uppercase mb-1" style="font-size: 10px;">Fuel Level</div>
                    <div class="h4 fw-bold text-dark">{{ $inspection->fuel_level }}%</div>
                  </div>
                  <div class="col-md-3">
                    <div class="small text-muted fw-bold text-uppercase mb-1" style="font-size: 10px;">Mileage Reading</div>
                    <div class="h4 fw-bold text-dark">{{ number_format($inspection->mileage_reading) }} km</div>
                  </div>
                  <div class="col-md-6">
                    <div class="small text-muted fw-bold text-uppercase mb-1" style="font-size: 10px;">Notes</div>
                    <div class="p-3 bg-light rounded-3">{{ $inspection->notes ?: 'No additional notes' }}</div>
                  </div>
                </div>
              </div>
            </div>

            {{-- Inspection Photos --}}
            @if($inspection->photos && count($inspection->photos) > 0)
              <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-0 pt-4 px-4">
                  <h5 class="fw-bold mb-0"><i class="bi bi-images me-2 text-info"></i>Inspection Photos ({{ count($inspection->photos) }})</h5>
                </div>
                <div class="card-body p-4">
                  <div class="row g-3">
                    @foreach($inspection->photos as $photo)
                      <div class="col-6 col-md-3">
                        <img src="{{ asset('storage/' . $photo) }}" alt="Inspection photo" class="photo-thumbnail clickable-photo" data-url="{{ asset('storage/' . $photo) }}">
                      </div>
                    @endforeach
                  </div>
                </div>
              </div>
            @endif
          @endif
        </div>

        {{-- RIGHT: INFO PANEL --}}
        <div class="col-lg-4">
          {{-- Status Card --}}
          <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-dark text-white border-0 py-3 px-4">
              <h6 class="fw-bold mb-0">Inspection Status</h6>
            </div>
            <div class="card-body p-4">
              <div class="mb-3">
                <div class="small text-muted fw-bold text-uppercase mb-1" style="font-size: 10px;">Status</div>
                <span class="badge rounded-pill px-3 py-2 {{ $inspection->status_badge_class }}">
                  {{ ucfirst(str_replace('_', ' ', $inspection->status)) }}
                </span>
              </div>
              <div class="mb-3">
                <div class="small text-muted fw-bold text-uppercase mb-1" style="font-size: 10px;">Type</div>
                <span class="badge rounded-pill px-3 py-2 {{ $inspection->type === 'before' ? 'bg-info text-white' : 'bg-warning text-dark' }}">
                  {{ $inspection->type === 'before' ? 'Before Pickup' : 'After Return' }}
                </span>
              </div>
              <div class="mb-3">
                <div class="small text-muted fw-bold text-uppercase mb-1" style="font-size: 10px;">Created</div>
                <div>{{ $inspection->created_at->format('d M Y, H:i') }}</div>
              </div>
              @if($inspection->inspected_at)
                <div class="mb-3">
                  <div class="small text-muted fw-bold text-uppercase mb-1" style="font-size: 10px;">Completed</div>
                  <div>{{ $inspection->inspected_at->format('d M Y, H:i') }}</div>
                </div>
              @endif
              @if($inspection->inspector)
                <div class="mb-3">
                  <div class="small text-muted fw-bold text-uppercase mb-1" style="font-size: 10px;">Inspector</div>
                  <div>{{ $inspection->inspector->name }}</div>
                </div>
              @endif

              @if($inspection->status === 'pending')
                <form method="POST" action="{{ route('staff.inspections.start', $inspection->inspection_id) }}" class="mt-4">
                  @csrf
                  <button type="submit" class="btn btn-success w-100">
                    <i class="bi bi-play-fill me-2"></i>Start Inspection
                  </button>
                </form>
              @endif
            </div>
          </div>

          {{-- Upload More Photos (if in progress or completed) --}}
          @if($inspection->status !== 'pending')
            <div class="card border-0 shadow-sm">
              <div class="card-header bg-white border-0 pt-4 px-4">
                <h6 class="fw-bold mb-0"><i class="bi bi-camera me-2"></i>Upload Photos</h6>
              </div>
              <div class="card-body p-4">
                <form method="POST" action="{{ route('staff.inspections.photos', $inspection->inspection_id) }}" enctype="multipart/form-data">
                  @csrf
                  <input type="file" name="photos[]" class="form-control mb-3" accept="image/*" multiple required>
                  <button type="submit" class="btn btn-outline-primary w-100">
                    <i class="bi bi-upload me-2"></i>Upload Photos
                  </button>
                </form>
              </div>
            </div>
          @endif
        </div>
      </div>
    </div>
  </div>
</div>

<style>
  .btn-hasta { background-color: var(--hasta); color: #fff; border: none; }
  .btn-hasta:hover { background-color: var(--hasta-darker); color: #fff; }
</style>
@push('scripts')
<script>
document.querySelectorAll('.clickable-photo').forEach(photo => {
  photo.addEventListener('click', function() {
    window.open(this.dataset.url, '_blank');
  });
});
</script>
@endpush
@endsection
