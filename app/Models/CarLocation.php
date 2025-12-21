<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CarLocation extends Model
{
    protected $primaryKey = 'location_id';

    protected $fillable = [
        'name',
        'type',
    ];
}
