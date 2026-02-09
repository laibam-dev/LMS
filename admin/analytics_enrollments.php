<?php
// Database config include kiya (BASE_URL yahan se initialize ho jayega)
include '../config/db.php';

header('Content-Type: application/json');

// Aapka original SQL logic (No changes)
$sql = "SELECT c.title AS course_title, COUNT(e.id) AS enroll_count
        FROM courses c
        LEFT JOIN enrollments e ON c.id = e.course_id
        GROUP BY c.id
        ORDER BY enroll_count DESC";
        
$result = mysqli_query($conn, $sql);

$courses = [];
$enrollments = [];

while ($row = mysqli_fetch_assoc($result)) {
    $courses[] = $row['course_title'];
    $enrollments[] = (int)$row['enroll_count'];
}

// JSON format mein data return karna
echo json_encode([
    'courses' => $courses,
    'enrollments' => $enrollments
]);