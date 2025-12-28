<?php
session_start();
require_once '../../config/db.php';

$student_id = $_SESSION['student_id'];
$lesson_id  = $_POST['lesson_id'];
$course_id  = $_POST['course_id'];

/* 1️⃣ Lesson complete insert (duplicate se bachao) */
$check = $conn->prepare(
    "SELECT id FROM lesson_completion WHERE student_id=? AND lesson_id=?"
);
$check->bind_param("ii", $student_id, $lesson_id);
$check->execute();

if($check->get_result()->num_rows == 0){
    $insert = $conn->prepare(
        "INSERT INTO lesson_completion (student_id, lesson_id) VALUES (?, ?)"
    );
    $insert->bind_param("ii", $student_id, $lesson_id);
    $insert->execute();
}

/* 2️⃣ Completed lessons count */
$completed_stmt = $conn->prepare("
    SELECT COUNT(*) AS completed 
    FROM lesson_completion lc
    JOIN lessons l ON lc.lesson_id = l.id
    WHERE lc.student_id = ? AND l.course_id = ?
");
$completed_stmt->bind_param("ii", $student_id, $course_id);
$completed_stmt->execute();
$completed = $completed_stmt->get_result()->fetch_assoc()['completed'];

/* 3️⃣ Total lessons count */
$total_stmt = $conn->prepare(
    "SELECT COUNT(*) AS total FROM lessons WHERE course_id = ?"
);
$total_stmt->bind_param("i", $course_id);
$total_stmt->execute();
$total = $total_stmt->get_result()->fetch_assoc()['total'];

/* 4️⃣ Progress calculate */
$progress = ($total > 0) ? round(($completed / $total) * 100) : 0;

/* 5️⃣ student_enrollment update */
$update = $conn->prepare(
    "UPDATE student_enrollment 
     SET progress = ? 
     WHERE student_id = ? AND course_id = ?"
);
$update->bind_param("iii", $progress, $student_id, $course_id);
$update->execute();

/* 6️⃣ Back to course page */
header("Location: courses.php?course_id=".$course_id);
exit();
