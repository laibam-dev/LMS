<?php
require_once "../config/db.php";
require_once "../config/base.php";
require_once "session.php";
 // ✅ BASE PATH

$instructor_id = $_SESSION['instructor_id'];

require_once "header.php";
require_once "sidebar.php";

$id        = (int)($_GET['id'] ?? 0);
$course_id = (int)($_GET['course_id'] ?? 0);

if ($id <= 0 || $course_id <= 0) {
    header("Location: " . BASE_URL . "/instructor/courses.php");
    exit;
}

/* Get assessment (only owner instructor can edit) */
$stmt = $conn->prepare("
    SELECT id, title, type, due_date
    FROM assessments
    WHERE id=? AND instructor_id=? AND course_id=?
    LIMIT 1
");
$stmt->bind_param("iii", $id, $instructor_id, $course_id);
$stmt->execute();
$assessment = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$assessment) {
    header("Location: " . BASE_URL . "/instructor/assessments.php?course_id=" . $course_id);
    exit;
}

$message = "";
$message_type = "";

/* Update assessment */
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $title    = trim($_POST['title'] ?? "");
    $type     = $_POST['type'] ?? "assignment";
    $due_date = !empty($_POST['due_date']) ? $_POST['due_date'] : null;

    if ($title === "") {
        $message = "Title is required.";
        $message_type = "error";
    } else {
        $stmt = $conn->prepare("
            UPDATE assessments
            SET title=?, type=?, due_date=?
            WHERE id=? AND instructor_id=? AND course_id=?
        ");
        $stmt->bind_param("sssiii", $title, $type, $due_date, $id, $instructor_id, $course_id);

        if ($stmt->execute()) {
            $message = "Assessment updated successfully!";
            $message_type = "success";

            $assessment['title'] = $title;
            $assessment['type'] = $type;
            $assessment['due_date'] = $due_date;
        } else {
            $message = "Failed to update assessment.";
            $message_type = "error";
        }

        $stmt->close();
    }
}
?>

<div class="main">

    <div class="top">
        <div>
            <h1>Edit Assessment</h1>
        </div>

        <a href="<?php echo BASE_URL; ?>/instructor/assessments.php?course_id=<?php echo $course_id; ?>" class="btn-light">
            ← Back
        </a>
    </div>

    <div class="form-card">

        <?php if (!empty($message)): ?>
            <div class="alert <?php echo $message_type === 'error' ? 'alert-error' : 'alert-success'; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="form-modern">

            <div class="form-group">
                <label>Title *</label>
                <input type="text" name="title" value="<?php echo htmlspecialchars($assessment['title']); ?>" required>
            </div>

            <div class="form-group">
                <label>Type *</label>
                <select name="type" required>
                    <option value="assignment" <?php echo ($assessment['type'] === 'assignment') ? 'selected' : ''; ?>>
                        Assignment
                    </option>
                    <option value="quiz" <?php echo ($assessment['type'] === 'quiz') ? 'selected' : ''; ?>>
                        Quiz
                    </option>
                </select>
            </div>

            <div class="form-group">
                <label>Due Date</label>
                <input type="date" name="due_date"
                       value="<?php echo !empty($assessment['due_date']) ? $assessment['due_date'] : ''; ?>">
            </div>

            <div class="form-actions">
                <a href="<?php echo BASE_URL; ?>/instructor/assessments.php?course_id=<?php echo $course_id; ?>" class="btn-light">
                    Cancel
                </a>
                <button type="submit" class="btn-primary">Update</button>
            </div>

        </form>

    </div>

</div>

<?php require_once "footer.php"; ?>
