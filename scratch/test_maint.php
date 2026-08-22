<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$html = view('maintenance.index', [
    'records' => \App\Models\MaintenanceRecord::with('vehicle')->latest()->get(),
    'vehicles' => \App\Models\Vehicle::all()
])->render();

file_put_contents(__DIR__ . '/maint_render.html', $html);
echo "Rendered length: " . strlen($html) . " bytes\n";
echo "Has schedulePMSModal: " . (strpos($html, 'schedulePMSModal') !== false ? 'YES' : 'NO') . "\n";
echo "Has updateStatusModal: " . (strpos($html, 'updateStatusModal') !== false ? 'YES' : 'NO') . "\n";
