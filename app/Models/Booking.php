<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Booking extends Model
{
    protected $primaryKey = 'booking_id';

    protected $fillable = [
        'customer_id',
        'car_id',
        'pickup_location_id',
        'dropoff_location_id',
        'start_datetime',
        'end_datetime',
        'rental_hours',
        'base_price',
        'promo_discount',
        'voucher_discount',
        'total_rental_amount',
        'deposit_amount',
        'deposit_used_amount',
        'deposit_refund_amount',
        'deposit_decision',
        'agreement_signed_at',
        'final_amount',
        'status',
    ];

    protected $casts = [
        'start_datetime' => 'datetime',
        'end_datetime' => 'datetime',
        'agreement_signed_at' => 'datetime',
        'rental_hours' => 'integer',
        'base_price' => 'decimal:2',
        'promo_discount' => 'decimal:2',
        'voucher_discount' => 'decimal:2',
        'total_rental_amount' => 'decimal:2',
        'deposit_amount' => 'decimal:2',
        'deposit_used_amount' => 'decimal:2',
        'deposit_refund_amount' => 'decimal:2',
        'final_amount' => 'decimal:2',
    ];

    public function car(): BelongsTo
    {
        return $this->belongsTo(Car::class, 'car_id');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function pickupLocation(): BelongsTo
    {
        return $this->belongsTo(CarLocation::class, 'pickup_location_id', 'location_id');
    }

    public function dropoffLocation(): BelongsTo
    {
        return $this->belongsTo(CarLocation::class, 'dropoff_location_id', 'location_id');
    }
}
