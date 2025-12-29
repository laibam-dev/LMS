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

/* IDS */
if (!isset($_GET['id'], $_GET['course_id'])) {
    die("Missing parameters");
}

$assignment_id = (int) $_GET['id'];
$course_id     = (int) $_GET['course_id'];
$instructor_id = (int) $_SESSION['user_id'];

/* FETCH ASSIGNMENT */
$q = mysqli_query($conn, "
    SELECT a.*, c.title AS course_title
    FROM assignments a
    JOIN courses c ON c.id = a.course_id
    WHERE a.id = $assignment_id
      AND a.course_id = $course_id
      AND c.instructor_id = $instructor_id
");

if (!$q || mysqli_num_rows($q) !== 1) {
    die("Assignment not found or access denied");
}

$assignment = mysqli_fetch_assoc($q);

/* UPDATE */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $instructions = trim($_POST['instructions']);
    $due_date = $_POST['due_date'] ?: NULL;

    mysqli_query($conn, "
        UPDATE assignments
        SET title = '".mysqli_real_escape_string($conn,$title)."',
            instructions = '".mysqli_real_escape_string($conn,$instructions)."',
            due_date = ".($due_date ? "'$due_date'" : "NULL")."
        WHERE id = $assignment_id
          AND course_id = $course_id
    ");

    header("Location: course_detail.php?course_id=$course_id");
    exit;
}
?>

<div class="container mt-4">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>Edit Assignment</h3>
        <a href="course_detail.php?course_id=<?= $course_id ?>" class="btn btn-outline-secondary btn-sm">
            ← Back
        </a>
    </div>

    <div class="card p-4 mx-auto" style="max-width: 650px;">
        <div class="mb-3 text-muted">
            Course: <strong><?= htmlspecialchars($assignment['course_title']) ?></strong>
        </div>

        <form method="POST">

            <div class="mb-3">
                <label class="form-label fw-bold">Assignment Title</label>
                <input class="form-control" name="title" required
                       value="<?= htmlspecialchars($assignment['title']) ?>">
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">Instructions</label>
                <textarea class="form-control" rows="5"
                          name="instructions"><?= htmlspecialchars($assignment['instructions']) ?></textarea>
            </div>

            <div class="mb-4">
                <label class="form-label fw-bold">Due Date</label>
                <input type="date" class="form-control"
                       name="due_date"
                       value="<?= $assignment['due_date'] ?>">
            </div>

            <button class="btn btn-primary w-100">
                Update Assignment
            </button>

        </form>
    </div>
</div>

<?php include "footer.php"; ?>
