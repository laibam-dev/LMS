<?php
// instructor/session.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['instructor_id']) || ($_SESSION['role'] ?? '') !== 'instructor') {
    header("Location: index.php?error=blocked");
    exit;
}

$instructor_id = (int)$_SESSION['instructor_id'];
$instructor_name = $_SESSION['instructor_name'] ?? 'Instructor';
$instructor_email = $_SESSION['instructor_email'] ?? '';
