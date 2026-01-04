@extends('layouts.app')
@section('title', 'Support Ticket #' . $ticket->ticket_id . ' - Hasta GoRent')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
@endpush

@section('content')
<div class="container-fluid px-4 px-md-5" style="padding-top: 32px; padding-bottom: 48px;">
  <div class="mb-4">
    <a href="{{ route('customer.support-tickets') }}" class="text-decoration-none small">&larr; Back to Support Tickets</a>
    <div class="d-flex align-items-center gap-3 mt-2">
      <h1 class="h3 fw-bold mb-0" style="color:#333;">Support Ticket #{{ $ticket->ticket_id }}</h1>
      @php
        $statusClasses = [
          'open' => 'bg-primary text-white',
          'in_progress' => 'bg-info text-white',
          'resolved' => 'bg-success text-white',
          'closed' => 'bg-secondary text-white'
        ];
        $statusClass = $statusClasses[$ticket->status] ?? 'bg-secondary text-white';
      @endphp
      <span class="badge rounded-pill px-3 py-2 {{ $statusClass }}">
        {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
      </span>
    </div>
  </div>

  <div class="row g-4">
    {{-- LEFT: TICKET DETAILS --}}
    <div class="col-lg-8">
      {{-- Ticket Information --}}
      <div class="card border-0 shadow-soft mb-4">
        <div class="card-header bg-white border-0 pt-4 px-4">
          <h5 class="fw-bold mb-0"><i class="bi bi-ticket-perforated me-2 text-primary"></i>Ticket Details</h5>
        </div>
        <div class="card-body p-4">
          <div class="row g-4 mb-4">
            <div class="col-md-6">
              <div class="small text-muted fw-bold text-uppercase mb-1" style="font-size: 10px;">Category</div>
              <span class="badge bg-light text-dark border px-3 py-2">
                {{ ucfirst(str_replace('_', ' ', $ticket->category)) }}
              </span>
              <div class="small text-muted mt-2">
                <i class="bi bi-calendar me-1"></i>Created: {{ $ticket->created_at->format('d M Y, H:i') }}
              </div>
            </div>
            @if($ticket->resolved_at)
              <div class="col-md-6">
                <div class="small text-muted fw-bold text-uppercase mb-1" style="font-size: 10px;">Resolved</div>
                <div class="fw-semibold">{{ $ticket->resolved_at->format('d M Y, H:i') }}</div>
              </div>
            @endif
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

      {{-- Staff Response --}}
      @if($ticket->staff_response)
        <div class="card border-0 shadow-soft mb-4 border-success-subtle">
          <div class="card-header bg-success-subtle border-0 pt-4 px-4">
            <h5 class="fw-bold mb-0 text-success"><i class="bi bi-check-circle me-2"></i>Staff Response</h5>
          </div>
          <div class="card-body p-4">
            <div class="p-3 bg-white rounded-4 border">
              <p class="mb-0" style="white-space: pre-wrap;">{{ $ticket->staff_response }}</p>
            </div>
            @if($ticket->assignedStaff)
              <div class="small text-muted mt-3">
                <i class="bi bi-person me-1"></i>Response from: {{ $ticket->assignedStaff->name }}
              </div>
            @endif
          </div>
        </div>
      @else
        <div class="card border-0 shadow-soft mb-4">
          <div class="card-body p-4 text-center">
            <i class="bi bi-clock-history text-muted mb-3" style="font-size: 48px;"></i>
            <p class="text-muted mb-0">Waiting for staff response...</p>
          </div>
        </div>
      @endif

      {{-- Linked Information --}}
      @if($ticket->booking || $ticket->car)
        <div class="card border-0 shadow-soft mb-4">
          <div class="card-header bg-white border-0 pt-4 px-4">
            <h5 class="fw-bold mb-0"><i class="bi bi-link-45deg me-2 text-info"></i>Related Information</h5>
          </div>
          <div class="card-body p-4">
            <div class="row g-3">
              @if($ticket->booking)
                <div class="col-md-6">
                  <div class="p-3 bg-light rounded-4 border-start border-4 border-primary">
                    <div class="small text-muted fw-bold text-uppercase mb-1" style="font-size: 10px;">Related Booking</div>
                    <a href="{{ route('customer.bookings.show', $ticket->booking_id) }}" class="fw-bold text-primary text-decoration-none">
                      Booking #{{ $ticket->booking_id }}
                    </a>
                    @if($ticket->booking->car)
                      <div class="small text-muted mt-1">{{ $ticket->booking->car->brand }} {{ $ticket->booking->car->model }}</div>
                    @endif
                  </div>
                </div>
              @endif
              @if($ticket->car && !$ticket->booking)
                <div class="col-md-6">
                  <div class="p-3 bg-light rounded-4 border-start border-4 border-info">
                    <div class="small text-muted fw-bold text-uppercase mb-1" style="font-size: 10px;">Related Vehicle</div>
                    <div class="fw-bold text-dark">{{ $ticket->car->brand }} {{ $ticket->car->model }}</div>
                    <div class="small text-muted">Plate: {{ $ticket->car->plate_number }}</div>
                  </div>
                </div>
              @endif
            </div>
          </div>
        </div>
      @endif
    </div>

    {{-- RIGHT: STATUS INFO --}}
    <div class="col-lg-4">
      <div class="card border-0 shadow-soft mb-4">
        <div class="card-header bg-dark text-white border-0 py-3 px-4">
          <h6 class="fw-bold mb-0">Ticket Status</h6>
        </div>
        <div class="card-body p-4">
          <div class="mb-3">
            <div class="small text-muted fw-bold text-uppercase mb-2" style="font-size: 10px;">Current Status</div>
            <span class="badge rounded-pill px-4 py-2 {{ $statusClass }}">
              {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
            </span>
          </div>

          @if($ticket->assignedStaff)
            <div class="mb-3">
              <div class="small text-muted fw-bold text-uppercase mb-1" style="font-size: 10px;">Assigned To</div>
              <div class="fw-semibold">{{ $ticket->assignedStaff->name }}</div>
            </div>
          @endif

          <hr class="my-4">

          <div class="small text-muted">
            <div class="mb-2">
              <strong>Submitted:</strong><br>
              {{ $ticket->created_at->format('d M Y, H:i') }}
            </div>
            @if($ticket->resolved_at)
              <div class="mb-2">
                <strong>Resolved:</strong><br>
                {{ $ticket->resolved_at->format('d M Y, H:i') }}
              </div>
            @endif
            @if($ticket->closed_at)
              <div>
                <strong>Closed:</strong><br>
                {{ $ticket->closed_at->format('d M Y, H:i') }}
              </div>
            @endif
          </div>
        </div>
      </div>

      <div class="card border-0 shadow-soft">
        <div class="card-body p-4">
          <h6 class="fw-bold mb-3">Need More Help?</h6>
          <p class="small text-muted mb-3">If you need to report another issue or have additional questions, you can submit a new support ticket.</p>
          <a href="{{ route('home') }}#support" class="btn btn-hasta w-100">
            <i class="bi bi-plus-lg me-2"></i>Report New Issue
          </a>
        </div>
      </div>
    </div>
  </div>
</div>

<style>
  .shadow-soft { box-shadow: 0 10px 20px rgba(0,0,0,0.05) !important; }
</style>
@endsection
