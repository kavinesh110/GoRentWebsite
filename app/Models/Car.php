<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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

    // Accessor to get full car name (brand + model)
    public function getNameAttribute()
    {
        return $this->brand . ' ' . $this->model;
    }
}