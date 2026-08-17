<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Vehicle;
use App\Models\Driver;
use App\Models\Trip;
use App\Models\FuelLog;
use App\Models\MaintenanceRecord;
use App\Models\PerformanceRecord;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create Core Users
        $admin = User::create([
            'name' => 'Green GSM Admin',
            'email' => 'admin@greengsm.com',
            'password' => Hash::make('Password@123'),
            'role' => 'admin',
        ]);

        $dispatcher = User::create([
            'name' => 'John Dispatcher',
            'email' => 'dispatcher@greengsm.com',
            'password' => Hash::make('Password@123'),
            'role' => 'dispatcher',
        ]);

        $driverNames = ['Juan Dela Cruz', 'Maria Santos', 'Jose Rizal', 'Pedro Penduko', 'Andres Bonifacio'];
        $licenses = ['N01-12-345678', 'N02-98-765432', 'N03-45-678901', 'N04-12-098765', 'N05-67-543210'];
        $driverModels = [];

        foreach ($driverNames as $index => $name) {
            $email = 'driver' . ($index + 1) . '@greengsm.com';
            $user = User::create([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make('Password@123'),
                'role' => 'driver',
            ]);

            $driverModels[] = Driver::create([
                'user_id' => $user->id,
                'license_number' => $licenses[$index],
                'status' => 'available',
                'performance_score' => 95.0 - ($index * 3.5),
                'total_trips' => 0,
                'total_distance_km' => 0.0,
            ]);
        }

        // 2. Create All-Electric VinFast Green GSM Fleet
        $vehicles = [
            [
                'license_plate' => 'EV-5421',
                'make' => 'VinFast',
                'model' => 'Nerio Green (EV)',
                'year' => 2025,
                'type' => 'Sedan',
                'status' => 'active',
                'fuel_capacity' => 60.0, // kWh battery capacity
                'current_gps_lat' => 14.5995,
                'current_gps_lng' => 120.9842,
            ],
            [
                'license_plate' => 'EV-9876',
                'make' => 'VinFast',
                'model' => 'VF 8 (Cyan EV)',
                'year' => 2025,
                'type' => 'SUV',
                'status' => 'active',
                'fuel_capacity' => 87.7,
                'current_gps_lat' => 14.5547,
                'current_gps_lng' => 121.0244,
            ],
            [
                'license_plate' => 'EV-1122',
                'make' => 'VinFast',
                'model' => 'VF e34 (Cyan EV)',
                'year' => 2025,
                'type' => 'Crossover',
                'status' => 'active',
                'fuel_capacity' => 42.0,
                'current_gps_lat' => 14.5378,
                'current_gps_lng' => 120.9993,
            ],
            [
                'license_plate' => 'EV-5634',
                'make' => 'VinFast',
                'model' => 'VF 5 (Cyan Compact EV)',
                'year' => 2025,
                'type' => 'Hatchback',
                'status' => 'active',
                'fuel_capacity' => 37.2,
                'current_gps_lat' => 14.5176,
                'current_gps_lng' => 121.0509,
            ],
            [
                'license_plate' => 'EV-4509',
                'make' => 'VinFast',
                'model' => 'VF 9 (Cyan Premium EV)',
                'year' => 2025,
                'type' => 'SUV',
                'status' => 'maintenance',
                'fuel_capacity' => 123.0,
                'current_gps_lat' => null,
                'current_gps_lng' => null,
            ]
        ];

        $vehicleModels = [];
        foreach ($vehicles as $v) {
            $vehicleModels[] = Vehicle::create($v);
        }

        // 3. Create historical completed trips (20 trips for training AI Linear Regression Model)
        $hubs = [
            ['name' => 'Manila', 'lat' => 14.5995, 'lng' => 120.9842],
            ['name' => 'Makati', 'lat' => 14.5547, 'lng' => 121.0244],
            ['name' => 'Quezon City', 'lat' => 14.6760, 'lng' => 121.0437],
            ['name' => 'Pasay', 'lat' => 14.5378, 'lng' => 120.9993],
            ['name' => 'Taguig', 'lat' => 14.5176, 'lng' => 121.0509],
        ];

        // Seed 20 historical trips with realistic fuel logs to train regression model
        for ($i = 0; $i < 20; $i++) {
            $start = $hubs[array_rand($hubs)];
            do {
                $end = $hubs[array_rand($hubs)];
            } while ($start['name'] === $end['name']);

            // Calculate mock distance: 5 to 20 km
            $distance = round(5.0 + (mt_rand(0, 150) / 10), 2);
            // Speed: 25 to 75 km/h
            $speed = mt_rand(25, 75);
            $durationMinutes = (int)round(($distance / $speed) * 60);

            $vehicle = $vehicleModels[array_rand($vehicleModels)];
            $driver = $driverModels[array_rand($driverModels)];

            // Predict base fuel using multipliers
            $typeMultiplier = match ($vehicle->type) {
                'Hatchback' => 0.8,
                'Sedan' => 1.0,
                'SUV' => 1.3,
                'Van' => 1.6,
                default => 1.0,
            };

            // Calculate actual fuel with some noise (+/- 15%)
            $baseConsumption = 0.08; // 8L/100km base
            $predictedLiters = (0.5 + ($distance * $baseConsumption) + ($speed * 0.0005)) * $typeMultiplier;
            $noise = 0.85 + (mt_rand(0, 30) / 100); // multiplier between 0.85 and 1.15
            $actualLiters = round($predictedLiters * $noise, 2);

            $trip = Trip::create([
                'booking_reference_id' => 'BKG-HIST-' . str_pad($i + 1, 4, '0', STR_PAD_LEFT),
                'driver_id' => $driver->id,
                'vehicle_id' => $vehicle->id,
                'start_location' => $start['name'],
                'end_location' => $end['name'],
                'start_lat' => $start['lat'],
                'start_lng' => $start['lng'],
                'end_lat' => $end['lat'],
                'end_lng' => $end['lng'],
                'status' => 'completed',
                'distance_km' => $distance,
                'estimated_duration_minutes' => $durationMinutes + 5,
                'actual_duration_minutes' => $durationMinutes,
                'estimated_fuel_liters' => round($predictedLiters, 2),
                'actual_fuel_liters' => $actualLiters,
                'start_time' => now()->subDays(20 - $i)->subHours(mt_rand(1, 8)),
                'end_time' => now()->subDays(20 - $i),
            ]);

            // Add corresponding FuelLog
            FuelLog::create([
                'vehicle_id' => $vehicle->id,
                'trip_id' => $trip->id,
                'date' => $trip->end_time->format('Y-m-d'),
                'amount_liters' => $actualLiters,
                'cost' => round($actualLiters * 11.50, 2), // PHP 11.50 per kWh EV charging rate
                'odometer_reading' => 5000 + ($i * 120) + $distance,
                'fuel_type' => 'Electric (kWh)',
            ]);

            // Add Performance Log
            $speeding = (mt_rand(0, 100) > 85) ? mt_rand(1, 3) : 0;
            $harshBraking = (mt_rand(0, 100) > 90) ? mt_rand(1, 2) : 0;
            $idleSec = mt_rand(30, 300);

            $perfScore = max(50, 100 - ($speeding * 8) - ($harshBraking * 12) - (int)($idleSec / 60));

            PerformanceRecord::create([
                'driver_id' => $driver->id,
                'trip_id' => $trip->id,
                'speeding_events' => $speeding,
                'harsh_braking_events' => $harshBraking,
                'idle_minutes' => round($idleSec / 60, 2),
                'safety_score' => $perfScore,
            ]);

            // Update driver totals
            $driver->increment('total_trips');
            $driver->increment('total_distance_km', $distance);
            
            // Recalculate average performance rating
            $currentTotalScore = $driver->performance_score * ($driver->total_trips - 1);
            $newAverage = ($currentTotalScore + $perfScore) / $driver->total_trips;
            $driver->update(['performance_score' => round($newAverage, 2)]);
        }

        // 4. Create Maintenance Records
        MaintenanceRecord::create([
            'vehicle_id' => $vehicleModels[0]->id,
            'service_type' => 'PMS Oil Change',
            'description' => 'Regular PMS oil change, filter replacement, spark plugs check.',
            'cost' => 3200.00,
            'status' => 'completed',
            'scheduled_date' => now()->subDays(15),
            'completion_date' => now()->subDays(15),
        ]);

        MaintenanceRecord::create([
            'vehicle_id' => $vehicleModels[1]->id,
            'service_type' => 'Tire Rotation & Alignment',
            'description' => 'Rotated four tires, adjusted wheel alignment and steering camber.',
            'cost' => 1800.00,
            'status' => 'completed',
            'scheduled_date' => now()->subDays(5),
            'completion_date' => now()->subDays(5),
        ]);

        // 5. Create Sample Vehicle Reservations (VRDS)
        \App\Models\VehicleReservation::create([
            'vehicle_id' => $vehicleModels[0]->id,
            'driver_id' => $driverModels[0]->id,
            'requested_by' => $admin->id,
            'purpose' => 'Corporate Executive Airport Pick-up',
            'reservation_date' => now()->addDays(1)->format('Y-m-d'),
            'start_time' => '09:00:00',
            'end_time' => '13:00:00',
            'status' => 'approved',
            'remarks' => 'Flight PR-102 arrival at NAIA Terminal 3.',
        ]);

        \App\Models\VehicleReservation::create([
            'vehicle_id' => $vehicleModels[1]->id,
            'driver_id' => $driverModels[1]->id,
            'requested_by' => $dispatcher->id,
            'purpose' => 'VIP Client Group Tour to BGC Taguig',
            'reservation_date' => now()->addDays(2)->format('Y-m-d'),
            'start_time' => '08:00:00',
            'end_time' => '17:00:00',
            'status' => 'pending',
            'remarks' => 'Requires clean interior and bottled water.',
        ]);

        \App\Models\VehicleReservation::create([
            'vehicle_id' => $vehicleModels[2]->id,
            'driver_id' => null,
            'requested_by' => $admin->id,
            'purpose' => 'Scheduled Fleet Shuttle Service',
            'reservation_date' => now()->addDays(3)->format('Y-m-d'),
            'start_time' => '07:00:00',
            'end_time' => '11:00:00',
            'status' => 'pending',
            'remarks' => 'Driver will be assigned by Dispatcher before start time.',
        ]);
    }
}
