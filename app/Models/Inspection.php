<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Inspection Model
 * Represents vehicle inspections performed during booking pickup/return
 * Tracks fuel level, odometer reading, condition notes
 * Records which staff member performed the inspection
 */
class Inspection extends Model
{
    protected $primaryKey = 'inspection_id';

    /**
     * The attributes that are mass assignable
     */
    protected $fillable = [
        'booking_id',
        'inspection_type',
        'datetime',
        'fuel_level',
        'odometer_reading',
        'notes',
        'inspected_by',
    ];

    /**
     * The attributes that should be cast to native types
     */
    protected $casts = [
        'datetime' => 'datetime', // When the inspection was performed
        'fuel_level' => 'integer', // Fuel level (0-8, where 0=empty, 8=full)
        'odometer_reading' => 'integer', // Car mileage at time of inspection
    ];

    /**
     * Get the booking this inspection is for
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class, 'booking_id', 'booking_id');
    }

    /**
     * Get the staff member who performed this inspection
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function staff(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'inspected_by', 'staff_id');
    }
}
