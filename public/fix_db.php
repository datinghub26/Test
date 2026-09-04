<?php

$possibleEnvFiles = [
    __DIR__ . '/../GTP/.env',
    __DIR__ . '/../.env',
    __DIR__ . '/.env',
    dirname(__DIR__) . '/.env',
    dirname(__DIR__) . '/GTP/.env',
];
$envFile = null;
foreach ($possibleEnvFiles as $candidate) {
    if (file_exists($candidate)) {
        $envFile = $candidate;
        break;
    }
}
if (!$envFile) {
    die("No .env found. Searched: " . implode(', ', $possibleEnvFiles));
}

$gtpDir = dirname($envFile);

$lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
$env = [];
foreach ($lines as $line) {
    if (str_starts_with(trim($line), '#') || !str_contains($line, '=')) continue;
    [$key, $val] = explode('=', $line, 2);
    $env[trim($key)] = trim(trim($val), '"\'');
}

$host = $env['DB_HOST'] ?? '127.0.0.1';
$user = $env['DB_USERNAME'] ?? '';
$pass = $env['DB_PASSWORD'] ?? '';
$db = $env['DB_DATABASE'] ?? '';
$port = (int)($env['DB_PORT'] ?? 3306);

$conn = new mysqli($host, $user, $pass, $db, $port);
if ($conn->connect_error) {
    die("MySQL Connection failed: " . $conn->connect_error);
}


// 1. Alter notifications table
$res = $conn->query("SHOW COLUMNS FROM `notifications` LIKE 'notifiable_type'");
if ($res && $res->num_rows == 0) {
    $sql = "ALTER TABLE `notifications` 
            ADD COLUMN `notifiable_type` VARCHAR(255) NULL AFTER `user_id`,
            ADD COLUMN `notifiable_id` BIGINT UNSIGNED NULL AFTER `notifiable_type`,
            ADD COLUMN `data` LONGTEXT NULL AFTER `notifiable_id`,
            ADD COLUMN `read_at` TIMESTAMP NULL AFTER `data`,
            ADD INDEX `notifications_notifiable_index` (`notifiable_type`, `notifiable_id`)";
    if ($conn->query($sql) === TRUE) {
        echo "<h3 style='color:green;'>SUCCESS: Added notifiable_type, notifiable_id, data, read_at columns to notifications table!</h3>";
    } else {
        echo "<h3 style='color:red;'>Error updating table: " . $conn->error . "</h3>";
    }
} else {
    echo "<h3 style='color:green;'>Columns already exist in notifications table.</h3>";
}

// 2. Remove bootstrap/cache/filament
$cacheDir = $gtpDir . '/bootstrap/cache/filament';
if (is_dir($cacheDir)) {
    $it = new RecursiveDirectoryIterator($cacheDir, RecursiveDirectoryIterator::SKIP_DOTS);
    $files = new RecursiveIteratorIterator($it, RecursiveIteratorIterator::CHILD_FIRST);
    foreach ($files as $file) {
        if ($file->isDir()) {
            @rmdir($file->getRealPath());
        } else {
            @unlink($file->getRealPath());
        }
    }
    @rmdir($cacheDir);
    echo "Removed bootstrap/cache/filament directory.<br>";
}

// 3. Clear view cache if exists
$viewCache = $gtpDir . '/storage/framework/views';
if (is_dir($viewCache)) {
    foreach (glob("$viewCache/*.php") as $file) {
        @unlink($file);
    }
    echo "Cleared compiled view cache.<br>";
}

// 4. Ensure storage files are linked or copied to public_html/storage
$srcStorage = $gtpDir . '/storage/app/public';
$pubStorage = __DIR__ . '/storage';
if (is_dir($srcStorage)) {
    if (!file_exists($pubStorage)) {
        @symlink($srcStorage, $pubStorage);
    }
    // If symlinks are not followed or directory exists, copy files so images never 404
    if (!is_dir($pubStorage)) {
        @mkdir($pubStorage, 0755, true);
    }
    $copied = 0;
    foreach (scandir($srcStorage) as $item) {
        if ($item === '.' || $item === '..') continue;
        $srcFile = $srcStorage . '/' . $item;
        $dstFile = $pubStorage . '/' . $item;
        if (is_file($srcFile) && !file_exists($dstFile)) {
            if (@copy($srcFile, $dstFile)) $copied++;
        }
    }
    echo "Storage check complete: $copied images copied to public web root.<br>";
}

echo "<h3>All set! <a href='/admin'>Go to Admin Panel</a> | <a href='/partners'>View Partners</a></h3>";

