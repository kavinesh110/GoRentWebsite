<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

/**
 * Customer Model
 * Represents a UTM student or staff member who can rent cars
 * Tracks verification status, blacklist status, deposits, and loyalty points
 */
class Customer extends Model
{
    protected $primaryKey = 'customer_id';

    /**
     * The attributes that are mass assignable
     */
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
        'college_name',
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

    /**
     * The attributes that should be cast to native types
     */
    protected $casts = [
        'is_blacklisted' => 'boolean',
        'verified_at' => 'datetime',
        'blacklist_since' => 'datetime',
        'deposit_balance' => 'decimal:2', // Customer's deposit account balance in RM
        'total_rental_hours' => 'integer', // Total hours rented (for loyalty tracking)
        'total_stamps' => 'integer', // Loyalty stamps earned (1 stamp per 9 rental hours)
    ];

    /**
     * Accessor: return full URL for IC / passport document
     */
    public function getIcUrlAttribute($value)
    {
        if (!$value) {
            return null;
        }
        if (filter_var($value, FILTER_VALIDATE_URL)) {
            return $value;
        }
        return Storage::url($value);
    }

    /**
     * Accessor: return full URL for UTM ID document
     */
    public function getUtmidUrlAttribute($value)
    {
        if (!$value) {
            return null;
        }
        if (filter_var($value, FILTER_VALIDATE_URL)) {
            return $value;
        }
        return Storage::url($value);
    }

    /**
     * Accessor: return full URL for driving licence document
     */
    public function getLicenseUrlAttribute($value)
    {
        if (!$value) {
            return null;
        }
        if (filter_var($value, FILTER_VALIDATE_URL)) {
            return $value;
        }
        return Storage::url($value);
    }

    /**
     * Get all bookings made by this customer
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'customer_id', 'customer_id');
    }

    /**
     * Check if customer profile is complete (required for booking)
     * Profile is complete when all required fields are filled:
     * - full_name
     * - phone
     * - utm_role
     * - ic_url (document uploaded)
     * - utmid_url (document uploaded)
     * - license_url (document uploaded)
     * 
     * @return bool
     */
    public function isProfileComplete(): bool
    {
        // Use getRawOriginal to check actual database values, not accessor URLs
        return !empty($this->full_name) &&
               !empty($this->phone) &&
               !empty($this->utm_role) &&
               !empty($this->getRawOriginal('ic_url')) &&
               !empty($this->getRawOriginal('utmid_url')) &&
               !empty($this->getRawOriginal('license_url'));
    }

    /**
     * Get list of missing profile fields
     * @return array
     */
    public function getMissingProfileFields(): array
    {
        $missing = [];
        if (empty($this->full_name)) $missing[] = 'Full Name';
        if (empty($this->phone)) $missing[] = 'Phone Number';
        if (empty($this->utm_role)) $missing[] = 'UTM Role';
        if (empty($this->getRawOriginal('ic_url'))) $missing[] = 'IC/Passport Document';
        if (empty($this->getRawOriginal('utmid_url'))) $missing[] = 'UTM ID Document';
        if (empty($this->getRawOriginal('license_url'))) $missing[] = 'Driving License Document';
        return $missing;
    }
}
