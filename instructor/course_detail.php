<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

error_reporting(E_ALL);
ini_set('display_errors', 1);

include "../config/db.php";
include "header.php";

/* AUTH CHECK */
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'instructor') {
    header("Location: login.php");
    exit;
}

/* COURSE ID CHECK */
if (!isset($_GET['course_id'])) {
    die("Course ID missing");
}

$course_id     = (int) $_GET['course_id'];
$instructor_id = (int) $_SESSION['user_id'];

/* FETCH COURSE */
$course_q = mysqli_query($conn, "
    SELECT *
    FROM courses
    WHERE id = $course_id
      AND instructor_id = $instructor_id
");

if (!$course_q || mysqli_num_rows($course_q) !== 1) {
    die("Course not found or access denied");
}

$course = mysqli_fetch_assoc($course_q);

/* ASSIGNMENTS */
$assignments_q = mysqli_query($conn, "
    SELECT *
    FROM assignments
    WHERE course_id = $course_id
    ORDER BY id DESC
");

/* STUDENTS */
$students_q = mysqli_query($conn, "
    SELECT u.*
    FROM enrollments e
    JOIN users u ON u.id = e.user_id
    WHERE e.course_id = $course_id
");
?>


<div class="container mt-4">

    <!-- COURSE HEADER -->
    <div class="card mb-4">
        <div class="card-body d-flex justify-content-between align-items-center">
            <div>
                <span class="badge bg-light text-dark mb-2"><?= htmlspecialchars($course['subject']) ?></span>
                <h2><?= htmlspecialchars($course['title']) ?></h2>
                <p class="text-muted">
                    <?= $course['description'] ?: 'No description available.' ?>
                </p>
                <small>
                    👥 <?= mysqli_num_rows($students_q) ?> Students &nbsp; 📄 <?= mysqli_num_rows($assignments_q) ?> Assignments
                </small>
            </div>
            <div class="text-end">
                <span class="badge bg-primary">CREATED ON</span><br>
                <?= date("F d, Y", strtotime($course['created_at'])) ?>
            </div>
        </div>
    </div>

    <!-- TABS -->
    <ul class="nav nav-pills mb-3">
        <li class="nav-item">
            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#assignments">
                Assignments
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#students">
                Students
            </button>
        </li>
    </ul>

    <div class="tab-content">

        <!-- ASSIGNMENTS TAB -->
        <div class="tab-pane fade show active" id="assignments">
            <div class="d-flex justify-content-between mb-3">
                <h5>Course Assignments</h5>
                <a href="add_assignment.php?course_id=<?= $course_id ?>" class="btn btn-primary btn-sm">
                    + New Assignment
                </a>
            </div>

            <?php if (mysqli_num_rows($assignments_q) === 0): ?>
                <div class="alert alert-light text-center">
                    📘 No assignments yet<br>
                    Create the first assignment for this course.
                </div>
            <?php else: ?>
                <ul class="list-group">
                    <?php while ($a = mysqli_fetch_assoc($assignments_q)): ?>
                        <li class="list-group-item">
                            <strong><?= htmlspecialchars($a['title']) ?></strong>
                            <div class="text-muted small"><?= $a['due_date'] ?></div>
                        </li>
                    <?php endwhile; ?>
                </ul>
            <?php endif; ?>
        </div>

        <!-- STUDENTS TAB -->
        <div class="tab-pane fade" id="students">
            <h5 class="mb-3">Enrolled Students</h5>

            <?php if (mysqli_num_rows($students_q) === 0): ?>
                <div class="alert alert-light text-center">
                    👥 No students enrolled<br>
                    Enroll students to grant them access.
                </div>
            <?php else: ?>
                <ul class="list-group">
                    <?php while ($s = mysqli_fetch_assoc($students_q)): ?>
                        <li class="list-group-item">
                            <?= htmlspecialchars($s['name']) ?>  
                            <small class="text-muted">(<?= $s['email'] ?>)</small>
                        </li>
                    <?php endwhile; ?>
                </ul>
            <?php endif; ?>
        </div>

    </div>

    <br>
    <a href="courses.php">← Back to Courses</a>
</div>

<?php include "footer.php"; ?>
