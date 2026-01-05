@extends('layouts.app')
@section('title', 'Promotions Calendar - Staff Dashboard')

@push('styles')
<style>
  .text-slate-800 { color: #1e293b; }
  .text-slate-700 { color: #334155; }
  .x-small { font-size: 11px; }
  .calendar-day { transition: background 0.2s ease; background: #fff; }
  .calendar-day:hover { background: #fcfcfd; }
  
  .today-circle {
    width: 28px;
    height: 28px;
    background-color: var(--bs-primary);
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
  }

  .today-highlight {
    background: #fdfcfe !important;
  }

  .activity-chip {
    transition: all 0.2s;
    font-size: 10px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    border: 1px solid rgba(79, 70, 229, 0.1);
  }
  .activity-chip:hover {
    background: #e0e7ff !important;
    transform: translateY(-1px);
    z-index: 10;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1) !important;
  }

  .btn-white {
    background: #fff;
    color: #1e293b;
    border-color: #e2e8f0;
  }
  .btn-white:hover {
    background: #f8fafc;
    border-color: #cbd5e1;
  }

  /* Popover Styles */
  .popover {
    border: none;
    box-shadow: 0 10px 25px rgba(0,0,0,0.1);
    max-width: 280px;
    border-radius: 12px;
  }
  .popover-header {
    background-color: #fff;
    color: #1e293b;
    border-bottom: 1px solid #f1f5f9;
    font-weight: bold;
    font-size: 13px;
    padding: 12px 16px;
  }
  .popover-body {
    color: #64748b;
    font-size: 12px;
    padding: 12px 16px;
  }

  @media (max-width: 768px) {
    .calendar-grid { grid-template-columns: repeat(1, 1fr) !important; }
    .calendar-grid-header { display: none !important; }
    .calendar-day { min-height: auto !important; border-right: none !important; }
  }
</style>
@endpush

@section('content')
<div class="d-flex" style="min-height: calc(100vh - 70px);">
  @include('staff._nav')
  <div class="flex-fill" style="background: #f8fafc;"> {{-- Light Background --}}
    <div class="container-fluid px-4 py-4">
      
      {{-- Calendar Header --}}
      <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div class="d-flex align-items-center gap-4">
          <h1 class="h4 fw-bold mb-0 text-slate-800" style="letter-spacing: -0.5px;">{{ $startDate->format('F Y') }}</h1>
          <div class="btn-group shadow-sm bg-white rounded-3">
            <a href="{{ route('staff.activities', ['month' => $startDate->copy()->subMonth()->month, 'year' => $startDate->copy()->subMonth()->year]) }}" class="btn btn-white border-light-subtle">
              <i class="bi bi-chevron-left"></i>
            </a>
            <a href="{{ route('staff.activities', ['month' => now()->month, 'year' => now()->year]) }}" class="btn btn-white border-light-subtle px-3 small fw-bold">Today</a>
            <a href="{{ route('staff.activities', ['month' => $startDate->copy()->addMonth()->month, 'year' => $startDate->copy()->addMonth()->year]) }}" class="btn btn-white border-light-subtle">
              <i class="bi bi-chevron-right"></i>
            </a>
          </div>
        </div>
        <div class="d-flex gap-2">
          <a href="{{ route('staff.activities.create') }}" class="btn btn-primary shadow-sm px-4 rounded-3 fw-bold">
            <i class="bi bi-plus-lg me-2"></i>Schedule Activity
          </a>
        </div>
      </div>

      {{-- CALENDAR GRID --}}
      <div class="calendar-wrapper border border-light-subtle rounded-4 overflow-hidden shadow-sm bg-white">
        <div class="calendar-grid-header d-grid text-center border-bottom border-light-subtle py-3 text-muted x-small fw-bold text-uppercase" style="grid-template-columns: repeat(7, 1fr); letter-spacing: 1px; background: #fcfcfd;">
          <div>Mon</div>
          <div>Tue</div>
          <div>Wed</div>
          <div>Thu</div>
          <div>Fri</div>
          <div>Sat</div>
          <div>Sun</div>
        </div>
        
        <div class="calendar-grid d-grid" style="grid-template-columns: repeat(7, 1fr); grid-auto-rows: minmax(120px, auto);">
          @php
            $currentDay = $calendarStart->copy();
          @endphp
          @while($currentDay <= $calendarEnd)
            @php
              $isCurrentMonth = $currentDay->month == $month;
              $isToday = $currentDay->isToday();
              $dayActivities = $activities->filter(function($activity) use ($currentDay) {
                  return $currentDay->between($activity->start_date->startOfDay(), $activity->end_date->endOfDay());
              });
            @endphp
            <div class="calendar-day p-2 border-end border-bottom border-light-subtle {{ !$isCurrentMonth ? 'bg-light text-muted opacity-50' : 'text-slate-700' }} {{ $isToday ? 'today-highlight' : '' }}" style="min-height: 140px;">
              <div class="d-flex justify-content-between align-items-center mb-2 px-1">
                <span class="fw-bold {{ $isToday ? 'today-circle' : '' }}">
                  {{ $currentDay->day }}
                </span>
              </div>
              
              <div class="day-activities d-flex flex-column gap-1">
                @foreach($dayActivities as $activity)
                  @php
                    $isStart = $currentDay->isSameDay($activity->start_date);
                    $isEnd = $currentDay->isSameDay($activity->end_date);
                  @endphp
                  <div class="activity-chip px-2 py-1 rounded-1 x-small fw-bold text-truncate position-relative shadow-sm" 
                       style="background: #eef2ff; border-left: 3px solid #4f46e5; color: #4338ca; cursor: pointer; border-radius: 4px;"
                       data-bs-toggle="popover" 
                       data-bs-trigger="hover focus"
                       data-bs-html="true"
                       title="{{ $activity->title }}"
                       data-bs-content="<div class='p-1'><p class='small mb-2 text-muted'>{{ $activity->start_date->format('d M') }} - {{ $activity->end_date->format('d M') }}</p><p class='small mb-0 text-dark'>{{ $activity->description ?: 'No description provided.' }}</p><hr class='my-2 opacity-10'><div class='d-flex gap-2'><a href='{{ route('staff.activities.edit', $activity->activity_id) }}' class='btn btn-xs btn-primary py-0 px-2' style='font-size: 10px;'>Edit</a></div></div>">
                    {{ $activity->title }}
                  </div>
                @endforeach
              </div>
            </div>
            @php $currentDay->addDay(); @endphp
          @endwhile
        </div>
      </div>

      {{-- Floating Add Button --}}
      <a href="{{ route('staff.activities.create') }}" class="btn btn-primary rounded-circle shadow-lg d-flex align-items-center justify-content-center position-fixed" 
         style="width: 56px; height: 56px; bottom: 30px; right: 30px; z-index: 1050; font-size: 24px;">
        <i class="bi bi-plus-lg"></i>
      </a>

    </div>
  </div>
</div>

@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function() {
    // Initialize all popovers
    var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'))
    var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
      return new bootstrap.Popover(popoverTriggerEl)
    })
  });
</script>
@endpush
@endsection