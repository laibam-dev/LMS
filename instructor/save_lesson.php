<?php
require_once "../config/db.php";
require_once "../config/base.php";
require_once "session.php";

session_start();

if (!isset($_SESSION['instructor_id'])) {
    header("Location: login.php");
    exit;
}

$instructor_id = (int)$_SESSION['instructor_id'];

$course_id = (int)($_POST['course_id'] ?? 0);
$title     = trim($_POST['title'] ?? '');
$content   = trim($_POST['content'] ?? '');
$video_url = trim($_POST['video_url'] ?? '');

if ($course_id <= 0 || $title === '') {
    header("Location: add_lesson.php");
    exit;
}

/* Verify course belongs to instructor */
$stmt = $conn->prepare("SELECT id FROM courses WHERE id = ? AND instructor_id = ?");
$stmt->bind_param("ii", $course_id, $instructor_id);
$stmt->execute();
if ($stmt->get_result()->num_rows === 0) {
    header("Location: lessons.php");
    exit;
}

/* PDF upload */
if (!isset($_FILES['pdf_file']) || $_FILES['pdf_file']['error'] !== 0) {
    header("Location: add_lesson.php?course_id=" . $course_id);
    exit;
}

$ext = strtolower(pathinfo($_FILES['pdf_file']['name'], PATHINFO_EXTENSION));
if ($ext !== 'pdf') {
    header("Location: add_lesson.php?course_id=" . $course_id);
    exit;
}

$upload_dir = "../uploads/lessons/";
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

$file_name = uniqid("lesson_") . ".pdf";
$target = $upload_dir . $file_name;

move_uploaded_file($_FILES['pdf_file']['tmp_name'], $target);

/* Insert */
$stmt = $conn->prepare(
    "INSERT INTO lessons (course_id, title, content, pdf_file, video_url, created_at)
     VALUES (?, ?, ?, ?, ?, NOW())"
);
$stmt->bind_param("issss", $course_id, $title, $content, $file_name, $video_url);
$stmt->execute();

header("Location: lessons.php?added=1");
exit;
