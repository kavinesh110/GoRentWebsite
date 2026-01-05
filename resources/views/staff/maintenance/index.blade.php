@extends('layouts.app')
@section('title', 'Maintenance Records - Staff Dashboard')

@section('content')
<div class="d-flex" style="min-height: calc(100vh - 70px);">
  @include('staff._nav')
  <div class="flex-fill" style="background: #f8fafc;">
    <div class="container-fluid px-4 px-md-5 py-4">
      
      {{-- Page Header --}}
      <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
          <a href="{{ route('staff.cars') }}" class="text-decoration-none small fw-bold text-hasta"><i class="bi bi-arrow-left me-1"></i>Back to Fleet</a>
          <h1 class="h4 fw-bold mt-2 mb-1" style="color:#1e293b; letter-spacing: -0.5px;">Maintenance History</h1>
          <p class="text-muted mb-0 small">Service logs for <strong>{{ $car->brand }} {{ $car->model }}</strong> ({{ strtoupper($car->plate_number) }})</p>
        </div>
        <div class="d-flex gap-2">
          @if($car->status === 'maintenance')
          <form method="POST" action="{{ route('staff.cars.setAvailable', $car->id) }}" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-success shadow-sm px-4 rounded-3 fw-bold">
              <i class="bi bi-check-circle me-2"></i>Mark as Available
            </button>
          </form>
          @endif
          <a href="{{ route('staff.maintenance.create', $car->id) }}" class="btn btn-hasta shadow-sm px-4 rounded-3 fw-bold">
            <i class="bi bi-plus-lg me-2"></i>Add Service Record
          </a>
        </div>
      </div>

      @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm d-flex align-items-center gap-2 mb-4 rounded-3">
          <i class="bi bi-check-circle-fill"></i>
          <div class="small fw-semibold">{{ session('success') }}</div>
          <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
        </div>
      @endif

      {{-- CAR STATS SUMMARY --}}
      <div class="row g-4 mb-4">
        <div class="col-md-3">
          <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
            <div class="card-body p-3">
              <div class="text-muted fw-bold text-uppercase x-small mb-1">Current Mileage</div>
              <div class="h5 fw-bold mb-0 text-slate-800">{{ number_format($car->current_mileage) }} <span class="small text-muted fw-normal">km</span></div>
            </div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
            <div class="card-body p-3">
              <div class="text-muted fw-bold text-uppercase x-small mb-1">Service Interval</div>
              <div class="h5 fw-bold mb-0 text-slate-800">{{ number_format($car->service_mileage_limit) }} <span class="small text-muted fw-normal">km</span></div>
            </div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
            <div class="card-body p-3">
              <div class="text-muted fw-bold text-uppercase x-small mb-1">Last Service</div>
              <div class="h5 fw-bold mb-0 text-slate-800">
                {{ $car->last_service_date ? $car->last_service_date->format('d M Y') : 'No History' }}
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="card border-0 shadow-sm rounded-4 h-100 bg-white border-start border-4 border-primary">
            <div class="card-body p-3">
              <div class="text-muted fw-bold text-uppercase x-small mb-1">Fleet Status</div>
              <div class="d-flex align-items-center gap-2">
                @php
                  $sClass = match($car->status) {
                    'available' => 'bg-success',
                    'maintenance' => 'bg-danger',
                    default => 'bg-warning text-dark'
                  };
                @endphp
                <span class="badge rounded-pill {{ $sClass }} fw-bold x-small text-uppercase px-3 py-2">{{ $car->status }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      {{-- MAINTENANCE LOG TABLE --}}
      <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
        <div class="card-header bg-white border-bottom py-3 px-4 d-flex justify-content-between align-items-center">
          <h6 class="fw-bold mb-0">Service Logs</h6>
          <span class="badge bg-light text-slate-700 border border-slate-200 small fw-bold px-3">{{ $records->total() }} Records</span>
        </div>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
              <thead class="bg-light-subtle">
                <tr>
                  <th class="ps-4 py-3 text-uppercase text-muted fw-bold" style="font-size: 10px; letter-spacing: 0.5px;">Service Date</th>
                  <th class="py-3 text-uppercase text-muted fw-bold" style="font-size: 10px; letter-spacing: 0.5px;">Mileage</th>
                  <th class="py-3 text-uppercase text-muted fw-bold" style="font-size: 10px; letter-spacing: 0.5px;">Service Description</th>
                  <th class="py-3 text-uppercase text-muted fw-bold" style="font-size: 10px; letter-spacing: 0.5px;">Expenditure</th>
                  <th class="pe-4 py-3 text-uppercase text-muted fw-bold text-end" style="font-size: 10px; letter-spacing: 0.5px;">Operations</th>
                </tr>
              </thead>
              <tbody class="border-top-0">
                @forelse($records as $record)
                  <tr>
                    <td class="ps-4">
                      <div class="fw-bold text-slate-800 mb-0">{{ $record->service_date->format('d M Y') }}</div>
                      <div class="text-muted x-small">{{ $record->service_date->diffForHumans() }}</div>
                    </td>
                    <td>
                      <span class="badge bg-light text-slate-700 border border-slate-200 fw-bold px-2 py-1" style="font-size: 11px;">
                        {{ number_format($record->mileage_at_service) }} km
                      </span>
                    </td>
                    <td>
                      <div class="small text-slate-700 fw-medium line-clamp-2" style="max-width: 400px;">
                        {{ $record->description ?: 'Routine maintenance checkup.' }}
                      </div>
                    </td>
                    <td>
                      @if($record->cost > 0)
                        <div class="fw-bold text-slate-800">RM{{ number_format($record->cost, 2) }}</div>
                      @else
                        <span class="text-muted small">---</span>
                      @endif
                    </td>
                    <td class="pe-4 text-end">
                      <div class="d-flex gap-2 justify-content-end">
                        <a href="{{ route('staff.maintenance.edit', [$car->id, $record->maintenance_id]) }}" class="btn btn-sm btn-white border rounded-pill px-3 fw-bold x-small shadow-sm">
                          <i class="bi bi-pencil-square me-1"></i>Edit
                        </a>
                        <form method="POST" action="{{ route('staff.maintenance.destroy', [$car->id, $record->maintenance_id]) }}" class="d-inline" onsubmit="return confirm('Are you sure you want to permanently delete this service record?');">
                          @csrf
                          @method('DELETE')
                          <button type="submit" class="btn btn-sm btn-outline-danger border rounded-pill px-2 fw-bold x-small shadow-sm">
                            <i class="bi bi-trash3"></i>
                          </button>
                        </form>
                      </div>
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="5" class="text-center py-5">
                      <div class="py-4">
                        <i class="bi bi-wrench-adjustable display-4 text-muted opacity-25 d-block mb-3"></i>
                        <h6 class="text-slate-700 fw-bold">No maintenance history</h6>
                        <p class="text-muted small">This vehicle doesn't have any registered service records yet.</p>
                        <a href="{{ route('staff.maintenance.create', $car->id) }}" class="btn btn-sm btn-hasta rounded-pill px-4 mt-2 shadow-sm">Register First Record</a>
                      </div>
                    </td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>

      {{-- PAGINATION --}}
      @if($records->hasPages())
        <div class="mt-4 d-flex justify-content-center">
          {{ $records->links() }}
        </div>
      @endif
    </div>
  </div>
</div>

<style>
  .text-slate-700 { color: #334155; }
  .text-slate-800 { color: #1e293b; }
  .x-small { font-size: 11px; }
  .bg-light-subtle { background-color: #f8fafc !important; }
  
  .table > :not(caption) > * > * {
    padding: 1.25rem 0.75rem;
    border-bottom: 1px solid #f1f5f9;
  }
  
  .btn-white {
    background: #fff;
    color: #1e293b;
    border-color: #e2e8f0;
    transition: all 0.2s;
  }
  .btn-white:hover {
    background: #f8fafc;
    border-color: #cbd5e1;
    color: var(--hasta);
  }

  .line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
  }

  /* Custom Pagination Styles */
  .pagination { 
    margin-bottom: 0; 
    gap: 5px;
  }
  .page-item .page-link { 
    border-radius: 10px !important; 
    padding: 8px 16px;
    color: #475569;
    border: 1px solid #e2e8f0;
    font-weight: 600;
    font-size: 13px;
    transition: all 0.2s;
    box-shadow: 0 2px 4px rgba(0,0,0,0.02);
  }
  .page-item:first-child .page-link, .page-item:last-child .page-link {
    border-radius: 10px !important;
  }
  .page-item.active .page-link { 
    background-color: var(--hasta); 
    border-color: var(--hasta); 
    color: #fff; 
    box-shadow: 0 4px 6px -1px rgba(203, 55, 55, 0.2);
  }
  .page-item .page-link:hover:not(.active) {
    background-color: #f8fafc;
    border-color: #cbd5e1;
    color: var(--hasta);
    transform: translateY(-1px);
  }
  .page-item.disabled .page-link {
    background-color: #f1f5f9;
    color: #94a3b8;
    border-color: #e2e8f0;
  }
</style>
@endsection