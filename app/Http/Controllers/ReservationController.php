<?php

namespace App\Http\Controllers;

use App\Models\VehicleReservation;
use App\Models\Vehicle;
use App\Models\Driver;
use App\Models\User;
use App\Models\EssVehicleRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class ReservationController extends Controller
{
    /**
     * Display reservation calendar and list.
     */
    public function index(Request $request)
    {
        $tab = $request->query('tab', 'calendar'); // 'calendar' or 'incoming'
        $reservations = VehicleReservation::with(['vehicle', 'driver.user', 'requestedBy'])->latest()->get();
        $vehicles = Vehicle::where('status', 'active')->get();
        $drivers = Driver::with('user')->get();
        $users = User::whereIn('role', ['admin', 'dispatcher', 'fleet_manager'])->get();

        // Safely retrieve incoming employee vehicle requests
        $incomingRequests = collect([]);
        if (Schema::hasTable('ess_vehicle_requests')) {
            $incomingRequests = EssVehicleRequest::with(['employee', 'assignedVehicle', 'assignedDriver.user'])->latest()->get();
        }

        $pendingIncomingCount = $incomingRequests->where('status', 'pending')->count();

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
                'title' => ($r->vehicle ? $r->vehicle->license_plate : 'Vehicle') . ' - ' . $r->purpose,
                'date' => $r->reservation_date,
                'start_time' => $r->start_time,
                'end_time' => $r->end_time,
                'color' => $colorMap[$r->status] ?? '#4F46E5',
                'status' => $r->status,
                'vehicle' => $r->vehicle ? ($r->vehicle->make . ' ' . $r->vehicle->model) : 'N/A',
                'driver' => ($r->driver && $r->driver->user) ? $r->driver->user->name : 'Unassigned',
                'requested_by' => $r->requestedBy ? $r->requestedBy->name : 'N/A',
            ];
        });

        return view('reservations.index', compact('reservations', 'vehicles', 'drivers', 'users', 'calendarEvents', 'tab', 'incomingRequests', 'pendingIncomingCount'));
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
        $rawDate = $request->input('date', date('Y-m-d'));
        try {
            if (strpos($rawDate, '/') !== false) {
                // Handle DD/MM/YYYY format
                $parts = explode('/', $rawDate);
                if (count($parts) === 3) {
                    $formattedDate = sprintf('%04d-%02d-%02d', $parts[2], $parts[1], $parts[0]);
                } else {
                    $formattedDate = \Carbon\Carbon::parse($rawDate)->format('Y-m-d');
                }
            } else {
                $formattedDate = \Carbon\Carbon::parse($rawDate)->format('Y-m-d');
            }
        } catch (\Exception $e) {
            $formattedDate = date('Y-m-d');
        }

        $reservedVehicleIds = VehicleReservation::where('reservation_date', $formattedDate)
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
            'date' => $formattedDate,
            'available' => $available,
            'reserved' => $reserved,
        ]);
    }

    /**
     * Approve incoming employee request and auto-create dispatch reservation.
     */
    public function approveIncomingRequest(Request $request, EssVehicleRequest $requestRecord)
    {
        $validated = $request->validate([
            'assigned_vehicle_id' => 'required|exists:vehicles,id',
            'assigned_driver_id'  => 'nullable|exists:drivers,id',
            'remarks'             => 'nullable|string',
        ]);

        $reservationDate = $requestRecord->reservation_date instanceof \DateTimeInterface 
            ? $requestRecord->reservation_date->format('Y-m-d') 
            : $requestRecord->reservation_date;

        // Check for time conflict
        $conflict = VehicleReservation::where('vehicle_id', $validated['assigned_vehicle_id'])
            ->where('reservation_date', $reservationDate)
            ->whereIn('status', ['pending', 'approved'])
            ->where(function ($query) use ($requestRecord) {
                $query->where('start_time', '<', $requestRecord->end_time)
                      ->where('end_time', '>', $requestRecord->start_time);
            })
            ->exists();

        if ($conflict) {
            return redirect()->back()
                ->with('error', 'Schedule conflict! The selected vehicle is already reserved for this time slot.');
        }

        $purposeText = $requestRecord->purpose_type_label . ': ' . $requestRecord->purpose_description . ' (Destination: ' . $requestRecord->destination . ')';

        $reservation = VehicleReservation::create([
            'vehicle_id'       => $validated['assigned_vehicle_id'],
            'driver_id'        => $validated['assigned_driver_id'] ?? null,
            'requested_by'     => $requestRecord->employee_id,
            'purpose'          => $purposeText,
            'reservation_date' => $reservationDate,
            'start_time'       => $requestRecord->start_time,
            'end_time'         => $requestRecord->end_time,
            'status'           => 'approved',
            'remarks'          => $validated['remarks'] ?? ('Auto-approved from incoming request #' . $requestRecord->id),
        ]);

        $requestRecord->update([
            'status'              => 'approved',
            'assigned_vehicle_id' => $validated['assigned_vehicle_id'],
            'assigned_driver_id'  => $validated['assigned_driver_id'] ?? null,
            'reservation_id'      => $reservation->id,
            'reviewed_by'         => session('user_id'),
        ]);

        return redirect()->route('reservations.index', ['tab' => 'incoming'])
            ->with('success', "Incoming Request #{$requestRecord->id} approved and dispatched! Added to calendar as Reservation #{$reservation->id}.");
    }

    /**
     * Reject incoming employee request.
     */
    public function rejectIncomingRequest(Request $request, EssVehicleRequest $requestRecord)
    {
        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:1000',
        ]);

        $requestRecord->update([
            'status'           => 'rejected',
            'rejection_reason' => $validated['rejection_reason'],
            'reviewed_by'      => session('user_id'),
        ]);

        return redirect()->route('reservations.index', ['tab' => 'incoming'])
            ->with('success', "Incoming Request #{$requestRecord->id} has been marked as rejected.");
    }

    /**
     * Store a sample incoming employee vehicle request for testing/defense demo.
     */
    public function storeSampleIncomingRequest(Request $request)
    {
        $validated = $request->validate([
            'purpose_type'        => 'required|in:meeting,field_visit,airport,inter_branch,errand,emergency',
            'purpose_description' => 'required|string|max:1000',
            'destination'         => 'required|string|max:255',
            'reservation_date'    => 'required|date|after_or_equal:today',
            'start_time'          => 'required',
            'end_time'            => 'required|after:start_time',
            'num_passengers'      => 'required|integer|min:1|max:30',
            'vehicle_preference'  => 'nullable|string|max:100',
            'urgency_level'       => 'required|in:routine,priority',
        ]);

        $user = User::first() ?? User::factory()->create();

        EssVehicleRequest::create([
            'employee_id'         => $user->id,
            'purpose_type'        => $validated['purpose_type'],
            'purpose_description' => $validated['purpose_description'],
            'destination'         => $validated['destination'],
            'reservation_date'    => $validated['reservation_date'],
            'start_time'          => $validated['start_time'],
            'end_time'            => $validated['end_time'],
            'num_passengers'      => $validated['num_passengers'],
            'vehicle_preference'  => $validated['vehicle_preference'] ?? null,
            'urgency_level'       => $validated['urgency_level'],
            'status'              => 'pending',
        ]);

        return redirect()->route('reservations.index', ['tab' => 'incoming'])
            ->with('success', 'Example incoming employee vehicle request created. Ready to demo dispatch approval workflow.');
    }
}
