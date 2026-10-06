<?php
require_once __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Driver;
use App\Models\User;

echo "--- LOCAL DRIVERS ---\n";
foreach (Driver::with('user')->get() as $d) {
    echo "Driver ID: {$d->id} | User ID: {$d->user_id} | Name: " . ($d->user?->name ?? 'N/A') . " | Email: " . ($d->user?->email ?? 'N/A') . " | License: {$d->license_number}\n";
}
