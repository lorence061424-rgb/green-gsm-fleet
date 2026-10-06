<?php

$host = 'gateway01.ap-southeast-1.prod.aws.tidbcloud.com';
$port = 4000;
$db   = 'hirna_fleet';
$user = '47KsTdme6q6U9GE.root';
$pass = '6yBtdWupZAsahP1S';

echo "Connecting to TiDB Cloud ($host:$port/$db)...\n";

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$db", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => false,
    ]);
    echo "Connected successfully to TiDB Cloud!\n";

    // 1. Check & Add Columns to vehicles
    echo "Migrating `vehicles` table...\n";
    $vehicleCols = [
        "dealership_company" => "VARCHAR(255) NULL",
        "or_number" => "VARCHAR(100) NULL",
        "cr_number" => "VARCHAR(100) NULL",
        "or_cr_expiry_date" => "DATE NULL",
        "registration_number" => "VARCHAR(100) NULL",
        "registration_date" => "DATE NULL",
        "registration_expiry_date" => "DATE NULL",
        "registration_status" => "VARCHAR(50) DEFAULT 'active'",
        "fuel_type" => "VARCHAR(50) DEFAULT 'Unleaded 91'"
    ];

    foreach ($vehicleCols as $col => $definition) {
        try {
            $stmt = $pdo->query("SHOW COLUMNS FROM `vehicles` LIKE '$col'");
            if ($stmt->rowCount() == 0) {
                $pdo->exec("ALTER TABLE `vehicles` ADD COLUMN `$col` $definition");
                echo "  + Added `$col` to `vehicles`\n";
            } else {
                echo "  - `$col` already exists in `vehicles`\n";
            }
        } catch (Exception $ex) {
            echo "  ! Error on $col: " . $ex->getMessage() . "\n";
        }
    }

    // 2. Check & Add Columns to trips
    echo "Migrating `trips` table...\n";
    $tripCols = [
        "toll_fees_amount" => "DECIMAL(10,2) DEFAULT 0.00",
        "toll_fees_breakdown" => "TEXT NULL"
    ];

    foreach ($tripCols as $col => $definition) {
        try {
            $stmt = $pdo->query("SHOW COLUMNS FROM `trips` LIKE '$col'");
            if ($stmt->rowCount() == 0) {
                $pdo->exec("ALTER TABLE `trips` ADD COLUMN `$col` $definition");
                echo "  + Added `$col` to `trips`\n";
            } else {
                echo "  - `$col` already exists in `trips`\n";
            }
        } catch (Exception $ex) {
            echo "  ! Error on $col: " . $ex->getMessage() . "\n";
        }
    }

    // 3. Check & Add Columns to maintenance_records
    echo "Migrating `maintenance_records` table...\n";
    $maintCols = [
        "repair_category" => "VARCHAR(50) DEFAULT 'maintenance'",
        "filter_status" => "VARCHAR(50) DEFAULT 'active'"
    ];

    foreach ($maintCols as $col => $definition) {
        try {
            $stmt = $pdo->query("SHOW COLUMNS FROM `maintenance_records` LIKE '$col'");
            if ($stmt->rowCount() == 0) {
                $pdo->exec("ALTER TABLE `maintenance_records` ADD COLUMN `$col` $definition");
                echo "  + Added `$col` to `maintenance_records`\n";
            } else {
                echo "  - `$col` already exists in `maintenance_records`\n";
            }
        } catch (Exception $ex) {
            echo "  ! Error on $col: " . $ex->getMessage() . "\n";
        }
    }

    echo "Updating sample seeded values on TiDB Cloud...\n";

    $pdo->exec("UPDATE `vehicles` SET `dealership_company` = 'Toyota Pasig Motors' WHERE `dealership_company` IS NULL");
    $pdo->exec("UPDATE `vehicles` SET `fuel_type` = 'Electric / EV' WHERE (`model` LIKE '%EV%' OR `make` LIKE '%VinFast%' OR `make` LIKE '%BYD%') AND (`fuel_type` IS NULL OR `fuel_type` = 'Unleaded 91')");
    $pdo->exec("UPDATE `vehicles` SET `or_number` = CONCAT('OR-2026-', LPAD(id, 6, '0')) WHERE `or_number` IS NULL");
    $pdo->exec("UPDATE `vehicles` SET `cr_number` = CONCAT('CR-2025-', LPAD(id, 6, '0')) WHERE `cr_number` IS NULL");
    $pdo->exec("UPDATE `vehicles` SET `registration_number` = CONCAT('LTO-NCR-', LPAD(id, 5, '0')) WHERE `registration_number` IS NULL");
    $pdo->exec("UPDATE `vehicles` SET `registration_expiry_date` = '2027-08-15', `registration_status` = 'active' WHERE `registration_status` IS NULL");

    $pdo->exec("UPDATE `maintenance_records` SET `filter_status` = IF(status IN ('in_progress', 'scheduled'), 'active', 'inactive') WHERE `filter_status` IS NULL OR `filter_status` = ''");
    $pdo->exec("UPDATE `maintenance_records` SET `repair_category` = IF(service_type LIKE '%preventive%' OR service_type LIKE '%routine%' OR service_type LIKE '%PMS%', 'maintenance', 'repair') WHERE `repair_category` IS NULL OR `repair_category` = ''");

    $pdo->exec("UPDATE `trips` SET `toll_fees_amount` = 214.00 WHERE `toll_fees_amount` = 0.00 AND `status` = 'completed'");

    echo "ALL TIDB CLOUD MIGRATIONS & SEEDING COMPLETED SUCCESSFULLY!\n";

} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
