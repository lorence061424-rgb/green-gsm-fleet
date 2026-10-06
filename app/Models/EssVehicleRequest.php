<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EssVehicleRequest extends Model
{
    protected $table = 'ess_vehicle_requests';

    protected $fillable = [
        'employee_id',
        'purpose_type',
        'purpose_description',
        'destination',
        'reservation_date',
        'start_time',
        'end_time',
        'num_passengers',
        'vehicle_preference',
        'driver_preference',
        'is_roundtrip',
        'urgency_level',
        'additional_remarks',
        'status',
        'rejection_reason',
        'assigned_vehicle_id',
        'assigned_driver_id',
        'reviewed_by',
        'reservation_id',
    ];

    protected $casts = [
        'reservation_date' => 'date',
        'is_roundtrip'     => 'boolean',
    ];

    // ── Relationships ────────────────────────────────────────────

    public function employee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'employee_id');
    }

    public function assignedVehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class, 'assigned_vehicle_id');
    }

    public function assignedDriver(): BelongsTo
    {
        return $this->belongsTo(Driver::class, 'assigned_driver_id');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function reservation(): BelongsTo
    {
        return $this->belongsTo(VehicleReservation::class, 'reservation_id');
    }

    // ── Helpers ──────────────────────────────────────────────────

    public function getPurposeTypeLabelAttribute(): string
    {
        return match($this->purpose_type) {
            'meeting'       => 'Meeting / Official Business',
            'field_visit'   => 'Field Visit / Inspection',
            'airport'       => 'Airport / Terminal Transfer',
            'inter_branch'  => 'Inter-Branch Travel',
            'errand'        => 'Errand / Delivery',
            'emergency'     => 'Emergency Use',
            default         => ucfirst($this->purpose_type),
        };
    }

    public function getStatusBadgeAttribute(): string
    {
        return match($this->status) {
            'approved'  => 'success',
            'rejected'  => 'danger',
            'completed' => 'secondary',
            'cancelled' => 'dark',
            default     => 'warning',
        };
    }
}
