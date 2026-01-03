@extends('layouts.app')
@section('title', 'Cancellation Request #{{ $cancelRequest->request_id }} - Staff Dashboard')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
@endpush

@section('content')
<div class="d-flex" style="min-height: calc(100vh - 60px);">
  @include('staff._nav')
  <div class="flex-fill" style="background: var(--bg);">
    <div class="container-fluid px-4 px-md-5" style="padding-top: 32px; padding-bottom: 48px;">
      
      <div class="mb-4">
        <a href="{{ route('staff.cancellation-requests') }}" class="text-decoration-none small">&larr; Back to Cancellation Requests</a>
        <h1 class="h3 fw-bold mt-2 mb-1" style="color:#333;">Cancellation Request #{{ $cancelRequest->request_id }}</h1>
        <p class="text-muted mb-0">Submitted on {{ $cancelRequest->created_at->format('d M Y, H:i') }}</p>
      </div>

      @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
          {{ session('success') }}
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
      @endif

      @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
          {{ session('error') }}
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
      @endif

      <div class="row g-4">
        {{-- Left Column: Request Details --}}
        <div class="col-lg-8">
          
          {{-- Status Card --}}
          <div class="card border-0 shadow-soft mb-4">
            <div class="card-body p-4">
              <div class="d-flex justify-content-between align-items-center">
                <div>
                  <h5 class="fw-bold mb-1">Request Status</h5>
                  <span class="badge 
                    {{ $cancelRequest->status === 'pending' ? 'bg-warning text-dark' : '' }}
                    {{ $cancelRequest->status === 'approved' ? 'bg-success' : '' }}
                    {{ $cancelRequest->status === 'refunded' ? 'bg-info' : '' }}
                    {{ $cancelRequest->status === 'rejected' ? 'bg-danger' : '' }}
                    fs-6
                  ">
                    {{ ucfirst($cancelRequest->status) }}
                  </span>
                </div>
                @if($cancelRequest->processed_at)
                  <div class="text-end">
                    <div class="small text-muted">Processed on</div>
                    <div>{{ $cancelRequest->processed_at->format('d M Y, H:i') }}</div>
                  </div>
                @endif
              </div>
            </div>
          </div>

          {{-- Cancellation Reason --}}
          <div class="card border-0 shadow-soft mb-4">
            <div class="card-body p-4">
              <h5 class="fw-bold mb-3"><i class="bi bi-question-circle me-2"></i> Cancellation Reason</h5>
              <div class="mb-3">
                <span class="badge bg-secondary fs-6">{{ $cancelRequest->reason_label }}</span>
              </div>
              @if($cancelRequest->reason_details)
                <div class="bg-light p-3 rounded">
                  <strong>Additional Details:</strong>
                  <p class="mb-0 mt-2">{{ $cancelRequest->reason_details }}</p>
                </div>
              @endif
            </div>
          </div>

          {{-- Bank Details for Refund --}}
          <div class="card border-0 shadow-soft mb-4">
            <div class="card-body p-4">
              <h5 class="fw-bold mb-3"><i class="bi bi-bank me-2"></i> Refund Bank Details</h5>
              <div class="row g-3">
                <div class="col-md-4">
                  <div class="small text-muted">Bank Name</div>
                  <div class="fw-semibold">{{ $cancelRequest->bank_name }}</div>
                </div>
                <div class="col-md-4">
                  <div class="small text-muted">Account Number</div>
                  <div class="fw-semibold">{{ $cancelRequest->bank_account_number }}</div>
                </div>
                <div class="col-md-4">
                  <div class="small text-muted">Account Holder</div>
                  <div class="fw-semibold">{{ $cancelRequest->bank_account_holder }}</div>
                </div>
              </div>
            </div>
          </div>

          {{-- Supporting Document --}}
          @if($cancelRequest->proof_document_url)
          <div class="card border-0 shadow-soft mb-4">
            <div class="card-body p-4">
              <h5 class="fw-bold mb-3"><i class="bi bi-file-earmark me-2"></i> Supporting Document</h5>
              <a href="{{ $cancelRequest->proof_document_url }}" target="_blank" class="btn btn-outline-primary">
                <i class="bi bi-download me-2"></i> View/Download Document
              </a>
            </div>
          </div>
          @endif

          {{-- Process Actions (only if pending or approved) --}}
          @if(in_array($cancelRequest->status, ['pending', 'approved']))
          <div class="card border-0 shadow-soft">
            <div class="card-body p-4">
              <h5 class="fw-bold mb-3"><i class="bi bi-gear me-2"></i> Process Request</h5>
              
              @if($cancelRequest->status === 'pending')
                <div class="row g-3">
                  {{-- Approve --}}
                  <div class="col-md-6">
                    <div class="card border-success h-100">
                      <div class="card-body">
                        <h6 class="fw-bold text-success"><i class="bi bi-check-circle me-2"></i> Approve Cancellation</h6>
                        <p class="small text-muted mb-3">Approve the request and cancel the booking. Customer can then receive refund.</p>
                        <form method="POST" action="{{ route('staff.cancellation-requests.process', $cancelRequest->request_id) }}">
                          @csrf
                          @method('PATCH')
                          <input type="hidden" name="action" value="approve">
                          <div class="mb-3">
                            <label class="form-label small">Staff Notes (Optional)</label>
                            <textarea name="staff_notes" class="form-control form-control-sm" rows="2" placeholder="Add any notes..."></textarea>
                          </div>
                          <button type="submit" class="btn btn-success btn-sm w-100" onclick="return confirm('Are you sure you want to approve this cancellation?');">
                            <i class="bi bi-check me-1"></i> Approve
                          </button>
                        </form>
                      </div>
                    </div>
                  </div>
                  
                  {{-- Reject --}}
                  <div class="col-md-6">
                    <div class="card border-danger h-100">
                      <div class="card-body">
                        <h6 class="fw-bold text-danger"><i class="bi bi-x-circle me-2"></i> Reject Cancellation</h6>
                        <p class="small text-muted mb-3">Reject the request. The booking will remain active.</p>
                        <form method="POST" action="{{ route('staff.cancellation-requests.process', $cancelRequest->request_id) }}">
                          @csrf
                          @method('PATCH')
                          <input type="hidden" name="action" value="reject">
                          <div class="mb-3">
                            <label class="form-label small">Reason for Rejection <span class="text-danger">*</span></label>
                            <textarea name="staff_notes" class="form-control form-control-sm" rows="2" placeholder="Explain why..." required></textarea>
                          </div>
                          <button type="submit" class="btn btn-danger btn-sm w-100" onclick="return confirm('Are you sure you want to reject this cancellation?');">
                            <i class="bi bi-x me-1"></i> Reject
                          </button>
                        </form>
                      </div>
                    </div>
                  </div>
                </div>
              @elseif($cancelRequest->status === 'approved')
                {{-- Mark as Refunded --}}
                <div class="card border-info">
                  <div class="card-body">
                    <h6 class="fw-bold text-info"><i class="bi bi-cash me-2"></i> Process Refund</h6>
                    <p class="small text-muted mb-3">Mark the refund as completed after transferring funds to customer's bank account.</p>
                    <form method="POST" action="{{ route('staff.cancellation-requests.process', $cancelRequest->request_id) }}">
                      @csrf
                      @method('PATCH')
                      <input type="hidden" name="action" value="refund">
                      <div class="row g-3">
                        <div class="col-md-6">
                          <label class="form-label small">Refund Amount (RM) <span class="text-danger">*</span></label>
                          <input type="number" name="refund_amount" class="form-control form-control-sm" step="0.01" min="0" value="{{ $cancelRequest->booking->deposit_amount ?? 0 }}" required>
                        </div>
                        <div class="col-md-6">
                          <label class="form-label small">Transaction Reference</label>
                          <input type="text" name="refund_reference" class="form-control form-control-sm" placeholder="Bank ref number...">
                        </div>
                        <div class="col-12">
                          <label class="form-label small">Notes (Optional)</label>
                          <textarea name="staff_notes" class="form-control form-control-sm" rows="2" placeholder="Add any notes..."></textarea>
                        </div>
                        <div class="col-12">
                          <button type="submit" class="btn btn-info btn-sm" onclick="return confirm('Confirm that refund has been processed?');">
                            <i class="bi bi-check2-circle me-1"></i> Mark as Refunded
                          </button>
                        </div>
                      </div>
                    </form>
                  </div>
                </div>
              @endif
            </div>
          </div>
          @endif

          {{-- Staff Notes (if processed) --}}
          @if($cancelRequest->staff_notes && in_array($cancelRequest->status, ['approved', 'rejected', 'refunded']))
          <div class="card border-0 shadow-soft mt-4">
            <div class="card-body p-4">
              <h5 class="fw-bold mb-3"><i class="bi bi-chat-left-text me-2"></i> Staff Notes</h5>
              <div class="bg-light p-3 rounded">
                {{ $cancelRequest->staff_notes }}
              </div>
            </div>
          </div>
          @endif

          {{-- Refund Info (if refunded) --}}
          @if($cancelRequest->status === 'refunded')
          <div class="card border-0 shadow-soft mt-4 border-info">
            <div class="card-body p-4">
              <h5 class="fw-bold mb-3 text-info"><i class="bi bi-cash-coin me-2"></i> Refund Information</h5>
              <div class="row g-3">
                <div class="col-md-4">
                  <div class="small text-muted">Refund Amount</div>
                  <div class="h4 fw-bold text-success">RM {{ number_format($cancelRequest->refund_amount, 2) }}</div>
                </div>
                <div class="col-md-4">
                  <div class="small text-muted">Reference</div>
                  <div class="fw-semibold">{{ $cancelRequest->refund_reference ?? 'N/A' }}</div>
                </div>
                <div class="col-md-4">
                  <div class="small text-muted">Refunded On</div>
                  <div class="fw-semibold">{{ $cancelRequest->refunded_at?->format('d M Y, H:i') ?? 'N/A' }}</div>
                </div>
              </div>
            </div>
          </div>
          @endif
        </div>

        {{-- Right Column: Booking & Customer Info --}}
        <div class="col-lg-4">
          {{-- Booking Info --}}
          <div class="card border-0 shadow-soft mb-4">
            <div class="card-body p-4">
              <h6 class="fw-bold mb-3"><i class="bi bi-calendar me-2"></i> Booking Details</h6>
              
              @if($cancelRequest->booking)
                @php $booking = $cancelRequest->booking; @endphp
                
                <div class="mb-3">
                  <a href="{{ route('staff.bookings.show', $booking->booking_id) }}" class="btn btn-sm btn-outline-primary w-100">
                    View Booking #{{ $booking->booking_id }}
                  </a>
                </div>
                
                @if($booking->car)
                  <div class="mb-3">
                    <div class="small text-muted">Car</div>
                    <div class="fw-semibold">{{ $booking->car->brand }} {{ $booking->car->model }}</div>
                    <div class="small text-muted">{{ $booking->car->plate_number }}</div>
                  </div>
                @endif
                
                <div class="mb-3">
                  <div class="small text-muted">Booking Period</div>
                  <div>{{ $booking->start_datetime?->format('d M Y, H:i') }}</div>
                  <div>to {{ $booking->end_datetime?->format('d M Y, H:i') }}</div>
                </div>
                
                <div class="mb-3">
                  <div class="small text-muted">Amount Paid</div>
                  <div class="h5 fw-bold">RM {{ number_format($booking->final_amount ?? 0, 2) }}</div>
                </div>
                
                <div>
                  <div class="small text-muted">Deposit Amount</div>
                  <div class="fw-semibold">RM {{ number_format($booking->deposit_amount ?? 0, 2) }}</div>
                </div>
              @endif
            </div>
          </div>

          {{-- Customer Info --}}
          <div class="card border-0 shadow-soft">
            <div class="card-body p-4">
              <h6 class="fw-bold mb-3"><i class="bi bi-person me-2"></i> Customer Details</h6>
              
              @if($cancelRequest->customer)
                @php $customer = $cancelRequest->customer; @endphp
                
                <div class="mb-3">
                  <div class="small text-muted">Name</div>
                  <div class="fw-semibold">{{ $customer->full_name }}</div>
                </div>
                
                <div class="mb-3">
                  <div class="small text-muted">Email</div>
                  <div>{{ $customer->email }}</div>
                </div>
                
                <div class="mb-3">
                  <div class="small text-muted">Phone</div>
                  <div>{{ $customer->phone ?? 'N/A' }}</div>
                </div>
                
                <a href="{{ route('staff.customers.show', $customer->customer_id) }}" class="btn btn-sm btn-outline-secondary w-100">
                  View Customer Profile
                </a>
              @endif
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>
</div>
@endsection
