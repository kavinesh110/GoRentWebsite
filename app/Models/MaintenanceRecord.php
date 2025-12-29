<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * MaintenanceRecord Model
 * Represents service/maintenance records for vehicles
 * Tracks service dates, mileage, costs, and descriptions
 */
class MaintenanceRecord extends Model
{
    protected $primaryKey = 'maintenance_id';

    /**
     * The attributes that are mass assignable
     */
    protected $fillable = [
        'car_id',
        'service_date',
        'description',
        'mileage_at_service',
        'cost',
    ];

    /**
     * The attributes that should be cast to native types
     */
    protected $casts = [
        'service_date' => 'date',
        'mileage_at_service' => 'integer',
        'cost' => 'decimal:2',
    ];

    /**
     * Get the car this maintenance record belongs to
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function car(): BelongsTo
    {
        return $this->belongsTo(Car::class, 'car_id', 'id');
    }
}
