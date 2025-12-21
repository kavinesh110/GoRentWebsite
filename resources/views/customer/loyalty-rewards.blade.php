@extends('layouts.app')
@section('title', 'Loyalty Rewards - Hasta GoRent')

@section('content')
<div class="container-custom" style="padding: 32px 24px 48px;">
  <div class="mb-4">
    <h1 class="h3 fw-bold mb-1" style="color:#333;">Loyalty Rewards</h1>
    <p class="text-muted mb-0" style="font-size: 14px;">Redeem vouchers and track your loyalty stamps.</p>
  </div>

  {{-- LOYALTY SUMMARY --}}
  <div class="row g-3 mb-4">
    <div class="col-md-4">
      <div class="card border-0 shadow-soft">
        <div class="card-body p-3 text-center">
          <div class="small text-muted text-uppercase mb-1">Total Stamps</div>
          <div class="h4 fw-bold mb-0 text-hasta">{{ $customer->total_stamps ?? 0 }}</div>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card border-0 shadow-soft">
        <div class="card-body p-3 text-center">
          <div class="small text-muted text-uppercase mb-1">Available Vouchers</div>
          <div class="h4 fw-bold mb-0">{{ $availableVouchers->count() }}</div>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card border-0 shadow-soft">
        <div class="card-body p-3 text-center">
          <div class="small text-muted text-uppercase mb-1">Redeemed Vouchers</div>
          <div class="h4 fw-bold mb-0">{{ $redeemedVouchers->count() }}</div>
        </div>
      </div>
    </div>
  </div>

  {{-- AVAILABLE VOUCHERS --}}
  <div class="card border-0 shadow-soft mb-4">
    <div class="card-body p-4">
      <h5 class="fw-bold mb-3">Available Vouchers</h5>
      @if($availableVouchers->count() > 0)
        <div class="row g-3">
          @foreach($availableVouchers as $voucher)
            <div class="col-md-6">
              <div class="border rounded p-3 h-100">
                <div class="d-flex justify-content-between align-items-start mb-2">
                  <div>
                    <h6 class="fw-bold mb-0">{{ $voucher->code }}</h6>
                    <small class="text-muted">{{ $voucher->description ?? 'No description' }}</small>
                  </div>
                  <span class="badge bg-warning">{{ $voucher->min_stamps_required }} stamps</span>
                </div>
                <div class="small mb-2">
                  <div class="d-flex justify-content-between">
                    <span>Discount:</span>
                    <strong class="text-success">
                      @if($voucher->discount_type === 'percent')
                        {{ number_format($voucher->discount_value) }}%
                      @else
                        RM {{ number_format($voucher->discount_value, 2) }}
                      @endif
                    </strong>
                  </div>
                  <div class="d-flex justify-content-between">
                    <span>Expires:</span>
                    <strong>{{ $voucher->expiry_date->format('d M Y') }}</strong>
                  </div>
                </div>
                @if(($customer->total_stamps ?? 0) >= $voucher->min_stamps_required)
                  <button class="btn btn-sm btn-hasta w-100 mt-2" disabled>Use in Next Booking</button>
                @else
                  <button class="btn btn-sm btn-outline-secondary w-100 mt-2" disabled>
                    Need {{ $voucher->min_stamps_required - ($customer->total_stamps ?? 0) }} more stamps
                  </button>
                @endif
              </div>
            </div>
          @endforeach
        </div>
      @else
        <p class="text-muted mb-0">No vouchers available at the moment. Keep earning stamps to unlock rewards!</p>
      @endif
    </div>
  </div>

  {{-- REDEEMED VOUCHERS --}}
  @if($redeemedVouchers->count() > 0)
  <div class="card border-0 shadow-soft">
    <div class="card-body p-4">
      <h5 class="fw-bold mb-3">Redeemed Vouchers</h5>
      <div class="table-responsive">
        <table class="table table-sm">
          <thead>
            <tr>
              <th>Voucher Code</th>
              <th>Discount</th>
              <th>Booking ID</th>
              <th>Redeemed On</th>
            </tr>
          </thead>
          <tbody>
            @foreach($redeemedVouchers as $redemption)
              <tr>
                <td>{{ $redemption->voucher->code ?? 'N/A' }}</td>
                <td class="text-success">-RM {{ number_format($redemption->discount_amount, 2) }}</td>
                <td>#{{ $redemption->booking_id }}</td>
                <td>{{ $redemption->redeemed_at->format('d M Y, H:i') }}</td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>
  @endif
</div>
@endsection
