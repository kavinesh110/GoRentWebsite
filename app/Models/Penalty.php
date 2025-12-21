<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Penalty Model
 * Represents penalties charged to customers for booking issues
 * Types: late return, fuel issues, damage, accidents, etc.
 * Supports installment payments (penalty can be paid in parts)
 */
class Penalty extends Model
{
    protected $primaryKey = 'penalty_id';

    /**
     * The attributes that are mass assignable
     */
    protected $fillable = [
        'booking_id',
        'penalty_type',
        'description',
        'amount',
        'is_installment',
        'status',
    ];

    /**
     * The attributes that should be cast to native types
     */
    protected $casts = [
        'amount' => 'decimal:2', // Penalty amount in RM
        'is_installment' => 'boolean', // Whether penalty can be paid in installments
    ];

    /**
     * Get the booking this penalty is associated with
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class, 'booking_id', 'booking_id');
    }
}
