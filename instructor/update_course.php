<?php
session_start();
include "../config/db.php";

if (!isset($_SESSION['instructor_id'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: courses.php");
    exit;
}

$instructor_id = $_SESSION['instructor_id'];

/* Sanitize input */
$course_id   = (int)($_POST['id'] ?? 0);
$title       = trim($_POST['title'] ?? '');
$subject     = trim($_POST['subject'] ?? '');
$description = trim($_POST['description'] ?? '');
$status      = $_POST['status'] ?? 'draft';

/* Validation */
if ($course_id <= 0 || $title === '') {
    header("Location: courses.php");
    exit;
}

if (!in_array($status, ['draft', 'published'])) {
    $status = 'draft';
}

/* Security check: instructor owns course */
$stmt = $conn->prepare(
    "SELECT id FROM courses WHERE id = ? AND instructor_id = ?"
);
$stmt->bind_param("ii", $course_id, $instructor_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: courses.php");
    exit;
}

/* Update course */
$stmt = $conn->prepare(
    "UPDATE courses
     SET title = ?, subject = ?, description = ?, status = ?
     WHERE id = ? AND instructor_id = ?"
);

$stmt->bind_param(
    "ssssii",
    $title,
    $subject,
    $description,
    $status,
    $course_id,
    $instructor_id
);

$stmt->execute();

/* Redirect back */
header("Location: courses.php?updated=1");
exit;
