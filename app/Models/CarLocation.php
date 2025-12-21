<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * CarLocation Model
 * Represents pickup and dropoff locations for car rentals
 * Type can be: 'pickup', 'dropoff', or 'both'
 */
class CarLocation extends Model
{
    protected $primaryKey = 'location_id';

    /**
     * The attributes that are mass assignable
     */
    protected $fillable = [
        'name',
        'type',
    ];
}
