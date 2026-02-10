<?php
require_once "../config/db.php";
require_once "../config/base.php";
require_once "session.php";


$instructor_id = $_SESSION['instructor_id'];

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

if ($courseRow && !empty($courseRow['title'])) {
    $courseTitle = $courseRow['title'];
}

/* Fetch quizzes */
$stmt = $conn->prepare("
    SELECT id, title, total_marks, created_at, due_date
    FROM quizzes
    WHERE course_id = ?
    ORDER BY created_at DESC
");
$stmt->bind_param("i", $course_id);
$stmt->execute();
$quizzes = $stmt->get_result();
$stmt->close();
?>

<div class="main">

    <div class="top">
        <div>
            <h1>Quizzes</h1>
            <p>Course: <b><?php echo htmlspecialchars($courseTitle); ?></b></p>
        </div>

        <div style="display:flex; gap:10px;">
            <a href="course_overview.php?course_id=<?php echo $course_id; ?>" class="btn-light">← Back</a>
            <a href="add_quiz.php?course_id=<?php echo $course_id; ?>" class="add-btn">+ Add Quiz</a>
        </div>
    </div>

    <div class="table-card">
        <table class="modern-table">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Total Marks</th>
                    <th>Due Date</th>
                    <th>Created</th>
                    <th style="width:260px;">Action</th>
                </tr>
            </thead>

            <tbody>
            <?php if ($quizzes->num_rows > 0): ?>
                <?php while($row = $quizzes->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['title']); ?></td>
                        <td><?php echo (int)$row['total_marks']; ?></td>

                        <td>
                            <?php
                                echo !empty($row['due_date'])
                                    ? date("m/d/Y", strtotime($row['due_date']))
                                    : "--";
                            ?>
                        </td>

                        <td><?php echo date("m/d/Y", strtotime($row['created_at'])); ?></td>

                        <td>
                            <div class="action-btns">

                                <!-- ✅ QUESTIONS -->
                                 <a class="btn-sm"
   href="add_quiz_questions.php?quiz_id=<?php echo (int)$row['id']; ?>&course_id=<?php echo $course_id; ?>">
   Questions
</a>

                                <!-- ✅ EDIT (FIXED) -->
                                <a class="btn-sm"
                                   href="edit_quiz.php?id=<?php echo (int)$row['id']; ?>&course_id=<?php echo $course_id; ?>">
                                    Edit
                                </a>

                                <!-- ✅ DELETE (FIXED) -->
                                <a class="btn-sm danger"
   href="delete_quiz.php?quiz_id=<?php echo (int)$row['id']; ?>&course_id=<?php echo $course_id; ?>"
   onclick="return confirm('Delete this quiz? All questions will also be deleted.');">
   Delete
</a>

                            </div>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5">No quizzes found.</td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>

</div>

<?php require_once "footer.php"; ?>
