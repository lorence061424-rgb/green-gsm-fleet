<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use App\Models\Driver;
use App\Models\Trip;
use App\Models\FuelLog;
use App\Models\MaintenanceRecord;
use App\Models\PerformanceRecord;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Vehicle counts
        $totalVehicles = Vehicle::count();
        $activeVehicles = Vehicle::where('status', 'active')->count();
        $maintenanceVehicles = Vehicle::where('status', 'maintenance')->count();
        $offlineVehicles = Vehicle::where('status', 'offline')->count();

        // 2. Trip counts
        $totalTrips = Trip::count();
        $completedTrips = Trip::where('status', 'completed')->count();
        $activeTrips = Trip::where('status', 'active')->count();

        // 3. Fuel Logs & Cost
        $totalFuelCost = FuelLog::sum('cost');
        $totalFuelLiters = FuelLog::sum('amount_liters');
        $totalDistance = Trip::where('status', 'completed')->sum('distance_km');
        
        // Average L/100km
        $avgEfficiency = ($totalDistance > 0) ? ($totalFuelLiters / $totalDistance) * 100 : 0;

        // 4. Maintenance Expense
        $totalMaintenanceCost = MaintenanceRecord::sum('cost');

        // 5. Driver Standings
        $topDrivers = Driver::with('user')
            ->orderBy('performance_score', 'desc')
            ->limit(5)
            ->get();

        // 6. Chart 1: Fuel Usage by Vehicle Type
        $fuelByType = FuelLog::join('vehicles', 'fuel_logs.vehicle_id', '=', 'vehicles.id')
            ->select('vehicles.type', DB::raw('SUM(fuel_logs.amount_liters) as total_liters'))
            ->groupBy('vehicles.type')
            ->get();

        // 7. Chart 2: Fuel Cost by Date (Last 7 Logs)
        $costHistory = FuelLog::orderBy('date', 'asc')
            ->limit(10)
            ->select('date', DB::raw('SUM(cost) as daily_cost'), DB::raw('SUM(amount_liters) as daily_liters'))
            ->groupBy('date')
            ->get();

        // 8. Alerts: Vehicles requiring maintenance (based on active status and scheduled maintenance)
        $pendingMaintenance = MaintenanceRecord::where('status', 'scheduled')
            ->where('scheduled_date', '<=', now()->addDays(3))
            ->with('vehicle')
            ->get();

        return view('dashboard', compact(
            'totalVehicles', 'activeVehicles', 'maintenanceVehicles', 'offlineVehicles',
            'totalTrips', 'completedTrips', 'activeTrips',
            'totalFuelCost', 'totalFuelLiters', 'avgEfficiency', 'totalMaintenanceCost',
            'topDrivers', 'fuelByType', 'costHistory', 'pendingMaintenance'
        ));
    }
}
