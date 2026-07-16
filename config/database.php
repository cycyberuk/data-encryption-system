<?php
/**
 * Database Configuration
 * Data Encryption System
 * Paul University - Computer Science Department
 */

// Database credentials
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'mscpua');

// Application settings
define('SITE_NAME', 'DATA ENCRYPTION AND CRYPTOGRAPHIC SOLUTIONS FOR INFORMATION SECURITY BY ORJI, CYRUS EBERE');
define('SITE_URL', 'http://localhost/MSCPUA/');
define('ENCRYPTION_ALGO', 'aes-256-cbc');
define('APP_VERSION', '1.0.0');

// Paul University colors
define('COLOR_PRIMARY', '#003366');      // Paul Blue
define('COLOR_SECONDARY', '#FFD700');    // Paul Gold
define('COLOR_ACCENT', '#F5F5F5');       // Light gray
define('COLOR_DARK', '#1a1a1a');         // Dark text

// Session settings
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', 0); // Set to 1 in production with HTTPS
session_start();

// Database connection function
function getDBConnection() {
    try {
        $pdo = new PDO(
            "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
            DB_USER,
            DB_PASS,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false
            ]
        );
        return $pdo;
    } catch (PDOException $e) {
        die("Database Connection Failed: " . $e->getMessage());
    }
}

// Initialize database connection
$pdo = getDBConnection();

// Enable error reporting for development
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>
