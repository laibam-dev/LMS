<?php
if (session_status() === PHP_SESSION_NONE) session_start();

include "../config/db.php";
include "header.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'instructor') {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['course_id'])) {
    die("Course ID missing");
}

$course_id = (int)$_GET['course_id'];
$instructor_id = (int)$_SESSION['user_id'];

/* check course belongs to this instructor */
$course_q = mysqli_query($conn, "
    SELECT id, title
    FROM courses
    WHERE id = $course_id AND instructor_id = $instructor_id
");

if (!$course_q || mysqli_num_rows($course_q) !== 1) {
    die("Course not found or access denied");
}

$course = mysqli_fetch_assoc($course_q);
?>

<div class="container mt-4" style="max-width: 720px;">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h3 class="mb-0">Add Assignment</h3>
            <small class="text-muted">Course: <?= htmlspecialchars($course['title']) ?></small>
        </div>
        <a href="course_detail.php?course_id=<?= $course_id ?>" class="btn btn-outline-secondary btn-sm">
            ← Back
        </a>
    </div>

    <?php if (isset($_GET['error']) && $_GET['error'] === 'title_required'): ?>
        <div class="alert alert-danger">Assignment title is required.</div>
    <?php endif; ?>

    <div class="card shadow-sm">
        <div class="card-body">
            <form method="POST" action="add_assignment_action.php">
                <input type="hidden" name="course_id" value="<?= $course_id ?>">

                <div class="mb-3">
                    <label class="form-label">Assignment Title</label>
                    <input type="text" name="title" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Instructions</label>
                    <textarea name="instructions" class="form-control" rows="5" placeholder="Write instructions..."></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Due Date</label>
                    <input type="date" name="due_date" class="form-control">
                </div>

                <button type="submit" class="btn btn-primary w-100">Create Assignment</button>
            </form>
        </div>
    </div>
</div>

<?php include "footer.php"; ?>
