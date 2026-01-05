@extends('layouts.app')
@section('title', 'Customer Feedback - Staff Dashboard')

@section('content')
<div class="d-flex" style="min-height: calc(100vh - 60px);">
  @include('staff._nav')
  <div class="flex-fill" style="background: var(--bg);">
    <div class="container-fluid px-4 px-md-5" style="padding-top: 32px; padding-bottom: 48px;">
      <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
        <div>
          <h1 class="h4 fw-800 mb-0" style="color:#1e293b;">Customer Feedback</h1>
          <p class="text-muted mb-0" style="font-size: 13px;">Monitor customer satisfaction and service quality.</p>
        </div>
      </div>

      @if(!($tableExists ?? true))
        <div class="alert alert-warning border-0 shadow-sm rounded-3 py-2 px-3 small">
          <strong>Feedback table not found:</strong> Please run migrations to enable this feature.
        </div>
      @else
        {{-- STATISTICS CARDS --}}
        <div class="row g-3 mb-4">
          <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100 rounded-3">
              <div class="card-body p-3">
                <div class="text-slate-500 small fw-700 text-uppercase mb-1" style="font-size: 10px; letter-spacing: 0.5px;">Total Reviews</div>
                <div class="d-flex align-items-center justify-content-between">
                  <h4 class="fw-800 mb-0 text-slate-700">{{ $stats['total'] ?? 0 }}</h4>
                  <i class="bi bi-chat-left-text text-slate-300 fs-5"></i>
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100 rounded-3">
              <div class="card-body p-3">
                <div class="text-slate-500 small fw-700 text-uppercase mb-1" style="font-size: 10px; letter-spacing: 0.5px;">Avg Rating</div>
                <div class="d-flex align-items-center justify-content-between">
                  <h4 class="fw-800 mb-0 text-slate-700">
                    @if($stats['averageRating'] ?? 0)
                      {{ number_format($stats['averageRating'], 1) }}<span class="fs-6 fw-normal text-muted ms-1">/ 5.0</span>
                    @else
                      0.0
                    @endif
                  </h4>
                  <i class="bi bi-star text-slate-300 fs-5"></i>
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100 rounded-3">
              <div class="card-body p-3">
                <div class="text-slate-500 small fw-700 text-uppercase mb-1" style="font-size: 10px; letter-spacing: 0.5px;">Reported Issues</div>
                <div class="d-flex align-items-center justify-content-between">
                  <h4 class="fw-800 mb-0 {{ ($stats['reportedIssues'] ?? 0) > 0 ? 'text-danger' : 'text-slate-700' }}">{{ $stats['reportedIssues'] ?? 0 }}</h4>
                  <i class="bi bi-exclamation-circle text-slate-300 fs-5"></i>
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100 rounded-3">
              <div class="card-body p-3">
                <div class="text-slate-500 small fw-700 text-uppercase mb-1" style="font-size: 10px; letter-spacing: 0.5px;">5-Star Reviews</div>
                <div class="d-flex align-items-center justify-content-between">
                  <h4 class="fw-800 mb-0 text-slate-700">{{ $stats['ratingDistribution'][5] ?? 0 }}</h4>
                  <i class="bi bi-hand-thumbs-up text-slate-300 fs-5"></i>
                </div>
              </div>
            </div>
          </div>
        </div>

        {{-- FILTERS --}}
        <div class="card border-0 shadow-sm mb-4 rounded-3">
          <div class="card-body p-2 px-3">
            <form method="GET" action="{{ route('staff.feedbacks') }}" class="row g-2 align-items-center">
              <div class="col-md-2">
                <select name="rating" class="form-select form-select-sm border-0 bg-light fw-600">
                  <option value="">All Ratings</option>
                  <option value="5" {{ ($filters['rating'] ?? '') === '5' ? 'selected' : '' }}>5 Stars</option>
                  <option value="4" {{ ($filters['rating'] ?? '') === '4' ? 'selected' : '' }}>4 Stars</option>
                  <option value="3" {{ ($filters['rating'] ?? '') === '3' ? 'selected' : '' }}>3 Stars</option>
                  <option value="2" {{ ($filters['rating'] ?? '') === '2' ? 'selected' : '' }}>2 Stars</option>
                  <option value="1" {{ ($filters['rating'] ?? '') === '1' ? 'selected' : '' }}>1 Star</option>
                </select>
              </div>
              <div class="col-md-2">
                <select name="reported_issue" class="form-select form-select-sm border-0 bg-light fw-600">
                  <option value="">All Feedback</option>
                  <option value="1" {{ ($filters['reported_issue'] ?? '') === '1' ? 'selected' : '' }}>Issues Only</option>
                  <option value="0" {{ ($filters['reported_issue'] ?? '') === '0' ? 'selected' : '' }}>Reviews Only</option>
                </select>
              </div>
              <div class="col-md-6">
                <div class="input-group input-group-sm">
                  <span class="input-group-text border-0 bg-light text-muted"><i class="bi bi-search" style="font-size: 12px;"></i></span>
                  <input type="text" name="search" class="form-control form-control-sm border-0 bg-light fw-500" placeholder="Search customer, booking, or comment..." value="{{ $filters['search'] ?? '' }}">
                </div>
              </div>
              <div class="col-md-2">
                <button type="submit" class="btn btn-hasta btn-sm w-100 rounded-2 fw-700 py-1">Apply</button>
              </div>
            </form>
          </div>
        </div>

        {{-- FEEDBACK LIST --}}
        <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
          <div class="card-body p-0">
            <div class="table-responsive">
              <table class="table table-hover align-middle mb-0" style="font-size: 13px;">
                <thead class="bg-light">
                  <tr>
                    <th class="ps-3 py-2 border-0 text-uppercase small fw-700 text-slate-500" style="font-size: 11px;">Customer</th>
                    <th class="py-2 border-0 text-uppercase small fw-700 text-slate-500" style="font-size: 11px;">Booking/Car</th>
                    <th class="py-2 border-0 text-uppercase small fw-700 text-slate-500" style="font-size: 11px;">Rating</th>
                    <th class="py-2 border-0 text-uppercase small fw-700 text-slate-500" style="font-size: 11px;">Comment</th>
                    <th class="py-2 border-0 text-uppercase small fw-700 text-slate-500" style="font-size: 11px;">Status</th>
                    <th class="pe-3 py-2 border-0 text-uppercase small fw-700 text-slate-500 text-end" style="font-size: 11px;">Date</th>
                  </tr>
                </thead>
                <tbody class="border-top-0">
                  @forelse($feedbacks as $feedback)
                    <tr>
                      <td class="ps-3 py-2">
                        <div class="d-flex align-items-center">
                          <div class="rounded-circle bg-light d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px; color: var(--slate-600); font-weight: 700; font-size: 12px;">
                            {{ strtoupper(substr($feedback->customer->full_name ?? 'N', 0, 1)) }}
                          </div>
                          <div>
                            <div class="fw-bold text-slate-700">{{ $feedback->customer->full_name ?? 'N/A' }}</div>
                            <div class="text-muted" style="font-size: 11px;">{{ $feedback->customer->email ?? '' }}</div>
                          </div>
                        </div>
                      </td>
                      <td class="py-2">
                        <a href="{{ route('staff.bookings.show', $feedback->booking_id) }}" class="text-decoration-none fw-700 text-primary">
                          #{{ $feedback->booking_id }}
                        </a>
                        @if($feedback->booking && $feedback->booking->car)
                          <div class="text-muted" style="font-size: 11px;">{{ $feedback->booking->car->brand }} {{ $feedback->booking->car->model }}</div>
                        @endif
                      </td>
                      <td class="py-2">
                        <div class="d-flex align-items-center gap-1">
                          <i class="bi bi-star-fill text-warning" style="font-size: 11px;"></i>
                          <span class="fw-700 text-slate-700">{{ $feedback->rating }}.0</span>
                        </div>
                      </td>
                      <td class="py-2" style="max-width: 350px;">
                        @if($feedback->comment)
                          <div class="text-slate-600 fw-500 line-clamp-2" title="{{ $feedback->comment }}">
                            {{ $feedback->comment }}
                          </div>
                        @else
                          <span class="text-muted italic opacity-50" style="font-size: 11px;">No comment</span>
                        @endif
                      </td>
                      <td class="py-2">
                        @if($feedback->reported_issue)
                          <span class="badge bg-danger-subtle text-danger border-0 rounded-pill px-2 py-1 fw-700" style="font-size: 10px;">
                            <i class="bi bi-exclamation-triangle-fill me-1"></i> ISSUE
                          </span>
                        @else
                          <span class="badge bg-success-subtle text-success border-0 rounded-pill px-2 py-1 fw-700" style="font-size: 10px;">
                            <i class="bi bi-check-circle-fill me-1"></i> NORMAL
                          </span>
                        @endif
                      </td>
                      <td class="pe-3 py-2 text-end">
                        <div class="fw-600 text-slate-700">{{ $feedback->created_at->format('d M Y') }}</div>
                        <div class="text-muted" style="font-size: 11px;">{{ $feedback->created_at->format('H:i') }}</div>
                      </td>
                    </tr>
                  @empty
                    <tr>
                      <td colspan="6" class="text-center py-4">
                        <small class="text-muted">No feedback records found</small>
                      </td>
                    </tr>
                  @endforelse
                </tbody>
              </table>
            </div>
          </div>
        </div>

        {{-- PAGINATION --}}
        <div class="mt-3 d-flex justify-content-center">
          {{ $feedbacks->links() }}
        </div>
      @endif
    </div>
  </div>
</div>

<style>
  .fw-800 { font-weight: 800; }
  .fw-700 { font-weight: 700; }
  .fw-600 { font-weight: 600; }
  .fw-500 { font-weight: 500; }
  
  .text-slate-500 { color: #64748b; }
  .text-slate-600 { color: #475569; }
  .text-slate-700 { color: #334155; }
  
  .bg-danger-subtle { background-color: #fee2e2; }
  .bg-success-subtle { background-color: #dcfce7; }
  
  .border-danger-subtle { border-color: #fecaca !important; }
  .border-success-subtle { border-color: #bbf7d0 !important; }

  /* Pagination Styling */
  .pagination {
    gap: 3px;
  }
  .page-item .page-link {
    border-radius: 4px !important;
    border: 1px solid #e2e8f0;
    padding: 4px 10px;
    color: #475569;
    font-size: 12px;
    font-weight: 600;
    transition: all 0.2s;
  }
  .page-item.active .page-link {
    background-color: var(--hasta);
    border-color: var(--hasta);
    color: white;
  }
  .page-item .page-link:hover:not(.active) {
    background-color: #f8fafc;
    border-color: #cbd5e1;
    color: var(--hasta);
    transform: translateY(-1px);
  }
</style>
@endsection
