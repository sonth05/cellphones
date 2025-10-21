<?php
declare(strict_types=1);

// Autoload (simple) - map App namespace to src
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $baseDir = __DIR__ . '/';
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }
    $relativeClass = substr($class, $len);
    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';
    if (file_exists($file)) {
        require $file;
    }
});

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Load config
require_once __DIR__ . '/config.php';

// Setup database connection (PDO)
use App\Lib\Database;
Database::init([
    'host' => DB_HOST,
    'port' => DB_PORT,
    'name' => DB_NAME,
    'user' => DB_USER,
    'pass' => DB_PASS,
    'charset' => 'utf8mb4'
]);
?>


