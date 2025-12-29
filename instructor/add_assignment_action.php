<?php
if (session_status() === PHP_SESSION_NONE) session_start();

include "../config/db.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'instructor') {
    header("Location: login.php");
    exit;
}

$instructor_id = (int)$_SESSION['user_id'];

$course_id = (int)($_POST['course_id'] ?? 0);
$title = trim($_POST['title'] ?? '');
$instructions = trim($_POST['instructions'] ?? '');
$due_date = trim($_POST['due_date'] ?? '');

if ($course_id <= 0) {
    die("Course ID missing");
}

if ($title === '') {
    header("Location: add_assignment.php?course_id=$course_id&error=title_required");
    exit;
}

/* verify course belongs to instructor */
$course_q = mysqli_query($conn, "
    SELECT id
    FROM courses
    WHERE id = $course_id AND instructor_id = $instructor_id
");

if (!$course_q || mysqli_num_rows($course_q) !== 1) {
    die("Course not found or access denied");
}

if ($due_date === '') {
    $due_date = null;
}

/* insert assignment */
$stmt = mysqli_prepare($conn, "
    INSERT INTO assignments (course_id, instructor_id, title, instructions, due_date)
    VALUES (?, ?, ?, ?, ?)
");

mysqli_stmt_bind_param(
    $stmt,
    "iisss",
    $course_id,
    $instructor_id,
    $title,
    $instructions,
    $due_date
);

mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

/* redirect back to course detail */
header("Location: course_detail.php?course_id=$course_id");
exit;
