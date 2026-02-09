<?php
require_once "../config/db.php";
require_once "../config/base.php";
require_once "session.php";


$instructor_id = $_SESSION['instructor_id'];

require_once "header.php";
require_once "sidebar.php";

$quiz_id   = (int)($_GET['quiz_id'] ?? $_GET['id'] ?? 0);
$course_id = (int)($_GET['course_id'] ?? 0);

if ($quiz_id <= 0 || $course_id <= 0) {
    header("Location: courses.php");
    exit;
}

/* ✅ Verify quiz belongs to instructor */
$stmt = $conn->prepare("
    SELECT q.id, q.title, q.total_marks, q.due_date
    FROM quizzes q
    JOIN courses c ON c.id = q.course_id
    WHERE q.id=? AND q.course_id=? AND c.instructor_id=?
    LIMIT 1
");
$stmt->bind_param("iii", $quiz_id, $course_id, $instructor_id);
$stmt->execute();
$quiz = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$quiz) {
    header("Location: quizzes.php?course_id=" . $course_id);
    exit;
}

$message = "";
$message_type = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $title       = trim($_POST['title'] ?? "");
    $total_marks = (int)($_POST['total_marks'] ?? 0);
    $due_date    = !empty($_POST['due_date']) ? $_POST['due_date'] : null;

    if ($title === "" || $total_marks <= 0) {
        $message = "Quiz title and total marks are required.";
        $message_type = "error";
    } else {
        $stmt = $conn->prepare("
            UPDATE quizzes
            SET title=?, total_marks=?, due_date=?
            WHERE id=? AND course_id=?
            LIMIT 1
        ");
        $stmt->bind_param("sisii", $title, $total_marks, $due_date, $quiz_id, $course_id);

        if ($stmt->execute()) {
            $message = "Quiz updated successfully!";
            $message_type = "success";

            // refresh quiz data
            $quiz['title'] = $title;
            $quiz['total_marks'] = $total_marks;
            $quiz['due_date'] = $due_date;
        } else {
            $message = "Failed to update quiz.";
            $message_type = "error";
        }
        $stmt->close();
    }
}
?>

<div class="main">

    <div class="top">
        <div>
            <h1>Edit Quiz</h1>
            <p>Update your quiz details</p>
        </div>

        <a href="quizzes.php?course_id=<?php echo $course_id; ?>" class="btn-light">← Back</a>
    </div>

    <div class="form-card">

        <?php if (!empty($message)): ?>
            <div class="alert <?php echo $message_type === 'error' ? 'alert-error' : 'alert-success'; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="form-modern">

            <div class="form-group">
                <label>Quiz Title *</label>
                <input type="text" name="title" required
                       value="<?php echo htmlspecialchars($quiz['title']); ?>">
            </div>

            <div class="form-group">
                <label>Total Marks *</label>
                <input type="number" name="total_marks" min="1" required
                       value="<?php echo (int)$quiz['total_marks']; ?>">
            </div>

            <div class="form-group">
                <label>Due Date</label>
                <input type="date" name="due_date"
                       value="<?php echo !empty($quiz['due_date']) ? htmlspecialchars($quiz['due_date']) : ''; ?>">
            </div>

            <div class="form-actions">
                <a href="quizzes.php?course_id=<?php echo $course_id; ?>" class="btn-light">Cancel</a>
                <button type="submit" class="btn-primary">Update Quiz</button>
            </div>

        </form>

    </div>

</div>

<?php require_once "footer.php"; ?>
