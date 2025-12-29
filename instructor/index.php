<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();


if (!isset($_SESSION['instructor_id'])) {
    header("Location: login.php");
    exit;
}
?>


<?php
// header + session check
include "../config/db.php";
include "header.php";

$instructor_id = (int)$_SESSION['user_id'];

// Active Courses (published)
$q1 = mysqli_query($conn, "SELECT COUNT(*) total FROM courses WHERE instructor_id=$instructor_id AND status='published'");
$active_courses = mysqli_fetch_assoc($q1)['total'] ?? 0;

/// TOTAL STUDENTS
$q_students = mysqli_query($conn, "
    SELECT COUNT(DISTINCT e.user_id) AS total
    FROM enrollments e
    JOIN courses c ON c.id = e.course_id
    WHERE c.instructor_id = $instructor_id
");
$total_students = mysqli_fetch_assoc($q_students)['total'] ?? 0;

// Upcoming Tasks
$q3 = mysqli_query($conn, "
  SELECT COUNT(*) total
  FROM assignments a
  JOIN courses c ON c.id = a.course_id
  WHERE c.instructor_id=$instructor_id
    AND a.due_date IS NOT NULL
    AND a.due_date >= CURDATE()
");
$upcoming_tasks = mysqli_fetch_assoc($q3)['total'] ?? 0;
?>

<div class="page-head">
  <div>
    <h1>Welcome back, <?= htmlspecialchars($_SESSION['name'] ?? 'Instructor') ?></h1>
    <p>Manage your courses and students from here.</p>
  </div>

  <!-- FIXED BUTTON -->
  <a
    href="courses.php?open=create"
    class="btn btn-primary px-4 py-2 rounded-3"
    style="width:auto; display:inline-flex; align-items:center;"
  >
    <i class="bi bi-plus-lg me-2"></i>
    Add Course
  </a>
</div>

<div class="stat-grid">
  <div class="cardx stat">
    <div class="ico"><i class="bi bi-book"></i></div>
    <div>
      <p class="label">Active Courses</p>
      <p class="val"><?= (int)$active_courses ?></p>
    </div>
  </div>

  <div class="cardx stat">
    <div class="ico"><i class="bi bi-people"></i></div>
    <div>
      <p class="label">Total Students</p>
      <p class="val"><?= (int)$total_students ?></p>
    </div>
  </div>

  <div class="cardx stat">
    <div class="ico"><i class="bi bi-calendar-event"></i></div>
    <div>
      <p class="label">Upcoming Tasks</p>
      <p class="val"><?= (int)$upcoming_tasks ?></p>
    </div>
  </div>
</div>

<?php include "footer.php"; ?>
