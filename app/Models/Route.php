<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Route extends Model
{
    protected $fillable = [
        'start_point',
        'end_point',
        'distance_km',
        'optimized_path',
        'avg_fuel_consumption',
    ];

    protected $casts = [
        'optimized_path' => 'array',
    ];
}
