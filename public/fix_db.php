<?php

// Security: Prevent public web access to database and file operations
if (php_sapi_name() !== 'cli') {
    $token = $_GET['key'] ?? '';
    if (empty($token) || $token !== 'erc_secure_deploy_2026') {
        http_response_code(404);
        die("<!DOCTYPE HTML PUBLIC \"-//IETF//DTD HTML 2.0//EN\">\n<html><head><title>404 Not Found</title></head><body><h1>Not Found</h1><p>The requested URL was not found on this server.</p></body></html>");
    }
}

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
$pubStorage = __DIR__ . '/storage';
if (!is_dir($pubStorage) && !is_link($pubStorage)) {
    @mkdir($pubStorage, 0755, true);
}

$appDir = $gtpDir . '/storage/app';
$publicAppDir = $gtpDir . '/storage/app/public';
if (!is_dir($publicAppDir)) {
    @mkdir($publicAppDir, 0755, true);
}

$copied = 0;
$foundFiles = [];

if (is_dir($appDir)) {
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($appDir, RecursiveDirectoryIterator::SKIP_DOTS)
    );
    foreach ($iterator as $file) {
        if ($file->isFile()) {
            $filename = $file->getFilename();
            $ext = strtolower($file->getExtension());
            if (in_array($ext, ['png', 'jpg', 'jpeg', 'webp', 'svg', 'gif', 'mp3', 'ico'])) {
                $foundFiles[] = $file->getPathname();
                $dest1 = $pubStorage . '/' . $filename;
                $dest2 = $publicAppDir . '/' . $filename;
                if (!file_exists($dest1) || filesize($dest1) === 0) {
                    if (@copy($file->getPathname(), $dest1)) $copied++;
                }
                if (!file_exists($dest2) || filesize($dest2) === 0) {
                    @copy($file->getPathname(), $dest2);
                }
            }
        }
    }
}

echo "Storage sync: Found " . count($foundFiles) . " media files. Copied $copied files to public_html/storage.<br>";
if (!empty($foundFiles)) {
    echo "<details><summary>View synced files (" . count($foundFiles) . ")</summary><pre>" . htmlspecialchars(implode("\n", $foundFiles)) . "</pre></details>";
}

echo "<h3>All set! <a href='/admin'>Go to Admin Panel</a> | <a href='/partners'>View Partners</a> | <a href='/earn'>View Earn</a></h3>";


