<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Inspection extends Model
{
    protected $primaryKey = 'inspection_id';

    protected $fillable = [
        'booking_id',
        'inspection_type',
        'datetime',
        'fuel_level',
        'odometer_reading',
        'notes',
        'inspected_by',
    ];

    protected $casts = [
        'datetime' => 'datetime',
        'fuel_level' => 'integer',
        'odometer_reading' => 'integer',
    ];

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class, 'booking_id', 'booking_id');
    }

    public function staff(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'inspected_by', 'staff_id');
    }
}
