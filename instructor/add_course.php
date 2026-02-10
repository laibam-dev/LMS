<?php
require_once "../config/db.php";
require_once "../config/base.php";
require_once "session.php";
   // ✅ BASE PATH ADDED

$instructor_id    = $_SESSION['instructor_id'];
$instructor_name  = $_SESSION['instructor_name'] ?? 'Instructor';
$instructor_email = $_SESSION['instructor_email'] ?? '';

require_once "header.php";
require_once "sidebar.php";

$error = "";
$success = "";

// Handle form submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $title       = trim($_POST['title'] ?? '');
    $subject     = trim($_POST['subject'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $status      = trim($_POST['status'] ?? 'Draft');

    if ($title === "") {
        $error = "Course title is required.";
    } else {
        $stmt = $conn->prepare("
            INSERT INTO courses (instructor_id, title, subject, description, status, created_at)
            VALUES (?, ?, ?, ?, ?, NOW())
        ");
        $stmt->bind_param("issss", $instructor_id, $title, $subject, $description, $status);

        if ($stmt->execute()) {
            header("Location: " . BASE_URL . "/instructor/courses.php");   // ✅ BASE PATH REDIRECT
            exit;
        } else {
            $error = "Something went wrong. Please try again.";
        }
        $stmt->close();
    }
}
?>

<div class="main">

    <div class="top">
        <div>
            <h1>Add Course</h1>
            <p>Create a new course.</p>
        </div>

        <!-- ✅ BASE PATH BACK -->
        <a href="<?php echo BASE_URL; ?>/instructor/courses.php" class="btn-light">← Back</a>
    </div>

    <div class="form-card">

        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <form method="POST" class="form-modern">

            <div class="form-grid-2">
                <div class="form-group">
                    <label>Title *</label>
                    <input type="text" name="title" placeholder="e.g. Introduction to Programming" required>
                </div>

                <div class="form-group">
                    <label>Subject</label>
                    <input type="text" name="subject" placeholder="e.g. Math, Computer">
                </div>
            </div>

            <div class="form-group">
                <label>Description</label>
                <textarea name="description" rows="5" placeholder="Write course details..."></textarea>
            </div>

            <div class="form-grid-2">
                <div class="form-group">
                    <label>Status</label>
                    <select name="status">
                        <option value="Draft">Draft</option>
                        <option value="Published">Published</option>
                    </select>
                </div>

                <div class="form-group"></div>
            </div>

            <div class="form-actions">
                <a href="<?php echo BASE_URL; ?>/instructor/courses.php" class="btn-light">Cancel</a>
                <button type="submit" class="btn-primary">Save Course</button>
            </div>

        </form>
    </div>

</div>

<?php require_once "footer.php"; ?>
