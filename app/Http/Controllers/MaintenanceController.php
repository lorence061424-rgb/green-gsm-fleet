<?php

namespace App\Http\Controllers;

use App\Models\MaintenanceRecord;
use App\Models\Vehicle;
use App\Models\EssMaintenanceRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class MaintenanceController extends Controller
{
    public function index(Request $request)
    {
        $filter = $request->query('filter');
        $tab = $request->query('tab', 'records'); // 'records' or 'incoming'
        $query = MaintenanceRecord::with('vehicle')->latest();

        if ($filter && in_array($filter, ['maintenance', 'active', 'inactive'])) {
            $query->filterHistory($filter);
        }

        $records = $query->get();
        $vehicles = Vehicle::all();

        // Safely retrieve incoming employee/driver requests
        $incomingRequests = collect([]);
        if (Schema::hasTable('ess_maintenance_requests')) {
            $incomingRequests = EssMaintenanceRequest::with(['employee', 'vehicle'])->latest()->get();
        }

        $pendingIncomingCount = $incomingRequests->where('status', 'pending')->count();

        return view('maintenance.index', compact('records', 'vehicles', 'filter', 'tab', 'incomingRequests', 'pendingIncomingCount'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'service_type' => 'required|string',
            'description' => 'nullable|string',
            'cost' => 'required|numeric|min:0',
            'status' => 'required|in:scheduled,in_progress,completed',
            'scheduled_date' => 'required|date',
        ]);

        if ($validated['status'] === 'completed') {
            $validated['completion_date'] = date('Y-m-d');
        }

        $record = MaintenanceRecord::create($validated);

        // Bi-directional synchronization with Fleet Vehicle Management
        $vehicle = Vehicle::find($validated['vehicle_id']);
        if ($vehicle) {
            if ($validated['status'] === 'in_progress') {
                $vehicle->update(['status' => 'maintenance']);
            } elseif ($validated['status'] === 'completed') {
                $vehicle->update(['status' => 'active']);
            }
        }

        return redirect()->back()->with('success', 'Preventive Maintenance Service (PMS) record created and vehicle availability synchronized.');
    }

    public function updateStatus(Request $request, MaintenanceRecord $record)
    {
        $validated = $request->validate([
            'status' => 'required|in:scheduled,in_progress,completed',
            'completion_date' => 'nullable|date',
            'cost' => 'required|numeric|min:0',
        ]);

        if ($validated['status'] === 'completed' && empty($validated['completion_date'])) {
            $validated['completion_date'] = date('Y-m-d');
        }

        $record->update($validated);

        // Bi-directional synchronization with Fleet Vehicle Management
        if ($record->vehicle) {
            if ($validated['status'] === 'in_progress') {
                $record->vehicle->update(['status' => 'maintenance']);
            } elseif ($validated['status'] === 'completed') {
                $record->vehicle->update(['status' => 'active']);
            } elseif ($validated['status'] === 'scheduled' && $record->vehicle->status === 'maintenance') {
                $record->vehicle->update(['status' => 'active']);
            }
        }

        $statusLabel = ucfirst(str_replace('_', ' ', $validated['status']));
        return redirect()->back()->with('success', "Maintenance status updated to '{$statusLabel}'. Vehicle availability in Fleet Management updated accordingly.");
    }

    public function destroy(MaintenanceRecord $record)
    {
        $vehicle = $record->vehicle;
        $serviceType = $record->service_type;
        $record->delete();

        if ($vehicle && $vehicle->status === 'maintenance') {
            $hasOtherMaintenance = MaintenanceRecord::where('vehicle_id', $vehicle->id)
                ->where('status', 'in_progress')
                ->exists();
            if (!$hasOtherMaintenance) {
                $vehicle->update(['status' => 'active']);
            }
        }

        return redirect()->back()->with('success', "Maintenance record for '{$serviceType}' deleted successfully.");
    }

    /**
     * Accept an incoming repair/reimbursement request and convert to PMS or approve reimbursement.
     */
    public function acceptIncomingRequest(Request $request, EssMaintenanceRequest $requestRecord)
    {
        $validated = $request->validate([
            'assigned_shop'   => 'nullable|string|max:255',
            'cost'            => 'required|numeric|min:0',
            'scheduled_date'  => 'required|date',
            'status'          => 'required|in:scheduled,in_progress,completed',
            'remarks'         => 'nullable|string',
        ]);

        $serviceType = match($requestRecord->request_type) {
            'reimbursement'         => 'Reimbursement Claim (Driver/Employee)',
            'scheduled_maintenance' => 'Scheduled Preventive Maintenance',
            default                 => 'Vehicle Anomaly Repair (' . ucfirst($requestRecord->urgency_level) . ' Urgency)',
        };

        $pmsRecord = MaintenanceRecord::create([
            'vehicle_id'      => $requestRecord->vehicle_id,
            'service_type'    => $serviceType,
            'description'     => $requestRecord->anomaly_description . ($validated['remarks'] ? "\n\nNotes: " . $validated['remarks'] : ''),
            'cost'            => $validated['cost'],
            'status'          => $validated['status'],
            'scheduled_date'  => $validated['scheduled_date'],
            'repair_category' => $requestRecord->request_type === 'scheduled_maintenance' ? 'maintenance' : 'repair',
            'filter_status'   => $validated['status'] === 'completed' ? 'inactive' : 'active',
        ]);

        // Sync request status
        $requestRecord->update([
            'status'                => 'approved',
            'assigned_shop'         => $validated['assigned_shop'] ?? 'Mabuhay Auto Shop',
            'estimated_cost'        => $validated['cost'],
            'scheduled_repair_date' => $validated['scheduled_date'],
            'maintenance_record_id' => $pmsRecord->id,
            'reviewed_by'           => session('user_id'),
        ]);

        // Bi-directional vehicle status sync
        if ($validated['status'] === 'in_progress' && $requestRecord->vehicle) {
            $requestRecord->vehicle->update(['status' => 'maintenance']);
        }

        return redirect()->route('maintenance.index', ['tab' => 'incoming'])
            ->with('success', "Incoming Request #{$requestRecord->id} accepted and converted to PMS Record #{$pmsRecord->id}.");
    }

    /**
     * Reject an incoming request.
     */
    public function rejectIncomingRequest(Request $request, EssMaintenanceRequest $requestRecord)
    {
        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:1000',
        ]);

        $requestRecord->update([
            'status'           => 'rejected',
            'rejection_reason' => $validated['rejection_reason'],
            'reviewed_by'      => session('user_id'),
        ]);

        return redirect()->route('maintenance.index', ['tab' => 'incoming'])
            ->with('success', "Incoming Request #{$requestRecord->id} has been marked as rejected.");
    }

    /**
     * Store a sample/mock incoming request for testing & defense demonstration.
     */
    public function storeSampleIncomingRequest(Request $request)
    {
        $validated = $request->validate([
            'vehicle_id'           => 'required|exists:vehicles,id',
            'request_type'         => 'required|in:repair,reimbursement,scheduled_maintenance',
            'anomaly_description'  => 'required|string|max:1000',
            'urgency_level'        => 'required|in:low,medium,high,critical',
            'reimbursement_amount' => 'nullable|numeric|min:0',
        ]);

        $user = User::first() ?? User::factory()->create();

        EssMaintenanceRequest::create([
            'employee_id'          => $user->id,
            'vehicle_id'           => $validated['vehicle_id'],
            'request_type'         => $validated['request_type'],
            'anomaly_description'  => $validated['anomaly_description'],
            'anomaly_date'         => now()->toDateString(),
            'urgency_level'        => $validated['urgency_level'],
            'reimbursement_amount' => $validated['reimbursement_amount'] ?? null,
            'status'               => 'pending',
        ]);

        return redirect()->route('maintenance.index', ['tab' => 'incoming'])
            ->with('success', 'Example incoming maintenance request created. Ready to demo approval workflow.');
    }
}
