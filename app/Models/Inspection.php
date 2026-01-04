<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Inspection Model
 * Represents vehicle inspections before pickup and after return
 */
class Inspection extends Model
{
    protected $primaryKey = 'inspection_id';

    protected $fillable = [
        'booking_id',
        'car_id',
        'type',
        'status',
        'datetime',
        'inspection_type',
        'exterior_condition',
        'interior_condition',
        'engine_condition',
        'fuel_level',
        'odometer_reading',
        'mileage_reading',
        'damages_found',
        'notes',
        'photos',
        'inspected_by',
        'inspected_at',
    ];

    protected $casts = [
        'photos' => 'array',
        'datetime' => 'datetime',
        'inspected_at' => 'datetime',
        'fuel_level' => 'integer',
        'mileage_reading' => 'integer',
    ];

    /**
     * Get the booking associated with this inspection
     */
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class, 'booking_id', 'booking_id');
    }

    /**
     * Get the car being inspected
     */
    public function car(): BelongsTo
    {
        return $this->belongsTo(Car::class, 'car_id', 'id');
    }

    /**
     * Get the staff member who performed the inspection
     */
    public function inspector(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'inspected_by', 'staff_id');
    }

    /**
     * Check if inspection is pending
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if inspection is completed
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Get type label
     */
    public function getTypeLabelAttribute(): string
    {
        return $this->type === 'before' ? 'Before Pickup' : 'After Return';
    }

    /**
     * Get status badge class
     */
    public function getStatusBadgeClassAttribute(): string
    {
        return match($this->status) {
            'pending' => 'bg-warning text-dark',
            'in_progress' => 'bg-info text-white',
            'completed' => 'bg-success text-white',
            default => 'bg-secondary text-white',
        };
    }
}
