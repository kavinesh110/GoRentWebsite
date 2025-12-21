<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
        'start_date',
        'end_date',
        'created_by',
    ];

    /**
     * The attributes that should be cast to native types
     */
    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    /**
     * Get the staff member who created this activity
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function staff(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'created_by', 'staff_id');
    }
}
