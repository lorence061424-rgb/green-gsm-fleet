<?php
/**
 * ESS Tables — TiDB Cloud Production Migration Script
 * Creates ess_maintenance_requests and ess_vehicle_requests tables on TiDB Cloud.
 * Run this once via: php scratch/migrate_ess_tidb.php
 */

$host   = 'gateway01.ap-southeast-1.prod.aws.tidbcloud.com';
$port   = 4000;
$db     = 'hirna_fleet';
$user   = '47KsTdme6q6U9GE.root';
$pass   = '6yBtdWupZAsahP1S';
$caFile = __DIR__ . '/cacert.pem';

if (!file_exists($caFile)) {
    die("❌ cacert.pem not found in scratch/. Download from https://curl.se/ca/cacert.pem\n");
}

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
    echo "✅ Connected to TiDB Cloud.\n\n";
} catch (PDOException $e) {
    die("❌ Connection failed: " . $e->getMessage() . "\n");
}

// ── Table 1: ess_maintenance_requests ─────────────────────────────────────────
$sql1 = "
CREATE TABLE IF NOT EXISTS `ess_maintenance_requests` (
    `id`                     BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `employee_id`             BIGINT UNSIGNED NOT NULL,
    `vehicle_id`              BIGINT UNSIGNED NOT NULL,
    `request_type`            ENUM('repair','reimbursement','scheduled_maintenance') NOT NULL DEFAULT 'repair',
    `anomaly_description`     TEXT NOT NULL,
    `anomaly_date`            DATE NOT NULL,
    `urgency_level`           ENUM('low','medium','high','critical') NOT NULL DEFAULT 'medium',
    `odometer_reading`        INT UNSIGNED NULL,
    `reimbursement_amount`    DECIMAL(10,2) NULL,
    `receipt_number`          VARCHAR(100) NULL,
    `supporting_docs`         TEXT NULL,
    `remarks`                 TEXT NULL,
    `status`                  ENUM('pending','approved','rejected','in_progress','completed') NOT NULL DEFAULT 'pending',
    `rejection_reason`        TEXT NULL,
    `assigned_shop`           VARCHAR(255) NULL,
    `estimated_cost`          DECIMAL(10,2) NULL,
    `actual_cost`             DECIMAL(10,2) NULL,
    `scheduled_repair_date`   DATE NULL,
    `completed_date`          DATE NULL,
    `reviewed_by`             BIGINT UNSIGNED NULL,
    `maintenance_record_id`   BIGINT UNSIGNED NULL,
    `created_at`              TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`              TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)
";

// ── Table 2: ess_vehicle_requests ──────────────────────────────────────────────
$sql2 = "
CREATE TABLE IF NOT EXISTS `ess_vehicle_requests` (
    `id`                  BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `employee_id`          BIGINT UNSIGNED NOT NULL,
    `purpose_type`         ENUM('meeting','field_visit','airport','inter_branch','errand','emergency') NOT NULL DEFAULT 'meeting',
    `purpose_description`  TEXT NOT NULL,
    `destination`          VARCHAR(255) NOT NULL,
    `reservation_date`     DATE NOT NULL,
    `start_time`           TIME NOT NULL,
    `end_time`             TIME NOT NULL,
    `num_passengers`       TINYINT UNSIGNED NOT NULL DEFAULT 1,
    `vehicle_preference`   VARCHAR(100) NULL,
    `driver_preference`    BIGINT UNSIGNED NULL,
    `is_roundtrip`         TINYINT(1) NOT NULL DEFAULT 1,
    `urgency_level`        ENUM('routine','priority') NOT NULL DEFAULT 'routine',
    `additional_remarks`   TEXT NULL,
    `status`               ENUM('pending','approved','rejected','completed','cancelled') NOT NULL DEFAULT 'pending',
    `rejection_reason`     TEXT NULL,
    `assigned_vehicle_id`  BIGINT UNSIGNED NULL,
    `assigned_driver_id`   BIGINT UNSIGNED NULL,
    `reviewed_by`          BIGINT UNSIGNED NULL,
    `reservation_id`       BIGINT UNSIGNED NULL,
    `created_at`           TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`           TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)
";

$tables = [
    'ess_maintenance_requests' => $sql1,
    'ess_vehicle_requests'     => $sql2,
];

foreach ($tables as $tableName => $sql) {
    // Check if already exists
    $exists = $pdo->query("SHOW TABLES LIKE '{$tableName}'")->fetch();
    if ($exists) {
        echo "⚠️  Table `{$tableName}` already exists — skipping.\n";
        continue;
    }
    try {
        $pdo->exec($sql);
        echo "✅ Created table `{$tableName}` successfully.\n";
    } catch (PDOException $e) {
        echo "❌ Failed to create `{$tableName}`: " . $e->getMessage() . "\n";
    }
}

echo "\n🎉 ESS TiDB Cloud migration complete!\n";
