<?php
/**
 * Database Connection File (PDO)
 * Connects Budgify to MySQL running in XAMPP.
 */

// Database configuration (default XAMPP settings)
define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'budgify');
define('DB_USER', 'root');
define('DB_PASS', '');

// Automatically detect project base URL (e.g., /budgify)
if (!defined('BASE_URL')) {
    $appRoot = str_replace('\\', '/', realpath(__DIR__ . '/..'));
    $docRoot = isset($_SERVER['DOCUMENT_ROOT']) ? realpath($_SERVER['DOCUMENT_ROOT']) : false;
    $baseUrl = '';

    if ($docRoot !== false) {
        $docRoot = rtrim(str_replace('\\', '/', $docRoot), '/');
        if (stripos($appRoot, $docRoot) === 0) {
            $relative = trim(substr($appRoot, strlen($docRoot)), '/');
            $baseUrl = $relative === '' ? '' : '/' . $relative;
        }
    }
    define('BASE_URL', $baseUrl);
}

// Create PDO connection
try {
    $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]);
} catch (PDOException $e) {
    die('Database connection failed. Please start MySQL in XAMPP and import database.sql.');
}

