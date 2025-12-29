@extends('layouts.app')
@section('title', 'Car Availability Calendar - Staff Dashboard')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
@endpush

@section('content')
<div class="d-flex" style="min-height: calc(100vh - 60px);">
  @include('staff._nav')
  <div class="flex-fill" style="background: var(--bg);">
    <div class="container-fluid px-4 px-md-5" style="padding-top: 32px; padding-bottom: 48px;">
      <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
        <div>
          <h1 class="h3 fw-bold mb-1" style="color:#333;">Car Availability Calendar</h1>
          <p class="text-muted mb-0" style="font-size: 14px;">View car bookings and availability by date.</p>
        </div>
      </div>

      {{-- FILTERS --}}
      <div class="card border-0 shadow-soft mb-4">
        <div class="card-body p-3">
          <form method="GET" action="{{ route('staff.calendar') }}" class="row g-2 align-items-end">
            <div class="col-md-4">
              <label class="form-label small">Car</label>
              <select name="car_id" class="form-select form-select-sm">
                <option value="">All Cars</option>
                @foreach($cars as $car)
                  <option value="{{ $car->id }}" {{ $selectedCarId == $car->id ? 'selected' : '' }}>
                    {{ $car->plate_number }} - {{ $car->brand }} {{ $car->model }}
                  </option>
                @endforeach
              </select>
            </div>
            <div class="col-md-3">
              <label class="form-label small">Month</label>
              <select name="month" class="form-select form-select-sm">
                @for($m = 1; $m <= 12; $m++)
                  <option value="{{ $m }}" {{ $selectedMonth == $m ? 'selected' : '' }}>
                    {{ \Carbon\Carbon::create(null, $m)->format('F') }}
                  </option>
                @endfor
              </select>
            </div>
            <div class="col-md-3">
              <label class="form-label small">Year</label>
              <select name="year" class="form-select form-select-sm">
                @for($y = now()->year - 1; $y <= now()->year + 1; $y++)
                  <option value="{{ $y }}" {{ $selectedYear == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
              </select>
            </div>
            <div class="col-md-2">
              <button type="submit" class="btn btn-sm btn-hasta w-100">View</button>
            </div>
          </form>
        </div>
      </div>

      {{-- CALENDAR --}}
      <div class="card border-0 shadow-soft">
        <div class="card-body p-4">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-bold mb-0">{{ $startDate->format('F Y') }}</h5>
            <div>
              <a href="{{ route('staff.calendar', array_merge(request()->query(), ['month' => $startDate->copy()->subMonth()->month, 'year' => $startDate->copy()->subMonth()->year])) }}" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-chevron-left"></i> Previous
              </a>
              <a href="{{ route('staff.calendar', array_merge(request()->query(), ['month' => $startDate->copy()->addMonth()->month, 'year' => $startDate->copy()->addMonth()->year])) }}" class="btn btn-sm btn-outline-secondary">
                Next <i class="bi bi-chevron-right"></i>
              </a>
            </div>
          </div>

          <div class="table-responsive">
            <table class="table table-bordered mb-0" style="font-size: 13px;">
              <thead>
                <tr>
                  <th style="width: 100px;">Date</th>
                  <th>Bookings</th>
                </tr>
              </thead>
              <tbody>
                @foreach($calendarData as $day => $data)
                  <tr>
                    <td class="text-center fw-semibold">
                      {{ $data['date']->format('d M') }}<br>
                      <small class="text-muted">{{ $data['date']->format('D') }}</small>
                    </td>
                    <td>
                      @if(count($data['bookings']) > 0)
                        <div class="d-flex flex-wrap gap-2">
                          @foreach($data['bookings'] as $booking)
                            <div class="badge bg-warning text-dark" style="font-size: 11px;" title="Booking #{{ $booking->booking_id }} - {{ $booking->customer->full_name ?? 'N/A' }}">
                              {{ $booking->car->plate_number ?? 'N/A' }} ({{ $booking->status }})
                            </div>
                          @endforeach
                        </div>
                      @else
                        <span class="text-muted small">Available</span>
                      @endif
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
