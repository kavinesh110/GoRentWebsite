<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RentalPhoto extends Model
{
    protected $primaryKey = 'photo_id';

    protected $fillable = [
        'booking_id',
        'uploaded_by_user_id',
        'uploaded_by_role',
        'photo_type',
        'photo_url',
        'taken_at',
    ];

    protected $casts = [
        'taken_at' => 'datetime',
    ];

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class, 'booking_id', 'booking_id');
    }
}
