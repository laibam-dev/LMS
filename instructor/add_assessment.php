<?php
require_once "../config/db.php";
require_once "../config/base.php";
require_once "session.php";
 

$instructor_id    = $_SESSION['instructor_id'];
$instructor_name  = $_SESSION['instructor_name'] ?? 'Instructor';
$instructor_email = $_SESSION['instructor_email'] ?? '';

require_once "header.php";
require_once "sidebar.php";

$message = "";
$message_type = "";

$course_id = (int)($_GET['course_id'] ?? 0);

/* Dropdown courses */
$stmt = $conn->prepare("SELECT id, title FROM courses WHERE instructor_id = ? ORDER BY title");
$stmt->bind_param("i", $instructor_id);
$stmt->execute();
$courses_result = $stmt->get_result();
$stmt->close();

/* Save assessment */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $course_id     = (int)($_POST['course_id'] ?? 0);
    $title         = trim($_POST['title'] ?? '');
    $type          = trim($_POST['type'] ?? '');
    $instructions  = trim($_POST['instructions'] ?? '');
    $due_date      = !empty($_POST['due_date']) ? $_POST['due_date'] : null;

    if ($course_id <= 0 || $title === '' || $type === '') {
        $message = "Assessment title, course, and type are required.";
        $message_type = "error";
    } else {

        $stmt = $conn->prepare("
            INSERT INTO assessments (course_id, instructor_id, title, type, instructions, due_date)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $stmt->bind_param("iissss", $course_id, $instructor_id, $title, $type, $instructions, $due_date);

        if ($stmt->execute()) {
            $assessment_id = $conn->insert_id;
            $stmt->close();

            // if quiz => redirect to add quiz questions page
            if (strtolower($type) === 'quiz') {
                header("Location: add_quiz_questions.php?assessment_id=" . $assessment_id . "&course_id=" . $course_id);
                exit;
            }

            header("Location: assessments.php?course_id=" . $course_id . "&added=1");
            exit;

        } else {
            $message = "Failed to add assessment.";
            $message_type = "error";
        }

        $stmt->close();
    }
}
?>

<div class="main">

    <div class="top">
        <div>
            <h1>Add Assessment</h1>
            <p>Create a new assessment for your course.</p>
        </div>

        <a href="assessments.php?course_id=<?php echo $course_id; ?>" class="btn-light">← Back</a>
    </div>

    <div class="form-card">

        <?php if (!empty($message)): ?>
            <div class="alert <?php echo $message_type === 'error' ? 'alert-error' : 'alert-success'; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="form-modern">

            <div class="form-group">
                <label>Course *</label>
                <select name="course_id" required>
                    <option value="">-- Select Course --</option>
                    <?php while ($c = $courses_result->fetch_assoc()): ?>
                        <option value="<?php echo (int)$c['id']; ?>"
                            <?php echo ($course_id == $c['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($c['title']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Title *</label>
                <input type="text" name="title" placeholder="e.g. Midterm Test" required>
            </div>

            <div class="form-group">
                <label>Type *</label>
                <select name="type" required>
                    <option value="">-- Select Type --</option>
                    <option value="assignment">Assignment</option>
                    <option value="quiz">Quiz</option>
                    <option value="test">Test</option>
                </select>
            </div>

            <div class="form-group">
                <label>Instructions</label>
                <textarea name="instructions" rows="5" placeholder="Write instructions for students..."></textarea>
            </div>

            <div class="form-group">
                <label>Due Date</label>
                <input type="date" name="due_date">
            </div>

            <div class="form-actions">
                <a href="assessments.php?course_id=<?php echo $course_id; ?>" class="btn-light">Cancel</a>
                <button type="submit" class="btn-primary">Save Assessment</button>
            </div>

        </form>

    </div>

</div>

<?php require_once "footer.php"; ?>
