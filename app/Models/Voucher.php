<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Voucher Model
 * Represents loyalty reward vouchers that customers can redeem
 * Vouchers require a minimum number of stamps and have expiry dates
 */
class Voucher extends Model
{
    protected $primaryKey = 'voucher_id';

    /**
     * The attributes that are mass assignable
     */
    protected $fillable = [
        'code',
        'description',
        'discount_type',
        'discount_value',
        'min_stamps_required',
        'expiry_date',
        'is_active',
    ];

    /**
     * The attributes that should be cast to native types
     */
    protected $casts = [
        'discount_value' => 'decimal:2', // Discount amount in RM (for fixed) or percentage (for percent)
        'min_stamps_required' => 'integer', // Minimum loyalty stamps needed to redeem this voucher
        'expiry_date' => 'date', // When the voucher expires
        'is_active' => 'boolean', // Whether the voucher is currently active
    ];

    /**
     * Get all redemptions of this voucher
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function redemptions(): HasMany
    {
        return $this->hasMany(VoucherRedemption::class, 'voucher_id', 'voucher_id');
    }
}
