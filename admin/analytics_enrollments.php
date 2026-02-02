<?php
include '../config/db.php';
header('Content-Type: application/json');

// Get enrollments per course
$sql = "SELECT c.course_name, COUNT(e.enrollment_id) as enroll_count FROM courses c LEFT JOIN enrollments e ON c.course_id = e.course_id GROUP BY c.course_id ORDER BY enroll_count DESC";
$result = mysqli_query($conn, $sql);

$courses = [];
$enrollments = [];
while ($row = mysqli_fetch_assoc($result)) {
    $courses[] = $row['course_name'];
    $enrollments[] = (int)$row['enroll_count'];
}

echo json_encode([
    'courses' => $courses,
    'enrollments' => $enrollments
]);
