<?php
session_start();
include "../config/db.php";

// Security check
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'instructor') {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: courses.php");
    exit;
}

$instructor_id = (int) $_SESSION['user_id'];

$title       = trim($_POST['title'] ?? '');
$subject     = trim($_POST['subject'] ?? '');
$description = trim($_POST['description'] ?? '');

if ($title === '') {
    header("Location: courses.php?error=title_required");
    exit;
}

$stmt = mysqli_prepare(
    $conn,
    "INSERT INTO courses (instructor_id, title, subject, description, status, created_at)
     VALUES (?, ?, ?, ?, 'draft', NOW())"
);

mysqli_stmt_bind_param(
    $stmt,
    "isss",
    $instructor_id,
    $title,
    $subject,
    $description
);

mysqli_stmt_execute($stmt);

mysqli_stmt_close($stmt);

// Back to courses list
header("Location: courses.php?created=1");
exit;

