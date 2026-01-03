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

    /**
     * Get all payments for this booking
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'booking_id', 'booking_id');
    }

    /**
     * Get the cancellation request for this booking (if any)
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function cancellationRequest(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(CancellationRequest::class, 'booking_id', 'booking_id');
    }

    /**
     * Check if booking has a pending or approved cancellation request
     * @return bool
     */
    public function hasCancellationRequest(): bool
    {
        return $this->cancellationRequest()
            ->whereIn('status', ['pending', 'approved', 'refunded'])
            ->exists();
    }

    /**
     * Determine the current booking phase (1-4)
     * Phase 1: Payment - User pays deposit + rental
     * Phase 2: Verification - Hasta verifies booking
     * Phase 3: Pickup - User signs agreement, uploads pickup photos
     * Phase 4: Return - User returns car, uploads return photos
     * 
     * @return int Current phase number (1-4)
     */
    public function getCurrentPhase(): int
    {
        // Phase 4: Completed - rental finished
        if ($this->status === 'completed') {
            return 4;
        }

        // Phase 3: Active rental - user has picked up the car
        if ($this->status === 'active') {
            return 3;
        }

        // Phase 2: Confirmed - waiting for pickup / agreement signing
        if ($this->status === 'confirmed') {
            return 2;
        }

        // Phase 1: Created - pending payment or verification
        return 1;
    }

    /**
     * Check if Phase 1 (Payment) is complete
     * Phase 1 is complete when customer has uploaded a deposit payment receipt
     * (doesn't need to be verified yet - that's Phase 2)
     * @return bool
     */
    public function isPhase1Complete(): bool
    {
        // Phase 1 is complete when any deposit payment has been uploaded (regardless of status)
        $hasPayment = $this->payments()
            ->where('payment_type', 'deposit')
            ->exists();
        
        return $hasPayment;
    }

    /**
     * Check if payment has been verified by staff
     * @return bool
     */
    public function isPaymentVerified(): bool
    {
        return $this->payments()
            ->where('payment_type', 'deposit')
            ->where('status', 'verified')
            ->exists();
    }

    /**
     * Check if Phase 2 (Verification) is complete
     * @return bool
     */
    public function isPhase2Complete(): bool
    {
        // Phase 2 is complete when booking is confirmed or beyond
        return in_array($this->status, ['confirmed', 'active', 'completed']);
    }

    /**
     * Check if Phase 3 (Pickup) is complete
     * @return bool
     */
    public function isPhase3Complete(): bool
    {
        // Phase 3 is complete when agreement is signed AND pickup photos exist
        $hasAgreement = $this->agreement_signed_at !== null;
        $hasPickupPhotos = $this->rentalPhotos()
            ->whereIn('photo_type', ['before', 'pickup', 'agreement'])
            ->exists();
        
        return $hasAgreement && $hasPickupPhotos && in_array($this->status, ['active', 'completed']);
    }

    /**
     * Check if Phase 4 (Return) is complete
     * @return bool
     */
    public function isPhase4Complete(): bool
    {
        // Phase 4 is complete when booking is completed with return photos
        if ($this->status !== 'completed') {
            return false;
        }

        $hasReturnPhotos = $this->rentalPhotos()
            ->whereIn('photo_type', ['after', 'key', 'parking'])
            ->exists();
        
        return $hasReturnPhotos;
    }

    /**
     * Get pickup photos for this booking
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getPickupPhotos()
    {
        return $this->rentalPhotos()
            ->whereIn('photo_type', ['before', 'pickup', 'agreement'])
            ->get();
    }

    /**
     * Get return photos for this booking
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getReturnPhotos()
    {
        return $this->rentalPhotos()
            ->whereIn('photo_type', ['after', 'key', 'parking'])
            ->get();
    }
}
