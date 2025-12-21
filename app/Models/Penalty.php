<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Penalty extends Model
{
    protected $primaryKey = 'penalty_id';

    protected $fillable = [
        'booking_id',
        'penalty_type',
        'description',
        'amount',
        'is_installment',
        'status',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'is_installment' => 'boolean',
    ];

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class, 'booking_id', 'booking_id');
    }
}
