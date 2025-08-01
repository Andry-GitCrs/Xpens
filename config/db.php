<?php
require_once 'env.php';
date_default_timezone_set('Africa/Nairobi');
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
loadEnv();
// Check if required keys exist
$host = $_ENV['DB_HOST'] ?? die("Missing DB_HOST in .env");
$db   = $_ENV['DB_NAME'] ?? die("Missing DB_NAME in .env");
$user = $_ENV['DB_USER'] ?? die("Missing DB_USER in .env");
$pass = $_ENV['DB_PASS'] ?? '';
try {
    $pdo = new PDO(
        "mysql:host={$_ENV['DB_HOST']};dbname={$_ENV['DB_NAME']}",
        $_ENV['DB_USER'],
        $_ENV['DB_PASS']
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (Exception $e) {
    die("DB Connection failed (Please try running your mariaDB service): " . $e->getMessage());
}
