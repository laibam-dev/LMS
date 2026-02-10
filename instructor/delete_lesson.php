<?php
require_once "../config/db.php";
require_once "../config/base.php";
require_once "session.php";
  // ✅ BASE PATH

$instructor_id = $_SESSION['instructor_id'];

$lesson_id = (int)($_GET['id'] ?? 0);
$course_id = (int)($_GET['course_id'] ?? 0);

if ($lesson_id <= 0 || $course_id <= 0) {
    header("Location: " . BASE_URL . "/instructor/courses.php");
    exit;
}

/*
  ✅ Secure delete:
  Only delete lesson if it belongs to instructor's course
*/
$stmt = $conn->prepare("
    DELETE lessons
    FROM lessons
    INNER JOIN courses ON courses.id = lessons.course_id
    WHERE lessons.id = ?
      AND lessons.course_id = ?
      AND courses.instructor_id = ?
");
$stmt->bind_param("iii", $lesson_id, $course_id, $instructor_id);
$stmt->execute();
$stmt->close();

/* ✅ Centralized redirect */
header("Location: " . BASE_URL . "/instructor/lessons.php?course_id=" . $course_id);
exit;
