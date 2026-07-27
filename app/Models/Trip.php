<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Trip extends Model
{
    protected $fillable = [
        'booking_reference_id',
        'driver_id',
        'vehicle_id',
        'start_location',
        'end_location',
        'start_lat',
        'start_lng',
        'end_lat',
        'end_lng',
        'status',
        'distance_km',
        'estimated_duration_minutes',
        'actual_duration_minutes',
        'estimated_fuel_liters',
        'actual_fuel_liters',
        'start_time',
        'end_time',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];

    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class);
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function logs(): HasMany
    {
        return $this->hasMany(TripLog::class);
    }

    public function performanceRecord(): HasOne
    {
        return $this->hasOne(PerformanceRecord::class);
    }

    public function fuelLog(): HasOne
    {
        return $this->hasOne(FuelLog::class);
    }
}
