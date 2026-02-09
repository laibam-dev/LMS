<?php
require_once "../config/db.php";
require_once "../config/base.php";
require_once "session.php";


$instructor_id    = $_SESSION['instructor_id'];
$instructor_name  = $_SESSION['instructor_name'] ?? 'Instructor';
$instructor_email = $_SESSION['instructor_email'] ?? '';

require_once "header.php";
require_once "sidebar.php";

$lesson_id = (int)($_GET['id'] ?? 0);
$course_id = (int)($_GET['course_id'] ?? 0);

if ($lesson_id <= 0 || $course_id <= 0) {
    header("Location: courses.php");
    exit;
}

$error = "";

/* Fetch lesson (only if belongs to this instructor) */
$stmt = $conn->prepare("
    SELECT lessons.id, lessons.title, lessons.content, lessons.video_url
    FROM lessons
    INNER JOIN courses ON courses.id = lessons.course_id
    WHERE lessons.id = ?
      AND lessons.course_id = ?
      AND courses.instructor_id = ?
    LIMIT 1
");
$stmt->bind_param("iii", $lesson_id, $course_id, $instructor_id);
$stmt->execute();
$lesson = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$lesson) {
    header("Location: lessons.php?course_id=" . $course_id);
    exit;
}

/* Update lesson */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title   = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $video   = trim($_POST['video_url'] ?? '');

    if ($title === '') {
        $error = "Lesson title is required.";
    } else {
        $stmt = $conn->prepare("
            UPDATE lessons
            SET title=?, content=?, video_url=?
            WHERE id=? AND course_id=?
        ");
        $stmt->bind_param("sssii", $title, $content, $video, $lesson_id, $course_id);

        if ($stmt->execute()) {
            $stmt->close();
            header("Location: lessons.php?course_id=" . $course_id);
            exit;
        } else {
            $error = "Update failed. Try again.";
        }
        $stmt->close();
    }
}
?>

<div class="main">

    <div class="top">
        <div>
            <h1>Edit Lesson</h1>
            <p>Update your lesson details</p>
        </div>

        <a href="lessons.php?course_id=<?php echo $course_id; ?>" class="btn-light">← Back</a>
    </div>

    <div class="form-card">

        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="POST" class="form-modern">

            <div class="form-group">
                <label>Lesson Title *</label>
                <input type="text" name="title" value="<?php echo htmlspecialchars($lesson['title']); ?>" required>
            </div>

            <div class="form-group">
                <label>Lesson Content</label>
                <textarea name="content" rows="6"><?php echo htmlspecialchars($lesson['content'] ?? ''); ?></textarea>
            </div>

            <div class="form-group">
                <label>Video URL (YouTube)</label>
                <input type="text" name="video_url" value="<?php echo htmlspecialchars($lesson['video_url'] ?? ''); ?>">
            </div>

            <div class="form-actions">
                <a href="lessons.php?course_id=<?php echo $course_id; ?>" class="btn-light">Cancel</a>
                <button type="submit" class="btn-primary">Save Changes</button>
            </div>

        </form>
    </div>

</div>

<?php require_once "footer.php"; ?>
