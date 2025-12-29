@extends('layouts.app')
@section('title', 'Customer Feedback - Staff Dashboard')

@section('content')
<div class="d-flex" style="min-height: calc(100vh - 60px);">
  @include('staff._nav')
  <div class="flex-fill" style="background: var(--bg);">
    <div class="container-fluid px-4 px-md-5" style="padding-top: 32px; padding-bottom: 48px;">
      <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
        <div>
          <h1 class="h3 fw-bold mb-1" style="color:#333;">Customer Feedback</h1>
          <p class="text-muted mb-0" style="font-size: 14px;">View and manage customer reviews and reported issues.</p>
        </div>
      </div>

      @if(!($tableExists ?? true))
        <div class="alert alert-warning border-0 shadow-sm">
          <strong>Feedback table not found:</strong> The feedback table has not been created in the database yet. Please run migrations to enable this feature.
        </div>
      @else
        {{-- STATISTICS CARDS --}}
        <div class="row g-3 mb-4">
          <div class="col-md-3">
            <div class="card border-0 shadow-soft h-100">
              <div class="card-body p-3 text-center">
                <div class="small text-muted text-uppercase mb-1">Total Feedback</div>
                <div class="h4 fw-bold mb-0">{{ $stats['total'] ?? 0 }}</div>
              </div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="card border-0 shadow-soft h-100">
              <div class="card-body p-3 text-center">
                <div class="small text-muted text-uppercase mb-1">Average Rating</div>
                <div class="h4 fw-bold mb-0">
                  @if($stats['averageRating'] ?? 0)
                    {{ number_format($stats['averageRating'], 1) }} <span class="text-warning">★</span>
                  @else
                    N/A
                  @endif
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="card border-0 shadow-soft h-100">
              <div class="card-body p-3 text-center">
                <div class="small text-muted text-uppercase mb-1">Reported Issues</div>
                <div class="h4 fw-bold mb-0 text-danger">{{ $stats['reportedIssues'] ?? 0 }}</div>
              </div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="card border-0 shadow-soft h-100">
              <div class="card-body p-3 text-center">
                <div class="small text-muted text-uppercase mb-1">5-Star Ratings</div>
                <div class="h4 fw-bold mb-0 text-success">{{ $stats['ratingDistribution'][5] ?? 0 }}</div>
              </div>
            </div>
          </div>
        </div>

        {{-- FILTERS --}}
        <div class="card border-0 shadow-soft mb-4">
          <div class="card-body p-3">
            <form method="GET" action="{{ route('staff.feedbacks') }}" class="row g-2 align-items-end">
              <div class="col-md-3">
                <label class="form-label small">Rating</label>
                <select name="rating" class="form-select form-select-sm">
                  <option value="">All ratings</option>
                  <option value="5" {{ ($filters['rating'] ?? '') === '5' ? 'selected' : '' }}>5 Stars</option>
                  <option value="4" {{ ($filters['rating'] ?? '') === '4' ? 'selected' : '' }}>4 Stars</option>
                  <option value="3" {{ ($filters['rating'] ?? '') === '3' ? 'selected' : '' }}>3 Stars</option>
                  <option value="2" {{ ($filters['rating'] ?? '') === '2' ? 'selected' : '' }}>2 Stars</option>
                  <option value="1" {{ ($filters['rating'] ?? '') === '1' ? 'selected' : '' }}>1 Star</option>
                </select>
              </div>
              <div class="col-md-3">
                <label class="form-label small">Reported Issues</label>
                <select name="reported_issue" class="form-select form-select-sm">
                  <option value="">All feedback</option>
                  <option value="1" {{ ($filters['reported_issue'] ?? '') === '1' ? 'selected' : '' }}>With Issues</option>
                  <option value="0" {{ ($filters['reported_issue'] ?? '') === '0' ? 'selected' : '' }}>No Issues</option>
                </select>
              </div>
              <div class="col-md-4">
                <label class="form-label small">Search</label>
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Customer name, email, or comment..." value="{{ $filters['search'] ?? '' }}">
              </div>
              <div class="col-md-2">
                <button type="submit" class="btn btn-sm btn-hasta w-100">Filter</button>
              </div>
            </form>
          </div>
        </div>

        {{-- FEEDBACK LIST --}}
        <div class="card border-0 shadow-soft">
          <div class="card-body p-0">
            <div class="table-responsive">
              <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                  <tr>
                    <th style="padding: 16px;">Customer</th>
                    <th>Booking</th>
                    <th>Car</th>
                    <th>Rating</th>
                    <th>Comment</th>
                    <th>Issue</th>
                    <th>Date</th>
                  </tr>
                </thead>
                <tbody>
                  @forelse($feedbacks as $feedback)
                    <tr>
                      <td>
                        <div class="fw-semibold">{{ $feedback->customer->full_name ?? 'N/A' }}</div>
                        <small class="text-muted">{{ $feedback->customer->email ?? '' }}</small>
                      </td>
                      <td>
                        <a href="{{ route('staff.bookings.show', $feedback->booking_id) }}" class="text-decoration-none">
                          #{{ $feedback->booking_id }}
                        </a>
                      </td>
                      <td>
                        @if($feedback->booking && $feedback->booking->car)
                          <div>{{ $feedback->booking->car->brand }} {{ $feedback->booking->car->model }}</div>
                          <small class="text-muted">{{ $feedback->booking->car->plate_number }}</small>
                        @else
                          <span class="text-muted">N/A</span>
                        @endif
                      </td>
                      <td>
                        <div class="d-flex align-items-center gap-1">
                          @for($i = 1; $i <= 5; $i++)
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="{{ $i <= $feedback->rating ? '#FFD700' : '#ddd' }}" stroke="currentColor" stroke-width="2">
                              <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                            </svg>
                          @endfor
                          <span class="ms-1 small text-muted">({{ $feedback->rating }})</span>
                        </div>
                      </td>
                      <td>
                        @if($feedback->comment)
                          <div class="text-truncate" style="max-width: 200px;" title="{{ $feedback->comment }}">
                            {{ \Illuminate\Support\Str::limit($feedback->comment, 60) }}
                          </div>
                        @else
                          <span class="text-muted">No comment</span>
                        @endif
                      </td>
                      <td>
                        @if($feedback->reported_issue)
                          <span class="badge bg-warning text-dark">Issue Reported</span>
                        @else
                          <span class="text-muted">-</span>
                        @endif
                      </td>
                      <td>
                        <div>{{ $feedback->created_at->format('d M Y') }}</div>
                        <small class="text-muted">{{ $feedback->created_at->format('H:i') }}</small>
                      </td>
                    </tr>
                  @empty
                    <tr>
                      <td colspan="7" class="text-center py-4 text-muted">No feedback found.</td>
                    </tr>
                  @endforelse
                </tbody>
              </table>
            </div>
          </div>
        </div>

        {{-- PAGINATION --}}
        <div class="mt-4">
          {{ $feedbacks->links() }}
        </div>
      @endif
    </div>
  </div>
</div>
@endsection
