<?php
session_start();
// BASE_URL aur database ke liye db.php include
require_once '../../config/db.php'; 

// Agar student login nahi hai toh login page par bhejein
if(!isset($_SESSION['student_id'])){
    header("Location: " . BASE_URL . "student/login.php");
    exit();
}

if (!isset($_GET['file'])) {
    die("File name missing.");
}

$file_name = basename($_GET['file']); 
// Internal server path (ye file read karne ke liye zaroori hai)
$file_path = "../../uploads/pdfs/" . $file_name; 

if (file_exists($file_path)) {
    header('Content-type: application/pdf');
    header('Content-Disposition: inline; filename="' . $file_name . '"');
    header('Content-Transfer-Encoding: binary');
    header('Accept-Ranges: bytes');
    readfile($file_path);
} else {
    echo "<div style='text-align:center; margin-top:50px; font-family:Arial;'>";
    echo "<h3 style='color:red;'>Error: PDF File nahi mili!</h3>";
    echo "<p>File ka naam: <b>$file_name</b></p>";
    echo "<p>Rasta (Path): <b>uploads/pdfs/</b></p>";
    echo "<br><a href='" . BASE_URL . "student/Dashboard/dashboard.php' style='padding:10px 20px; background:#17a2b8; color:white; text-decoration:none; border-radius:5px;'>Wapis Dashboard par jayein</a>";
    echo "</div>";
}
?>