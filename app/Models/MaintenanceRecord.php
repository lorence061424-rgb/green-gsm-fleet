<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MaintenanceRecord extends Model
{
    protected $fillable = [
        'vehicle_id',
        'service_type',
        'repair_category',
        'description',
        'cost',
        'status',
        'filter_status',
        'scheduled_date',
        'completion_date',
    ];

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function scopeFilterHistory($query, $filter)
    {
        if ($filter === 'maintenance') {
            return $query->where(function ($q) {
                $q->where('repair_category', 'maintenance')
                  ->orWhere('service_type', 'LIKE', '%preventive%')
                  ->orWhere('service_type', 'LIKE', '%routine%');
            });
        } elseif ($filter === 'active') {
            return $query->where(function ($q) {
                $q->where('filter_status', 'active')
                  ->orWhereIn('status', ['scheduled', 'in_progress']);
            });
        } elseif ($filter === 'inactive') {
            return $query->where(function ($q) {
                $q->where('filter_status', 'inactive')
                  ->orWhereIn('status', ['completed', 'cancelled']);
            });
        }

        return $query;
    }
}
