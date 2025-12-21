<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Feedback Model
 * Represents customer reviews/feedback for completed bookings
 * Stores rating (1-5 stars), comments, and issue reporting
 */
class Feedback extends Model
{
    protected $primaryKey = 'feedback_id';

    /**
     * The attributes that are mass assignable
     */
    protected $fillable = [
        'booking_id',
        'customer_id',
        'rating',
        'comment',
        'reported_issue',
    ];

    /**
     * The attributes that should be cast to native types
     */
    protected $casts = [
        'rating' => 'integer', // Star rating from 1 to 5
        'reported_issue' => 'boolean', // Flag if customer reported an issue
    ];

    /**
     * Get the booking this feedback is for
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class, 'booking_id', 'booking_id');
    }

    /**
     * Get the customer who submitted this feedback
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'customer_id');
    }
}
