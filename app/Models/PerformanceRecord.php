<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PerformanceRecord extends Model
{
    protected $fillable = [
        'driver_id',
        'trip_id',
        'speeding_events',
        'harsh_braking_events',
        'idle_minutes',
        'safety_score',
    ];

    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class);
    }

    public function trip(): BelongsTo
    {
        return $this->belongsTo(Trip::class);
    }
}
