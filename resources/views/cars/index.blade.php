@extends('layouts.app')
@section('title', 'All Cars - Hasta GoRent')

@section('content')

<style>
  :root {
    --hasta-red: #cb3737;
    --hasta-dark: #a92c2c;
    --bg-light: #f8f9fa;
    --text-main: #2d3436;
    --text-muted: #636e72;
  }

  /* Floating Search Card */
  .search-card-container {
    max-width: 1200px;
    margin: 30px auto 0 auto;
    padding: 0 24px;
    position: relative;
    z-index: 100;
  }
  .search-card {
    background: #fff;
    border-radius: 24px;
    padding: 30px;
    box-shadow: 0 30px 60px rgba(0,0,0,0.15);
    border: 1px solid #f0f0f0;
  }
  .search-card .form-label {
    font-size: 11px;
    font-weight: 700;
    color: #95a5a6;
    text-transform: uppercase;
    margin-bottom: 10px;
    letter-spacing: 0.5px;
  }
  .search-card .form-control, 
  .search-card .form-select {
    border: 1px solid #eee;
    padding: 14px 16px;
    font-weight: 600;
    font-size: 15px;
    border-radius: 12px;
    transition: 0.2s;
  }
  .search-card .form-control:focus, 
  .search-card .form-select:focus {
    border-color: var(--hasta);
    box-shadow: 0 0 0 4px rgba(203,55,55,0.1);
  }

  /* Car Cards */
  .car-card {
    border: none;
    border-radius: 24px;
    overflow: hidden;
    transition: 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
    background: #FFFFFF !important;
    box-shadow: 0 10px 30px rgba(0,0,0,0.05);
    height: 100%;
    display: flex;
    flex-direction: column;
  }
  .car-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 20px 40px rgba(0,0,0,0.1);
  }
  .car-img-container {
    height: 220px;
    background: #FFFFFF !important;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 30px;
    position: relative;
  }
  .car-img-container img {
    max-width: 100%;
    max-height: 100%;
    object-fit: contain;
    filter: drop-shadow(0 10px 15px rgba(0,0,0,0.08));
  }

  .btn-hasta-primary {
    background: var(--hasta);
    color: #fff;
    border: none;
    padding: 14px 28px;
    border-radius: 14px;
    font-weight: 700;
    transition: 0.3s;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
  }
  .btn-hasta-primary:hover {
    background: var(--hasta-dark);
    color: #fff;
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(203,55,55,0.3);
  }

  @media (max-width: 991px) {
    .search-card { padding: 20px; }
    .search-card-container { margin-top: 20px; }
  }

  @media (max-width: 767px) {
    .search-card { padding: 20px; }
    .search-card-container { margin-top: 15px; }
  }
</style>

{{-- MOBILE BACK BUTTON --}}
<div class="d-md-none" style="background: var(--hasta-darker); padding: 12px 16px;">
  <a href="{{ route('home') }}" class="text-white text-decoration-none d-inline-flex align-items-center gap-2" style="font-size: 14px; font-weight: 500;">
    <i class="bi bi-arrow-left"></i> Back to Home
  </a>
</div>

