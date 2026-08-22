<?php

namespace App\Services;

use App\Models\FuelLog;
use App\Models\Trip;

class FuelPredictionService
{
    // Model parameters for VinFast EV Battery Energy (kWh)
    private $weights = [
        'intercept' => 0.5,
        'distance' => 0.12,    // ~12 kWh per 100km base energy rate
        'speed' => 0.0005,     // Speed factor
    ];

    /**
     * Train the linear regression model on historical completed trips that have fuel/kWh logs.
     */
    public function trainModel(): array
    {
        $trips = Trip::where('status', 'completed')
            ->whereNotNull('actual_fuel_liters')
            ->whereNotNull('actual_duration_minutes')
            ->where('distance_km', '>', 0)
            ->get();

        if ($trips->count() < 5) {
            return [
                'success' => false,
                'message' => 'Insufficient data. Need at least 5 completed trips with energy logs to train.'
            ];
        }

        $samples = [];
        foreach ($trips as $trip) {
            $avgSpeed = ($trip->distance_km / ($trip->actual_duration_minutes / 60));
            $typeMultiplier = $this->getVehicleTypeMultiplier($trip->vehicle->type ?? 'Sedan');
            
            $normalizedFuel = $trip->actual_fuel_liters / $typeMultiplier;

            $samples[] = [
                'distance' => $trip->distance_km,
                'speed' => $avgSpeed,
                'actual_fuel' => $normalizedFuel
            ];
        }

        $lr = 0.0001;
        $epochs = 1500;

        $w0 = $this->weights['intercept'];
        $w1 = $this->weights['distance'];
        $w2 = $this->weights['speed'];

        $n = count($samples);

        for ($i = 0; $i < $epochs; $i++) {
            $grad_w0 = 0;
            $grad_w1 = 0;
            $grad_w2 = 0;

            foreach ($samples as $sample) {
                $pred = $w0 + ($w1 * $sample['distance']) + ($w2 * $sample['speed']);
                $error = $pred - $sample['actual_fuel'];

                $grad_w0 += $error;
                $grad_w1 += $error * $sample['distance'];
                $grad_w2 += $error * $sample['speed'];
            }

            $w0 -= $lr * ($grad_w0 / $n);
            $w1 -= $lr * ($grad_w1 / $n);
            $w2 -= $lr * ($grad_w2 / $n);
        }

        $this->weights['intercept'] = max(0.01, $w0);
        $this->weights['distance'] = max(0.01, $w1);
        $this->weights['speed'] = max(0.00001, $w2);

        cache(['ai_fuel_weights' => $this->weights], now()->addYears(1));

        return [
            'success' => true,
            'weights' => $this->weights,
            'message' => 'AI EV Energy Model trained successfully on ' . $n . ' completed rides.'
        ];
    }

    public function getWeights()
    {
        return cache('ai_fuel_weights', $this->weights);
    }

    /**
     * Predict fuel/energy usage based on fuel type: Gasoline (Gas), Diesel, or Electric (EV).
     */
    public function predict(float $distance, float $avgSpeed, string $vehicleType, string $fuelType = 'gasoline'): float
    {
        $weights = $this->getWeights();
        $typeMultiplier = $this->getVehicleTypeMultiplier($vehicleType);
        $fuelTypeMultiplier = $this->getFuelTypeMultiplier($fuelType);

        $speedFactor = 1.0;
        if ($avgSpeed > 90) {
            $speedFactor = 1.0 + (($avgSpeed - 90) * 0.012);
        } elseif ($avgSpeed < 30) {
            $speedFactor = 1.0 + ((30 - $avgSpeed) * 0.025);
        }

        $predicted = ($weights['intercept'] + 
                      ($distance * $weights['distance']) + 
                      ($avgSpeed * $weights['speed'])) * 
                      $typeMultiplier * $fuelTypeMultiplier * $speedFactor;

        return round(max(0.1, $predicted), 2);
    }

    /**
     * Get multiplier based on fuel type: Gasoline (Gas), Diesel, Electric
     */
    public function getFuelTypeMultiplier(string $fuelType): float
    {
        return match (strtolower($fuelType)) {
            'gasoline', 'gas', 'unleaded', 'premium' => 0.85, // Gas Liters
            'diesel' => 0.72, // Diesel Liters
            'electric', 'ev', 'battery' => 1.0, // Electric kWh
            default => 0.85, // Default to Gas
        };
    }

