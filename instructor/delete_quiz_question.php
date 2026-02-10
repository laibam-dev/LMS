<?php
require_once "../config/db.php";
require_once "../config/base.php";
require_once "session.php";
 // ✅ BASE PATH

$instructor_id = $_SESSION['instructor_id'];

require_once "header.php";
require_once "sidebar.php";

$quiz_id   = (int)($_GET['quiz_id'] ?? 0);
$course_id = (int)($_GET['course_id'] ?? 0);

if ($quiz_id <= 0 || $course_id <= 0) {
    header("Location: " . BASE_URL . "/instructor/courses.php");
    exit;
}

/* Fetch Quiz Title */
$quizTitle = "Quiz";
$stmt = $conn->prepare("
    SELECT q.title
    FROM quizzes q
    JOIN courses c ON c.id = q.course_id
    WHERE q.id = ? AND c.instructor_id = ?
    LIMIT 1
");
$stmt->bind_param("ii", $quiz_id, $instructor_id);
$stmt->execute();
$quizRow = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$quizRow) {
    header("Location: " . BASE_URL . "/instructor/quizzes.php?course_id=" . $course_id);
    exit;
}

$quizTitle = $quizRow['title'];

$message = "";
$message_type = "";

/* ADD QUESTION */
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $question = trim($_POST['question'] ?? '');
    $a = trim($_POST['option_a'] ?? '');
    $b = trim($_POST['option_b'] ?? '');
    $c = trim($_POST['option_c'] ?? '');
    $d = trim($_POST['option_d'] ?? '');
    $correct = strtoupper(trim($_POST['correct_option'] ?? ''));

    if ($question === "" || $a === "" || $b === "" || $c === "" || $d === "" || !in_array($correct, ['A','B','C','D'])) {
        $message = "Please fill all fields and choose correct option.";
        $message_type = "error";
    } else {

        $stmt = $conn->prepare("
            INSERT INTO quiz_questions (assessment_id, question, option_a, option_b, option_c, option_d, correct_option)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->bind_param("issssss", $quiz_id, $question, $a, $b, $c, $d, $correct);

        if ($stmt->execute()) {
            $message = "Question added successfully!";
            $message_type = "success";
        } else {
            $message = "Failed to add question.";
            $message_type = "error";
        }
        $stmt->close();
    }
}

/* FETCH QUESTIONS */
$stmt = $conn->prepare("
    SELECT id, question, correct_option
    FROM quiz_questions
    WHERE assessment_id = ?
    ORDER BY id DESC
");
$stmt->bind_param("i", $quiz_id);
$stmt->execute();
$questions = $stmt->get_result();
$stmt->close();
?>

<div class="main">

    <div class="top">
        <div>
            <h1>Quiz Questions</h1>
            <p>Quiz: <b><?php echo htmlspecialchars($quizTitle); ?></b></p>
        </div>

        <a href="<?php echo BASE_URL; ?>/instructor/quizzes.php?course_id=<?php echo $course_id; ?>" class="btn-light">
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
                <label>Question *</label>
                <textarea name="question" required placeholder="Enter question here..."></textarea>
            </div>

            <div class="two-col">
                <div class="form-group">
                    <label>Option A *</label>
                    <input type="text" name="option_a" required>
                </div>

                <div class="form-group">
                    <label>Option B *</label>
                    <input type="text" name="option_b" required>
                </div>

                <div class="form-group">
                    <label>Option C *</label>
                    <input type="text" name="option_c" required>
                </div>

                <div class="form-group">
                    <label>Option D *</label>
                    <input type="text" name="option_d" required>
                </div>
            </div>

            <div class="form-group">
                <label>Correct Option *</label>
                <select name="correct_option" required>
                    <option value="">Select</option>
                    <option value="A">A</option>
                    <option value="B">B</option>
                    <option value="C">C</option>
                    <option value="D">D</option>
                </select>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-primary">+ Add Question</button>
            </div>

        </form>

    </div>

    <div class="table-card">
        <table class="modern-table">
            <thead>
                <tr>
                    <th>Question</th>
                    <th>Correct</th>
                    <th style="width:150px;">Action</th>
                </tr>
            </thead>
            <tbody>
            <?php if($questions->num_rows > 0): ?>
                <?php while($q = $questions->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($q['question']); ?></td>
                        <td><b><?php echo htmlspecialchars($q['correct_option']); ?></b></td>
                        <td>
                            <a class="btn-sm danger"
                               href="<?php echo BASE_URL; ?>/instructor/delete_quiz_question.php?id=<?php echo (int)$q['id']; ?>&quiz_id=<?php echo $quiz_id; ?>&course_id=<?php echo $course_id; ?>"
                               onclick="return confirm('Delete this question?');">
                                Delete
                            </a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="3">No questions added yet.</td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>

</div>

<?php require_once "footer.php"; ?>
