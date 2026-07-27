<?php

namespace App\Http\Controllers;

use App\Models\Trip;
use App\Models\TripLog;
use App\Models\Vehicle;
use App\Models\Driver;
use App\Models\FuelLog;
use App\Models\PerformanceRecord;
use App\Services\FuelPredictionService;
use App\Services\RoutingService;
use Illuminate\Http\Request;

class TripController extends Controller
{
    protected $routingService;
    protected $fuelPredictionService;

    public function __construct(RoutingService $routingService, FuelPredictionService $fuelPredictionService)
    {
        $this->routingService = $routingService;
        $this->fuelPredictionService = $fuelPredictionService;
    }

    public function index()
    {
        $trips = Trip::with(['driver.user', 'vehicle'])->latest()->get();
        $vehicles = Vehicle::where('status', 'active')->get();
        $drivers = Driver::where('status', 'available')->with('user')->get();
        $hubs = $this->routingService->getHubs();

        return view('trips.index', compact('trips', 'vehicles', 'drivers', 'hubs'));
    }

    /**
     * Preview route options, traffic delays, and predict fuel usage before scheduling.
     */
    public function planRoutePreview(Request $request)
    {
        $validated = $request->validate([
            'start' => 'required|string',
            'end' => 'required|string',
            'vehicle_type' => 'required|string',
        ]);

        $routeDetails = $this->routingService->planRoute($validated['start'], $validated['end'], $validated['vehicle_type']);
        return response()->json($routeDetails);
    }

    /**
     * Create and schedule a trip with option for auto-assigning vehicle and driver.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'start_location' => 'required|string',
            'end_location' => 'required|string',
            'distance_km' => 'required|numeric|min:0.1',
            'estimated_duration_minutes' => 'required|integer|min:1',
            'estimated_fuel_liters' => 'required|numeric',
            'vehicle_id' => 'nullable|exists:vehicles,id',
            'driver_id' => 'nullable|exists:drivers,id',
            'auto_assign' => 'nullable|boolean',
        ]);

        $startCoords = $this->routingService->getHubs()[$validated['start_location']] ?? ['lat' => 14.5995, 'lng' => 120.9842];
        $endCoords = $this->routingService->getHubs()[$validated['end_location']] ?? ['lat' => 14.5547, 'lng' => 121.0244];

        $vehicleId = $validated['vehicle_id'];
        $driverId = $validated['driver_id'];

        // Auto assignment logic
        if ($request->has('auto_assign') && $validated['auto_assign']) {
            // Find first active and available vehicle
            $vehicle = Vehicle::where('status', 'active')
                ->whereDoesntHave('trips', function($q) {
                    $q->whereIn('status', ['scheduled', 'active']);
                })->first();

            // Find first available driver
            $driver = Driver::where('status', 'available')
                ->whereDoesntHave('trips', function($q) {
                    $q->whereIn('status', ['scheduled', 'active']);
                })->first();

            if (!$vehicle || !$driver) {
                return redirect()->back()->with('error', 'Auto-assignment failed: No available vehicles or drivers.');
            }

            $vehicleId = $vehicle->id;
            $driverId = $driver->id;
        }

        $trip = Trip::create([
            'start_location' => $validated['start_location'],
            'end_location' => $validated['end_location'],
            'start_lat' => $startCoords['lat'],
            'start_lng' => $startCoords['lng'],
            'end_lat' => $endCoords['lat'],
            'end_lng' => $endCoords['lng'],
            'distance_km' => $validated['distance_km'],
            'estimated_duration_minutes' => $validated['estimated_duration_minutes'],
            'estimated_fuel_liters' => $validated['estimated_fuel_liters'],
            'vehicle_id' => $vehicleId,
            'driver_id' => $driverId,
            'status' => 'scheduled',
        ]);

        // Mark driver and vehicle as assigned/on_trip
        if ($driverId) {
            Driver::find($driverId)->update(['status' => 'on_trip']);
        }

        return redirect()->route('trips.index')->with('success', 'Trip scheduled successfully. Trip ID: ' . $trip->id);
    }

    /**
     * Start the trip (make it active)
     */
    public function startTrip(Trip $trip)
    {
        $trip->update([
            'status' => 'active',
            'start_time' => now(),
        ]);

        return redirect()->back()->with('success', 'Trip started. Live tracking simulation active.');
    }

