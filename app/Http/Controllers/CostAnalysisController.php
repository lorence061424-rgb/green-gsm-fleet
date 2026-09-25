<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use App\Models\Driver;
use App\Models\Trip;
use App\Models\FuelLog;
use App\Models\MaintenanceRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CostAnalysisController extends Controller
{
    /**
     * Display Transport Cost Analysis & Optimization (TCAO) Dashboard
     */
    public function index(Request $request)
    {
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $preset = $request->get('preset', 'all');

        // Handle Quick Presets if custom date range is not provided
        if (empty($startDate) && empty($endDate) && $preset !== 'all') {
            if ($preset === 'today') {
                $startDate = now()->toDateString();
                $endDate = now()->toDateString();
            } elseif ($preset === 'month') {
                $startDate = now()->startOfMonth()->toDateString();
                $endDate = now()->endOfMonth()->toDateString();
            } elseif ($preset === 'last30') {
                $startDate = now()->subDays(30)->toDateString();
                $endDate = now()->toDateString();
            } elseif ($preset === 'year') {
                $startDate = now()->startOfYear()->toDateString();
                $endDate = now()->endOfYear()->toDateString();
            }
        }

        // Build base queries with optional date filters
        $tripQuery = Trip::where('status', 'completed');
        $fuelQuery = FuelLog::query();
        $maintQuery = MaintenanceRecord::query();

        if ($startDate && $endDate) {
            $tripQuery->where(function($q) use ($startDate, $endDate) {
                $q->whereBetween(DB::raw('DATE(created_at)'), [$startDate, $endDate])
                  ->orWhereBetween(DB::raw('DATE(updated_at)'), [$startDate, $endDate]);
            });
            $fuelQuery->whereBetween('date', [$startDate, $endDate]);
            $maintQuery->whereBetween('scheduled_date', [$startDate, $endDate]);
        }

        // Total Fleet Metrics
        $totalDistance = (float) (clone $tripQuery)->sum('distance_km');
        $totalFuelCost = (float) (clone $fuelQuery)->sum('cost');
        $totalMaintenanceCost = (float) (clone $maintQuery)->sum('cost');
        $totalOperationalCost = $totalFuelCost + $totalMaintenanceCost;

        // Raw float metrics for calculations
        $rawCostPerKm = $totalDistance > 0 ? ($totalOperationalCost / $totalDistance) : 0;
        $rawFuelCostPerKm = $totalDistance > 0 ? ($totalFuelCost / $totalDistance) : 0;
        $rawMaintCostPerKm = $totalDistance > 0 ? ($totalMaintenanceCost / $totalDistance) : 0;

        // Formatted metrics for display
        $costPerKm = number_format($rawCostPerKm, 2);
        $fuelCostPerKm = number_format($rawFuelCostPerKm, 2);
        $maintCostPerKm = number_format($rawMaintCostPerKm, 2);

        // Bulk Aggregations to Eliminate N+1 DB Queries
        $tripDistanceByVehicle = (clone $tripQuery)
            ->select('vehicle_id', DB::raw('SUM(distance_km) as total_distance'))
            ->groupBy('vehicle_id')
            ->pluck('total_distance', 'vehicle_id');

        $fuelCostByVehicle = (clone $fuelQuery)
            ->select('vehicle_id', DB::raw('SUM(cost) as total_cost'), DB::raw('MIN(fuel_type) as sample_fuel_type'))
            ->groupBy('vehicle_id')
            ->get()
            ->keyBy('vehicle_id');

        $maintCostByVehicle = (clone $maintQuery)
            ->select('vehicle_id', DB::raw('SUM(cost) as total_cost'))
            ->groupBy('vehicle_id')
            ->pluck('total_cost', 'vehicle_id');

        // Vehicle Cost Breakdown
        $vehicles = Vehicle::withCount(['trips' => function($q) use ($startDate, $endDate) {
            $q->where('status', 'completed');
            if ($startDate && $endDate) {
                $q->whereBetween(DB::raw('DATE(created_at)'), [$startDate, $endDate]);
            }
        }])->get()->map(function ($vehicle) use ($tripDistanceByVehicle, $fuelCostByVehicle, $maintCostByVehicle) {
            $distance = (float) ($tripDistanceByVehicle[$vehicle->id] ?? 0);
            $fuelLog = $fuelCostByVehicle->get($vehicle->id);
            $fuelCost = (float) ($fuelLog ? $fuelLog->total_cost : 0);
            $maintCost = (float) ($maintCostByVehicle[$vehicle->id] ?? 0);
            $totalCost = $fuelCost + $maintCost;

            $logFuelType = $fuelLog ? $fuelLog->sample_fuel_type : null;
            $fuelType = $logFuelType ?: (str_contains(strtolower($vehicle->model . ' ' . $vehicle->make . ' ' . $vehicle->type), 'ev') || str_contains(strtolower($vehicle->model), 'vinfast') ? 'Electric (kWh)' : 'Gasoline (Liters)');

            return [
                'id' => $vehicle->id,
                'license_plate' => $vehicle->license_plate,
                'model' => $vehicle->make . ' ' . $vehicle->model,
                'type' => $vehicle->type,
                'fuel_type' => $fuelType,
                'trips_completed' => $vehicle->trips_count,
                'distance_km' => round($distance, 1),
                'fuel_cost' => round($fuelCost, 2),
                'maintenance_cost' => round($maintCost, 2),
                'total_cost' => round($totalCost, 2),
                'cost_per_km' => $distance > 0 ? round($totalCost / $distance, 2) : 0,
                'efficiency_score' => $distance > 0 ? max(50, min(100, round(100 - (($totalCost / $distance) * 2), 1))) : 85,
            ];
        })->sortByDesc('total_cost');

        // Bulk Driver Aggregations
        $tripStatsByDriver = (clone $tripQuery)
            ->select('driver_id', DB::raw('SUM(distance_km) as total_distance'), DB::raw('SUM(actual_duration_minutes) as total_duration'))
            ->groupBy('driver_id')
            ->get()
            ->keyBy('driver_id');

        // Driver Efficiency & Cost Analysis
        $drivers = Driver::with('user')->get()->map(function ($driver) use ($tripStatsByDriver) {
            $stats = $tripStatsByDriver->get($driver->id);
            $distance = (float) ($stats ? $stats->total_distance : 0);
            $fuelCost = 0;

            $perfScore = (float) ($driver->performance_score ?? 100);

            return [
                'id' => $driver->id,
                'name' => $driver->user ? $driver->user->name : 'Driver #' . $driver->id,
                'license' => $driver->license_number,
                'rating' => 4.8,
                'safety_score' => $perfScore,
                'total_distance' => round($distance, 1),
                'fuel_cost' => round($fuelCost, 2),
                'cost_per_km' => $distance > 0 ? round($fuelCost / $distance, 2) : 0,
                'efficiency_tier' => $perfScore >= 85 ? 'High Efficiency' : ($perfScore >= 70 ? 'Moderate' : 'Needs Training'),
            ];
        })->sortByDesc('safety_score');

        // AI Cost Optimization Suggestions
        $optimizationInsights = [];

        $threshold = $rawCostPerKm * 1.25;
        $highCostVehicles = $vehicles->filter(function($v) use ($threshold) {
            return $v['cost_per_km'] > $threshold;
        });

        if ($highCostVehicles->count() > 0) {
            $names = $highCostVehicles->pluck('license_plate')->implode(', ');
            $optimizationInsights[] = [
                'type' => 'warning',
                'title' => 'High Operational Cost Flagged',
                'description' => "Vehicles ({$names}) have a cost/km exceeding fleet average by over 25%. Scheduled maintenance or driver retraining is recommended.",
                'potential_savings' => '₱' . number_format($highCostVehicles->sum('total_cost') * 0.15, 2) . ' / month'
            ];
        }

        if ($totalFuelCost > 0) {
            $optimizationInsights[] = [
                'type' => 'success',
                'title' => 'AI Eco-Routing Savings Opportunity',
                'description' => "Enforcing AI eco-routes on long-haul dispatches can reduce total EV energy charging costs by an estimated 11.4%.",
                'potential_savings' => '₱' . number_format($totalFuelCost * 0.114, 2) . ' / month'
            ];
        }

        return view('cost-analysis.index', compact(
            'totalDistance', 'totalFuelCost', 'totalMaintenanceCost', 'totalOperationalCost',
            'costPerKm', 'fuelCostPerKm', 'maintCostPerKm',
            'vehicles', 'drivers', 'optimizationInsights', 'startDate', 'endDate', 'preset'
        ));
    }

    /**
     * Export TCAO Fleet Cost Analysis report as CSV download
     */
    public function exportCsv(Request $request)
    {
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $fileName = 'Hirna_TCAO_Cost_Analysis_' . date('Y-m-d_His') . '.csv';

        $tripQuery = Trip::where('status', 'completed');
        $fuelQuery = FuelLog::query();
        $maintQuery = MaintenanceRecord::query();

        if ($startDate && $endDate) {
            $tripQuery->whereBetween(DB::raw('DATE(created_at)'), [$startDate, $endDate]);
            $fuelQuery->whereBetween('date', [$startDate, $endDate]);
            $maintQuery->whereBetween('scheduled_date', [$startDate, $endDate]);
        }

        $vehicles = Vehicle::all()->map(function ($vehicle) use ($tripQuery, $fuelQuery, $maintQuery) {
            $distance = (float) (clone $tripQuery)->where('vehicle_id', $vehicle->id)->sum('distance_km');
            $fuelCost = (float) (clone $fuelQuery)->where('vehicle_id', $vehicle->id)->sum('cost');
            $maintCost = (float) (clone $maintQuery)->where('vehicle_id', $vehicle->id)->sum('cost');
            $totalCost = $fuelCost + $maintCost;

            $logFuelType = (clone $fuelQuery)->where('vehicle_id', $vehicle->id)->value('fuel_type');
            $fuelType = $logFuelType ?: (str_contains(strtolower($vehicle->model . ' ' . $vehicle->make . ' ' . $vehicle->type), 'ev') || str_contains(strtolower($vehicle->model), 'vinfast') ? 'Electric (kWh)' : 'Gasoline (Liters)');

            return [
                'license_plate' => $vehicle->license_plate,
                'model' => $vehicle->make . ' ' . $vehicle->model,
                'type' => $vehicle->type,
                'fuel_type' => $fuelType,
                'trips_completed' => (clone $tripQuery)->where('vehicle_id', $vehicle->id)->count(),
                'distance_km' => round($distance, 1),
                'fuel_cost' => round($fuelCost, 2),
                'maintenance_cost' => round($maintCost, 2),
                'total_cost' => round($totalCost, 2),
                'cost_per_km' => $distance > 0 ? round($totalCost / $distance, 2) : 0,
                'efficiency_score' => $distance > 0 ? max(50, min(100, round(100 - (($totalCost / $distance) * 2), 1))) : 85,
            ];
        });

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['License Plate', 'Model', 'Vehicle Type', 'Fuel / Energy Source', 'Trips Completed', 'Distance (km)', 'Fuel Cost (PHP)', 'Maintenance Cost (PHP)', 'Total Cost (PHP)', 'Cost per KM (PHP)', 'Efficiency Score (%)'];

        $callback = function() use($vehicles, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($vehicles as $row) {
                fputcsv($file, [
                    $row['license_plate'],
                    $row['model'],
                    $row['type'],
                    $row['fuel_type'],
                    $row['trips_completed'],
                    $row['distance_km'],
                    $row['fuel_cost'],
                    $row['maintenance_cost'],
                    $row['total_cost'],
                    $row['cost_per_km'],
                    $row['efficiency_score'] . '%'
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Render printable PDF view for TCAO report
     */
    public function exportPdf(Request $request)
    {
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        $tripQuery = Trip::where('status', 'completed');
        $fuelQuery = FuelLog::query();
        $maintQuery = MaintenanceRecord::query();

        if ($startDate && $endDate) {
            $tripQuery->whereBetween(DB::raw('DATE(created_at)'), [$startDate, $endDate]);
            $fuelQuery->whereBetween('date', [$startDate, $endDate]);
            $maintQuery->whereBetween('scheduled_date', [$startDate, $endDate]);
        }

        $totalDistance = (float) (clone $tripQuery)->sum('distance_km');
        $totalFuelCost = (float) (clone $fuelQuery)->sum('cost');
        $totalMaintenanceCost = (float) (clone $maintQuery)->sum('cost');
        $totalOperationalCost = $totalFuelCost + $totalMaintenanceCost;

        $costPerKm = number_format($totalDistance > 0 ? ($totalOperationalCost / $totalDistance) : 0, 2);
        $fuelCostPerKm = number_format($totalDistance > 0 ? ($totalFuelCost / $totalDistance) : 0, 2);
        $maintCostPerKm = number_format($totalDistance > 0 ? ($totalMaintenanceCost / $totalDistance) : 0, 2);

        $vehicles = Vehicle::all()->map(function ($vehicle) use ($tripQuery, $fuelQuery, $maintQuery) {
            $distance = (float) (clone $tripQuery)->where('vehicle_id', $vehicle->id)->sum('distance_km');
            $fuelCost = (float) (clone $fuelQuery)->where('vehicle_id', $vehicle->id)->sum('cost');
            $maintCost = (float) (clone $maintQuery)->where('vehicle_id', $vehicle->id)->sum('cost');
            $totalCost = $fuelCost + $maintCost;

            $logFuelType = (clone $fuelQuery)->where('vehicle_id', $vehicle->id)->value('fuel_type');
            $fuelType = $logFuelType ?: (str_contains(strtolower($vehicle->model . ' ' . $vehicle->make . ' ' . $vehicle->type), 'ev') || str_contains(strtolower($vehicle->model), 'vinfast') ? 'Electric (kWh)' : 'Gasoline (Liters)');

            return [
                'license_plate' => $vehicle->license_plate,
                'model' => $vehicle->make . ' ' . $vehicle->model,
                'fuel_type' => $fuelType,
                'distance_km' => round($distance, 1),
                'fuel_cost' => round($fuelCost, 2),
                'maintenance_cost' => round($maintCost, 2),
                'total_cost' => round($totalCost, 2),
                'cost_per_km' => $distance > 0 ? round($totalCost / $distance, 2) : 0,
            ];
        });

        return view('cost-analysis.pdf', compact(
            'totalDistance', 'totalFuelCost', 'totalMaintenanceCost', 'totalOperationalCost',
            'costPerKm', 'fuelCostPerKm', 'maintCostPerKm', 'vehicles', 'startDate', 'endDate'
        ));
    }

}

