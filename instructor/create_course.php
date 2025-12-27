<?php

include "../config/db.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'instructor') {
    header("Location: login.php");
    exit;
}

$course_id = intval($_GET['id']);
$instructor_id = $_SESSION['user_id'];

/* Course check (security) */
$course_q = "SELECT * FROM courses 
             WHERE id = $course_id AND instructor_id = $instructor_id";
$course_r = mysqli_query($conn, $course_q);

if (mysqli_num_rows($course_r) !== 1) {
    die("Course not found or access denied");
}

$course = mysqli_fetch_assoc($course_r);

/* Lessons */
$lessons_q = "SELECT * FROM lessons WHERE course_id = $course_id";
$lessons_r = mysqli_query($conn, $lessons_q);
?>

<h2><?= htmlspecialchars($course['title']) ?></h2>
<p><?= nl2br(htmlspecialchars($course['description'])) ?></p>

<a href="add_lesson.php?course_id=<?= $course_id ?>">➕ Add Lesson</a>
<br><br>

<h3>Lessons</h3>

<?php if (mysqli_num_rows($lessons_r) > 0) { ?>
    <ul>
        <?php while ($lesson = mysqli_fetch_assoc($lessons_r)) { ?>
            <li>
                <?= htmlspecialchars($lesson['title']) ?>
                <?php if (!empty($lesson['pdf_file'])) { ?>
                    - <a href="../uploads/pdfs/<?= $lesson['pdf_file'] ?>" target="_blank">PDF</a>
                <?php } ?>
            </li>
        <?php } ?>
    </ul>
<?php } else { ?>
    <p>No lessons yet.</p>
<?php } ?>

<div class="container mt-4">
    <div class="card">
        <div class="card-header bg-dark text-white">
            Create New Course
        </div>

        <div class="card-body">
            <form method="post">

                <div class="mb-3">
                    <label class="form-label">Course Title</label>
                    <input type="text" name="title" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Course Description</label>
                    <textarea name="description" class="form-control" rows="4"></textarea>
                </div>

                <button class="btn btn-success">Create Course</button>
                <a href="courses.php" class="btn btn-secondary ms-2">Cancel</a>

            </form>
        </div>
    </div>
</div>


<br>
<a href="courses.php">← Back to Courses</a>
