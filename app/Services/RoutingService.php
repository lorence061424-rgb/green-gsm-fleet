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
        if (!isset($this->hubs[$start]) || !isset($this->hubs[$end])) {
            // Default to Haversine straight-line if they aren't preconfigured hubs
            $lat1 = $this->hubs[$start]['lat'] ?? 14.5995;
            $lng1 = $this->hubs[$start]['lng'] ?? 120.9842;
            $lat2 = $this->hubs[$end]['lat'] ?? 14.5547;
            $lng2 = $this->hubs[$end]['lng'] ?? 121.0244;
            $distance = $this->haversineDistance($lat1, $lng1, $lat2, $lng2);
            
            $fuelPredictor = new FuelPredictionService();
            $predictedFuel = $fuelPredictor->predict($distance, 50, $vehicleType);

            return [
                'start' => $start,
                'end' => $end,
                'routes' => [
                    [
                        'name' => 'Direct Path (GPS Heuristic)',
                        'distance_km' => $distance,
                        'avg_speed_kmh' => 45,
                        'duration_minutes' => round(($distance / 45) * 60),
                        'congestion' => 'Moderate',
                        'estimated_fuel' => $predictedFuel,
                        'path' => [
                            ['lat' => $lat1, 'lng' => $lng1],
                            ['lat' => $lat2, 'lng' => $lng2],
                        ],
                        'is_eco' => true,
                    ]
                ]
            ];
        }

        $routesList = [];
        $fuelPredictor = new FuelPredictionService();

        // 1. Direct Edge (if exists in graph)
        if (isset($this->graph[$start][$end])) {
            $edge = $this->graph[$start][$end];
            $distance = $edge['distance'];
            
            // Adjust speed by simulated real-time congestion
            // Randomize congestion slightly to make it feel alive!
            $randCongestion = $edge['congestion'] * (0.9 + (mt_rand(0, 20) / 100)); // +/- 10%
            $actualSpeed = round($edge['speed'] / $randCongestion, 1);
            $duration = round(($distance / $actualSpeed) * 60);
            
            // Calculate fuel prediction
            $predictedFuel = $fuelPredictor->predict($distance, $actualSpeed, $vehicleType);

            $routesList[] = [
                'name' => $edge['highway'] ? 'Expressway Route (Skyway)' : 'Standard City Route',
                'distance_km' => $distance,
                'avg_speed_kmh' => $actualSpeed,
                'duration_minutes' => $duration,
                'congestion' => $this->getCongestionText($randCongestion),
                'estimated_fuel' => $predictedFuel,
                'path' => $this->generateInterpolatedPath($this->hubs[$start], $this->hubs[$end]),
                'is_eco' => false,
            ];
        }

        // 2. Alternative Path (e.g. via a middle hub)
        foreach ($this->hubs as $via => $coords) {
            if ($via === $start || $via === $end) continue;

            if (isset($this->graph[$start][$via]) && isset($this->graph[$via][$end])) {
                $edge1 = $this->graph[$start][$via];
                $edge2 = $this->graph[$via][$end];

                $distance = $edge1['distance'] + $edge2['distance'];
                
                // Cumulative speed/congestion
                $randCong1 = $edge1['congestion'] * (0.95 + (mt_rand(0, 10) / 100));
                $randCong2 = $edge2['congestion'] * (0.95 + (mt_rand(0, 10) / 100));
                
                $speed1 = $edge1['speed'] / $randCong1;
                $speed2 = $edge2['speed'] / $randCong2;
                $avgSpeed = round(($speed1 + $speed2) / 2, 1);

                $duration1 = ($edge1['distance'] / $speed1) * 60;
                $duration2 = ($edge2['distance'] / $speed2) * 60;
                $duration = round($duration1 + $duration2);

                $predictedFuel = $fuelPredictor->predict($distance, $avgSpeed, $vehicleType);

                $routesList[] = [
                    'name' => "Alternative Route (via $via)",
                    'distance_km' => $distance,
                    'avg_speed_kmh' => $avgSpeed,
                    'duration_minutes' => $duration,
                    'congestion' => $this->getCongestionText(($randCong1 + $randCong2) / 2),
                    'estimated_fuel' => $predictedFuel,
                    'path' => array_merge(
                        $this->generateInterpolatedPath($this->hubs[$start], $this->hubs[$via]),
                        array_slice($this->generateInterpolatedPath($this->hubs[$via], $this->hubs[$end]), 1)
                    ),
                    'is_eco' => false,
                ];
            }
        }

        // Sort routes: find the most fuel efficient one and mark it as is_eco = true
        usort($routesList, function($a, $b) {
            return $a['estimated_fuel'] <=> $b['estimated_fuel'];
        });

        if (count($routesList) > 0) {
            $routesList[0]['is_eco'] = true;
        }

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
