<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Driver extends Model
{
    protected $fillable = [
        'user_id',
        'license_number',
        'status',
        'performance_score',
        'total_trips',
        'total_distance_km',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function trips(): HasMany
    {
        return $this->hasMany(Trip::class);
    }

    public function performanceRecords(): HasMany
    {
        return $this->hasMany(PerformanceRecord::class);
    }
}
