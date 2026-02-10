<?php
require_once "../config/db.php";
require_once "../config/base.php";
require_once "session.php";

$instructor_id    = $_SESSION['instructor_id'];
$instructor_name  = $_SESSION['instructor_name'] ?? 'Instructor';
$instructor_email = $_SESSION['instructor_email'] ?? '';

require_once "header.php";
require_once "sidebar.php";

$course_id = (int)($_GET['course_id'] ?? 0);

// Optional: fetch course title
$courseTitle = "Course Overview";
if ($course_id > 0) {
    $stmt = $conn->prepare("SELECT title FROM courses WHERE id=? AND instructor_id=? LIMIT 1");
    $stmt->bind_param("ii", $course_id, $instructor_id);
    $stmt->execute();
    $res = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!empty($res['title'])) {
        $courseTitle = $res['title'];
    }
}
?>

<div class="main">

    <div class="top">
        <div>
            <h1>Actions</h1>
            <p>Choose what you want to manage for: <b><?php echo htmlspecialchars($courseTitle); ?></b></p>
        </div>

        <a href="<?php echo BASE_URL; ?>/instructor/courses.php" class="btn-light">← Back</a>
    </div>

    <div class="action-buttons">

        <a class="action-btn lessons"
           href="<?php echo BASE_URL; ?>/instructor/lessons.php?course_id=<?php echo $course_id; ?>">
            <div class="icon">📚</div>
            <div class="title">Lessons</div>
            <div class="subtitle">Manage course lessons</div>
        </a>

        <a class="action-btn assessments"
           href="<?php echo BASE_URL; ?>/instructor/assessments.php?course_id=<?php echo $course_id; ?>">
            <div class="icon">📝</div>
            <div class="title">Assessments</div>
            <div class="subtitle">Assignments & grading</div>
        </a>

        <a class="action-btn quizzes"
           href="<?php echo BASE_URL; ?>/instructor/quizzes.php?course_id=<?php echo $course_id; ?>">
            <div class="icon">✅</div>
            <div class="title">Quizzes</div>
            <div class="subtitle">Tests & MCQs</div>
        </a>

    </div>

</div>

<?php require_once "footer.php"; ?>
