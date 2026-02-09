<?php
require_once "../config/db.php";
require_once "../config/base.php";
require_once "session.php";


$instructor_id = $_SESSION['instructor_id'];
$course_id = (int)($_GET['id'] ?? 0);

if ($course_id <= 0) {
    header("Location: courses.php?error=Invalid course id");
    exit;
}

/* ✅ Verify course belongs to this instructor */
$stmt = $conn->prepare("SELECT id FROM courses WHERE id=? AND instructor_id=? LIMIT 1");
$stmt->bind_param("ii", $course_id, $instructor_id);
$stmt->execute();
$course = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$course) {
    header("Location: courses.php?error=Course not found");
    exit;
}

$conn->begin_transaction();

try {
    /* ✅ 1) Delete quiz questions of this course */
    $stmt = $conn->prepare("
        DELETE qq
        FROM quiz_questions qq
        INNER JOIN quizzes q ON q.id = qq.quiz_id
        WHERE q.course_id = ?
    ");
    $stmt->bind_param("i", $course_id);
    $stmt->execute();
    $stmt->close();

    /* ✅ 2) Delete quizzes of this course */
    $stmt = $conn->prepare("DELETE FROM quizzes WHERE course_id=?");
    $stmt->bind_param("i", $course_id);
    $stmt->execute();
    $stmt->close();

    /* ✅ 3) Delete assessments questions (if you have assessment_questions table) */
    // Uncomment if table exists
    /*
    $stmt = $conn->prepare("
        DELETE aq
        FROM assessment_questions aq
        INNER JOIN assessments a ON a.id = aq.assessment_id
        WHERE a.course_id = ?
    ");
    $stmt->bind_param("i", $course_id);
    $stmt->execute();
    $stmt->close();
    */

    /* ✅ 4) Delete assessments */
    $stmt = $conn->prepare("DELETE FROM assessments WHERE course_id=?");
    $stmt->bind_param("i", $course_id);
    $stmt->execute();
    $stmt->close();

    /* ✅ 5) Delete lessons */
    $stmt = $conn->prepare("DELETE FROM lessons WHERE course_id=?");
    $stmt->bind_param("i", $course_id);
    $stmt->execute();
    $stmt->close();

    /* ✅ 6) Delete enrollments */
    $stmt = $conn->prepare("DELETE FROM enrollments WHERE course_id=?");
    $stmt->bind_param("i", $course_id);
    $stmt->execute();
    $stmt->close();

    /* ✅ 7) Finally delete course */
    $stmt = $conn->prepare("DELETE FROM courses WHERE id=? AND instructor_id=? LIMIT 1");
    $stmt->bind_param("ii", $course_id, $instructor_id);
    $stmt->execute();
    $stmt->close();

    $conn->commit();

    header("Location: courses.php?success=Course deleted successfully");
    exit;

} catch (Exception $e) {
    $conn->rollback();
    header("Location: courses.php?error=Delete failed");
    exit;
}
