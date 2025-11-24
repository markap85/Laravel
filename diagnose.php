<?php
echo "<h2>Laravel Diagnostic</h2>";

// Check PHP version
echo "<strong>PHP Version:</strong> " . phpversion() . "<br>";

// Check .env file
$envPath = __DIR__ . '/../.env';
if (file_exists($envPath)) {
    echo "<strong>.env file found.</strong><br>";
    $envContent = file_get_contents($envPath);
    echo "<pre>" . htmlspecialchars($envContent) . "</pre>";
} else {
    echo "<strong>.env file NOT found!</strong><br>";
}

// Check database connection
try {
    require __DIR__ . '/../vendor/autoload.php';
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
    $dotenv->load();
    
    $connection = $_ENV['DB_CONNECTION'] ?? 'mysql';
    
    if ($connection === 'sqlite') {
        $dbPath = $_ENV['DB_DATABASE'] ?? '';
        if (!file_exists($dbPath)) {
            echo "<strong>SQLite database file NOT found at:</strong> " . htmlspecialchars($dbPath) . "<br>";
        } else {
            $pdo = new PDO("sqlite:$dbPath");
            echo "<strong>Database connection successful (SQLite).</strong><br>";
        }
    } else {
        $host = $_ENV['DB_HOST'] ?? 'localhost';
        $db = $_ENV['DB_DATABASE'] ?? '';
        $user = $_ENV['DB_USERNAME'] ?? '';
        $pass = $_ENV['DB_PASSWORD'] ?? '';
        $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
        echo "<strong>Database connection successful (MySQL).</strong><br>";
    }
} catch (Exception $e) {
    echo "<strong>Database connection failed:</strong> " . htmlspecialchars($e->getMessage()) . "<br>";
}

// Check .htaccess file
$htaccessPath = __DIR__ . '/.htaccess';
if (file_exists($htaccessPath)) {
    echo "<strong>.htaccess file found.</strong><br>";
    $htaccessContent = file_get_contents($htaccessPath);
    echo "<pre>" . htmlspecialchars($htaccessContent) . "</pre>";
} else {
    echo "<strong>.htaccess file NOT found!</strong><br>";
}

echo "<h3>Storage Permissions Check</h3>";

// Check storage directories
$storagePath = __DIR__ . '/../storage';
$bootstrapCachePath = __DIR__ . '/../bootstrap/cache';

$directories = [
    'storage' => $storagePath,
    'storage/framework' => $storagePath . '/framework',
    'storage/framework/cache' => $storagePath . '/framework/cache',
    'storage/framework/sessions' => $storagePath . '/framework/sessions',
    'storage/framework/views' => $storagePath . '/framework/views',
    'storage/logs' => $storagePath . '/logs',
    'bootstrap/cache' => $bootstrapCachePath,
];

foreach ($directories as $name => $path) {
    if (file_exists($path)) {
        $perms = substr(sprintf('%o', fileperms($path)), -4);
        $writable = is_writable($path) ? '✓ Writable' : '✗ NOT writable';
        echo "<strong>$name:</strong> Exists | Permissions: $perms | $writable<br>";
    } else {
        echo "<strong>$name:</strong> ✗ MISSING<br>";
    }
}

echo "<h3>Check Latest Error Log</h3>";
$logPath = $storagePath . '/logs/laravel.log';
if (file_exists($logPath)) {
    $logContent = file_get_contents($logPath);
    $lines = explode("\n", $logContent);
    
    // Get last 100 lines to capture full error
    $lastLines = array_slice($lines, -100);
    echo "<strong>Last 100 lines of laravel.log:</strong><br>";
    echo "<pre style='max-height: 500px; overflow-y: scroll; background: #f5f5f5; padding: 10px;'>" . htmlspecialchars(implode("\n", $lastLines)) . "</pre>";
    
    // Try to find the most recent exception/error
    $reversedLines = array_reverse($lines);
    foreach ($reversedLines as $line) {
        if (strpos($line, 'production.ERROR') !== false || strpos($line, 'Exception') !== false) {
            echo "<h4>Most Recent Error:</h4>";
            echo "<pre style='background: #ffe6e6; padding: 10px;'>" . htmlspecialchars($line) . "</pre>";
            break;
        }
    }
} else {
    echo "<strong>laravel.log NOT found.</strong><br>";
}
?>