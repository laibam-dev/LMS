<?php
session_start();
include "../config/db.php";

// login protection
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

// analytics queries
$totalUsers = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(*) AS total FROM users")
)['total'];

$totalStudents = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(*) AS total FROM users WHERE role='student'")
)['total'];

$totalInstructors = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(*) AS total FROM users WHERE role='instructor'")
)['total'];

$totalCourses = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(*) AS total FROM courses")
)['total'];
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Analytics</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

<h2>Analytics</h2>

<p>Total Users: <b><?= $totalUsers ?></b></p>
<p>Total Students: <b><?= $totalStudents ?></b></p>
<p>Total Instructors: <b><?= $totalInstructors ?></b></p>
<p>Total Courses: <b><?= $totalCourses ?></b></p>

<hr>

<canvas id="analyticsChart" width="300"></canvas>

<script>
const ctx = document.getElementById('analyticsChart');

new Chart(ctx, {
    type: 'pie',
    data: {
        labels: ['Students', 'Instructors'],
        datasets: [{
            data: [<?= $totalStudents ?>, <?= $totalInstructors ?>]
        }]
    }
});
</script>

<br>
<a href="index.php">⬅ Back to Dashboard</a>

<link rel="stylesheet" href="../assets/css/admin.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="../assets/js/chart.js"></script>


</body>
</html>
