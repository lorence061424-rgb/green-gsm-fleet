<?php
/**
 * Seed realistic incoming requests for PMS & Reservation modules.
 * Runs on both local and TiDB Cloud production DB.
 */

require_once __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Vehicle;
use App\Models\User;
use App\Models\EssMaintenanceRequest;
use App\Models\EssVehicleRequest;

$admin = User::where('role', 'admin')->first() ?? User::first();
$vehicles = Vehicle::where('status', 'active')->get();

if ($vehicles->count() < 2) {
    echo "⚠️ Need at least 2 active vehicles to seed.\n";
    exit;
}

$v1 = $vehicles[0];
$v2 = $vehicles[1];
$v3 = $vehicles[2] ?? $vehicles[0];

// 1. Seed Incoming Maintenance Requests if empty
if (EssMaintenanceRequest::count() === 0) {
    EssMaintenanceRequest::create([
        'employee_id'          => $admin->id,
        'vehicle_id'           => $v1->id,
        'request_type'         => 'repair',
        'anomaly_description'  => 'Driver observed heavy squeaking noise on front brake pads and steering vibration when braking above 60 km/h.',
        'anomaly_date'         => now()->subDays(1)->toDateString(),
        'urgency_level'        => 'high',
        'odometer_reading'     => 48250,
        'status'               => 'pending',
    ]);

    EssMaintenanceRequest::create([
        'employee_id'          => $admin->id,
        'vehicle_id'           => $v2->id,
        'request_type'         => 'reimbursement',
        'anomaly_description'  => 'Emergency tire puncture repair & vulcanizing during official client trip to Batangas. Official receipt OR#88341 attached.',
        'anomaly_date'         => now()->subDays(2)->toDateString(),
        'urgency_level'        => 'medium',
        'reimbursement_amount' => 1450.00,
        'receipt_number'       => 'OR-202610-88341',
        'status'               => 'pending',
    ]);

    EssMaintenanceRequest::create([
        'employee_id'          => $admin->id,
        'vehicle_id'           => $v3->id,
        'request_type'         => 'scheduled_maintenance',
        'anomaly_description'  => 'Routine 40,000 km PMS oil change, spark plug check, and engine coolant flush due this week.',
        'anomaly_date'         => now()->toDateString(),
        'urgency_level'        => 'low',
        'odometer_reading'     => 40120,
        'status'               => 'pending',
    ]);

    echo "✅ Seeded 3 sample incoming maintenance requests.\n";
} else {
    echo "ℹ️ Incoming maintenance requests already exist (" . EssMaintenanceRequest::count() . " records).\n";
}

// 2. Seed Incoming Vehicle Requests if empty
if (EssVehicleRequest::count() === 0) {
    EssVehicleRequest::create([
        'employee_id'         => $admin->id,
        'purpose_type'        => 'meeting',
        'purpose_description'=> 'Executive Board & Investor Relations Meeting with transport for 4 department heads.',
        'destination'         => 'Makati Shangri-La Hotel, Ayala Avenue, Makati City',
        'reservation_date'    => now()->addDays(2)->toDateString(),
        'start_time'          => '08:30:00',
        'end_time'            => '12:30:00',
        'num_passengers'      => 4,
        'vehicle_preference'  => 'SUV',
        'is_roundtrip'        => 1,
        'urgency_level'       => 'priority',
        'additional_remarks'  => 'VIP passengers onboard. Please ensure vehicle is cleaned and air-conditioning is optimal.',
        'status'              => 'pending',
    ]);

    EssVehicleRequest::create([
        'employee_id'         => $admin->id,
        'purpose_type'        => 'field_visit',
        'purpose_description'=> 'Regional branch solar terminal site inspection and hardware audit.',
        'destination'         => 'Hirna Hub Laguna, Calamba Technopark, Laguna',
        'reservation_date'    => now()->addDays(3)->toDateString(),
        'start_time'          => '07:00:00',
        'end_time'            => '17:00:00',
        'num_passengers'      => 6,
        'vehicle_preference'  => 'Van',
        'is_roundtrip'        => 1,
        'urgency_level'       => 'routine',
        'additional_remarks'  => 'Transporting audit equipment and laptops.',
        'status'              => 'pending',
    ]);

    EssVehicleRequest::create([
        'employee_id'         => $admin->id,
        'purpose_type'        => 'airport',
        'purpose_description'=> 'Airport reception for keynote speaker arriving from Davao branch.',
        'destination'         => 'NAIA Terminal 3 Arrival Area, Pasay City',
        'reservation_date'    => now()->addDays(1)->toDateString(),
        'start_time'          => '14:00:00',
        'end_time'            => '18:00:00',
        'num_passengers'      => 2,
        'vehicle_preference'  => 'Sedan',
        'is_roundtrip'        => 1,
        'urgency_level'       => 'priority',
        'additional_remarks'  => 'Flight PR 2814 scheduled landing at 2:45 PM.',
        'status'              => 'pending',
    ]);

    echo "✅ Seeded 3 sample incoming vehicle booking requests.\n";
} else {
    echo "ℹ️ Incoming vehicle booking requests already exist (" . EssVehicleRequest::count() . " records).\n";
}
