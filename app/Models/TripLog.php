<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TripLog extends Model
{
    // Disable timestamps since we use custom timestamp
    public $timestamps = false;

    protected $fillable = [
        'trip_id',
        'lat',
        'lng',
        'speed_kmh',
        'idle_time_seconds',
        'timestamp',
    ];

    protected $casts = [
        'timestamp' => 'datetime',
    ];

    public function trip(): BelongsTo
    {
        return $this->belongsTo(Trip::class);
    }
}
