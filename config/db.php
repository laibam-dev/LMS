<?php
// 1. Check current host (Localhost or Online)
if ($_SERVER['HTTP_HOST'] == 'localhost') {
    // Localhost settings (VS Code)
    define('BASE_URL', 'http://localhost/LMS/');
    $host = "localhost";
    $user = "root";
    $password = "";
    $dbname = "lms"; // Jo aapne bataya
} else {
    // InfinityFree settings (Online)
    define('BASE_URL', 'http://laiba-lms.great-site.net/LMS/');
    $host = "sql303.infinityfree.com"; 
    $user = "if0_40800821";         
    $password = "r7890laiba1"; 
    $dbname = "if0_40800821_lms"; 
}

// 2. Create Connection
$conn = new mysqli($host, $user, $password, $dbname);

// 3. Check Connection
if($conn->connect_error){
    die("Connection failed: " . $conn->connect_error);
}

function log_activity($conn, $user_id, $action, $description) {
    $action = mysqli_real_escape_string($conn, $action);
    $description = mysqli_real_escape_string($conn, $description);
    $query = "INSERT INTO activity_log (user_id, action, description) VALUES ('$user_id', '$action', '$description')";
    mysqli_query($conn, $query);
}
?>