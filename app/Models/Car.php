<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

/**
 * Car Model
 * Represents a vehicle in the Hasta Travels & Tours fleet
 * Handles car information, status, pricing, and maintenance tracking
 */
class Car extends Model
{
    /**
     * The attributes that are mass assignable
     */
    protected $fillable = [
        'plate_number',
        'brand',
        'model',
        'car_type',
        'fuel_type',
        'year',
        'base_rate_per_hour',
        'status',
        'initial_mileage',
        'current_mileage',
        'service_mileage_limit',
        'last_service_date',
        'image_url',
        'gps_enabled',
    ];

    /**
     * The attributes that should be cast to native types
     */
    protected $casts = [
        'year' => 'integer',
        'base_rate_per_hour' => 'decimal:2', // Pricing per hour in RM
        'initial_mileage' => 'integer',
        'current_mileage' => 'integer',
        'service_mileage_limit' => 'integer',
        'last_service_date' => 'date',
        'gps_enabled' => 'boolean',
    ];

    /**
     * Accessor: Get the full car name (brand + model)
     * @return string
     */
    public function getNameAttribute()
    {
        return trim(($this->brand ?? '') . ' ' . ($this->model ?? ''));
    }

    /**
     * Accessor: Get the full URL for the car image
     * Handles both uploaded files (via Storage) and external URLs (for backward compatibility)
     * 
     * @param string|null $value The stored image path or URL
     * @return string|null Full URL to the image
     */
    public function getImageUrlAttribute($value)
    {
        if (!$value) {
            return null;
        }

        // If it's already a full URL (for backward compatibility with old data), return as-is
        if (filter_var($value, FILTER_VALIDATE_URL)) {
            return $value;
        }

        // Otherwise, use Storage::url for uploaded files
        return Storage::url($value);
    }

    /**
     * Get all bookings for this car
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function bookings(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Booking::class, 'car_id');
    }

    /**
     * Get all feedbacks/reviews for this car
     * Uses hasManyThrough relationship: Car -> Bookings -> Feedbacks
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function feedbacks(): \Illuminate\Database\Eloquent\Relations\HasManyThrough
    {
        return $this->hasManyThrough(
            Feedback::class,
            Booking::class,
            'car_id',        // Foreign key on bookings table
            'booking_id',    // Foreign key on feedbacks table
            'id',            // Local key on cars table
            'booking_id'     // Local key on bookings table
        );
    }
}
