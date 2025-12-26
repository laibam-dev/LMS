<?php
session_start();
include "../config/db.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'instructor') {
    header("Location: login.php");
    exit;
}

$course_id = intval($_GET['id']);
$status = $_GET['status'];
$instructor_id = $_SESSION['user_id'];

if (!in_array($status, ['draft', 'published'])) {
    die("Invalid status");
}

$sql = "UPDATE courses 
        SET status = '$status' 
        WHERE id = $course_id AND instructor_id = $instructor_id";

mysqli_query($conn, $sql);

header("Location: courses.php");
exit;
