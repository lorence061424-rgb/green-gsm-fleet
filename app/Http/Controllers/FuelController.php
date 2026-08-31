<?php

namespace App\Http\Controllers;

use App\Models\FuelLog;
use App\Models\Vehicle;
use App\Models\Driver;
use App\Services\FuelPredictionService;
use Illuminate\Http\Request;

class FuelController extends Controller
{
    protected $fuelPredictionService;

    public function __construct(FuelPredictionService $fuelPredictionService)
    {
        $this->fuelPredictionService = $fuelPredictionService;
    }

    public function index()
    {
        $logs = FuelLog::with(['vehicle', 'trip.driver.user'])->latest()->paginate(8);
        $vehicles = Vehicle::all();
        $drivers = Driver::with('user')->get();
        $weights = $this->fuelPredictionService->getWeights();

        return view('fuel.index', ['logs' => $logs, 'fuelLogs' => $logs, 'vehicles' => $vehicles, 'drivers' => $drivers, 'weights' => $weights]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'date' => 'required|date',
            'amount_liters' => 'required|numeric|min:0.1',
            'cost' => 'required|numeric|min:1',
            'odometer_reading' => 'required|numeric|min:1',
            'fuel_type' => 'required|string',
        ]);

        FuelLog::create($validated);

        return redirect()->back()->with('success', 'Fuel transaction logged successfully.');
    }

    /**
     * Endpoint for interactive AI Prediction Test Panel on UI.
     */
    public function predict(Request $request)
    {
        $validated = $request->validate([
            'distance' => 'required|numeric|min:0.1',
            'speed' => 'required|numeric|min:1',
            'vehicle_type' => 'required|string',
            'fuel_type' => 'nullable|string',
            'unit_price' => 'nullable|numeric|min:0',
            'actual_fuel' => 'nullable|numeric|min:0',
        ]);

        $actualFuel = $request->filled('actual_fuel') ? (float)$validated['actual_fuel'] : null;
        $fuelType = $request->get('fuel_type', 'gasoline');
        $unitPrice = $request->filled('unit_price') ? (float)$validated['unit_price'] : null;

        $analysis = $this->fuelPredictionService->analyzeTripEfficiency(
            (float)$validated['distance'],
            (float)$validated['speed'],
            $validated['vehicle_type'],
            $actualFuel,
            $fuelType,
            $unitPrice
        );

        return response()->json($analysis);
    }

    /**
     * Train the model with Gradient Descent using actual trips database data.
     */
    public function train()
    {
        $result = $this->fuelPredictionService->trainModel();
        
        if ($result['success']) {
            return redirect()->back()->with('success', $result['message']);
        }
        
        return redirect()->back()->with('error', $result['message']);
    }
}
