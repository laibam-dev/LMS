<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);


include "header.php";
include "../config/db.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'instructor') {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['id'])) {
    die("Course ID missing");
}

$course_id = intval($_GET['id']);
$instructor_id = $_SESSION['user_id'];

/* Fetch course */
$sql = "SELECT * FROM courses 
        WHERE id = $course_id 
        AND instructor_id = $instructor_id";

$result = mysqli_query($conn, $sql);

if (!$result) {
    die("Query error: " . mysqli_error($conn));
}

if (mysqli_num_rows($result) !== 1) {
    die("Course not found or access denied");
}

$course = mysqli_fetch_assoc($result);

/* Fetch lessons */
$lessons_q = "SELECT * FROM lessons WHERE course_id = $course_id";
$lessons_r = mysqli_query($conn, $lessons_q);
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h3><?= htmlspecialchars($course['title']) ?></h3>
            <p class="text-muted mb-0"><?= nl2br(htmlspecialchars($course['description'])) ?></p>
        </div>

        <a href="add_lesson.php?course_id=<?= $course_id ?>" class="btn btn-success">
            + Add Lesson
        </a>
    </div>

    <hr>
    <h5>Lessons</h5>


<?php if (mysqli_num_rows($lessons_r) > 0) { ?>
<div class="list-group mt-3">
<?php while ($lesson = mysqli_fetch_assoc($lessons_r)) { ?>
    <div class="list-group-item d-flex justify-content-between align-items-center">
        <div>
            <strong><?= htmlspecialchars($lesson['title']) ?></strong>
            <?php if (!empty($lesson['pdf_file'])) { ?>
                <br>
                <a href="../uploads/pdfs/<?= $lesson['pdf_file'] ?>" target="_blank" class="text-decoration-none">
                    📄 View PDF
                </a>
            <?php } ?>
        </div>

        <div>
            <a href="edit_lesson.php?id=<?= $lesson['id'] ?>&course_id=<?= $course_id ?>"
               class="btn btn-sm btn-outline-secondary me-2">Edit</a>

            <a href="delete_lesson.php?id=<?= $lesson['id'] ?>&course_id=<?= $course_id ?>"
               class="btn btn-sm btn-outline-danger"
               onclick="return confirm('Delete this lesson?')">Delete</a>
        </div>
    </div>
<?php } ?>
</div>

<?php } else { ?>
    <p>No lessons yet.</p>
<?php } ?>

<br>
<a href="courses.php">← Back to Courses</a>
<?php include "footer.php"; ?>
