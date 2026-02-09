<?php
require_once "../config/db.php";
require_once "../config/base.php";
require_once "session.php";
   // ✅ BASE PATH

$instructor_id = $_SESSION['instructor_id'];
$instructor_name = $_SESSION['instructor_name'] ?? 'Instructor';
$instructor_email = $_SESSION['instructor_email'] ?? '';

require_once "header.php";
require_once "sidebar.php";

/* Fetch Courses */
$stmt = $conn->prepare("
    SELECT id, title, subject, status, created_at
    FROM courses
    WHERE instructor_id = ?
    ORDER BY created_at DESC
");
$stmt->bind_param("i", $instructor_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<div class="main">

    <div class="top">
        <div>
            <h1>Courses</h1>
            <p>Manage all your courses here.</p>
        </div>
        <a href="<?php echo BASE_URL; ?>/instructor/add_course.php" class="add-btn">
            + Add Course
        </a>
    </div>

    <div class="table-card">
        <table class="modern-table">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Subject</th>
                    <th>Status</th>
                    <th>Created</th>
                    <th style="width: 180px;">Actions</th>
                </tr>
            </thead>

            <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): ?>
                    <?php $statusClass = strtolower($row['status']); ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['title']); ?></td>

                        <td>
                            <?php echo htmlspecialchars($row['subject'] ?: 'General'); ?>
                        </td>

                        <td>
                            <span class="badge <?php echo $statusClass; ?>">
                                <?php echo htmlspecialchars($row['status']); ?>
                            </span>
                        </td>

                        <td><?php echo date("m/d/Y", strtotime($row['created_at'])); ?></td>

                        <td>
                            <div class="action-btns">

                                <a class="btn-sm"
                                   href="<?php echo BASE_URL; ?>/instructor/edit_course.php?id=<?php echo (int)$row['id']; ?>">
                                    Edit
                                </a>

                                <a class="btn-sm danger"
                                   href="<?php echo BASE_URL; ?>/instructor/delete_course.php?id=<?php echo (int)$row['id']; ?>"
                                   onclick="return confirm('Delete this course?');">
                                    Delete
                                </a>

                            </div>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5">No courses found.</td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>

</div>

<?php require_once "footer.php"; ?>
