<?php
require_once "../config/db.php";
require_once "../config/base.php";
require_once "session.php";


$instructor_id = $_SESSION['instructor_id'];

$id = (int)($_GET['id'] ?? 0);
$course_id = (int)($_GET['course_id'] ?? 0);

if ($id <= 0 || $course_id <= 0) {
    header("Location: assessments.php");
    exit;
}

/* Security: delete only instructor own assessment */
$stmt = $conn->prepare("
    DELETE FROM assessments 
    WHERE id = ? AND instructor_id = ?
");
$stmt->bind_param("ii", $id, $instructor_id);
$stmt->execute();
$stmt->close();

header("Location: assessments.php?course_id=" . $course_id . "&deleted=1");
exit;
