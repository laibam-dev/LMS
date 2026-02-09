<?php
require_once "../config/db.php";
require_once "../config/base.php";
require_once "session.php";


$instructor_name  = $_SESSION['instructor_name'] ?? 'Instructor';
$instructor_email = $_SESSION['instructor_email'] ?? '';

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

/* Fetch assessments */
$stmt = $conn->prepare("
    SELECT id, title, type, due_date, created_at
    FROM assessments
    WHERE course_id = ?
    ORDER BY created_at DESC
");
$stmt->bind_param("i", $course_id);
$stmt->execute();
$assessments = $stmt->get_result();
$stmt->close();
?>

<div class="main">

    <div class="top">
        <div>
            <h1>Assessments</h1>
            <p>Course: <b><?php echo htmlspecialchars($courseTitle); ?></b></p>
        </div>

        <div style="display:flex; gap:10px;">
            <a href="course_overview.php?course_id=<?php echo $course_id; ?>" class="btn-light">← Back</a>
            <a href="add_assessment.php?course_id=<?php echo $course_id; ?>" class="add-btn">+ Add Assessment</a>
        </div>
    </div>

    <div class="table-card">
        <table class="modern-table">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Type</th>
                    <th>Due Date</th>
                    <th>Created</th>
                    <th style="width:180px;">Action</th>
                </tr>
            </thead>

            <tbody>
            <?php if ($assessments->num_rows > 0): ?>
                <?php while($row = $assessments->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['title']); ?></td>

                        <td>
                            <span class="badge">
                                <?php echo htmlspecialchars($row['type'] ?? 'Assessment'); ?>
                            </span>
                        </td>

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
                                <a class="btn-sm"
                                   href="edit_assessment.php?id=<?php echo (int)$row['id']; ?>&course_id=<?php echo $course_id; ?>">
                                    Edit
                                </a>

                                <a class="btn-sm danger"
                                   href="delete_assessment.php?id=<?php echo (int)$row['id']; ?>&course_id=<?php echo $course_id; ?>"
                                   onclick="return confirm('Delete this assessment?');">
                                    Delete
                                </a>
                            </div>
                        </td>

                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5">No assessments found.</td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>

</div>

<?php require_once "footer.php"; ?>
