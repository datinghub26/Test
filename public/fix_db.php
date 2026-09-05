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

try {
    $conn = new PDO("mysql:host={$host};port={$port};dbname={$db};charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $e) {
    die("PDO Connection failed: " . $e->getMessage());
}

// 1. Alter notifications table
$stmt = $conn->query("SHOW COLUMNS FROM `notifications` LIKE 'notifiable_type'");
$col = $stmt->fetch();
if (!$col) {
    $sql = "ALTER TABLE `notifications` 
            ADD COLUMN `notifiable_type` VARCHAR(255) NULL AFTER `user_id`,
            ADD COLUMN `notifiable_id` BIGINT UNSIGNED NULL AFTER `notifiable_type`,
            ADD COLUMN `data` LONGTEXT NULL AFTER `notifiable_id`,
            ADD COLUMN `read_at` TIMESTAMP NULL AFTER `data`,
            ADD INDEX `notifications_notifiable_index` (`notifiable_type`, `notifiable_id`)";
    $conn->exec($sql);
    echo "<h3 style='color:green;'>SUCCESS: Added notifiable_type, notifiable_id, data, read_at columns to notifications table!</h3>";
} else {
    echo "<h3 style='color:green;'>Columns already exist in notifications table.</h3>";
}

// Diagnostic: Show cashout_methods
echo "<h4>Cashout Methods:</h4><ul>";
$methodsStmt = $conn->query("SELECT id, name, image, category, is_active FROM `cashout_methods`");
while ($row = $methodsStmt->fetch()) {
    echo "<li>ID: {$row['id']} | Name: <b>{$row['name']}</b> | Image: <code>" . htmlspecialchars($row['image'] ?? 'NULL') . "</code> | Category: {$row['category']} | Active: {$row['is_active']}</li>";
}
echo "</ul>";

// Diagnostic: Show providers
echo "<h4>Providers:</h4><ul>";
$provStmt = $conn->query("SELECT id, name, image, is_active FROM `providers`");
while ($row = $provStmt->fetch()) {
    echo "<li>ID: {$row['id']} | Name: <b>{$row['name']}</b> | Image: <code>" . htmlspecialchars($row['image'] ?? 'NULL') . "</code> | Active: {$row['is_active']}</li>";
}
echo "</ul>";


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

// Subdirectories to specifically ensure exist in both storage locations
foreach (['methods', 'providers', 'sounds', 'avatars'] as $sub) {
    @mkdir($publicAppDir . '/' . $sub, 0755, true);
    if (is_dir($pubStorage)) {
        @mkdir($pubStorage . '/' . $sub, 0755, true);
    }
}

if (is_dir($appDir)) {
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($appDir, RecursiveDirectoryIterator::SKIP_DOTS)
    );
    foreach ($iterator as $file) {
        if ($file->isFile()) {
            $pathname = $file->getPathname();
            $filename = $file->getFilename();
            $ext = strtolower($file->getExtension());
            if (in_array($ext, ['png', 'jpg', 'jpeg', 'webp', 'svg', 'gif', 'mp3', 'ico'])) {
                $foundFiles[] = $pathname;

                // Determine relative path relative to app or public
                $relFromApp = ltrim(str_replace(['\\', $appDir], ['/', ''], $pathname), '/');
                $relFromPublic = ltrim(str_replace(['\\', $publicAppDir], ['/', ''], $pathname), '/');

                // Candidates for destination
                $dests = [
                    $pubStorage . '/' . $filename,
                    $publicAppDir . '/' . $filename,
                    $pubStorage . '/' . $relFromPublic,
                    $publicAppDir . '/' . $relFromPublic,
                ];

                // If file is inside a subfolder like methods/ or providers/
                foreach (['methods', 'providers', 'sounds', 'avatars'] as $sub) {
                    if (str_contains($pathname, DIRECTORY_SEPARATOR . $sub . DIRECTORY_SEPARATOR) || str_contains($pathname, '/' . $sub . '/')) {
                        $dests[] = $pubStorage . '/' . $sub . '/' . $filename;
                        $dests[] = $publicAppDir . '/' . $sub . '/' . $filename;
                    }
                }

                foreach (array_unique($dests) as $dest) {
                    $parent = dirname($dest);
                    if (!is_dir($parent)) {
                        @mkdir($parent, 0755, true);
                    }
                    if (!file_exists($dest) || filesize($dest) === 0) {
                        if (@copy($pathname, $dest)) {
                            $copied++;
                        }
                    }
                }
            }
        }
    }
}

echo "Storage sync: Found " . count($foundFiles) . " media files. Copied $copied files to public_html/storage.<br>";

// Search for 01KY files across home directory
echo "<h4>Search for 01KY files:</h4><pre>";
$searchCmd = "find /home/earneslx -name '*01KY*' 2>/dev/null";
$searchOutput = shell_exec($searchCmd);
echo htmlspecialchars($searchOutput ?: "No 01KY files found with find command.\n");

// Also check where Livewire / Filament stores temporary uploads
$livewireDirs = [
    $gtpDir . '/storage/app/livewire-tmp',
    $gtpDir . '/storage/app/public/livewire-tmp',
    '/home/earneslx/tmp',
    sys_get_temp_dir(),
];
foreach ($livewireDirs as $ld) {
    if (is_dir($ld)) {
        echo "Dir $ld exists. Files: " . count(scandir($ld)) . "\n";
    }
}
echo "</pre>";

if (!empty($foundFiles)) {
    echo "<details><summary>View synced files (" . count($foundFiles) . ")</summary><pre>" . htmlspecialchars(implode("\n", $foundFiles)) . "</pre></details>";
}

echo "<h3>All set! <a href='/admin'>Go to Admin Panel</a> | <a href='/partners'>View Partners</a> | <a href='/earn'>View Earn</a></h3>";


