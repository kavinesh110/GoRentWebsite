<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Car extends Model
{
    protected $fillable = [
        'name',
        'description',
        'price_per_day',
        'exterior_image',
        'interior_image',
        'features',
    ];

    protected $casts = [
        'features' => 'array',
        'price_per_day' => 'decimal:2',
    ];
}