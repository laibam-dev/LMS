<?php
session_start();
include "../config/db.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'instructor') {
    header("Location: login.php");
    exit;
}

$course_id  = (int)($_POST['course_id'] ?? 0);
$student_id = trim($_POST['student_id'] ?? '');
$email      = trim($_POST['email'] ?? '');

$_SESSION['active_tab'] = 'students';
$_SESSION['open_enroll_modal'] = true;

/* VALIDATION */
if (!$course_id || !$student_id || !$email) {
    $_SESSION['enroll_error'] = "Student ID and Email are required.";
    header("Location: course_detail.php?course_id=$course_id");
    exit;
}

$student_id = (int)$student_id;
$email_safe = mysqli_real_escape_string($conn, $email);

/* FIND STUDENT (ID + EMAIL MUST MATCH SAME USER) */
$student_q = mysqli_query($conn, "
    SELECT id FROM users
    WHERE id=$student_id
      AND email='$email_safe'
      AND role='student'
");

if (mysqli_num_rows($student_q) !== 1) {
    $_SESSION['enroll_error'] = "Student ID and Email do not match.";
    header("Location: course_detail.php?course_id=$course_id");
    exit;
}

/* CHECK ALREADY ENROLLED */
$check_q = mysqli_query($conn, "
    SELECT id FROM enrollments
    WHERE course_id=$course_id AND user_id=$student_id
");

if (mysqli_num_rows($check_q) > 0) {
    $_SESSION['enroll_error'] = "Student is already enrolled.";
    header("Location: course_detail.php?course_id=$course_id");
    exit;
}

/* ENROLL */
mysqli_query($conn, "
    INSERT INTO enrollments (course_id, user_id, status, enrolled_at)
    VALUES ($course_id, $student_id, 'active', NOW())
");

$_SESSION['enroll_success'] = "Student enrolled successfully.";
header("Location: course_detail.php?course_id=$course_id");
exit;
