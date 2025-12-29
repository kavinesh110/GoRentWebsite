@extends('layouts.app')
@section('title', 'Vouchers - Staff Dashboard')

@section('content')
<div class="d-flex" style="min-height: calc(100vh - 60px);">
  @include('staff._nav')
  <div class="flex-fill" style="background: var(--bg);">
    <div class="container-fluid px-4 px-md-5" style="padding-top: 32px; padding-bottom: 48px;">
      <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
        <div>
          <h1 class="h3 fw-bold mb-1" style="color:#333;">Voucher Management</h1>
          <p class="text-muted mb-0" style="font-size: 14px;">Create and manage loyalty reward vouchers for customers.</p>
        </div>
        <a href="{{ route('staff.vouchers.create') }}" class="btn btn-hasta">+ New Voucher</a>
      </div>

      @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
          {{ session('success') }}
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
      @endif

      {{-- FILTERS --}}
      <div class="card border-0 shadow-soft mb-4">
        <div class="card-body p-3">
          <form method="GET" action="{{ route('staff.vouchers') }}" class="row g-2 align-items-end">
            <div class="col-md-4">
              <label class="form-label small">Status</label>
              <select name="status" class="form-select form-select-sm">
                <option value="">All vouchers</option>
                <option value="active" {{ ($filters['status'] ?? '') === 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ ($filters['status'] ?? '') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                <option value="expired" {{ ($filters['status'] ?? '') === 'expired' ? 'selected' : '' }}>Expired</option>
              </select>
            </div>
            <div class="col-md-4">
              <label class="form-label small">Search</label>
              <input type="text" name="search" class="form-control form-control-sm" placeholder="Code or description..." value="{{ $filters['search'] ?? '' }}">
            </div>
            <div class="col-md-2">
              <button type="submit" class="btn btn-sm btn-hasta w-100">Filter</button>
            </div>
            <div class="col-md-2">
              <a href="{{ route('staff.vouchers') }}" class="btn btn-sm btn-outline-secondary w-100">Clear</a>
            </div>
          </form>
        </div>
      </div>

      {{-- VOUCHERS TABLE --}}
      <div class="card border-0 shadow-soft">
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
              <thead class="table-light">
                <tr>
                  <th style="padding: 16px;">Code</th>
                  <th>Description</th>
                  <th>Discount</th>
                  <th>Min Stamps</th>
                  <th>Expiry Date</th>
                  <th>Status</th>
                  <th>Redemptions</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                @forelse($vouchers as $voucher)
                  <tr>
                    <td><strong>{{ $voucher->code }}</strong></td>
                    <td>{{ \Illuminate\Support\Str::limit($voucher->description ?? 'No description', 50) }}</td>
                    <td>
                      @if($voucher->discount_type === 'percent')
                        {{ number_format($voucher->discount_value, 0) }}%
                      @else
                        RM {{ number_format($voucher->discount_value, 2) }}
                      @endif
                    </td>
                    <td>{{ $voucher->min_stamps_required }}</td>
                    <td>{{ $voucher->expiry_date->format('d M Y') }}</td>
                    <td>
                      @if($voucher->expiry_date < now())
                        <span class="badge bg-danger">Expired</span>
                      @elseif($voucher->is_active)
                        <span class="badge bg-success">Active</span>
                      @else
                        <span class="badge bg-secondary">Inactive</span>
                      @endif
                    </td>
                    <td>{{ $voucher->redemptions->count() }}</td>
                    <td>
                      <div class="d-flex gap-1">
                        <a href="{{ route('staff.vouchers.edit', $voucher->voucher_id) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                        <form method="POST" action="{{ route('staff.vouchers.destroy', $voucher->voucher_id) }}" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this voucher?');">
                          @csrf
                          @method('DELETE')
                          <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                        </form>
                      </div>
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="8" class="text-center py-4 text-muted">No vouchers found. <a href="{{ route('staff.vouchers.create') }}">Create your first voucher</a></td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>

      {{-- PAGINATION --}}
      <div class="mt-4">
        {{ $vouchers->links() }}
      </div>
    </div>
  </div>
</div>
@endsection
