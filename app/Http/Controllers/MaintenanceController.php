<?php

namespace App\Http\Controllers;

use App\Models\MaintenanceRecord;
use App\Models\Vehicle;
use Illuminate\Http\Request;

class MaintenanceController extends Controller
{
    public function index()
    {
        $records = MaintenanceRecord::with('vehicle')->latest()->get();
        $vehicles = Vehicle::all();

        return view('maintenance.index', compact('records', 'vehicles'));
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

        $record = MaintenanceRecord::create($validated);

        // Update vehicle status to maintenance if appropriate
        if ($validated['status'] === 'in_progress') {
            Vehicle::find($validated['vehicle_id'])->update(['status' => 'maintenance']);
        }

        return redirect()->back()->with('success', 'Maintenance scheduled successfully.');
    }

    public function updateStatus(Request $request, MaintenanceRecord $record)
    {
        $validated = $request->validate([
            'status' => 'required|in:scheduled,in_progress,completed',
            'completion_date' => 'nullable|date|required_if:status,completed',
            'cost' => 'required|numeric|min:0',
        ]);

        $record->update($validated);

        // If completed, release vehicle to active; if in progress, set to maintenance
        if ($validated['status'] === 'completed') {
            $record->vehicle->update(['status' => 'active']);
        } elseif ($validated['status'] === 'in_progress') {
            $record->vehicle->update(['status' => 'maintenance']);
        }

        return redirect()->back()->with('success', 'Maintenance record updated successfully.');
    }
}
