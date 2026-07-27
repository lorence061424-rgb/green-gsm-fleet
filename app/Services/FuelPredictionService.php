<?php

namespace App\Services;

use App\Models\FuelLog;
use App\Models\Trip;

class FuelPredictionService
{
    // Model parameters: intercept, weight_distance, weight_speed
    // We also use a multiplier based on vehicle type (Sedan=1.0, SUV=1.3, Van=1.6, Hatchback=0.8, Truck=2.2)
    private $weights = [
        'intercept' => 0.5,
        'distance' => 0.08,    // 8L/100km base rate (0.08 liters per km)
        'speed' => 0.0005,     // Speed factor
    ];

    /**
     * Train the linear regression model on historical completed trips that have fuel logs.
     * We'll run a few iterations of Gradient Descent to update the weights based on actual data!
     */
    public function trainModel(): array
    {
        // Get completed trips that have actual_fuel_liters and actual duration/speed
        $trips = Trip::where('status', 'completed')
            ->whereNotNull('actual_fuel_liters')
            ->whereNotNull('actual_duration_minutes')
            ->where('distance_km', '>', 0)
            ->get();

        if ($trips->count() < 5) {
            return [
                'success' => false,
                'message' => 'Insufficient data. Need at least 5 completed trips with fuel logs to train.'
            ];
        }

        // Format data: feature array and target array
        $samples = [];
        foreach ($trips as $trip) {
            $avgSpeed = ($trip->distance_km / ($trip->actual_duration_minutes / 60));
            $typeMultiplier = $this->getVehicleTypeMultiplier($trip->vehicle->type ?? 'Sedan');
            
            // Adjust fuel by the type multiplier to normalize features
            $normalizedFuel = $trip->actual_fuel_liters / $typeMultiplier;

            $samples[] = [
                'distance' => $trip->distance_km,
                'speed' => $avgSpeed,
                'actual_fuel' => $normalizedFuel
            ];
        }

        // Simple Multi-variable Gradient Descent
        $lr = 0.0001; // Learning rate
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

            // Update weights using gradients
            $w0 -= $lr * ($grad_w0 / $n);
            $w1 -= $lr * ($grad_w1 / $n);
            $w2 -= $lr * ($grad_w2 / $n);
        }

        // Save the updated weights
        $this->weights['intercept'] = max(0.01, $w0);
        $this->weights['distance'] = max(0.01, $w1);
        $this->weights['speed'] = max(0.00001, $w2);

        // Store weights in Laravel cache to persist them!
        cache(['ai_fuel_weights' => $this->weights], now()->addYears(1));

        return [
            'success' => true,
            'weights' => $this->weights,
            'message' => 'Model trained successfully on ' . $n . ' trips.'
        ];
    }

    /**
     * Get weights, loading from cache if trained.
     */
    public function getWeights()
    {
        return cache('ai_fuel_weights', $this->weights);
    }

    /**
     * Predict fuel usage.
     */
    public function predict(float $distance, float $avgSpeed, string $vehicleType): float
    {
        $weights = $this->getWeights();
        $typeMultiplier = $this->getVehicleTypeMultiplier($vehicleType);

        // Speed efficiency penalty:
        // Optimal speed for fuel economy is usually 60-80 km/h.
        // Speeds lower than 30 km/h (heavy traffic/idling) or higher than 90 km/h (high drag) consume more.
        $speedFactor = 1.0;
        if ($avgSpeed > 90) {
            $speedFactor = 1.0 + (($avgSpeed - 90) * 0.012); // high drag penalty
        } elseif ($avgSpeed < 30) {
            $speedFactor = 1.0 + ((30 - $avgSpeed) * 0.025); // idling/traffic penalty
        }

        $predicted = ($weights['intercept'] + 
                      ($distance * $weights['distance']) + 
                      ($avgSpeed * $weights['speed'])) * 
                      $typeMultiplier * $speedFactor;

        return round(max(0.1, $predicted), 2);
    }

    /**
     * Get multiplier based on vehicle type
     */
    public function getVehicleTypeMultiplier(string $type): float
    {
        return match ($type) {
            'Hatchback' => 0.8,
            'Sedan' => 1.0,
            'Crossover' => 1.1,
            'SUV' => 1.3,
            'Van' => 1.6,
            'Truck' => 2.2,
            default => 1.0,
        };
    }

    /**
     * Analyze route efficiency and suggest improvements.
     */
    public function analyzeTripEfficiency(float $distance, float $avgSpeed, string $vehicleType, float $actualFuel = null): array
    {
        $predicted = $this->predict($distance, $avgSpeed, $vehicleType);
        $insights = [];

        // Check speed efficiency
        if ($avgSpeed < 30) {
            $insights[] = "Heavy traffic or excessive idling detected. Suggest routes with fewer traffic signals or dispatch outside peak hours.";
        } elseif ($avgSpeed > 90) {
            $insights[] = "High speed detected. Driving above 90 km/h increases aerodynamic drag. Advise driver to maintain speeds between 60-80 km/h for optimal fuel efficiency.";
        } else {
            $insights[] = "Average speed was within the optimal fuel efficiency range (60-80 km/h).";
        }

        // Compare actual vs predicted if actual fuel is logged
        $isInefficient = false;
        if ($actualFuel !== null) {
            $deviation = (($actualFuel - $predicted) / $predicted) * 100;
            if ($deviation > 15) {
                $isInefficient = true;
                $insights[] = sprintf("Actual fuel usage was %.1f%% higher than predicted. This could indicate aggressive driving (harsh acceleration/braking) or maintenance issues.", $deviation);
            } elseif ($deviation < -15) {
                $insights[] = sprintf("Actual fuel usage was %.1f%% lower than predicted. Driver displayed highly efficient driving habits.", abs($deviation));
            }
        }

        return [
            'predicted_fuel' => $predicted,
            'is_inefficient' => $isInefficient,
            'insights' => $insights,
            'recommendations' => [
                "Ensure tires are inflated to the recommended PSI (saves up to 3% fuel).",
                "Minimize engine idling. Idle for more than 10 seconds uses more fuel than restarting the engine.",
                "Smooth accelerations: Avoid stomping on the pedal."
            ]
        ];
    }
}
