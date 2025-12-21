<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\CarLocation;

/**
 * Booking Model
 * Represents a car rental booking/reservation
 * Tracks booking lifecycle from creation to completion
 * Handles pricing, deposits, penalties, inspections, and feedback
 */
class Booking extends Model
{
    protected $primaryKey = 'booking_id';

    /**
     * The attributes that are mass assignable
     */
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

    /**
     * The attributes that should be cast to native types
     */
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

    /**
     * Get the car associated with this booking
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function car(): BelongsTo
    {
        return $this->belongsTo(Car::class, 'car_id');
    }

    /**
     * Get the customer who made this booking
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    /**
     * Get all penalties associated with this booking
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function penalties(): HasMany
    {
        return $this->hasMany(Penalty::class, 'booking_id', 'booking_id');
    }

    /**
     * Get all inspections (pickup/dropoff) for this booking
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function inspections(): HasMany
    {
        return $this->hasMany(Inspection::class, 'booking_id', 'booking_id');
    }

    /**
     * Get all rental photos uploaded for this booking
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function rentalPhotos(): HasMany
    {
        return $this->hasMany(RentalPhoto::class, 'booking_id', 'booking_id');
    }

    /**
     * Get the pickup location for this booking
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function pickupLocation(): BelongsTo
    {
        return $this->belongsTo(CarLocation::class, 'pickup_location_id', 'location_id');
    }

    /**
     * Get the dropoff location for this booking
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function dropoffLocation(): BelongsTo
    {
        return $this->belongsTo(CarLocation::class, 'dropoff_location_id', 'location_id');
    }

    /**
     * Get the feedback/review for this booking (one-to-one relationship)
     * Each booking can have at most one feedback
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function feedback(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Feedback::class, 'booking_id', 'booking_id');
    }
}
