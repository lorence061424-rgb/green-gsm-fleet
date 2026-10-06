<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Vehicle;
use App\Models\Trip;
use App\Models\MaintenanceRecord;

echo "Updating vehicles with Dealership, OR/CR, LTO Registration, and Fuel Type...\n";

$dealerships = [
    'Toyota Pasig Motors',
    'VinFast Metro Manila',
    'BYD Philippines',
    'Hyundai Quezon Ave',
    'Nissan Manila Bay',
    'Honda Greenhills'
];

$fuelTypes = [
    'Unleaded 91',
    'Premium 95',
    'Euro 5 Diesel',
    'Electric / EV',
    'Hybrid'
];

$vehicles = Vehicle::all();
foreach ($vehicles as $index => $v) {
    $dealership = $dealerships[$index % count($dealerships)];
    $fuelType = (str_contains(strtolower($v->model), 'ev') || str_contains(strtolower($v->make), 'vinfast') || str_contains(strtolower($v->make), 'byd')) 
        ? 'Electric / EV' 
        : $fuelTypes[$index % (count($fuelTypes) - 1)];

    $orNum = "OR-2026-" . str_pad($v->id * 1423, 6, "0", STR_PAD_LEFT);
    $crNum = "CR-2025-" . str_pad($v->id * 7821, 6, "0", STR_PAD_LEFT);
    $regNum = "LTO-NCR-" . str_pad($v->id * 9912, 5, "0", STR_PAD_LEFT);
    
    $regStatus = ($index % 4 === 0) ? 'pending_renewal' : 'active';
    $regExpiry = ($regStatus === 'pending_renewal') ? '2026-10-31' : '2027-08-15';
    $orCrExpiry = '2027-08-15';

    $v->update([
        'dealership_company' => $dealership,
        'or_number' => $orNum,
        'cr_number' => $crNum,
        'or_cr_expiry_date' => $orCrExpiry,
        'registration_number' => $regNum,
        'registration_date' => '2025-08-15',
        'registration_expiry_date' => $regExpiry,
        'registration_status' => $regStatus,
        'fuel_type' => $fuelType,
    ]);
    echo "  - Vehicle #{$v->id} ({$v->license_plate}): Dealership={$dealership}, Fuel={$fuelType}, RegStatus={$regStatus}\n";
}

echo "Updating trips with Toll Fees breakdown...\n";
$trips = Trip::all();
$tollSampleBreakdowns = [
    [
        'amount' => 478.00,
        'breakdown' => [
            ['expressway' => 'SLEX', 'plaza' => 'Nichols Plaza', 'amount' => 214.00],
            ['expressway' => 'Skyway Stage 3', 'plaza' => 'Buendia Exit', 'amount' => 264.00]
        ]
    ],
    [
        'amount' => 165.00,
        'breakdown' => [
            ['expressway' => 'NLEX', 'plaza' => 'Balintawak Tollgate', 'amount' => 165.00]
        ]
    ],
    [
        'amount' => 320.00,
        'breakdown' => [
            ['expressway' => 'NAIAx', 'plaza' => 'Terminal 3 Ramp', 'amount' => 170.00],
            ['expressway' => 'CAVITEX', 'plaza' => 'Paranaque Tollgate', 'amount' => 150.00]
        ]
    ],
    [
        'amount' => 0.00,
        'breakdown' => []
    ]
];

foreach ($trips as $index => $t) {
    $sample = $tollSampleBreakdowns[$index % count($tollSampleBreakdowns)];
    $t->update([
        'toll_fees_amount' => $sample['amount'],
        'toll_fees_breakdown' => $sample['breakdown'],
    ]);
    echo "  - Trip #{$t->id}: Toll Fees = ₱{$sample['amount']}\n";
}

echo "Updating maintenance records with Repair Category and Filter Status...\n";
$mRecords = MaintenanceRecord::all();

if ($mRecords->count() === 0 && $vehicles->count() > 0) {
    // Seed initial maintenance records if none exist
    foreach ($vehicles as $v) {
        MaintenanceRecord::create([
            'vehicle_id' => $v->id,
            'service_type' => 'Preventive Maintenance (Oil Change & Brake Check)',
            'repair_category' => 'maintenance',
            'description' => 'Regular 5,000 km PMS routine checkup.',
            'cost' => 4500.00,
            'status' => 'completed',
            'filter_status' => 'maintenance',
            'scheduled_date' => now()->subDays(15),
            'completion_date' => now()->subDays(14),
        ]);

        MaintenanceRecord::create([
            'vehicle_id' => $v->id,
            'service_type' => 'Battery Cell Diagnostics & Inverter Calibration',
            'repair_category' => 'repair',
            'description' => 'Active electrical repair order submitted to Team 8 Motorshop.',
            'cost' => 12500.00,
            'status' => 'in_progress',
            'filter_status' => 'active',
            'scheduled_date' => now()->subDays(2),
            'completion_date' => null,
        ]);

        MaintenanceRecord::create([
            'vehicle_id' => $v->id,
            'service_type' => 'Tire Replacement & Wheel Alignment',
            'repair_category' => 'repair',
            'description' => 'Replaced worn front tires with new Dunlop tires.',
            'cost' => 9800.00,
            'status' => 'completed',
            'filter_status' => 'inactive',
            'scheduled_date' => now()->subDays(45),
            'completion_date' => now()->subDays(44),
        ]);
    }
} else {
    foreach ($mRecords as $index => $mr) {
        $filterStatus = ($mr->status === 'in_progress' || $mr->status === 'scheduled') ? 'active' : (($index % 2 === 0) ? 'maintenance' : 'inactive');
        $category = ($filterStatus === 'maintenance' || str_contains(strtolower($mr->service_type), 'preventive')) ? 'maintenance' : 'repair';
        $mr->update([
            'repair_category' => $category,
            'filter_status' => $filterStatus,
        ]);
    }
}

echo "Successfully updated database seeded records!\n";