    /**
     * Simulate real-time GPS telemetry updates, speeding behavior, and harsh braking.
     */
    public function simulateTelemetry(Request $request, Trip $trip)
    {
        if ($trip->status !== 'active') {
            return response()->json(['error' => 'Trip is not active'], 400);
        }

        $validated = $request->validate([
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
            'speed' => 'required|numeric',
            'idle_seconds' => 'nullable|integer',
            'is_harsh_braking' => 'nullable|boolean',
        ]);

        // Log coordinate breadcrumb
        $log = TripLog::create([
            'trip_id' => $trip->id,
            'lat' => $validated['lat'],
            'lng' => $validated['lng'],
            'speed_kmh' => $validated['speed'],
            'idle_time_seconds' => $validated['idle_seconds'] ?? 0,
        ]);

        // Check for safety violations
        $speeding = $validated['speed'] > 80 ? 1 : 0;
        $harshBraking = ($request->has('is_harsh_braking') && $validated['is_harsh_braking']) ? 1 : 0;
        $idleMin = ($validated['idle_seconds'] ?? 0) / 60;

        // Update or Create Performance Record
        $performance = PerformanceRecord::firstOrCreate(
            ['trip_id' => $trip->id, 'driver_id' => $trip->driver_id]
        );

        $performance->increment('speeding_events', $speeding);
        $performance->increment('harsh_braking_events', $harshBraking);
        $performance->increment('idle_minutes', $idleMin);

        // Calculate Safety Score: Start at 100, deduct points per violation
        $penalty = ($performance->speeding_events * 5) + ($performance->harsh_braking_events * 10) + (int)($performance->idle_minutes * 2);
        $score = max(0, 100 - $penalty);
        $performance->update(['safety_score' => $score]);

        // Live coordinate update for the vehicle
        if ($trip->vehicle) {
            $trip->vehicle->update([
                'current_gps_lat' => $validated['lat'],
                'current_gps_lng' => $validated['lng'],
            ]);
        }

        return response()->json([
            'status' => 'success',
            'log' => $log,
            'violations' => [
                'speeding' => $speeding,
                'harsh_braking' => $harshBraking,
            ],
            'current_safety_score' => $score
        ]);
    }

    /**
     * Complete the trip and calculate final fuel, duration, and driver metrics.
     */
    public function completeTrip(Request $request, Trip $trip)
    {
        $validated = $request->validate([
            'actual_fuel_liters' => 'required|numeric|min:0.1',
            'actual_duration_minutes' => 'required|integer|min:1',
        ]);

        // Calculate average speed
        $avgSpeed = $trip->distance_km / ($validated['actual_duration_minutes'] / 60);

        $trip->update([
            'status' => 'completed',
            'end_time' => now(),
            'actual_fuel_liters' => $validated['actual_fuel_liters'],
            'actual_duration_minutes' => $validated['actual_duration_minutes'],
        ]);

        // Release driver and vehicle
        if ($trip->driver) {
            // Update driver cumulative metrics
            $perf = PerformanceRecord::where('trip_id', $trip->id)->first();
            $tripScore = $perf ? $perf->safety_score : 100;

            $driver = $trip->driver;
            $newTotalTrips = $driver->total_trips + 1;
            $newTotalDistance = $driver->total_distance_km + $trip->distance_km;
            // Running average for safety score
            $newScore = (($driver->performance_score * $driver->total_trips) + $tripScore) / $newTotalTrips;

            $driver->update([
                'status' => 'available',
                'total_trips' => $newTotalTrips,
                'total_distance_km' => $newTotalDistance,
                'performance_score' => round($newScore, 2)
            ]);
        }

        // Add to fuel logs for reporting & AI training
        FuelLog::create([
            'vehicle_id' => $trip->vehicle_id,
            'trip_id' => $trip->id,
            'date' => date('Y-m-d'),
            'amount_liters' => $validated['actual_fuel_liters'],
            'cost' => $validated['actual_fuel_liters'] * 60, // Assumed cost of 60 PHP per liter
            'odometer_reading' => ($trip->vehicle->year * 100) + $trip->distance_km, // dummy mileage increase
        ]);

        return redirect()->route('trips.index')->with('success', 'Trip completed successfully! Fuel usage and driver stats logged.');
    }
}
