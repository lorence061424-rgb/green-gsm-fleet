<?php

// Create required writable directories in /tmp for Vercel serverless environment
$tmpStorage = '/tmp/storage';
foreach ([
    $tmpStorage . '/framework/views',
    $tmpStorage . '/framework/sessions',
    $tmpStorage . '/framework/cache/data',
    $tmpStorage . '/logs',
    '/tmp/bootstrap/cache',
] as $dir) {
    if (!is_dir($dir)) {
        @mkdir($dir, 0755, true);
    }
}

// Auto-create SQLite DB in /tmp for Vercel serverless demo deployment
$dbFile = '/tmp/database.sqlite';
$isNewDb = !file_exists($dbFile) || filesize($dbFile) === 0;
if (!file_exists($dbFile)) {
    touch($dbFile);
}

// Set environment variables for Vercel serverless SQLite
$_ENV['DB_CONNECTION'] = 'sqlite';
$_ENV['DB_DATABASE'] = $dbFile;
putenv("DB_CONNECTION=sqlite");
putenv("DB_DATABASE={$dbFile}");

// Forward serverless request to public/index.php
require __DIR__ . '/../public/index.php';

// Auto-seed SQLite DB if fresh
if ($isNewDb) {
    try {
        \Illuminate\Support\Facades\Artisan::call('migrate:fresh', ['--force' => true, '--seed' => true]);
    } catch (\Throwable $e) {
        // Silently pass
    }
}
