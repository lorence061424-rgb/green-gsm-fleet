<?php

namespace App\Services;

class RoutingService
{
    // Pre-configured hubs with coordinates in Metro Manila (ideal for TNVS capstone)
    private $hubs = [
        'Manila' => ['lat' => 14.5995, 'lng' => 120.9842],
        'Makati' => ['lat' => 14.5547, 'lng' => 121.0244],
        'Quezon City' => ['lat' => 14.6760, 'lng' => 121.0437],
        'Pasay' => ['lat' => 14.5378, 'lng' => 120.9993],
        'Taguig' => ['lat' => 14.5176, 'lng' => 121.0509],
    ];

    // Adjacency list representing routes/edges between hubs
    // Structure: Source => [ Destination => [base_distance_km, base_speed_kmh, congestion_level (1.0 = clear, 2.5 = heavy traffic)] ]
    private $graph = [
        'Manila' => [
            'Makati' => ['distance' => 8.5, 'speed' => 40, 'congestion' => 1.8, 'highway' => false],
            'Pasay' => ['distance' => 7.0, 'speed' => 35, 'congestion' => 1.5, 'highway' => false],
            'Quezon City' => ['distance' => 12.0, 'speed' => 30, 'congestion' => 2.2, 'highway' => false],
        ],
        'Makati' => [
            'Manila' => ['distance' => 8.5, 'speed' => 40, 'congestion' => 1.8, 'highway' => false],
            'Taguig' => ['distance' => 5.2, 'speed' => 35, 'congestion' => 1.3, 'highway' => false],
            'Pasay' => ['distance' => 6.5, 'speed' => 45, 'congestion' => 1.6, 'highway' => false],
            'Quezon City' => ['distance' => 18.0, 'speed' => 75, 'congestion' => 1.1, 'highway' => true], // Skyway/Highway route
        ],
        'Quezon City' => [
            'Manila' => ['distance' => 12.0, 'speed' => 30, 'congestion' => 2.2, 'highway' => false],
            'Makati' => ['distance' => 18.0, 'speed' => 75, 'congestion' => 1.1, 'highway' => true], // Skyway Route
            'Taguig' => ['distance' => 16.5, 'speed' => 35, 'congestion' => 2.0, 'highway' => false], // C5 Road Route
        ],
        'Pasay' => [
            'Manila' => ['distance' => 7.0, 'speed' => 35, 'congestion' => 1.5, 'highway' => false],
            'Makati' => ['distance' => 6.5, 'speed' => 45, 'congestion' => 1.6, 'highway' => false],
            'Taguig' => ['distance' => 8.0, 'speed' => 50, 'congestion' => 1.2, 'highway' => false],
        ],
        'Taguig' => [
            'Makati' => ['distance' => 5.2, 'speed' => 35, 'congestion' => 1.3, 'highway' => false],
            'Quezon City' => ['distance' => 16.5, 'speed' => 35, 'congestion' => 2.0, 'highway' => false],
            'Pasay' => ['distance' => 8.0, 'speed' => 50, 'congestion' => 1.2, 'highway' => false],
        ]
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
     * Generate routes/alternatives between start and destination hubs, 
     * simulating traffic and recommending the most fuel-efficient option.
     */
    public function planRoute(string $start, string $end, string $vehicleType): array
    {
        $startCoords = $this->hubs[$start] ?? ['lat' => 14.5995, 'lng' => 120.9842];
        $endCoords = $this->hubs[$end] ?? ['lat' => 14.5547, 'lng' => 121.0244];

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
