<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use App\Models\Trip;
use App\Services\RoutingService;
use App\Services\FuelPredictionService;
use Illuminate\Http\Request;

class RouteController extends Controller
{
    protected $routingService;
    protected $fuelPredictionService;

    public function __construct(RoutingService $routingService, FuelPredictionService $fuelPredictionService)
    {
        $this->routingService = $routingService;
        $this->fuelPredictionService = $fuelPredictionService;
    }

    /**
     * Dedicated Route Planning & Optimization Module (Module 6)
     */
    public function index()
    {
        $hubs = $this->routingService->getHubs();
        $vehicles = Vehicle::where('status', 'active')->get();
        $recentTrips = Trip::with(['vehicle', 'driver.user'])->latest()->limit(5)->get();

        return view('routes.index', compact('hubs', 'vehicles', 'recentTrips'));
    }

    /**
     * Compute multi-route optimization matrix (Shortest vs Eco-Route vs Traffic Avoidance)
     */
    public function planRoute(Request $request)
    {
        $validated = $request->validate([
            'start' => 'required|string',
            'end' => 'required|string',
            'vehicle_type' => 'required|string',
        ]);

        $routeData = $this->routingService->planRoute($validated['start'], $validated['end'], $validated['vehicle_type']);
        
        // Add Hirna Vehicle kWh prediction & cost optimization to each route option
        if (!empty($routeData['routes'])) {
            foreach ($routeData['routes'] as &$route) {
                $predictedKwh = $this->fuelPredictionService->predict(
                    (float) $route['distance_km'],
                    (float) ($route['distance_km'] / max(0.1, $route['duration_minutes'] / 60)),
                    $validated['vehicle_type']
                );
                
                $route['predicted_kwh'] = round($predictedKwh, 2);
                $route['charging_cost_php'] = round($predictedKwh * 11.50, 2);
            }
        }

        return response()->json($routeData);
    }
}
