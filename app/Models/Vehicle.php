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
        'dealership_company',
        'or_number',
        'cr_number',
        'or_cr_expiry_date',
        'registration_number',
        'registration_date',
        'registration_expiry_date',
        'registration_status',
        'year',
        'type',
        'status',
        'fuel_capacity',
        'fuel_type',
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
