<?php
if (session_status() === PHP_SESSION_NONE) session_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);

include "../config/db.php";
include "header.php";

/* AUTH */
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'instructor') {
    header("Location: login.php");
    exit;
}

/* COURSE ID */
if (!isset($_GET['course_id'])) {
    die("Course ID missing");
}

$course_id     = (int) $_GET['course_id'];
$instructor_id = (int) $_SESSION['user_id'];

/* FETCH COURSE */
$q = mysqli_query($conn, "
    SELECT *
    FROM courses
    WHERE id = $course_id
      AND instructor_id = $instructor_id
");

if (!$q || mysqli_num_rows($q) !== 1) {
    die("Course not found or access denied");
}

$course = mysqli_fetch_assoc($q);

/* UPDATE COURSE */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $title = trim($_POST['title']);
    $subject = trim($_POST['subject']);
    $description = trim($_POST['description']);

    mysqli_query($conn, "
        UPDATE courses
        SET title = '".mysqli_real_escape_string($conn,$title)."',
            subject = '".mysqli_real_escape_string($conn,$subject)."',
            description = '".mysqli_real_escape_string($conn,$description)."'
        WHERE id = $course_id
          AND instructor_id = $instructor_id
    ");

    header("Location: course_detail.php?course_id=$course_id");
    exit;
}
?>

<div class="container mt-4">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>Edit Course</h3>
        <a href="course_detail.php?course_id=<?= $course_id ?>"
           class="btn btn-outline-secondary btn-sm">
            ← Back
        </a>
    </div>

    <div class="card p-4 mx-auto" style="max-width: 650px;">

        <form method="POST">

            <div class="mb-3">
                <label class="form-label fw-bold">Course Title</label>
                <input class="form-control"
                       name="title"
                       required
                       value="<?= htmlspecialchars($course['title']) ?>">
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">Subject</label>
                <input class="form-control"
                       name="subject"
                       value="<?= htmlspecialchars($course['subject']) ?>">
            </div>

            <div class="mb-4">
                <label class="form-label fw-bold">Description</label>
                <textarea class="form-control"
                          rows="4"
                          name="description"><?= htmlspecialchars($course['description']) ?></textarea>
            </div>

            <button class="btn btn-primary w-100">
                Update Course
            </button>

        </form>

    </div>
</div>

<?php include "footer.php"; ?>
