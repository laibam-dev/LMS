<?php
session_start();
include "../config/db.php";

$instructor_id = $_SESSION['user_id'];

$sql = "
SELECT 
    users.name AS student_name,
    users.email,
    courses.title AS course_title,
    enrollments.enrolled_at
FROM enrollments
JOIN users ON users.id = enrollments.student_id
JOIN courses ON courses.id = enrollments.course_id
WHERE courses.instructor_id = $instructor_id
ORDER BY enrollments.enrolled_at DESC
";

$result = mysqli_query($conn, $sql);
?>

<h2>Enrolled Students</h2>

<table border="1" cellpadding="8">
<tr>
    <th>Student Name</th>
    <th>Email</th>
    <th>Course</th>
    <th>Enrolled At</th>
</tr>

<?php while ($row = mysqli_fetch_assoc($result)) { ?>
<tr>
    <td><?= htmlspecialchars($row['student_name']) ?></td>
    <td><?= htmlspecialchars($row['email']) ?></td>
    <td><?= htmlspecialchars($row['course_title']) ?></td>
    <td><?= $row['enrolled_at'] ?></td>
</tr>
<?php } ?>
</table>
