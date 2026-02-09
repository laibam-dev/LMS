<?php
require_once "../config/db.php";
require_once "../config/base.php";
require_once "session.php";

$instructor_id    = $_SESSION['instructor_id'];
$instructor_name  = $_SESSION['instructor_name'] ?? 'Instructor';

require_once "header.php";
require_once "sidebar.php";

$course_id = (int)($_GET['course_id'] ?? 0);

if ($course_id <= 0) {
    header("Location: courses.php");
    exit;
}

/* Fetch course title */
$courseTitle = "Course";
$stmt = $conn->prepare("SELECT title FROM courses WHERE id=? AND instructor_id=? LIMIT 1");
$stmt->bind_param("ii", $course_id, $instructor_id);
$stmt->execute();
$courseRow = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$courseRow) {
    header("Location: courses.php");
    exit;
}

$courseTitle = $courseRow['title'];

$message = "";
$message_type = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $title       = trim($_POST["title"] ?? "");
    $total_marks = (int)($_POST["total_marks"] ?? 0);
    $due_date    = !empty($_POST["due_date"]) ? $_POST["due_date"] : null;

    if ($title === "" || $total_marks <= 0) {
        $message = "Quiz title and total marks are required.";
        $message_type = "error";
    } else {

        $stmt = $conn->prepare("
            INSERT INTO quizzes (course_id, title, total_marks, due_date, created_at)
            VALUES (?, ?, ?, ?, NOW())
        ");
        $stmt->bind_param("isis", $course_id, $title, $total_marks, $due_date);

        if ($stmt->execute()) {
            $quiz_id = $conn->insert_id;
            $stmt->close();

            header("Location: add_quiz_questions.php?quiz_id=" . $quiz_id . "&course_id=" . $course_id);
            exit;
        } else {
            $message = "Failed to create quiz.";
            $message_type = "error";
        }

        $stmt->close();
    }
}
?>

<div class="main">

    <div class="top">
        <div>
            <h1>Add Quiz</h1>
            <p>Course: <b><?php echo htmlspecialchars($courseTitle); ?></b></p>
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
                <input type="text" name="title" placeholder="e.g. Chapter 1 Quiz" required>
            </div>

            <div class="form-group">
                <label>Total Marks *</label>
                <input type="number" name="total_marks" min="1" placeholder="e.g. 20" required>
            </div>

            <div class="form-group">
                <label>Due Date</label>
                <input type="date" name="due_date">
            </div>

            <div class="form-actions">
                <a href="quizzes.php?course_id=<?php echo $course_id; ?>" class="btn-light">Cancel</a>
                <button type="submit" class="btn-primary">Next → Add Questions</button>
            </div>

        </form>

    </div>

</div>

<?php require_once "footer.php"; ?>
