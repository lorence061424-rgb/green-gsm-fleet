<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EssMaintenanceRequest extends Model
{
    protected $table = 'ess_maintenance_requests';

    protected $fillable = [
        'employee_id',
        'vehicle_id',
        'request_type',
        'anomaly_description',
        'anomaly_date',
        'urgency_level',
        'odometer_reading',
        'reimbursement_amount',
        'receipt_number',
        'supporting_docs',
        'remarks',
        'status',
        'rejection_reason',
        'assigned_shop',
        'estimated_cost',
        'actual_cost',
        'scheduled_repair_date',
        'completed_date',
        'reviewed_by',
        'maintenance_record_id',
    ];

    protected $casts = [
        'anomaly_date'          => 'date',
        'scheduled_repair_date' => 'date',
        'completed_date'        => 'date',
        'reimbursement_amount'  => 'decimal:2',
        'estimated_cost'        => 'decimal:2',
        'actual_cost'           => 'decimal:2',
    ];

    // ── Relationships ────────────────────────────────────────────

    public function employee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'employee_id');
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class, 'vehicle_id');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function maintenanceRecord(): BelongsTo
    {
        return $this->belongsTo(MaintenanceRecord::class, 'maintenance_record_id');
    }

    // ── Helpers ──────────────────────────────────────────────────

    public function getUrgencyBadgeAttribute(): string
    {
        return match($this->urgency_level) {
            'critical' => 'danger',
            'high'     => 'warning',
            'medium'   => 'info',
            default    => 'secondary',
        };
    }

    public function getStatusBadgeAttribute(): string
    {
        return match($this->status) {
            'approved'    => 'success',
            'rejected'    => 'danger',
            'in_progress' => 'warning',
            'completed'   => 'secondary',
            default       => 'primary',
        };
    }

    public function getRequestTypeLabelAttribute(): string
    {
        return match($this->request_type) {
            'repair'                  => 'Vehicle Repair',
            'reimbursement'           => 'Reimbursement',
            'scheduled_maintenance'   => 'Scheduled Maintenance (PMS)',
            default                   => ucfirst($this->request_type),
        };
    }
}
