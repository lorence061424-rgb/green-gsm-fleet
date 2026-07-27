<?php

namespace App\Http\Controllers;

use App\Models\FuelLog;
use App\Models\Vehicle;
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
        $fuelLogs = FuelLog::with(['vehicle', 'trip'])->latest()->get();
        $vehicles = Vehicle::all();
        $weights = $this->fuelPredictionService->getWeights();

        return view('fuel.index', compact('fuelLogs', 'vehicles', 'weights'));
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
            'actual_fuel' => 'nullable|numeric|min:0',
        ]);

        $actualFuel = $request->filled('actual_fuel') ? (float)$validated['actual_fuel'] : null;

        $analysis = $this->fuelPredictionService->analyzeTripEfficiency(
            (float)$validated['distance'],
            (float)$validated['speed'],
            $validated['vehicle_type'],
            $actualFuel
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
