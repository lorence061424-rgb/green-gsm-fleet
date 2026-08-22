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

        // 3. Energy Logs & Charging Cost (kWh)
        $totalFuelCost = FuelLog::sum('cost');
        $totalFuelLiters = FuelLog::sum('amount_liters'); // Stores kWh
        $totalDistance = Trip::where('status', 'completed')->sum('distance_km');
        
        // Average kWh/100km
        $avgEfficiency = ($totalDistance > 0) ? ($totalFuelLiters / $totalDistance) * 100 : 0;

        // 4. Maintenance Expense
        $totalMaintenanceCost = MaintenanceRecord::sum('cost');

        // 5. Driver Standings
        $topDrivers = Driver::with('user')
            ->orderBy('performance_score', 'desc')
            ->limit(5)
            ->get();

        // 6. Hirna Vehicle Fleet Breakdown
        $vinfastFleet = Vehicle::orderBy('id', 'asc')->get();

        // 7. Chart 1: Energy Usage by Vehicle Category (Dynamic Database Aggregation)
        $fuelByType = FuelLog::join('vehicles', 'fuel_logs.vehicle_id', '=', 'vehicles.id')
            ->select('vehicles.type', DB::raw('COALESCE(SUM(fuel_logs.amount_liters), 0) as total_liters'))
            ->groupBy('vehicles.type')
            ->get();

        if ($fuelByType->isEmpty()) {
            $fuelByType = Vehicle::select('type', DB::raw('0 as total_liters'))
                ->groupBy('type')
                ->get();
        }

        // 8. Chart 2: Daily Fuel & Energy Expense History (Dynamic Database Aggregation)
        $costHistory = FuelLog::select(
                DB::raw('DATE(date) as log_date'), 
                DB::raw('COALESCE(SUM(cost), 0) as daily_cost'), 
                DB::raw('COALESCE(SUM(amount_liters), 0) as daily_liters')
            )
            ->groupBy(DB::raw('DATE(date)'))
            ->orderBy('log_date', 'asc')
            ->limit(10)
            ->get()
            ->map(function($item) {
                return (object)[
                    'date' => \Carbon\Carbon::parse($item->log_date)->format('M d'),
                    'daily_cost' => (float)$item->daily_cost,
                    'daily_liters' => (float)$item->daily_liters,
                ];
            });

        // 9. Maintenance Alerts
        $pendingMaintenance = MaintenanceRecord::where('status', 'scheduled')
            ->where('scheduled_date', '<=', now()->addDays(3))
            ->with('vehicle')
            ->get();

        return view('dashboard', compact(
            'totalVehicles', 'activeVehicles', 'maintenanceVehicles', 'offlineVehicles',
            'totalTrips', 'completedTrips', 'activeTrips',
            'totalFuelCost', 'totalFuelLiters', 'avgEfficiency', 'totalMaintenanceCost',
            'topDrivers', 'vinfastFleet', 'fuelByType', 'costHistory', 'pendingMaintenance'
        ));
    }

    /**
     * Import dataset (CSV / JSON) into system database
     */
    public function importData(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'import_file' => 'required|file|max:5048',
            'import_type' => 'required|string',
        ]);

        $file = $request->file('import_file');
        $type = $request->get('import_type', 'vehicles');
        $extension = strtolower($file->getClientOriginalExtension());
        $count = 0;

        if ($extension === 'json') {
            $data = json_decode(file_get_contents($file->getRealPath()), true);
            if (is_array($data)) {
                foreach ($data as $item) {
                    if ($type === 'vehicles' && isset($item['license_plate'])) {
                        Vehicle::updateOrCreate(
                            ['license_plate' => $item['license_plate']],
                            [
                                'make' => $item['make'] ?? 'Hirna',
                                'model' => $item['model'] ?? 'Nerio Green',
                                'year' => $item['year'] ?? 2026,
                                'type' => $item['type'] ?? 'Sedan',
                                'fuel_capacity' => $item['fuel_capacity'] ?? 42.0,
                                'status' => $item['status'] ?? 'active',
                            ]
                        );
                        $count++;
                    }
                }
            }
        } else {
            $handle = fopen($file->getRealPath(), 'r');
            $header = fgetcsv($handle);
            while (($row = fgetcsv($handle)) !== false) {
                if (count($row) >= 2) {
                    if ($type === 'vehicles') {
                        Vehicle::updateOrCreate(
                            ['license_plate' => trim($row[1] ?? $row[0])],
                            [
                                'make' => trim($row[0] ?? 'Hirna'),
                                'model' => trim($row[1] ?? 'Nerio Green'),
                                'year' => intval($row[2] ?? 2026),
                                'type' => trim($row[3] ?? 'Sedan'),
                                'fuel_capacity' => floatval($row[4] ?? 42.0),
                                'status' => 'active',
                            ]
                        );
                        $count++;
                    }
                }
            }
            fclose($handle);
        }

        return redirect()->back()->with('success', "Import successful! Processed {$count} records into system database.");
    }
}