    /**
     * Get multiplier based on Hirna vehicle model category
     */
    public function getVehicleTypeMultiplier(string $type): float
    {
        return match ($type) {
            'Tricycle', 'Trike', 'Hirna Traysikel', 'E-Trike' => 0.45, // 3-Wheeler Trike (High Mileage Efficiency)
            'VF 5', 'Hatchback', 'Compact', 'Taxi Sedan' => 0.85,
            'Nerio Green', 'Sedan', 'Standard Taxi' => 1.0,
            'VF e34', 'Crossover', 'Premium Taxi' => 1.1,
            'VF 8', 'SUV', 'MPV Taxi' => 1.35,
            'VF 9', 'Van', 'Truck', 'Fleet Bus' => 1.6,
            default => 1.0,
        };
    }

    /**
     * Analyze route efficiency and calculate predicted fuel/energy quantity and price forecast.
     */
    public function analyzeTripEfficiency(float $distance, float $avgSpeed, string $vehicleType, float $actualFuel = null, string $fuelType = 'gasoline', float $unitPrice = null): array
    {
        $predicted = $this->predict($distance, $avgSpeed, $vehicleType, $fuelType);

        if (!$unitPrice) {
            $unitPrice = match (strtolower($fuelType)) {
                'gasoline', 'gas', 'unleaded' => 64.50, // ₱64.50/L
                'diesel' => 58.00, // ₱58.00/L
                'electric', 'ev' => 11.50, // ₱11.50/kWh
                default => 64.50,
            };
        }

        $unitLabel = match (strtolower($fuelType)) {
            'gasoline', 'gas', 'unleaded' => 'Liters (Gas)',
            'diesel' => 'Liters (Diesel)',
            'electric', 'ev' => 'kWh (EV)',
            default => 'Liters (Gas)',
        };

        $predictedCost = round($predicted * $unitPrice, 2);
        $insights = [];

        if (in_array(strtolower($fuelType), ['gasoline', 'gas', 'unleaded', 'diesel'])) {
            $insights[] = sprintf("Gasoline/Engine fuel burn predicted at %.2f %s (@ ₱%.2f/Liter). Total Cost: ₱%.2f.", $predicted, $unitLabel, $unitPrice, $predictedCost);
        } else {
            $insights[] = sprintf("EV Battery energy consumption predicted at %.2f kWh (@ ₱%.2f/kWh). Total Cost: ₱%.2f.", $predicted, $unitPrice, $predictedCost);
        }

        if ($avgSpeed < 30) {
            $insights[] = "Heavy traffic or excessive idling detected. Recommend eco-routing to bypass congested corridors.";
        } elseif ($avgSpeed > 90) {
            $insights[] = "High speed (>90 km/h) increases aerodynamic drag. Advise driver to cruise between 60-80 km/h for peak efficiency.";
        } else {
            $insights[] = "Cruising speed was within optimal fuel efficiency range (60-80 km/h).";
        }

        $isInefficient = false;
        if ($actualFuel !== null) {
            $deviation = (($actualFuel - $predicted) / $predicted) * 100;
            if ($deviation > 15) {
                $isInefficient = true;
                $insights[] = sprintf("Actual fuel usage was %.1f%% higher than predicted. Check for aggressive acceleration or HVAC load.", $deviation);
            } elseif ($deviation < -15) {
                $insights[] = sprintf("Actual fuel usage was %.1f%% lower than predicted. Driver displayed optimal eco-driving habits.", abs($deviation));
            }
        }

        return [
            'predicted_fuel' => $predicted,
            'predicted_cost' => $predictedCost,
            'unit_price' => $unitPrice,
            'fuel_unit' => $unitLabel,
            'fuel_type' => ucfirst($fuelType),
            'is_inefficient' => $isInefficient,
            'insights' => $insights,
            'recommendations' => [
                "Maintain steady cruising speeds to optimize engine fuel combustion / battery pack efficiency.",
                "Ensure tire pressure is maintained at recommended PSI levels to reduce rolling resistance.",
                "Avoid unnecessary engine idling during passenger waiting periods."
            ]
        ];
    }
}
