<?php

namespace App\Services;

class RoutingService
{
    // Pre-configured hubs with coordinates in Metro Manila (ideal for TNVS capstone)
    private $hubs = [
        'Manila' => ['lat' => 14.5995, 'lng' => 120.9842],
        'Makati' => ['lat' => 14.5547, 'lng' => 121.0244],
        'BGC' => ['lat' => 14.5492, 'lng' => 121.0558],
        'Quezon City' => ['lat' => 14.6760, 'lng' => 121.0437],
        'Pasay' => ['lat' => 14.5378, 'lng' => 120.9993],
        'NAIA' => ['lat' => 14.5204, 'lng' => 121.0134],
        'Alabang' => ['lat' => 14.4172, 'lng' => 121.0408],
        'Ortigas' => ['lat' => 14.5869, 'lng' => 121.0614],
    ];

    public function getHubs(): array
    {
        return $this->hubs;
    }

    /**
     * Compute distance between any two lat/lng coordinates using Haversine formula.
     */
    public function haversineDistance(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $earthRadius = 6371; // km
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLng / 2) * sin($dLng / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return round($earthRadius * $c, 2);
    }

    /**
     * Helper to resolve hub coordinates matching exact or partial key names.
     */
    public function resolveHubCoords(string $name): array
    {
        foreach ($this->hubs as $key => $coords) {
            if ($name === $key || str_contains(strtolower($name), strtolower($key)) || str_contains(strtolower($key), strtolower($name))) {
                return $coords;
            }
        }
        return ['lat' => 14.5995, 'lng' => 120.9842];
    }

    /**
     * Generate routes/alternatives between start and destination hubs, 
     * simulating traffic and recommending the most fuel-efficient option.
     */
    public function planRoute(string $start, string $end, string $vehicleType): array
    {
        $startCoords = $this->resolveHubCoords($start);
        $endCoords = $this->resolveHubCoords($end);

        $lat1 = $startCoords['lat'];
        $lng1 = $startCoords['lng'];
        $lat2 = $endCoords['lat'];
        $lng2 = $endCoords['lng'];

        $distance = $this->haversineDistance($lat1, $lng1, $lat2, $lng2);
        $fuelPredictor = new FuelPredictionService();

        // 1. Direct Eco Route
        $speed1 = 48.5;
        $kwh1 = $fuelPredictor->predict($distance, $speed1, $vehicleType);
        $cost1 = round($kwh1 * 11.50, 2);
        $duration1 = round(($distance / $speed1) * 60);

        // 2. Highway / Express Route
        $dist2 = round($distance * 1.15, 1);
        $speed2 = 68.0;
        $kwh2 = $fuelPredictor->predict($dist2, $speed2, $vehicleType);
        $cost2 = round($kwh2 * 11.50, 2);
        $duration2 = round(($dist2 / $speed2) * 60);

        // 3. City Bypass Route
        $dist3 = round($distance * 1.25, 1);
        $speed3 = 35.0;
        $kwh3 = $fuelPredictor->predict($dist3, $speed3, $vehicleType);
        $cost3 = round($kwh3 * 11.50, 2);
        $duration3 = round(($dist3 / $speed3) * 60);

        $routesList = [
            [
                'name' => 'Zero-Emission Eco-Route (Recommended)',
                'tag' => 'Recommended Eco-Path 🌿',
                'distance_km' => $distance,
                'avg_speed_kmh' => $speed1,
                'duration_minutes' => $duration1,
                'traffic_condition' => '🟢 Low Congestion (Flowing @ 48 km/h)',
                'predicted_kwh' => $kwh1,
                'estimated_fuel' => $kwh1,
                'charging_cost_php' => number_format($cost1, 2),
                'description' => "Optimized for $vehicleType regenerative braking. Bypasses heavy intersections.",
                'is_eco' => true,
                'path' => [
                    ['lat' => $lat1, 'lng' => $lng1],
                    ['lat' => $lat2, 'lng' => $lng2],
                ]
            ],
            [
                'name' => 'Expressway / Skyway Route',
                'tag' => 'Fastest ETA ⚡',
                'distance_km' => $dist2,
                'avg_speed_kmh' => $speed2,
                'duration_minutes' => $duration2,
                'traffic_condition' => '🟡 Moderate Highway Flow (Speed: 68 km/h)',
                'predicted_kwh' => $kwh2,
                'estimated_fuel' => $kwh2,
                'charging_cost_php' => number_format($cost2, 2),
                'description' => 'Higher average speed via Skyway corridor. Saves up to 8 minutes travel time.',
                'is_eco' => false,
                'path' => [
                    ['lat' => $lat1, 'lng' => $lng1],
                    ['lat' => $lat2, 'lng' => $lng2],
                ]
            ],
            [
                'name' => 'Standard City Arterial Route',
                'tag' => 'City Bypass 🚗',
                'distance_km' => $dist3,
                'avg_speed_kmh' => $speed3,
                'duration_minutes' => $duration3,
                'traffic_condition' => '🔴 Heavy Urban Traffic (+12 min delay)',
                'predicted_kwh' => $kwh3,
                'estimated_fuel' => $kwh3,
                'charging_cost_php' => number_format($cost3, 2),
                'description' => 'Follows main surface avenues (Taft/EDSA). High stop-and-go energy consumption.',
                'is_eco' => false,
                'path' => [
                    ['lat' => $lat1, 'lng' => $lng1],
                    ['lat' => $lat2, 'lng' => $lng2],
                ]
            ]
        ];

        return [
            'start' => $start,
            'end' => $end,
            'routes' => $routesList
        ];
    }

    private function getCongestionText(float $congestion): string
    {
        if ($congestion > 1.8) return 'Heavy';
        if ($congestion > 1.3) return 'Moderate';
        return 'Clear';
    }

    /**
     * Interpolates 5 intermediate coordinates between start and end to simulate GPS breadcrumbs.
     */
    private function generateInterpolatedPath(array $start, array $end): array
    {
        $points = [];
        $steps = 6;
        for ($i = 0; $i <= $steps; $i++) {
            $fraction = $i / $steps;
            $points[] = [
                'lat' => round($start['lat'] + ($end['lat'] - $start['lat']) * $fraction, 6),
                'lng' => round($start['lng'] + ($end['lng'] - $start['lng']) * $fraction, 6),
            ];
        }
        return $points;
    }
}
