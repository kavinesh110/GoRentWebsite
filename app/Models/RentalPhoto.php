<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

/**
 * RentalPhoto Model
 * Represents photos uploaded during car rentals
 * Can be uploaded by customers or staff
 * Used to document car condition before/after rental, damage, etc.
 */
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

    /**
     * Accessor: Get the full URL for the rental photo
     * Handles both uploaded files (via Storage) and external URLs
     * 
     * @param string|null $value The stored photo path or URL
     * @return string|null Full URL to the photo
     */
    public function getPhotoUrlAttribute($value)
    {
        if (!$value) {
            return null;
        }

        // If it's already a full URL, return as-is
        if (filter_var($value, FILTER_VALIDATE_URL)) {
            return $value;
        }

        // Otherwise, use Storage::url for uploaded files
        return Storage::url($value);
    }

    /**
     * Get the booking this photo belongs to
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class, 'booking_id', 'booking_id');
    }
}
