<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $primaryKey = 'customer_id';

    protected $fillable = [
        'full_name',
        'email',
        'phone',
        'password_hash',
        'ic_url',
        'utmid_url',
        'license_url',
        'utm_role',
        'college_id',
        'verification_status',
        'verified_by',
        'verified_at',
        'is_blacklisted',
        'blacklist_reason',
        'blacklist_since',
        'deposit_balance',
        'total_rental_hours',
        'total_stamps',
    ];

    protected $casts = [
        'is_blacklisted' => 'boolean',
        'verified_at' => 'datetime',
        'blacklist_since' => 'datetime',
        'deposit_balance' => 'decimal:2',
        'total_rental_hours' => 'integer',
        'total_stamps' => 'integer',
    ];
}
