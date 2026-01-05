<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

/**
 * Payment Model
 * Represents payments made for bookings (deposits, rental fees, penalties, refunds)
 * Tracks payment method, receipt, verification status
 */
class Payment extends Model
{
    protected $primaryKey = 'payment_id';

    /**
     * The attributes that are mass assignable
     */
    protected $fillable = [
        'booking_id',
        'penalty_id',
        'amount',
        'payment_type',
        'payment_method',
        'bank_name',
        'account_holder_name',
        'account_number',
        'receipt_url',
        'payment_date',
        'status',
    ];

    /**
     * The attributes that should be cast to native types
     */
    protected $casts = [
        'amount' => 'decimal:2', // Payment amount in RM
        'payment_date' => 'datetime', // When the payment was made
    ];

    /**
     * Get the booking this payment is for
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class, 'booking_id', 'booking_id');
    }

    /**
     * Get the penalty this payment is for (if applicable)
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function penalty(): BelongsTo
    {
        return $this->belongsTo(Penalty::class, 'penalty_id', 'penalty_id');
    }

    /**
     * Accessor: Get the full URL for the payment receipt
     * Handles both uploaded files (via Storage) and external URLs
     * 
     * @param string|null $value The stored receipt path or URL
     * @return string|null Full URL to the receipt
     */
    public function getReceiptUrlAttribute($value)
    {
        if (!$value) {
            return null;
        }

        // If it's already a full URL (for backward compatibility), return as-is
        if (filter_var($value, FILTER_VALIDATE_URL)) {
            return $value;
        }

        // Otherwise, use Storage::url for uploaded files
        return Storage::url($value);
    }
}
