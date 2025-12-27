<?php
session_start();
include "../config/db.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'instructor') {
    header("Location: login.php");
    exit;
}

$lesson_id = intval($_GET['id']);
$course_id = intval($_GET['course_id']);

/* Delete lesson */
$sql = "DELETE FROM lessons WHERE id = $lesson_id";
mysqli_query($conn, $sql);

header("Location: course_detail.php?id=$course_id");
exit;
