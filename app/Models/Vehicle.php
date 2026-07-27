<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Vehicle extends Model
{
    protected $fillable = [
        'license_plate',
        'model',
        'make',
        'year',
        'type',
        'status',
        'fuel_capacity',
        'current_gps_lat',
        'current_gps_lng',
    ];

    public function trips(): HasMany
    {
        return $this->hasMany(Trip::class);
    }

    public function fuelLogs(): HasMany
    {
        return $this->hasMany(FuelLog::class);
    }

    public function maintenanceRecords(): HasMany
    {
        return $this->hasMany(MaintenanceRecord::class);
    }
}
