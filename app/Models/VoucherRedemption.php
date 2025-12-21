<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * VoucherRedemption Model
 * Represents a customer's redemption of a voucher
 * Links a voucher to a specific booking where it was used
 * Tracks when the voucher was redeemed and the discount amount applied
 */
class VoucherRedemption extends Model
{
    protected $primaryKey = 'redemption_id';

    /**
     * The attributes that are mass assignable
     */
    protected $fillable = [
        'voucher_id',
        'customer_id',
        'booking_id',
        'discount_amount',
        'redeemed_at',
    ];

    /**
     * The attributes that should be cast to native types
     */
    protected $casts = [
        'discount_amount' => 'decimal:2', // Actual discount amount applied (in RM)
        'redeemed_at' => 'datetime', // When the voucher was redeemed
    ];

    /**
     * Get the voucher that was redeemed
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function voucher(): BelongsTo
    {
        return $this->belongsTo(Voucher::class, 'voucher_id', 'voucher_id');
    }

    /**
     * Get the customer who redeemed this voucher
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'customer_id');
    }

    /**
     * Get the booking where this voucher was used
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class, 'booking_id', 'booking_id');
    }
}
