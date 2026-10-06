<?php

namespace App\Http\Controllers;

use App\Models\EssMaintenanceRequest;
use App\Models\MaintenanceRecord;
use App\Models\Vehicle;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class EssMaintenanceRequestController extends Controller
{
    /**
     * ESS employee portal — list own requests + submission form.
     */
    public function index(Request $request)
    {
        $userId = session('user_id');
        $user   = User::find($userId);

        if (!$user) {
            return redirect()->route('login')->with('error', 'Session expired. Please log in again.');
        }

        // Employees see only their own requests; admins/fleet managers see all
        $isManager = in_array($user->role, ['admin', 'fleet_manager']);

        $query = EssMaintenanceRequest::with(['employee', 'vehicle', 'reviewer'])
            ->latest();

        if (!$isManager) {
            $query->where('employee_id', $userId);
        }

        $essRequests = $query->get();
        $vehicles    = Vehicle::where('status', 'active')->get();

        return view('ess.maintenance.index', compact('essRequests', 'vehicles', 'user', 'isManager'));
    }

    /**
     * Employee submits a new maintenance / repair / reimbursement request.
     */
    public function store(Request $request)
    {
        $userId = session('user_id');

        if (!$userId) {
            return redirect()->route('login')->with('error', 'Session expired.');
        }

        $validated = $request->validate([
            'vehicle_id'           => 'required|exists:vehicles,id',
            'request_type'         => 'required|in:repair,reimbursement,scheduled_maintenance',
            'anomaly_description'  => 'required|string|max:2000',
            'anomaly_date'         => 'required|date',
            'urgency_level'        => 'required|in:low,medium,high,critical',
            'odometer_reading'     => 'nullable|integer|min:0',
            'reimbursement_amount' => 'nullable|numeric|min:0',
            'receipt_number'       => 'nullable|string|max:100',
            'remarks'              => 'nullable|string|max:1000',
        ]);

        // Reimbursement requires amount
        if ($validated['request_type'] === 'reimbursement' && empty($validated['reimbursement_amount'])) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Reimbursement amount is required for reimbursement requests.');
        }

        EssMaintenanceRequest::create([
            'employee_id'          => $userId,
            'vehicle_id'           => $validated['vehicle_id'],
            'request_type'         => $validated['request_type'],
            'anomaly_description'  => $validated['anomaly_description'],
            'anomaly_date'         => $validated['anomaly_date'],
            'urgency_level'        => $validated['urgency_level'],
            'odometer_reading'     => $validated['odometer_reading'] ?? null,
            'reimbursement_amount' => $validated['reimbursement_amount'] ?? null,
            'receipt_number'       => $validated['receipt_number'] ?? null,
            'remarks'              => $validated['remarks'] ?? null,
            'status'               => 'pending',
        ]);

        return redirect()->route('ess.maintenance.index')
            ->with('success', 'Your maintenance request has been submitted and is pending review by the Fleet Manager.');
    }

    /**
     * Fleet Manager reviews the ESS request queue.
     */
    public function managerQueue(Request $request)
    {
        $filter    = $request->query('filter', 'all');
        $urgency   = $request->query('urgency', 'all');

        $query = EssMaintenanceRequest::with(['employee', 'vehicle', 'reviewer'])->latest();

        if ($filter !== 'all') {
            $query->where('status', $filter);
        }

        if ($urgency !== 'all') {
            $query->where('urgency_level', $urgency);
        }

        $essRequests = $query->get();
        $pendingCount    = EssMaintenanceRequest::where('status', 'pending')->count();
        $inProgressCount = EssMaintenanceRequest::where('status', 'in_progress')->count();

        return view('ess.maintenance.manager', compact('essRequests', 'filter', 'urgency', 'pendingCount', 'inProgressCount'));
    }

    /**
     * Fleet Manager approves a request and assigns motorshop + details.
     */
    public function approve(Request $request, EssMaintenanceRequest $essRequest)
    {
        $userId = session('user_id');

        $validated = $request->validate([
            'assigned_shop'         => 'required|string|max:255',
            'estimated_cost'        => 'required|numeric|min:0',
            'scheduled_repair_date' => 'required|date',
            'remarks'               => 'nullable|string|max:1000',
        ]);

        $essRequest->update([
            'status'                => 'approved',
            'assigned_shop'         => $validated['assigned_shop'],
            'estimated_cost'        => $validated['estimated_cost'],
            'scheduled_repair_date' => $validated['scheduled_repair_date'],
            'remarks'               => $validated['remarks'] ?? $essRequest->remarks,
            'reviewed_by'           => $userId,
        ]);

        return redirect()->back()
            ->with('success', "ESS Request #{$essRequest->id} approved. Assigned to {$validated['assigned_shop']}.");
    }

    /**
     * Fleet Manager rejects a request with a reason.
     */
    public function reject(Request $request, EssMaintenanceRequest $essRequest)
    {
        $userId = session('user_id');

        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:1000',
        ]);

        $essRequest->update([
            'status'           => 'rejected',
            'rejection_reason' => $validated['rejection_reason'],
            'reviewed_by'      => $userId,
        ]);

        return redirect()->back()
            ->with('success', "ESS Request #{$essRequest->id} has been rejected.");
    }

    /**
     * Fleet Manager converts approved ESS request into a PMS maintenance record.
     * This avoids double-entry — the PMS record is auto-populated from ESS request data.
     */
    public function convertToPms(EssMaintenanceRequest $essRequest)
    {
        // Guard: only approved requests can be converted
        if ($essRequest->status !== 'approved') {
            return redirect()->back()
                ->with('error', 'Only approved ESS requests can be converted to a PMS record.');
        }

        // Guard: prevent duplicate conversion
        if ($essRequest->maintenance_record_id) {
            return redirect()->back()
                ->with('error', 'This ESS request has already been converted to a PMS record.');
        }

        $serviceTypeMap = [
            'repair'                => 'Vehicle Repair (ESS Request)',
            'reimbursement'         => 'Reimbursement Repair (ESS)',
            'scheduled_maintenance' => 'Preventive Maintenance (ESS Request)',
        ];

        $maintenanceRecord = MaintenanceRecord::create([
            'vehicle_id'    => $essRequest->vehicle_id,
            'service_type'  => $serviceTypeMap[$essRequest->request_type] ?? 'ESS Repair Request',
            'description'   => $essRequest->anomaly_description . ($essRequest->remarks ? "\n\nRemarks: " . $essRequest->remarks : ''),
            'cost'          => $essRequest->estimated_cost ?? 0,
            'status'        => 'scheduled',
            'scheduled_date'=> $essRequest->scheduled_repair_date ?? now()->toDateString(),
            'repair_category' => 'repair',
            'filter_status'  => 'active',
        ]);

        // Link the ESS request to the new PMS record
        $essRequest->update([
            'maintenance_record_id' => $maintenanceRecord->id,
            'status'                => 'in_progress',
        ]);

        return redirect()->back()
            ->with('success', "ESS Request #{$essRequest->id} converted to PMS Record #{$maintenanceRecord->id} successfully. Vehicle repair is now tracked in Maintenance.");
    }

    /**
     * Fleet Manager marks an ESS request as completed with actual cost.
     */
    public function complete(Request $request, EssMaintenanceRequest $essRequest)
    {
        $validated = $request->validate([
            'actual_cost'    => 'required|numeric|min:0',
            'completed_date' => 'nullable|date',
        ]);

        $essRequest->update([
            'actual_cost'    => $validated['actual_cost'],
            'completed_date' => $validated['completed_date'] ?? now()->toDateString(),
            'status'         => 'completed',
        ]);

        // Also sync the linked maintenance record if it exists
        if ($essRequest->maintenance_record_id) {
            MaintenanceRecord::where('id', $essRequest->maintenance_record_id)->update([
                'status'          => 'completed',
                'cost'            => $validated['actual_cost'],
                'completion_date' => $validated['completed_date'] ?? now()->toDateString(),
                'filter_status'   => 'inactive',
            ]);
        }

        return redirect()->back()
            ->with('success', "ESS Request #{$essRequest->id} marked as completed. Actual cost: ₱" . number_format($validated['actual_cost'], 2));
    }
}
