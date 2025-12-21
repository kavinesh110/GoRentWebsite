<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Staff Model
 * Represents Hasta Travels & Tours staff members
 * Staff can manage cars, bookings, customers, and activities
 */
class Staff extends Model
{
    protected $primaryKey = 'staff_id';

    /**
     * The attributes that are mass assignable
     */
    protected $fillable = [
        'name',
        'email',
        'password_hash',
        'role',
    ];
}
