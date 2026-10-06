<?php

namespace App\Http\Controllers;

use App\Models\EssVehicleRequest;
use App\Models\VehicleReservation;
use App\Models\Vehicle;
use App\Models\Driver;
use App\Models\User;
use Illuminate\Http\Request;

class EssVehicleRequestController extends Controller
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

        $isManager = in_array($user->role, ['admin', 'fleet_manager', 'dispatcher']);

        $query = EssVehicleRequest::with(['employee', 'assignedVehicle', 'assignedDriver.user', 'reviewer'])
            ->latest();

        if (!$isManager) {
            $query->where('employee_id', $userId);
        }

        $essRequests = $query->get();
        $vehicles    = Vehicle::where('status', 'active')->get();
        $drivers     = Driver::with('user')->get();

        return view('ess.vehicles.index', compact('essRequests', 'vehicles', 'drivers', 'user', 'isManager'));
    }

    /**
     * Employee submits a new vehicle reservation request via ESS.
     */
    public function store(Request $request)
    {
        $userId = session('user_id');

        if (!$userId) {
            return redirect()->route('login')->with('error', 'Session expired.');
        }

        $validated = $request->validate([
            'purpose_type'        => 'required|in:meeting,field_visit,airport,inter_branch,errand,emergency',
            'purpose_description' => 'required|string|max:1000',
            'destination'         => 'required|string|max:255',
            'reservation_date'    => 'required|date|after_or_equal:today',
            'start_time'          => 'required',
            'end_time'            => 'required|after:start_time',
            'num_passengers'      => 'required|integer|min:1|max:30',
            'vehicle_preference'  => 'nullable|string|max:100',
            'is_roundtrip'        => 'nullable|boolean',
            'urgency_level'       => 'required|in:routine,priority',
            'additional_remarks'  => 'nullable|string|max:1000',
        ]);

        EssVehicleRequest::create([
            'employee_id'         => $userId,
            'purpose_type'        => $validated['purpose_type'],
            'purpose_description' => $validated['purpose_description'],
            'destination'         => $validated['destination'],
            'reservation_date'    => $validated['reservation_date'],
            'start_time'          => $validated['start_time'],
            'end_time'            => $validated['end_time'],
            'num_passengers'      => $validated['num_passengers'],
            'vehicle_preference'  => $validated['vehicle_preference'] ?? null,
            'is_roundtrip'        => $request->has('is_roundtrip') ? 1 : 0,
            'urgency_level'       => $validated['urgency_level'],
            'additional_remarks'  => $validated['additional_remarks'] ?? null,
            'status'              => 'pending',
        ]);

        return redirect()->route('ess.vehicles.index')
            ->with('success', 'Your vehicle reservation request has been submitted and is pending approval from the Fleet Manager.');
    }

    /**
     * Fleet Manager / Dispatcher queue view.
     */
    public function managerQueue(Request $request)
    {
        $filter  = $request->query('filter', 'all');
        $urgency = $request->query('urgency', 'all');

        $query = EssVehicleRequest::with(['employee', 'assignedVehicle', 'assignedDriver.user', 'reviewer'])->latest();

        if ($filter !== 'all') {
            $query->where('status', $filter);
        }
        if ($urgency !== 'all') {
            $query->where('urgency_level', $urgency);
        }

        $essRequests     = $query->get();
        $vehicles        = Vehicle::where('status', 'active')->get();
        $drivers         = Driver::with('user')->get();
        $pendingCount    = EssVehicleRequest::where('status', 'pending')->count();

        return view('ess.vehicles.manager', compact('essRequests', 'vehicles', 'drivers', 'filter', 'urgency', 'pendingCount'));
    }

    /**
     * Approve and assign vehicle + driver. Auto-creates a VehicleReservation record.
     */
    public function approve(Request $request, EssVehicleRequest $essRequest)
    {
        $userId = session('user_id');

        $validated = $request->validate([
            'assigned_vehicle_id' => 'required|exists:vehicles,id',
            'assigned_driver_id'  => 'nullable|exists:drivers,id',
        ]);

        // Check for time conflict on assigned vehicle
        $conflict = VehicleReservation::where('vehicle_id', $validated['assigned_vehicle_id'])
            ->where('reservation_date', $essRequest->reservation_date->format('Y-m-d'))
            ->whereIn('status', ['pending', 'approved'])
            ->where(function ($q) use ($essRequest) {
                $q->where('start_time', '<', $essRequest->end_time)
                  ->where('end_time', '>', $essRequest->start_time);
            })
            ->exists();

        if ($conflict) {
            return redirect()->back()
                ->with('error', 'Schedule conflict! The selected vehicle is already reserved during that time slot. Please choose a different vehicle.');
        }

        // Auto-create a VehicleReservation record
        $purposeLabels = [
            'meeting'      => 'Meeting / Official Business',
            'field_visit'  => 'Field Visit / Inspection',
            'airport'      => 'Airport / Terminal Transfer',
            'inter_branch' => 'Inter-Branch Travel',
            'errand'       => 'Errand / Delivery',
            'emergency'    => 'Emergency Use',
        ];

        $reservation = VehicleReservation::create([
            'vehicle_id'       => $validated['assigned_vehicle_id'],
            'driver_id'        => $validated['assigned_driver_id'] ?? null,
            'requested_by'     => $essRequest->employee_id,
            'purpose'          => ($purposeLabels[$essRequest->purpose_type] ?? 'ESS Request') . ': ' . $essRequest->purpose_description,
            'reservation_date' => $essRequest->reservation_date->format('Y-m-d'),
            'start_time'       => $essRequest->start_time,
            'end_time'         => $essRequest->end_time,
            'status'           => 'approved',
            'remarks'          => 'Auto-created from ESS Request #' . $essRequest->id . '. Destination: ' . $essRequest->destination
                                  . ($essRequest->additional_remarks ? '. ' . $essRequest->additional_remarks : ''),
        ]);

        // Update the ESS request record
        $essRequest->update([
            'status'              => 'approved',
            'assigned_vehicle_id' => $validated['assigned_vehicle_id'],
            'assigned_driver_id'  => $validated['assigned_driver_id'] ?? null,
            'reviewed_by'         => $userId,
            'reservation_id'      => $reservation->id,
        ]);

        return redirect()->back()
            ->with('success', "ESS Vehicle Request #{$essRequest->id} approved! Reservation #{$reservation->id} created and visible on the Dispatch Calendar.");
    }

    /**
     * Reject an ESS vehicle request with a reason.
     */
    public function reject(Request $request, EssVehicleRequest $essRequest)
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
            ->with('success', "ESS Vehicle Request #{$essRequest->id} has been rejected.");
    }

    /**
     * Mark ESS vehicle request as cancelled (by employee).
     */
    public function cancel(EssVehicleRequest $essRequest)
    {
        $userId = session('user_id');

        // Only the employee who submitted or an admin can cancel
        if ($essRequest->employee_id != $userId && !in_array(session('user_role'), ['admin', 'fleet_manager'])) {
            return redirect()->back()->with('error', 'You are not authorized to cancel this request.');
        }

        if (!in_array($essRequest->status, ['pending', 'approved'])) {
            return redirect()->back()->with('error', 'Only pending or approved requests can be cancelled.');
        }

        $essRequest->update(['status' => 'cancelled']);

        // If it had a linked reservation, cancel that too
        if ($essRequest->reservation_id) {
            VehicleReservation::where('id', $essRequest->reservation_id)->update(['status' => 'cancelled']);
        }

        return redirect()->back()
            ->with('success', "ESS Vehicle Request #{$essRequest->id} has been cancelled.");
    }
}
