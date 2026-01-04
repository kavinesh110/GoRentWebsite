@extends('layouts.app')
@section('title', 'My Support Tickets - Hasta GoRent')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
@endpush

@section('content')
<div class="container-fluid px-4 px-md-5" style="padding-top: 32px; padding-bottom: 48px;">
  <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
    <div>
      <h1 class="h3 fw-bold mb-1" style="color:#333;">My Support Tickets</h1>
      <p class="text-muted mb-0" style="font-size: 14px;">Track the status of your car issue reports and view staff responses.</p>
    </div>
    <div>
      <a href="{{ route('home') }}#support" class="btn btn-hasta">
        <i class="bi bi-plus-lg"></i> Report New Issue
      </a>
    </div>
  </div>

  @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
      <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  @endif

  {{-- FILTERS --}}
  <div class="card border-0 shadow-soft mb-4">
    <div class="card-body p-4">
      <form method="GET" action="{{ route('customer.support-tickets') }}" class="row g-3 align-items-end">
        <div class="col-md-4 col-lg-3">
          <label class="form-label fw-semibold small">Filter by Status</label>
          <select name="status" class="form-select border-0 bg-light">
            <option value="">All statuses</option>
            <option value="open" {{ ($filters['status'] ?? '') === 'open' ? 'selected' : '' }}>Open</option>
            <option value="in_progress" {{ ($filters['status'] ?? '') === 'in_progress' ? 'selected' : '' }}>In Progress</option>
            <option value="resolved" {{ ($filters['status'] ?? '') === 'resolved' ? 'selected' : '' }}>Resolved</option>
            <option value="closed" {{ ($filters['status'] ?? '') === 'closed' ? 'selected' : '' }}>Closed</option>
          </select>
        </div>
        <div class="col-md-3 col-lg-2">
          <button type="submit" class="btn btn-hasta w-100">Apply Filter</button>
        </div>
        @if(request('status'))
          <div class="col-md-3 col-lg-2">
            <a href="{{ route('customer.support-tickets') }}" class="btn btn-outline-secondary w-100">Clear</a>
          </div>
        @endif
      </form>
    </div>
  </div>

  {{-- TICKETS LIST --}}
  <div class="row g-4">
    @forelse($tickets as $ticket)
      <div class="col-12">
        <div class="card border-0 shadow-soft overflow-hidden">
          <div class="card-body p-4">
            <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-3">
              <div class="flex-grow-1">
                <div class="d-flex align-items-center gap-2 mb-2">
                  <h5 class="fw-bold mb-0">Ticket #{{ $ticket->ticket_id }}</h5>
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
                  <span class="badge bg-light text-dark border px-2 py-1">
                    {{ ucfirst(str_replace('_', ' ', $ticket->category)) }}
                  </span>
                </div>
                <h6 class="fw-semibold mb-2">{{ $ticket->subject }}</h6>
                <p class="text-muted small mb-0">{{ Str::limit($ticket->description, 150) }}</p>
              </div>
              <div class="text-md-end">
                <div class="small text-muted mb-1">Submitted</div>
                <div class="fw-semibold small">{{ $ticket->created_at->format('d M Y') }}</div>
                <div class="text-muted small">{{ $ticket->created_at->format('H:i') }}</div>
              </div>
            </div>

            @if($ticket->staff_response)
              <div class="alert alert-info border-0 mb-3" style="background: #e7f3ff;">
                <div class="d-flex align-items-start gap-2">
                  <i class="bi bi-info-circle-fill mt-1" style="color: #0d6efd;"></i>
                  <div class="flex-grow-1">
                    <strong class="d-block mb-1" style="color: #0d6efd;">Staff Response:</strong>
                    <p class="mb-0 small" style="color: #0a58ca;">{{ Str::limit($ticket->staff_response, 200) }}</p>
                  </div>
                </div>
              </div>
            @endif

            <div class="d-flex justify-content-end">
              <a href="{{ route('customer.support-tickets.show', $ticket->ticket_id) }}" class="btn btn-sm btn-outline-hasta px-4">
                View Details <i class="bi bi-arrow-right ms-1"></i>
              </a>
            </div>
          </div>
        </div>
      </div>
    @empty
      <div class="col-12">
        <div class="card border-0 shadow-soft py-5">
          <div class="card-body text-center py-5">
            <div class="mb-3 text-muted">
              <i class="bi bi-headset" style="font-size: 64px;"></i>
            </div>
            <h5 class="fw-bold">No Support Tickets</h5>
            <p class="text-muted">You haven't submitted any car issue reports yet.</p>
            <a href="{{ route('home') }}#support" class="btn btn-hasta mt-2 px-4">Report Car Issue</a>
          </div>
        </div>
      </div>
    @endforelse
  </div>

  {{-- PAGINATION --}}
  <div class="mt-5 d-flex justify-content-center">
    {{ $tickets->links() }}
  </div>
</div>

<style>
  .btn-outline-hasta {
    color: var(--hasta);
    border-color: var(--hasta);
  }
  .btn-outline-hasta:hover {
    background-color: var(--hasta);
    color: white;
  }
</style>
@endsection
