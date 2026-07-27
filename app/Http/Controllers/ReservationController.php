<?php

namespace App\Http\Controllers;

use App\Models\VehicleReservation;
use App\Models\Vehicle;
use App\Models\Driver;
use App\Models\User;
use Illuminate\Http\Request;

class ReservationController extends Controller
{
    /**
     * Display reservation calendar and list.
     */
    public function index()
    {
        $reservations = VehicleReservation::with(['vehicle', 'driver.user', 'requestedBy'])->latest()->get();
        $vehicles = Vehicle::where('status', 'active')->get();
        $drivers = Driver::with('user')->get();
        $users = User::whereIn('role', ['admin', 'dispatcher', 'fleet_manager'])->get();

        // Build calendar events array for JavaScript
        $calendarEvents = $reservations->map(function ($r) {
            $colorMap = [
                'pending' => '#F59E0B',
                'approved' => '#10B981',
                'rejected' => '#EF4444',
                'completed' => '#6B7280',
                'cancelled' => '#9CA3AF',
            ];
            return [
                'id' => $r->id,
                'title' => $r->vehicle->license_plate . ' - ' . $r->purpose,
                'date' => $r->reservation_date,
                'start_time' => $r->start_time,
                'end_time' => $r->end_time,
                'color' => $colorMap[$r->status] ?? '#4F46E5',
                'status' => $r->status,
                'vehicle' => $r->vehicle->make . ' ' . $r->vehicle->model,
                'driver' => $r->driver ? $r->driver->user->name : 'Unassigned',
                'requested_by' => $r->requestedBy->name,
            ];
        });

        return view('reservations.index', compact('reservations', 'vehicles', 'drivers', 'users', 'calendarEvents'));
    }

    /**
     * Store a new vehicle reservation.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'driver_id' => 'nullable|exists:drivers,id',
            'purpose' => 'required|string|max:255',
            'reservation_date' => 'required|date|after_or_equal:today',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
            'remarks' => 'nullable|string',
        ]);

        // Check for time conflicts on the same vehicle and date
        $conflict = VehicleReservation::where('vehicle_id', $validated['vehicle_id'])
            ->where('reservation_date', $validated['reservation_date'])
            ->whereIn('status', ['pending', 'approved'])
            ->where(function ($query) use ($validated) {
                $query->where(function ($q) use ($validated) {
                    $q->where('start_time', '<', $validated['end_time'])
                      ->where('end_time', '>', $validated['start_time']);
                });
            })->exists();

        if ($conflict) {
            return redirect()->back()->with('error', 'Schedule conflict! This vehicle is already reserved during that time slot.');
        }

        // Use first admin user as requestor (since we don't have auth yet)
        $requestedBy = User::where('role', 'admin')->first();

        VehicleReservation::create([
            'vehicle_id' => $validated['vehicle_id'],
            'driver_id' => $validated['driver_id'],
            'requested_by' => $requestedBy->id,
            'purpose' => $validated['purpose'],
            'reservation_date' => $validated['reservation_date'],
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'],
            'status' => 'pending',
            'remarks' => $validated['remarks'],
        ]);

        return redirect()->back()->with('success', 'Reservation submitted successfully and is pending approval.');
    }

    /**
     * Approve or reject a reservation.
     */
    public function updateStatus(Request $request, VehicleReservation $reservation)
    {
        $validated = $request->validate([
            'status' => 'required|in:approved,rejected,completed,cancelled',
        ]);

        $reservation->update(['status' => $validated['status']]);

        $statusLabel = ucfirst($validated['status']);
        return redirect()->back()->with('success', "Reservation #{$reservation->id} has been {$statusLabel}.");
    }

    /**
     * Check vehicle availability for a given date (AJAX).
     */
    public function checkAvailability(Request $request)
    {
        $validated = $request->validate([
            'date' => 'required|date',
        ]);

        $reservedVehicleIds = VehicleReservation::where('reservation_date', $validated['date'])
            ->whereIn('status', ['pending', 'approved'])
            ->pluck('vehicle_id')
            ->toArray();

        $available = Vehicle::where('status', 'active')
            ->whereNotIn('id', $reservedVehicleIds)
            ->get(['id', 'license_plate', 'make', 'model', 'type']);

        $reserved = Vehicle::where('status', 'active')
            ->whereIn('id', $reservedVehicleIds)
            ->get(['id', 'license_plate', 'make', 'model', 'type']);

        return response()->json([
            'date' => $validated['date'],
            'available' => $available,
            'reserved' => $reserved,
        ]);
    }
}
