@extends('layouts.app')
@section('title', 'Support Ticket #' . $ticket->ticket_id)

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
<style>
  .fw-800 { font-weight: 800; }
  .fw-700 { font-weight: 700; }
  .fw-600 { font-weight: 600; }
  .fw-500 { font-weight: 500; }
  .text-slate-500 { color: #64748b; }
  .text-slate-600 { color: #475569; }
  .text-slate-700 { color: #334155; }
  .bg-slate-50 { background-color: #f8fafc; }
  .ticket-section-label { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: #94a3b8; margin-bottom: 8px; display: block; }
  .response-box { border-left: 3px solid #e2e8f0; padding-left: 16px; margin-top: 12px; }
  .form-label-sm { font-size: 12px; font-weight: 700; color: #475569; margin-bottom: 6px; text-transform: uppercase; letter-spacing: 0.3px; }
  .card-action-header { background: #1e293b; color: white; padding: 12px 20px; border-radius: 8px 8px 0 0; }
</style>
@endpush

@section('content')
<div class="d-flex" style="min-height: calc(100vh - 60px);">
  @include('staff._nav')
  <div class="flex-fill bg-slate-50">
    <div class="container-fluid px-4 px-md-5 py-4">
      {{-- Header & Breadcrumbs --}}
      <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
          <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1" style="font-size: 13px;">
              <li class="breadcrumb-item"><a href="{{ route('staff.support-tickets') }}" class="text-decoration-none text-slate-500">Support Tickets</a></li>
              <li class="breadcrumb-item active text-slate-700 fw-600">Ticket #{{ $ticket->ticket_id }}</li>
            </ol>
          </nav>
          <div class="d-flex align-items-center gap-3">
            <h1 class="h4 fw-800 mb-0 text-slate-800">Support Ticket #{{ $ticket->ticket_id }}</h1>
            @php
              $statusColors = [
                'open' => ['bg' => '#eff6ff', 'text' => '#2563eb', 'border' => '#dbeafe'],
                'in_progress' => ['bg' => '#ecfeff', 'text' => '#0891b2', 'border' => '#cffafe'],
                'resolved' => ['bg' => '#f0fdf4', 'text' => '#16a34a', 'border' => '#dcfce7'],
                'closed' => ['bg' => '#f8fafc', 'text' => '#64748b', 'border' => '#e2e8f0']
              ];
              $colors = $statusColors[$ticket->status] ?? $statusColors['closed'];
            @endphp
            <span class="badge rounded-pill px-3 py-1 fw-700" style="background: {{ $colors['bg'] }}; color: {{ $colors['text'] }}; border: 1px solid {{ $colors['border'] }}; font-size: 11px;">
              {{ strtoupper(str_replace('_', ' ', $ticket->status)) }}
            </span>
            @if($ticket->flagged_for_maintenance)
              <span class="badge rounded-pill px-3 py-1 fw-700" style="background: #fffbeb; color: #d97706; border: 1px solid #fef3c7; font-size: 11px;">
                <i class="bi bi-tools me-1"></i>MAINTENANCE
              </span>
            @endif
          </div>
        </div>
      </div>

      @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm rounded-3 py-2 px-3 small mb-4 d-flex align-items-center gap-2">
          <i class="bi bi-check-circle-fill"></i>
          <span>{{ session('success') }}</span>
          <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" style="font-size: 10px;"></button>
        </div>
      @endif

      <div class="row g-4">
        {{-- LEFT COLUMN: TICKET CONTENT --}}
        <div class="col-lg-8">
          <div class="card border-0 shadow-sm rounded-3 mb-4">
            <div class="card-body p-4">
              {{-- Subject & Metadata --}}
              <div class="mb-4">
                <span class="ticket-section-label">Subject</span>
                <h5 class="fw-800 text-slate-800 mb-3">{{ $ticket->subject }}</h5>
                
                <div class="row g-3">
                  <div class="col-md-6">
                    <span class="ticket-section-label">Customer</span>
                    <div class="d-flex align-items-center">
                      <div class="rounded-circle bg-light d-flex align-items-center justify-content-center me-2 fw-700 text-slate-600" style="width: 32px; height: 32px; font-size: 12px;">
                        {{ strtoupper(substr($ticket->name ?? 'C', 0, 1)) }}
                      </div>
                      <div>
                        <div class="fw-700 text-slate-700" style="font-size: 14px;">{{ $ticket->name }}</div>
                        <div class="text-slate-500" style="font-size: 12px;">{{ $ticket->email }} @if($ticket->phone) • {{ $ticket->phone }} @endif</div>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-3">
                    <span class="ticket-section-label">Category</span>
                    <span class="fw-600 text-slate-700" style="font-size: 13px;">{{ ucfirst(str_replace('_', ' ', $ticket->category)) }}</span>
                  </div>
                  <div class="col-md-3">
                    <span class="ticket-section-label">Submitted</span>
                    <span class="fw-600 text-slate-700" style="font-size: 13px;">{{ $ticket->created_at->format('d M Y') }}</span>
                    <div class="text-slate-400" style="font-size: 11px;">{{ $ticket->created_at->format('H:i') }}</div>
                  </div>
                </div>
              </div>

              {{-- Description --}}
              <div class="mb-4">
                <span class="ticket-section-label">Initial Request</span>
                <div class="bg-light p-3 rounded-3 border-start border-4 border-slate-300">
                  <p class="mb-0 text-slate-700 fw-500" style="white-space: pre-wrap; font-size: 14px; line-height: 1.6;">{{ $ticket->description }}</p>
                </div>
              </div>

              {{-- Staff Response --}}
              @if($ticket->staff_response)
                <div>
                  <span class="ticket-section-label">Resolution / Staff Response</span>
                  <div class="bg-white p-3 rounded-3 border border-success-subtle shadow-sm" style="border-left: 4px solid #16a34a !important;">
                    <p class="mb-2 text-slate-700 fw-600" style="white-space: pre-wrap; font-size: 14px; line-height: 1.6;">{{ $ticket->staff_response }}</p>
                    @if($ticket->assignedStaff)
                      <div class="d-flex align-items-center gap-2 pt-2 border-top border-light">
                        <i class="bi bi-person-check-fill text-success small"></i>
                        <span class="text-slate-500 fw-600" style="font-size: 12px;">Resolved by {{ $ticket->assignedStaff->name }}</span>
                      </div>
                    @endif
                  </div>
                </div>
              @endif
            </div>
          </div>

          {{-- Linked Information --}}
          @if($ticket->booking || $ticket->car || $ticket->maintenanceRecord)
            <div class="card border-0 shadow-sm rounded-3 mb-4">
              <div class="card-header bg-white py-3 px-4 border-bottom">
                <h6 class="fw-800 text-slate-800 mb-0" style="font-size: 14px;">Linked Information</h6>
              </div>
              <div class="card-body p-4">
                <div class="row g-4">
                  @php $linkedCar = $ticket->car ?? ($ticket->booking ? $ticket->booking->car : null); @endphp
                  
                  @if($linkedCar)
                    <div class="col-md-6">
                      <div class="border rounded-3 overflow-hidden bg-white h-100 shadow-sm">
                        <div class="p-3">
                          <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                              <div class="small text-slate-500 fw-700 text-uppercase" style="font-size: 10px;">Linked Vehicle</div>
                              <h6 class="fw-800 text-slate-800 mb-0">{{ $linkedCar->brand }} {{ $linkedCar->model }}</h6>
                              <div class="text-slate-500 fw-600" style="font-size: 12px;">{{ $linkedCar->plate_number }}</div>
                            </div>
                            <a href="{{ route('staff.cars.edit', $linkedCar->id) }}" class="btn btn-sm btn-light rounded-2" title="Edit Car">
                              <i class="bi bi-arrow-up-right-square"></i>
                            </a>
                          </div>
                          @if($linkedCar->image_url)
                            <img src="{{ $linkedCar->image_url }}" alt="Car" class="img-fluid rounded-2 mt-2" style="height: 120px; width: 100%; object-fit: cover;">
                          @endif
                        </div>
                      </div>
                    </div>
                  @endif

                  @if($ticket->booking)
                    <div class="col-md-6">
                      <div class="border rounded-3 bg-white h-100 shadow-sm p-3">
                        <div class="d-flex justify-content-between align-items-start">
                          <div>
                            <div class="small text-slate-500 fw-700 text-uppercase" style="font-size: 10px;">Linked Booking</div>
                            <h6 class="fw-800 text-slate-800 mb-0">Booking #{{ $ticket->booking_id }}</h6>
                            <div class="text-slate-500 fw-600 mt-1" style="font-size: 12px;">
                              {{ $ticket->booking->start_datetime->format('d M') }} — {{ $ticket->booking->end_datetime->format('d M Y') }}
                            </div>
                          </div>
                          <a href="{{ route('staff.bookings.show', $ticket->booking_id) }}" class="btn btn-sm btn-light rounded-2" title="View Booking">
                            <i class="bi bi-eye"></i>
                          </a>
                        </div>
                        <div class="mt-3 pt-3 border-top d-flex gap-2">
                          <span class="badge bg-light text-slate-600 border rounded-pill px-2 py-1" style="font-size: 10px;">
                            {{ strtoupper($ticket->booking->status) }}
                          </span>
                        </div>
                      </div>
                    </div>
                  @endif

                  @if($ticket->maintenanceRecord)
                    <div class="col-md-6">
                      <div class="border rounded-3 bg-white h-100 shadow-sm p-3">
                        <div class="d-flex justify-content-between align-items-start">
                          <div>
                            <div class="small text-slate-500 fw-700 text-uppercase" style="font-size: 10px;">Maintenance Record</div>
                            <h6 class="fw-800 text-slate-800 mb-0">Record #{{ $ticket->maintenanceRecord->maintenance_id }}</h6>
                            <div class="text-slate-500 fw-600 mt-1" style="font-size: 12px;">
                              Service Date: {{ $ticket->maintenanceRecord->service_date->format('d M Y') }}
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  @endif
                </div>
              </div>
            </div>
          @endif
        </div>

        {{-- RIGHT COLUMN: ACTIONS --}}
        <div class="col-lg-4">
          <div class="card border-0 shadow-sm rounded-3 overflow-hidden mb-4">
            <div class="card-action-header">
              <h6 class="fw-800 mb-0" style="font-size: 13px;">Update Ticket</h6>
            </div>
            <div class="card-body p-4">
              <form method="POST" action="{{ route('staff.support-tickets.update', $ticket->ticket_id) }}">
                @csrf
                @method('PATCH')

                <div class="mb-3">
                  <label class="form-label-sm">Status</label>
                  <select name="status" class="form-select form-select-sm border-0 bg-light fw-600 rounded-2 py-2">
                    <option value="open" {{ $ticket->status === 'open' ? 'selected' : '' }}>OPEN</option>
                    <option value="in_progress" {{ $ticket->status === 'in_progress' ? 'selected' : '' }}>IN PROGRESS</option>
                    <option value="resolved" {{ $ticket->status === 'resolved' ? 'selected' : '' }}>RESOLVED</option>
                    <option value="closed" {{ $ticket->status === 'closed' ? 'selected' : '' }}>CLOSED</option>
                  </select>
                </div>

                <div class="mb-3">
                  <label class="form-label-sm">Assign To</label>
                  <select name="assigned_to" class="form-select form-select-sm border-0 bg-light fw-600 rounded-2 py-2">
                    <option value="">Unassigned</option>
                    @foreach($staffMembers as $staff)
                      <option value="{{ $staff->staff_id }}" {{ $ticket->assigned_to == $staff->staff_id ? 'selected' : '' }}>
                        {{ strtoupper($staff->name) }}
                      </option>
                    @endforeach
                  </select>
                </div>

                <div class="mb-4">
                  <label class="form-label-sm">Internal Response</label>
                  <textarea name="staff_response" class="form-control form-control-sm border-0 bg-light fw-500 rounded-2 p-3" rows="5" placeholder="Resolution details...">{{ $ticket->staff_response }}</textarea>
                </div>

                <div class="mb-3">
                  <label class="form-label-sm">Link to Booking</label>
                  <select name="booking_id" class="form-select form-select-sm border-0 bg-light fw-600 rounded-2 py-2">
                    <option value="">No booking link</option>
                    @foreach($customerBookings as $booking)
                      <option value="{{ $booking->booking_id }}" {{ $ticket->booking_id == $booking->booking_id ? 'selected' : '' }}>
                        #{{ $booking->booking_id }} - {{ $booking->car->brand ?? 'N/A' }} {{ $booking->car->model ?? '' }}
                      </option>
                    @endforeach
                  </select>
                </div>

                <div class="mb-3">
                  <label class="form-label-sm">Link to Vehicle</label>
                  <select name="car_id" class="form-select form-select-sm border-0 bg-light fw-600 rounded-2 py-2">
                    <option value="">No vehicle link</option>
                    @foreach($cars as $car)
                      <option value="{{ $car->id }}" {{ $ticket->car_id == $car->id ? 'selected' : '' }}>
                        {{ $car->brand }} {{ $car->model }} ({{ $car->plate_number }})
                      </option>
                    @endforeach
                  </select>
                </div>

                <div class="bg-light p-3 rounded-3 mb-4 border">
                  <label class="form-label-sm mb-2">Maintenance Flag</label>
                  <div class="d-flex gap-4">
                    <div class="form-check">
                      <input class="form-check-input" type="radio" name="flagged_for_maintenance" id="maintYes" value="1" {{ $ticket->flagged_for_maintenance ? 'checked' : '' }}>
                      <label class="form-check-label small fw-700 text-danger" for="maintYes">YES</label>
                    </div>
                    <div class="form-check">
                      <input class="form-check-input" type="radio" name="flagged_for_maintenance" id="maintNo" value="0" {{ !$ticket->flagged_for_maintenance ? 'checked' : '' }}>
                      <label class="form-check-label small fw-700 text-slate-500" for="maintNo">NO</label>
                    </div>
                  </div>
                  <small class="text-slate-400 mt-2 d-block" style="font-size: 10px;">Marking as YES will flag this car for maintenance.</small>
                </div>

                <button type="submit" class="btn btn-hasta w-100 py-2 fw-800 rounded-2 shadow-sm">
                  SAVE CHANGES
                </button>
              </form>
            </div>
          </div>

          {{-- Quick Links --}}
          <div class="card border-0 shadow-sm rounded-3">
            <div class="card-body p-3">
              <span class="ticket-section-label ms-2">Resources</span>
              <div class="d-grid gap-2">
                <a href="{{ route('staff.maintenance-issues') }}" class="btn btn-sm btn-light text-slate-700 fw-600 text-start px-3 py-2 rounded-2">
                  <i class="bi bi-tools me-2 text-warning"></i>Maintenance Queue
                </a>
                @if($linkedCar)
                  <a href="{{ route('staff.maintenance.index', $linkedCar->id) }}" class="btn btn-sm btn-light text-slate-700 fw-600 text-start px-3 py-2 rounded-2">
                    <i class="bi bi-clock-history me-2 text-info"></i>Vehicle History
                  </a>
                @endif
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<style>
  .btn-hasta { background: var(--hasta); color: white; border: none; transition: all 0.2s; }
  .btn-hasta:hover { background: var(--hasta-dark); color: white; transform: translateY(-1px); }
  .line-clamp-2 { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
</style>
@endsection
