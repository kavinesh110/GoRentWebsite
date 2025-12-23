<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

/**
 * Activity Model
 * Represents company activities, promotions, and campaigns
 * Used to display promotional content on homepage
 */
class Activity extends Model
{
    protected $primaryKey = 'activity_id';

    /**
     * The attributes that are mass assignable
     */
    protected $fillable = [
        'title',
        'description',
        'image_url',
        'start_date',
        'end_date',
        'created_by',
    ];

    /**
     * The attributes that should be cast to native types
     */
    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
    ];

    /**
     * Accessor: Get the full URL for the activity image.
     * Handles both uploaded files (via Storage) and existing absolute URLs.
     *
     * @param  string|null  $value
     * @return string|null
     */
    public function getImageUrlAttribute($value)
    {
        if (!$value) {
            return null;
        }

        // If it's already an absolute URL, return as-is
        if (filter_var($value, FILTER_VALIDATE_URL)) {
            return $value;
        }

        // Otherwise, treat it as a path in the public storage disk
        return Storage::url($value);
    }

    /**
     * Get the staff member who created this activity.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function staff(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'created_by', 'staff_id');
    }
}

