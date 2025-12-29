@extends('layouts.app')
@section('title', 'Maintenance Records - Staff Dashboard')

@section('content')
<div class="d-flex" style="min-height: calc(100vh - 60px);">
  @include('staff._nav')
  <div class="flex-fill" style="background: var(--bg);">
    <div class="container-fluid px-4 px-md-5" style="padding-top: 32px; padding-bottom: 48px;">
      <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
        <div>
          <a href="{{ route('staff.cars') }}" class="text-decoration-none small">&larr; Back to cars</a>
          <h1 class="h3 fw-bold mt-2 mb-1" style="color:#333;">Maintenance Records</h1>
          <p class="text-muted mb-0" style="font-size: 14px;">Service history for {{ $car->brand }} {{ $car->model }} ({{ $car->plate_number }})</p>
        </div>
        <a href="{{ route('staff.maintenance.create', $car->id) }}" class="btn btn-hasta">+ New Record</a>
      </div>

      @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
          {{ session('success') }}
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
      @endif

      {{-- CAR INFO CARD --}}
      <div class="card border-0 shadow-soft mb-4">
        <div class="card-body p-3">
          <div class="row g-3">
            <div class="col-md-3">
              <div class="small text-muted">Current Mileage</div>
              <div class="fw-bold">{{ number_format($car->current_mileage) }} km</div>
            </div>
            <div class="col-md-3">
              <div class="small text-muted">Service Limit</div>
              <div class="fw-bold">{{ number_format($car->service_mileage_limit) }} km</div>
            </div>
            <div class="col-md-3">
              <div class="small text-muted">Last Service</div>
              <div class="fw-bold">{{ $car->last_service_date ? $car->last_service_date->format('d M Y') : 'Never' }}</div>
            </div>
            <div class="col-md-3">
              <div class="small text-muted">Status</div>
              <div>
                <span class="badge {{ $car->status === 'available' ? 'bg-success' : ($car->status === 'in_use' ? 'bg-warning' : 'bg-danger') }}">
                  {{ ucfirst($car->status) }}
                </span>
              </div>
            </div>
          </div>
        </div>
      </div>

      {{-- MAINTENANCE RECORDS TABLE --}}
      <div class="card border-0 shadow-soft">
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
              <thead class="table-light">
                <tr>
                  <th style="padding: 16px;">Service Date</th>
                  <th>Mileage</th>
                  <th>Description</th>
                  <th>Cost (RM)</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                @forelse($records as $record)
                  <tr>
                    <td><strong>{{ $record->service_date->format('d M Y') }}</strong></td>
                    <td>{{ number_format($record->mileage_at_service) }} km</td>
                    <td>{{ \Illuminate\Support\Str::limit($record->description ?? 'No description', 60) }}</td>
                    <td>{{ $record->cost ? 'RM ' . number_format($record->cost, 2) : '-' }}</td>
                    <td>
                      <div class="d-flex gap-1">
                        <a href="{{ route('staff.maintenance.edit', [$car->id, $record->maintenance_id]) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                        <form method="POST" action="{{ route('staff.maintenance.destroy', [$car->id, $record->maintenance_id]) }}" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this maintenance record?');">
                          @csrf
                          @method('DELETE')
                          <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                        </form>
                      </div>
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="5" class="text-center py-4 text-muted">No maintenance records found. <a href="{{ route('staff.maintenance.create', $car->id) }}">Add the first record</a></td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>

      {{-- PAGINATION --}}
      <div class="mt-4">
        {{ $records->links() }}
      </div>
    </div>
  </div>
</div>
@endsection
