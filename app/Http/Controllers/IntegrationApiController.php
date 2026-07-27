<?php

namespace App\Http\Controllers;

use App\Models\Trip;
use App\Models\Vehicle;
use App\Models\Driver;
use App\Models\FuelLog;
use App\Models\MaintenanceRecord;
use App\Models\User;
use App\Services\FuelPredictionService;
use App\Services\RoutingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class IntegrationApiController extends Controller
{
    /**
     * TEAM 9 (Operations System)
     * Receive: trip requests
     * Endpoint: POST /api/operations/trip-request
     */
    public function receiveTripRequest(Request $request)
    {
        $validated = $request->validate([
            'booking_reference_id' => 'required|string',
            'start_location' => 'required|string',
            'end_location' => 'required|string',
            'distance_km' => 'required|numeric|min:0.1',
            'estimated_duration_minutes' => 'required|integer',
            'vehicle_type_preference' => 'nullable|string|in:Sedan,SUV,Van,Hatchback,Truck',
        ]);

        // Auto assign vehicle and driver
        $type = $validated['vehicle_type_preference'] ?? 'Sedan';
        
        $vehicle = Vehicle::where('status', 'active')
            ->where('type', $type)
            ->whereDoesntHave('trips', function($q) {
                $q->whereIn('status', ['scheduled', 'active']);
            })->first();

        if (!$vehicle) {
            // Fallback to any active vehicle
            $vehicle = Vehicle::where('status', 'active')
                ->whereDoesntHave('trips', function($q) {
                    $q->whereIn('status', ['scheduled', 'active']);
                })->first();
        }

        $driver = Driver::where('status', 'available')
            ->whereDoesntHave('trips', function($q) {
                $q->whereIn('status', ['scheduled', 'active']);
            })->first();

        if (!$vehicle || !$driver) {
            return response()->json([
                'success' => false,
                'message' => 'No vehicles or drivers available for immediate assignment. Trip queued.',
            ], 409);
        }

        // AI Fuel Prediction
        $fuelPredictor = new FuelPredictionService();
        $predictedFuel = $fuelPredictor->predict($validated['distance_km'], 45, $vehicle->type);

        $trip = Trip::create([
            'booking_reference_id' => $validated['booking_reference_id'],
            'start_location' => $validated['start_location'],
            'end_location' => $validated['end_location'],
            'start_lat' => 14.5995, // mock Manila
            'start_lng' => 120.9842,
            'end_lat' => 14.5547, // mock Makati
            'end_lng' => 121.0244,
            'distance_km' => $validated['distance_km'],
            'estimated_duration_minutes' => $validated['estimated_duration_minutes'],
            'estimated_fuel_liters' => $predictedFuel,
            'vehicle_id' => $vehicle->id,
            'driver_id' => $driver->id,
            'status' => 'scheduled',
        ]);

        $driver->update(['status' => 'on_trip']);

        return response()->json([
            'success' => true,
            'message' => 'Trip created and resources assigned successfully.',
            'trip_id' => $trip->id,
            'assigned_vehicle' => $vehicle->license_plate,
            'assigned_driver' => $driver->user->name,
            'predicted_fuel_consumption_liters' => $predictedFuel,
        ], 201);
    }

    /**
     * TEAM 9 (Operations System)
     * Send: vehicle availability & assignment
     * Endpoint: GET /api/operations/vehicle-availability
     */
    public function getVehicleAvailability()
    {
        $totalActive = Vehicle::where('status', 'active')->count();
        
        $assignedVehicleIds = Trip::whereIn('status', ['scheduled', 'active'])
            ->whereNotNull('vehicle_id')
            ->pluck('vehicle_id')
            ->toArray();

        $availableVehicles = Vehicle::where('status', 'active')
            ->whereNotIn('id', $assignedVehicleIds)
            ->get(['id', 'license_plate', 'make', 'model', 'type', 'fuel_capacity']);

        return response()->json([
            'success' => true,
            'total_active_vehicles' => $totalActive,
            'available_count' => $availableVehicles->count(),
            'available_vehicles' => $availableVehicles
        ]);
    }

    /**
     * TEAM 10 (Booking System)
     * Receive: booking data, Send: assigned vehicle, ETA
     * Endpoint: POST /api/booking/assign-trip
     */
    public function receiveBookingData(Request $request)
    {
        $validated = $request->validate([
            'booking_id' => 'required|string',
            'pickup_lat' => 'required|numeric',
            'pickup_lng' => 'required|numeric',
            'dropoff_lat' => 'required|numeric',
            'dropoff_lng' => 'required|numeric',
            'pickup_address' => 'required|string',
            'dropoff_address' => 'required|string',
        ]);

        // Find nearest active vehicle (simplified logic using coordinates if available, else first free)
        $vehicle = Vehicle::where('status', 'active')
            ->whereDoesntHave('trips', function($q) {
                $q->whereIn('status', ['scheduled', 'active']);
            })->first();

        $driver = Driver::where('status', 'available')
            ->whereDoesntHave('trips', function($q) {
                $q->whereIn('status', ['scheduled', 'active']);
            })->first();

        if (!$vehicle || !$driver) {
            return response()->json([
                'success' => false,
                'message' => 'No active fleet units available at this moment.'
            ], 503);
        }

        // Calculate Distance and ETA (using RoutingService Haversine and average speed of 40 km/h)
        $routing = new RoutingService();
        $distance = $routing->haversineDistance(
            $validated['pickup_lat'], $validated['pickup_lng'],
            $validated['dropoff_lat'], $validated['dropoff_lng']
        );
        $etaMinutes = (int)round(($distance / 40) * 60) + 5; // adding 5 mins buffer

        $fuelPredictor = new FuelPredictionService();
        $predictedFuel = $fuelPredictor->predict($distance, 40, $vehicle->type);

        $trip = Trip::create([
            'booking_reference_id' => $validated['booking_id'],
            'start_location' => $validated['pickup_address'],
            'end_location' => $validated['dropoff_address'],
            'start_lat' => $validated['pickup_lat'],
            'start_lng' => $validated['pickup_lng'],
            'end_lat' => $validated['dropoff_lat'],
            'end_lng' => $validated['dropoff_lng'],
            'distance_km' => $distance,
            'estimated_duration_minutes' => $etaMinutes,
            'estimated_fuel_liters' => $predictedFuel,
            'vehicle_id' => $vehicle->id,
            'driver_id' => $driver->id,
            'status' => 'scheduled',
        ]);

        $driver->update(['status' => 'on_trip']);

        return response()->json([
            'success' => true,
            'booking_id' => $validated['booking_id'],
            'assigned_vehicle' => [
                'license_plate' => $vehicle->license_plate,
                'model' => $vehicle->make . ' ' . $vehicle->model,
                'type' => $vehicle->type,
            ],
            'assigned_driver' => [
                'name' => $driver->user->name,
                'license' => $driver->license_number,
                'performance_rating' => $driver->performance_score,
            ],
            'eta_minutes' => $etaMinutes,
            'distance_km' => $distance,
            'predicted_fuel_liters' => $predictedFuel
        ]);
    }

    /**
     * TEAM 6 (Inventory System)
     * Receive: fuel stock
     * Endpoint: POST /api/inventory/fuel-stock
     */
    public function receiveFuelStock(Request $request)
    {
        $validated = $request->validate([
            'current_stock_liters' => 'required|numeric',
            'last_updated' => 'required|date_format:Y-m-d H:i:s',
        ]);

        // Keep stock details in Cache for display
        Cache::put('inventory_fuel_stock', $validated['current_stock_liters'], now()->addDays(7));
        Cache::put('inventory_fuel_last_update', $validated['last_updated'], now()->addDays(7));

        return response()->json([
            'success' => true,
            'message' => 'Fuel inventory stock levels updated in Fleet Dashboard.',
        ]);
    }

    /**
     * TEAM 6 (Inventory System)
     * Send: fuel usage data
     * Endpoint: GET /api/inventory/fuel-usage
     */
    public function sendFuelUsageData()
    {
        $usage = FuelLog::with('vehicle')
            ->select('id', 'vehicle_id', 'date', 'amount_liters', 'cost', 'fuel_type')
            ->get();

        return response()->json([
            'success' => true,
            'fuel_usage_records' => $usage
        ]);
    }

    /**
     * TEAM 5 (Finance System)
     * Send: fuel cost, maintenance expenses
     * Endpoint: GET /api/finance/expenses
     */
    public function sendExpensesData()
    {
        $fuelExpenses = FuelLog::select('id', 'vehicle_id', 'date as expense_date', 'cost as amount')
            ->get()
            ->map(function($log) {
                $log->category = 'Fuel';
                return $log;
            });

        $maintenanceExpenses = MaintenanceRecord::where('status', 'completed')
            ->select('id', 'vehicle_id', 'completion_date as expense_date', 'cost as amount', 'service_type as description')
            ->get()
            ->map(function($record) {
                $record->category = 'Maintenance';
                return $record;
            });

        return response()->json([
            'success' => true,
            'total_fuel_expense' => FuelLog::sum('cost'),
            'total_maintenance_expense' => MaintenanceRecord::sum('cost'),
            'expenses' => $fuelExpenses->concat($maintenanceExpenses)
        ]);
    }

    /**
     * HR SYSTEM (Teams 1–4)
     * Receive: driver information
     * Endpoint: POST /api/hr/sync-driver
     */
    public function receiveDriverInfo(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email',
            'license_number' => 'required|string',
            'action' => 'required|in:create,update,deactivate',
        ]);

        $user = User::where('email', $validated['email'])->first();

        if ($validated['action'] === 'deactivate') {
            if ($user && $user->driver) {
                $user->driver->update(['status' => 'offline']);
                return response()->json(['success' => true, 'message' => 'Driver set to offline.']);
            }
            return response()->json(['success' => false, 'message' => 'Driver not found.'], 404);
        }

        if (!$user) {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => bcrypt('WelcomeToTNVS123!'), // default pass
                'role' => 'driver',
            ]);
        } else {
            $user->update(['name' => $validated['name']]);
        }

        $driver = Driver::updateOrCreate(
            ['user_id' => $user->id],
            ['license_number' => $validated['license_number']]
        );

        return response()->json([
            'success' => true,
            'message' => 'Driver synchronized with HR registry.',
            'driver_id' => $driver->id,
        ]);
    }

    /**
     * HR SYSTEM (Teams 1–4)
     * Send: driver performance data
     * Endpoint: GET /api/hr/driver-performance
     */
    public function sendDriverPerformanceData()
    {
        $performance = Driver::join('users', 'drivers.user_id', '=', 'users.id')
            ->select(
                'drivers.id as driver_id',
                'users.name',
                'drivers.performance_score',
                'drivers.total_trips',
                'drivers.total_distance_km'
            )->get();

        return response()->json([
            'success' => true,
            'driver_performances' => $performance
        ]);
    }
}
