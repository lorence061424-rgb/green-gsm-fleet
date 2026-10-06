<?php
/**
 * Seed realistic incoming requests directly to TiDB Cloud production DB.
 */

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
} catch (PDOException $e) {
    die("❌ Connection failed: " . $e->getMessage() . "\n");
}

// Get admin and vehicles from TiDB
$adminId = $pdo->query("SELECT id FROM users LIMIT 1")->fetchColumn() ?: 1;
$vehicles = $pdo->query("SELECT id FROM vehicles LIMIT 3")->fetchAll(PDO::FETCH_COLUMN);

if (count($vehicles) < 2) {
    echo "⚠️ Not enough vehicles on TiDB.\n";
    exit;
}

$v1 = $vehicles[0];
$v2 = $vehicles[1];
$v3 = $vehicles[2] ?? $vehicles[0];

// Check and seed ess_maintenance_requests
$mCount = $pdo->query("SELECT COUNT(*) FROM ess_maintenance_requests")->fetchColumn();
if ($mCount == 0) {
    $stmt = $pdo->prepare("INSERT INTO ess_maintenance_requests (employee_id, vehicle_id, request_type, anomaly_description, anomaly_date, urgency_level, odometer_reading, reimbursement_amount, receipt_number, status, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())");
    
    $stmt->execute([$adminId, $v1, 'repair', 'Driver observed heavy squeaking noise on front brake pads and steering vibration when braking above 60 km/h.', date('Y-m-d', strtotime('-1 day')), 'high', 48250, null, null, 'pending']);
    $stmt->execute([$adminId, $v2, 'reimbursement', 'Emergency tire puncture repair & vulcanizing during official client trip to Batangas. Official receipt OR#88341 attached.', date('Y-m-d', strtotime('-2 days')), 'medium', null, 1450.00, 'OR-202610-88341', 'pending']);
    $stmt->execute([$adminId, $v3, 'scheduled_maintenance', 'Routine 40,000 km PMS oil change, spark plug check, and engine coolant flush due this week.', date('Y-m-d'), 'low', 40120, null, null, 'pending']);
    echo "✅ Seeded 3 sample incoming maintenance requests on TiDB.\n";
} else {
    echo "ℹ️ TiDB ess_maintenance_requests already has {$mCount} records.\n";
}

// Check and seed ess_vehicle_requests
$vCount = $pdo->query("SELECT COUNT(*) FROM ess_vehicle_requests")->fetchColumn();
if ($vCount == 0) {
    $stmt2 = $pdo->prepare("INSERT INTO ess_vehicle_requests (employee_id, purpose_type, purpose_description, destination, reservation_date, start_time, end_time, num_passengers, vehicle_preference, is_roundtrip, urgency_level, additional_remarks, status, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())");
    
    $stmt2->execute([$adminId, 'meeting', 'Executive Board & Investor Relations Meeting with transport for 4 department heads.', 'Makati Shangri-La Hotel, Ayala Avenue, Makati City', date('Y-m-d', strtotime('+2 days')), '08:30:00', '12:30:00', 4, 'SUV', 1, 'priority', 'VIP passengers onboard. Please ensure vehicle is cleaned and air-conditioning is optimal.', 'pending']);
    $stmt2->execute([$adminId, 'field_visit', 'Regional branch solar terminal site inspection and hardware audit.', 'Hirna Hub Laguna, Calamba Technopark, Laguna', date('Y-m-d', strtotime('+3 days')), '07:00:00', '17:00:00', 6, 'Van', 1, 'routine', 'Transporting audit equipment and laptops.', 'pending']);
    $stmt2->execute([$adminId, 'airport', 'Airport reception for keynote speaker arriving from Davao branch.', 'NAIA Terminal 3 Arrival Area, Pasay City', date('Y-m-d', strtotime('+1 day')), '14:00:00', '18:00:00', 2, 'Sedan', 1, 'priority', 'Flight PR 2814 scheduled landing at 2:45 PM.', 'pending']);
    echo "✅ Seeded 3 sample incoming vehicle requests on TiDB.\n";
} else {
    echo "ℹ️ TiDB ess_vehicle_requests already has {$vCount} records.\n";
}

echo "🎉 TiDB Cloud incoming requests seeding finished.\n";
