<?php
/**
 * Update driver emails to valid, professional Hirna email addresses.
 * Updates both local MySQL and TiDB Cloud production database.
 */

require_once __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Driver;

echo "=== UPDATING LOCAL DRIVER EMAILS ===\n";

$emailMap = [
    'Juan Dela Cruz'   => 'juan.delacruz@hirna.ph',
    'Maria Santos'     => 'maria.santos@hirna.ph',
    'Jose Rizal'       => 'jose.rizal@hirna.ph',
    'Pedro Penduko'    => 'pedro.penduko@hirna.ph',
    'Andres Bonifacio' => 'andres.bonifacio@hirna.ph',
];

$drivers = Driver::with('user')->get();
foreach ($drivers as $driver) {
    if ($driver->user) {
        $name = $driver->user->name;
        $newEmail = $emailMap[$name] ?? (strtolower(str_replace(' ', '.', $name)) . '@hirna.ph');
        
        $driver->user->update([
            'email' => $newEmail
        ]);
        echo "✅ Updated Driver #{$driver->id} ({$name}): {$newEmail}\n";
    }
}

// Also check any driver user without a Driver model
$driverUsers = User::where('role', 'driver')->get();
foreach ($driverUsers as $u) {
    if (!str_contains($u->email, '@')) {
        $newEmail = strtolower(str_replace(' ', '.', $u->name)) . '@hirna.ph';
        $u->update(['email' => $newEmail]);
        echo "✅ Updated User #{$u->id} ({$u->name}): {$newEmail}\n";
    }
}

// ── TiDB Cloud Production DB Update ──
echo "\n=== UPDATING TIDB CLOUD DRIVER EMAILS ===\n";
$host   = 'gateway01.ap-southeast-1.prod.aws.tidbcloud.com';
$port   = 4000;
$db     = 'hirna_fleet';
$user   = '47KsTdme6q6U9GE.root';
$pass   = '6yBtdWupZAsahP1S';
$caFile = __DIR__ . '/cacert.pem';

try {
    $pdo = new PDO(
        "mysql:host={$host};port={$port};dbname={$db};charset=utf8mb4",
        $user,
        $pass,
        [
            PDO::ATTR_ERRMODE                  => PDO::ERRMODE_EXCEPTION,
            PDO::MYSQL_ATTR_SSL_CA             => $caFile,
            PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => false,
        ]
    );
    echo "✅ Connected to TiDB Cloud.\n";

    $stmt = $pdo->query("SELECT d.id, d.user_id, u.name, u.email FROM drivers d JOIN users u ON d.user_id = u.id");
    $tidbDrivers = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $upStmt = $pdo->prepare("UPDATE users SET email = ? WHERE id = ?");
    foreach ($tidbDrivers as $td) {
        $name = $td['name'];
        $newEmail = $emailMap[$name] ?? (strtolower(str_replace(' ', '.', $name)) . '@hirna.ph');
        $upStmt->execute([$newEmail, $td['user_id']]);
        echo "✅ Updated TiDB Driver #{$td['id']} ({$name}): {$newEmail}\n";
    }

    // Fix any other driver role users on TiDB
    $pdo->exec("UPDATE users SET email = REPLACE(LOWER(CONCAT(REPLACE(name, ' ', '.'), '@hirna.ph')), '..', '.') WHERE role = 'driver' AND email NOT LIKE '%@%'");

    echo "🎉 All driver emails successfully formatted on Local & TiDB Cloud!\n";
} catch (Exception $e) {
    echo "❌ TiDB update notice: " . $e->getMessage() . "\n";
}
