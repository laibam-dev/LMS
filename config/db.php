<?php
// config/db.php - Database connection for LMS

$host = 'localhost';
$db   = 'lms';  // Aapne jo database banayi hai uska naam
$user = 'root';
$pass = '';     // XAMPP ka default password khali hota hai

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    // Error handling set karein
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>