{{-- FLOATING SEARCH CARD --}}
<div class="search-card-container">
  <div class="search-card">
    <form action="{{ route('cars.index') }}" method="GET" class="row g-3">
      <div class="col-lg-3 col-md-6">
        <label class="form-label">Pickup Location</label>
        <div class="input-group">
          <span class="input-group-text bg-white border-end-0"><i class="bi bi-geo-alt text-muted"></i></span>
          <select name="location" class="form-select border-start-0">
            @if(isset($locations) && $locations->count() > 0)
              @foreach($locations as $loc)
                <option value="{{ $loc->name }}" {{ ($filters['location'] ?? '') === $loc->name || ($filters['location'] === '' && $loc->name === 'Student Mall') ? 'selected' : '' }}>{{ $loc->name }}</option>
              @endforeach
            @else
              <option value="Student Mall" selected>Student Mall</option>
            @endif
          </select>
        </div>
      </div>
      <div class="col-lg-2 col-md-3 col-6">
        <label class="form-label">Pickup Date</label>
        <input type="date" name="start_date" class="form-control" value="{{ $filters['start_date'] ?? date('Y-m-d') }}">
      </div>
      <div class="col-lg-2 col-md-3 col-6">
        <label class="form-label">Return Date</label>
        <input type="date" name="end_date" class="form-control" value="{{ $filters['end_date'] ?? date('Y-m-d', strtotime('+1 day')) }}">
      </div>
      <div class="col-lg-3 col-md-6">
        <label class="form-label">Car Type</label>
        <div class="input-group">
          <span class="input-group-text bg-white border-end-0"><i class="bi bi-funnel text-muted"></i></span>
          <select name="type" class="form-select border-start-0">
            <option value="">All Categories</option>
            <option value="hatchback" {{ ($filters['type'] ?? '') === 'hatchback' ? 'selected' : '' }}>Hatchback (Budget)</option>
            <option value="sedan" {{ ($filters['type'] ?? '') === 'sedan' ? 'selected' : '' }}>Sedan (Comfort)</option>
            <option value="suv" {{ ($filters['type'] ?? '') === 'suv' ? 'selected' : '' }}>SUV (Family)</option>
            <option value="mpv" {{ ($filters['type'] ?? '') === 'mpv' ? 'selected' : '' }}>MPV (Group)</option>
          </select>
        </div>
      </div>
      <div class="col-lg-2 col-md-6 d-flex align-items-end">
        <button type="submit" class="btn-hasta-primary w-100"><i class="bi bi-search me-2"></i> Find Cars</button>
      </div>
    </form>
  </div>
</div>

<div class="container mt-5 pt-5">
  {{-- CAR GRID HEADER --}}
  <div class="d-flex justify-content-between align-items-end mb-4">
    <div>
      <h2 class="fw-bold mb-1">All Available Vehicles</h2>
      <p class="text-muted small">
        @if(request()->has('type') && request()->type !== '')
          Showing {{ $cars->total() }} {{ ucfirst(request()->type) }} vehicles
        @else
          Showing {{ $cars->total() }} vehicles in our fleet
        @endif
      </p>
    </div>
    <a href="{{ route('home') }}" class="text-decoration-none fw-bold" style="color: var(--hasta);">
      <i class="bi bi-arrow-left me-1"></i> Back to Home
    </a>
  </div>

  {{-- CAR GRID --}}
  <div class="row g-4 mb-5 pb-5">
    @forelse($cars as $car)
      <div class="col-lg-3 col-md-6">
        <div class="car-card">
          <div class="car-img-container">
            @if($car->image_url)
              <img src="{{ $car->image_url }}" alt="{{ $car->brand }}">
            @else
              <i class="bi bi-image text-muted" style="font-size: 3rem;"></i>
            @endif
          </div>
          <div class="card-body p-4">
            <h6 class="fw-bold mb-1">{{ $car->brand }} {{ $car->model }}</h6>
            <div class="d-flex gap-3 mb-4 text-muted" style="font-size: 12px;">
              <span><i class="bi bi-people me-1"></i> 5 Seats</span>
              <span><i class="bi bi-gear me-1"></i> Auto</span>
            </div>
            <div class="d-flex justify-content-between align-items-center mt-auto">
              <div>
                <span class="h5 fw-bold mb-0">RM {{ number_format($car->base_rate_per_hour * 24, 0) }}</span>
                <span class="text-muted small">/day</span>
              </div>
              <a href="{{ route('cars.show', $car->id) }}" class="btn btn-outline-dark btn-sm rounded-pill px-3 fw-bold">Rent Now</a>
            </div>
          </div>
        </div>
      </div>
    @empty
      <div class="col-12 text-center py-5">
        <i class="bi bi-car-front text-muted" style="font-size: 4rem;"></i>
        <p class="text-muted mt-3">No cars found matching your filters.</p>
        <a href="{{ route('cars.index') }}" class="btn btn-outline-primary mt-2">Clear Filters</a>
      </div>
    @endforelse
  </div>

  {{-- PAGINATION --}}
  @if($cars->hasPages())
    <div class="d-flex justify-content-center mb-5">
      {{ $cars->links() }}
    </div>
  @endif
</div>

@endsection

@push('scripts')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css">
@endpush
