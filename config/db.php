<?php

// Detect environment
if ($_SERVER['HTTP_HOST'] === 'localhost') {

    // Local XAMPP settings
    define('BASE_URL', 'http://localhost/LMS/');

    $host = "localhost";
    $user = "root";
    $password = "";
    $dbname = "lms";
    $port = 3307;

} else {

    // Online hosting settings
    define('BASE_URL', 'http://laiba-lms.great-site.net/LMS/');

    $host = "sql303.infinityfree.com";
    $user = "if0_40800821";
    $password = "r7890laiba1";
    $dbname = "if0_40800821_lms";
    $port = 3306;
}

// Create connection
$conn = new mysqli($host, $user, $password, $dbname, $port);

// Check connection
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Optional: set charset
$conn->set_charset("utf8mb4");

// -----------------------------
// Activity Logger Function
// -----------------------------
function log_activity($conn, $user_id, $action, $description) {

    $action = mysqli_real_escape_string($conn, $action);
    $description = mysqli_real_escape_string($conn, $description);

    $sql = "INSERT INTO activity_log (user_id, action, description)
            VALUES ('$user_id', '$action', '$description')";

    mysqli_query($conn, $sql);
}

?>
