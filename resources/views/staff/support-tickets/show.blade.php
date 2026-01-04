@extends('layouts.app')
@section('title', 'Support Ticket #' . $ticket->ticket_id . ' - Staff Dashboard')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
@endpush

@section('content')
<div class="d-flex" style="min-height: calc(100vh - 60px);">
  @include('staff._nav')
  <div class="flex-fill" style="background: #f8fafc;">
    <div class="container-fluid px-4 px-md-5" style="padding-top: 32px; padding-bottom: 48px;">
      <div class="mb-4">
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb mb-2">
            <li class="breadcrumb-item"><a href="{{ route('staff.support-tickets') }}" class="text-decoration-none">Support Tickets</a></li>
            <li class="breadcrumb-item active">#{{ $ticket->ticket_id }}</li>
          </ol>
        </nav>
        <div class="d-flex align-items-center gap-3 flex-wrap">
          <h1 class="h3 fw-bold mb-0" style="color:#1a202c;">Support Ticket #{{ $ticket->ticket_id }}</h1>
          @php
            $statusClasses = [
              'open' => 'bg-primary text-white',
              'in_progress' => 'bg-info text-white',
              'resolved' => 'bg-success text-white',
              'closed' => 'bg-secondary text-white'
            ];
            $statusClass = $statusClasses[$ticket->status] ?? 'bg-secondary text-white';
          @endphp
          <span class="badge rounded-pill px-3 py-2 {{ $statusClass }} shadow-sm">
            {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
          </span>
          @if($ticket->flagged_for_maintenance)
            <span class="badge rounded-pill px-3 py-2 bg-warning text-dark shadow-sm">
              <i class="bi bi-tools me-1"></i>In Maintenance
            </span>
          @endif
        </div>
      </div>

      @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm d-flex align-items-center gap-2 mb-4">
          <i class="bi bi-check-circle-fill"></i>
          <div>{{ session('success') }}</div>
          <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
        </div>
      @endif

      <div class="row g-4">
        {{-- LEFT: TICKET DETAILS --}}
        <div class="col-lg-8">
          {{-- Ticket Information --}}
          <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-0 pt-4 px-4">
              <h5 class="fw-bold mb-0"><i class="bi bi-ticket-perforated me-2 text-primary"></i>Ticket Details</h5>
            </div>
            <div class="card-body p-4">
              <div class="row g-4 mb-4">
                <div class="col-md-6">
                  <div class="small text-muted fw-bold text-uppercase mb-1" style="font-size: 10px;">Customer</div>
                  <div class="fw-bold text-dark">{{ $ticket->name }}</div>
                  <div class="small text-muted"><i class="bi bi-envelope me-1"></i>{{ $ticket->email }}</div>
                  @if($ticket->phone)
                    <div class="small text-muted"><i class="bi bi-telephone me-1"></i>{{ $ticket->phone }}</div>
                  @endif
                </div>
                <div class="col-md-6">
                  <div class="small text-muted fw-bold text-uppercase mb-1" style="font-size: 10px;">Category</div>
                  <span class="badge bg-light text-dark border px-3 py-2">
                    {{ ucfirst(str_replace('_', ' ', $ticket->category)) }}
                  </span>
                  <div class="small text-muted mt-2">
                    <i class="bi bi-calendar me-1"></i>Created: {{ $ticket->created_at->format('d M Y, H:i') }}
                  </div>
                </div>
              </div>

              <div class="mb-3">
                <div class="small text-muted fw-bold text-uppercase mb-2" style="font-size: 10px;">Subject</div>
                <h5 class="fw-bold text-dark">{{ $ticket->subject }}</h5>
              </div>

              <div>
                <div class="small text-muted fw-bold text-uppercase mb-2" style="font-size: 10px;">Description</div>
                <div class="p-3 bg-light rounded-4 border-start border-4 border-primary">
                  <p class="mb-0 text-dark" style="white-space: pre-wrap;">{{ $ticket->description }}</p>
                </div>
              </div>
            </div>
          </div>

          {{-- Staff Response Section --}}
          @if($ticket->staff_response)
            <div class="card border-0 shadow-sm mb-4 border-success-subtle">
              <div class="card-header bg-success-subtle border-0 pt-4 px-4">
                <h5 class="fw-bold mb-0 text-success"><i class="bi bi-check-circle me-2"></i>Staff Response</h5>
              </div>
              <div class="card-body p-4">
                <div class="p-3 bg-white rounded-4 border">
                  <p class="mb-0" style="white-space: pre-wrap;">{{ $ticket->staff_response }}</p>
                </div>
                @if($ticket->assignedStaff)
                  <div class="small text-muted mt-2">
                    <i class="bi bi-person me-1"></i>Responded by: {{ $ticket->assignedStaff->name }}
                  </div>
                @endif
              </div>
            </div>
          @endif

          {{-- Linked Information --}}
          @if($ticket->booking || $ticket->car || $ticket->maintenanceRecord)
            <div class="card border-0 shadow-sm mb-4">
              <div class="card-header bg-white border-0 pt-4 px-4">
                <h5 class="fw-bold mb-0"><i class="bi bi-link-45deg me-2 text-info"></i>Linked Information</h5>
              </div>
              <div class="card-body p-4">
                <div class="row g-3">
                  @php
                    // Determine which car to show (from booking or direct link)
                    $linkedCar = $ticket->car ?? ($ticket->booking ? $ticket->booking->car : null);
                  @endphp
                  
                  @if($linkedCar)
                    <div class="col-12 mb-3">
                      <div class="border rounded-4 overflow-hidden" style="max-width: 500px;">
                        @if($linkedCar->image_url)
                          <img src="{{ $linkedCar->image_url }}" alt="{{ $linkedCar->brand }} {{ $linkedCar->model }}" class="w-100" style="height: 250px; object-fit: cover;">
                        @else
                          <div class="bg-light d-flex align-items-center justify-content-center" style="height: 250px;">
                            <div class="text-center text-muted">
                              <i class="bi bi-car-front display-4 d-block mb-2"></i>
                              <div class="small">No image available</div>
                            </div>
                          </div>
                        @endif
                        <div class="p-3 bg-light">
                          <div class="fw-bold text-dark mb-1">{{ $linkedCar->brand }} {{ $linkedCar->model }}</div>
                          <div class="small text-muted mb-2">Plate: {{ $linkedCar->plate_number }}</div>
                          <a href="{{ route('staff.cars.edit', $linkedCar->id) }}" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-pencil-square me-1"></i>View Car Details
                          </a>
                        </div>
                      </div>
                    </div>
                  @endif
                  
                  @if($ticket->booking)
                    <div class="col-md-6">
                      <div class="p-3 bg-light rounded-4 border-start border-4 border-primary">
                        <div class="small text-muted fw-bold text-uppercase mb-1" style="font-size: 10px;">Related Booking</div>
                        <a href="{{ route('staff.bookings.show', $ticket->booking_id) }}" class="fw-bold text-primary text-decoration-none">
                          Booking #{{ $ticket->booking_id }}
                        </a>
                        @if($ticket->booking->car)
                          <div class="small text-muted mt-1">{{ $ticket->booking->car->brand }} {{ $ticket->booking->car->model }}</div>
                        @endif
                      </div>
                    </div>
                  @endif
                  @if($ticket->maintenanceRecord)
                    <div class="col-md-6">
                      <div class="p-3 bg-light rounded-4 border-start border-4 border-warning">
                        <div class="small text-muted fw-bold text-uppercase mb-1" style="font-size: 10px;">Maintenance Record</div>
                        <div class="fw-bold text-dark">Record #{{ $ticket->maintenanceRecord->maintenance_id }}</div>
                        <div class="small text-muted">Service Date: {{ $ticket->maintenanceRecord->service_date->format('d M Y') }}</div>
                      </div>
                    </div>
                  @endif
                </div>
              </div>
            </div>
          @endif
        </div>

        {{-- RIGHT: ACTIONS PANEL --}}
        <div class="col-lg-4">
          {{-- Update Status & Response --}}
          <div class="card border-0 shadow-sm mb-4 shadow-soft">
            <div class="card-header bg-dark text-white border-0 py-3 px-4">
              <h6 class="fw-bold mb-0">Manage Ticket</h6>
            </div>
            <div class="card-body p-4">
              <form method="POST" action="{{ route('staff.support-tickets.update', $ticket->ticket_id) }}">
                @csrf
                @method('PATCH')

                <div class="mb-3">
                  <label class="x-small">Status</label>
                  <select name="status" class="form-select border-2 shadow-none" required>
                    <option value="open" {{ $ticket->status === 'open' ? 'selected' : '' }}>Open</option>
                    <option value="in_progress" {{ $ticket->status === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                    <option value="resolved" {{ $ticket->status === 'resolved' ? 'selected' : '' }}>Resolved</option>
                    <option value="closed" {{ $ticket->status === 'closed' ? 'selected' : '' }}>Closed</option>
                  </select>
                </div>

                <div class="mb-3">
                  <label class="x-small">Assign To</label>
                  <select name="assigned_to" class="form-select border-2 shadow-none">
                    <option value="">Unassigned</option>
                    @foreach($staffMembers as $staff)
                      <option value="{{ $staff->staff_id }}" {{ $ticket->assigned_to == $staff->staff_id ? 'selected' : '' }}>
                        {{ $staff->name }}
                      </option>
                    @endforeach
                  </select>
                </div>

                <div class="mb-3">
                  <label class="x-small">Staff Response</label>
                  <textarea name="staff_response" class="form-control" rows="4" placeholder="Add your response to the customer...">{{ $ticket->staff_response }}</textarea>
                </div>

                <hr class="my-4">

                <div class="mb-3">
                  <label class="x-small">Link to Booking (Optional)</label>
                  <select name="booking_id" class="form-select border-2 shadow-none">
                    <option value="">No booking link</option>
                    @foreach($customerBookings as $booking)
                      <option value="{{ $booking->booking_id }}" {{ $ticket->booking_id == $booking->booking_id ? 'selected' : '' }}>
                        Booking #{{ $booking->booking_id }} - {{ $booking->car->brand ?? 'N/A' }} {{ $booking->car->model ?? '' }} ({{ $booking->created_at->format('d M Y') }})
                      </option>
                    @endforeach
                  </select>
                </div>

                <div class="mb-3">
                  <label class="x-small">Link to Vehicle (Optional)</label>
                  <select name="car_id" class="form-select border-2 shadow-none">
                    <option value="">No vehicle link</option>
                    @foreach($cars as $car)
                      <option value="{{ $car->id }}" {{ $ticket->car_id == $car->id ? 'selected' : '' }}>
                        {{ $car->brand }} {{ $car->model }} ({{ $car->plate_number }})
                      </option>
                    @endforeach
                  </select>
                </div>

                <div class="mb-4">
                  <label class="x-small">Add to Maintenance Issues</label>
                  <div class="d-flex gap-2 mt-2">
                    <div class="form-check form-check-inline">
                      <input class="form-check-input" type="radio" name="flagged_for_maintenance" id="maintenanceYes" value="1" {{ $ticket->flagged_for_maintenance ? 'checked' : '' }}>
                      <label class="form-check-label fw-semibold text-success" for="maintenanceYes">
                        <i class="bi bi-check-circle me-1"></i>Yes
                      </label>
                    </div>
                    <div class="form-check form-check-inline">
                      <input class="form-check-input" type="radio" name="flagged_for_maintenance" id="maintenanceNo" value="0" {{ !$ticket->flagged_for_maintenance ? 'checked' : '' }}>
                      <label class="form-check-label fw-semibold text-secondary" for="maintenanceNo">
                        <i class="bi bi-x-circle me-1"></i>No
                      </label>
                    </div>
                  </div>
                  <small class="text-muted d-block mt-2">
                    <i class="bi bi-info-circle me-1"></i>If "Yes", this issue will appear in the Maintenance Issues section
                  </small>
                </div>

                <button type="submit" class="btn btn-hasta w-100 py-2 fw-bold shadow-sm">
                  <i class="bi bi-save me-2"></i>Update Ticket
                </button>
              </form>
            </div>
          </div>

          {{-- Quick Actions --}}
          <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-0 pt-4 px-4">
              <h6 class="fw-bold mb-0"><i class="bi bi-lightning me-2 text-warning"></i>Quick Actions</h6>
            </div>
            <div class="card-body p-4">
              <div class="d-grid gap-2">
                <a href="{{ route('staff.maintenance-issues') }}" class="btn btn-outline-warning">
                  <i class="bi bi-tools me-2"></i>View All Maintenance Issues
                </a>
                @php
                  $linkedCar = $ticket->car ?? ($ticket->booking ? $ticket->booking->car : null);
                @endphp
                @if($linkedCar)
                  <a href="{{ route('staff.maintenance.index', $linkedCar->id) }}" class="btn btn-outline-info">
                    <i class="bi bi-wrench me-2"></i>View Car Maintenance History
                  </a>
                  <a href="{{ route('staff.cars.edit', $linkedCar->id) }}" class="btn btn-outline-secondary">
                    <i class="bi bi-car-front me-2"></i>Edit Vehicle Details
                  </a>
                @endif
              </div>
            </div>
          </div>

          {{-- Customer Information --}}
          @if($ticket->customer)
            <div class="card border-0 shadow-sm">
              <div class="card-header bg-white border-0 pt-4 px-4">
                <h6 class="fw-bold mb-0">Customer Profile</h6>
              </div>
              <div class="card-body p-4">
                <div class="d-flex align-items-center gap-3 mb-3">
                  <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center fw-bold shadow-sm" style="width: 42px; height: 42px;">
                    {{ substr($ticket->customer->full_name ?? 'C', 0, 1) }}
                  </div>
                  <div>
                    <div class="fw-bold text-dark">{{ $ticket->customer->full_name }}</div>
                    <div class="small text-muted">{{ $ticket->customer->email }}</div>
                  </div>
                </div>
                <a href="{{ route('staff.customers.show', $ticket->customer_id) }}" class="btn btn-sm btn-outline-primary w-100">
                  View Customer Profile
                </a>
              </div>
            </div>
          @endif
        </div>
      </div>
    </div>
  </div>
</div>

<style>
  .shadow-soft { box-shadow: 0 10px 20px rgba(0,0,0,0.05) !important; }
  .x-small { font-size: 10px; font-weight: 700; text-transform: uppercase; color: #718096; display: block; margin-bottom: 4px; }
  .btn-hasta { background-color: var(--hasta); color: #fff; border: none; }
  .btn-hasta:hover { background-color: var(--hasta-darker); color: #fff; }
</style>
@endsection
