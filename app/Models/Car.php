<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Car extends Model
{
    protected $fillable = [
        'plate_number',
        'brand',
        'model',
        'fuel_type',
        'year',
        'base_rate_per_hour',
        'status',
        'current_mileage',
        'service_mileage_limit',
        'last_service_date',
        'image_url',
        'gps_enabled',
    ];

    protected $casts = [
        'year' => 'integer',
        'base_rate_per_hour' => 'decimal:2',
        'current_mileage' => 'integer',
        'service_mileage_limit' => 'integer',
        'last_service_date' => 'date',
        'gps_enabled' => 'boolean',
    ];

    public function getNameAttribute()
    {
        return trim(($this->brand ?? '') . ' ' . ($this->model ?? ''));
    }

    /**
     * Get the full URL for the car image
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

    public function bookings(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Booking::class, 'car_id');
    }

    public function feedbacks(): \Illuminate\Database\Eloquent\Relations\HasManyThrough
    {
        return $this->hasManyThrough(
            Feedback::class,
            Booking::class,
            'car_id',
            'booking_id',
            'id',
            'booking_id'
        );
    }
}
