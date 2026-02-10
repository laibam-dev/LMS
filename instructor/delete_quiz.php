<?php
require_once "../config/db.php";
require_once "../config/base.php";
require_once "session.php";


$instructor_id = $_SESSION['instructor_id'];

$quiz_id   = (int)($_GET['quiz_id'] ?? 0);
$course_id = (int)($_GET['course_id'] ?? 0);

if ($quiz_id <= 0 || $course_id <= 0) {
    header("Location: courses.php");
    exit;
}

/* ✅ Verify quiz belongs to this instructor */
$stmt = $conn->prepare("
    SELECT q.id
    FROM quizzes q
    JOIN courses c ON c.id = q.course_id
    WHERE q.id=? AND q.course_id=? AND c.instructor_id=?
    LIMIT 1
");
$stmt->bind_param("iii", $quiz_id, $course_id, $instructor_id);
$stmt->execute();
$quiz = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$quiz) {
    header("Location: quizzes.php?course_id=" . $course_id);
    exit;
}

/* ✅ Delete quiz questions first */
$stmt = $conn->prepare("DELETE FROM quiz_questions WHERE quiz_id=?");
$stmt->bind_param("i", $quiz_id);
$stmt->execute();
$stmt->close();

/* ✅ Delete quiz */
$stmt = $conn->prepare("DELETE FROM quizzes WHERE id=? AND course_id=? LIMIT 1");
$stmt->bind_param("ii", $quiz_id, $course_id);
$stmt->execute();
$stmt->close();

header("Location: quizzes.php?course_id=" . $course_id);
exit;
?>
