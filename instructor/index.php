<?php
// header + session check
include "header.php";
include "../config/db.php";

$instructor_id = $_SESSION['user_id'];

/* ===== Dashboard Stats ===== */

/* Total courses */
$q1 = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total 
     FROM courses 
     WHERE instructor_id = $instructor_id"
);
$total_courses = mysqli_fetch_assoc($q1)['total'];

/* Published courses */
$q2 = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total 
     FROM courses 
     WHERE instructor_id = $instructor_id 
       AND status = 'published'"
);
$published_courses = mysqli_fetch_assoc($q2)['total'];

/* Total lessons */
$q3 = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total 
     FROM lessons 
     WHERE course_id IN (
         SELECT id FROM courses WHERE instructor_id = $instructor_id
     )"
);
$total_lessons = mysqli_fetch_assoc($q3)['total'];

/* PDFs uploaded */
$q4 = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total 
     FROM lessons 
     WHERE pdf_file IS NOT NULL 
       AND pdf_file != '' 
       AND course_id IN (
           SELECT id FROM courses WHERE instructor_id = $instructor_id
       )"
);
$total_pdfs = mysqli_fetch_assoc($q4)['total'];
?>

<!-- PAGE CONTENT -->

<div class="container-fluid">

    <!-- Welcome Card -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h5 class="mb-1">
                Welcome, <?= htmlspecialchars($_SESSION['name']) ?>
            </h5>
            <p class="text-muted mb-0">
                From here you can manage your courses and lessons.
            </p>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row g-3">

        <div class="col-md-3">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted">Total Courses</h6>
                    <h3><?= $total_courses ?></h3>
                </div>
            </div>
        </div>

     

        <div class="col-md-3">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h6>Total Lessons</h6>
                    <h3><?= $total_lessons ?></h3>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h6>PDFs Uploaded</h6>
                    <h3><?= $total_pdfs ?></h3>
                </div>
            </div>
        </div>

    </div>
</div>



</div> <!-- content -->
</body>
</html>