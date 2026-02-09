<?php
require_once "../config/db.php";
require_once "../config/base.php";
require_once "session.php";


$instructor_id = $_SESSION['instructor_id'];

require_once __DIR__ . "/header.php";
require_once __DIR__ . "/sidebar.php";

$id = (int)($_GET['id'] ?? 0);

if ($id <= 0) {
    header("Location: " . BASE_URL . "/instructor/courses.php");
    exit;
}

$message = "";
$message_type = "";

/* ✅ Get course (only owner instructor can edit) */
$stmt = $conn->prepare("
    SELECT title, subject, description, status
    FROM courses
    WHERE id=? AND instructor_id=?
    LIMIT 1
");
$stmt->bind_param("ii", $id, $instructor_id);
$stmt->execute();
$course = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$course) {
    header("Location: " . BASE_URL . "/instructor/courses.php");
    exit;
}

$title = $course['title'];
$subject = $course['subject'];
$description = $course['description'];
$status = $course['status'];

/* ✅ Update course */
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $title = trim($_POST['title'] ?? "");
    $subject = trim($_POST['subject'] ?? "");
    $description = trim($_POST['description'] ?? "");
    $status = trim($_POST['status'] ?? "draft");

    if ($title === "" || $subject === "" || $description === "") {
        $message = "Please fill all fields.";
        $message_type = "error";
    } else {
        $stmt = $conn->prepare("
            UPDATE courses
            SET title=?, subject=?, description=?, status=?
            WHERE id=? AND instructor_id=?
        ");
        $stmt->bind_param("ssssii", $title, $subject, $description, $status, $id, $instructor_id);
        $stmt->execute();
        $stmt->close();

        header("Location: " . BASE_URL . "/instructor/courses.php?success=Course updated successfully");
        exit;
    }
}
?>

<div class="main">

    <div class="top">
        <div>
            <h1>Edit Course</h1>
            <p>Update course details.</p>
        </div>

        <div style="display:flex; gap:10px;">
            <a href="<?php echo BASE_URL; ?>/instructor/courses.php" class="btn-light">← Back</a>
        </div>
    </div>

    <div class="form-card">

        <?php if (!empty($message)): ?>
            <div class="alert <?php echo ($message_type === "error") ? "alert-error" : "alert-success"; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="form-modern">

            <div class="form-row">
                <div class="form-group" style="flex:1;">
                    <label>Title *</label>
                    <input type="text" name="title" value="<?php echo htmlspecialchars($title); ?>" required>
                </div>

                <div class="form-group" style="flex:1;">
                    <label>Subject *</label>
                    <input type="text" name="subject" value="<?php echo htmlspecialchars($subject); ?>" required>
                </div>
            </div>

            <div class="form-group">
                <label>Description *</label>
                <textarea name="description" rows="5" required><?php echo htmlspecialchars($description); ?></textarea>
            </div>

            <div class="form-group">
                <label>Status *</label>
                <select name="status" required>
                    <option value="draft" <?php echo ($status === "draft") ? "selected" : ""; ?>>Draft</option>
                    <option value="published" <?php echo ($status === "published") ? "selected" : ""; ?>>Published</option>
                </select>
            </div>

            <div style="display:flex; gap:10px; justify-content:flex-end;">
                <a href="<?php echo BASE_URL; ?>/instructor/courses.php" class="btn-light">Cancel</a>
                <button type="submit" class="add-btn">Update Course</button>
            </div>

        </form>
    </div>

</div>

<?php require_once __DIR__ . "/footer.php"; ?>
<?php
require_once __DIR__ . "/session.php";
require_once __DIR__ . "/../config/db.php";
require_once __DIR__ . "/../config/base.php";

$instructor_id = $_SESSION['instructor_id'];

require_once __DIR__ . "/header.php";
require_once __DIR__ . "/sidebar.php";

$id = (int)($_GET['id'] ?? 0);

if ($id <= 0) {
    header("Location: " . BASE_URL . "/instructor/courses.php");
    exit;
}

$message = "";
$message_type = "";

/* ✅ Get course (only owner instructor can edit) */
$stmt = $conn->prepare("
    SELECT title, subject, description, status
    FROM courses
    WHERE id=? AND instructor_id=?
    LIMIT 1
");
$stmt->bind_param("ii", $id, $instructor_id);
$stmt->execute();
$course = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$course) {
    header("Location: " . BASE_URL . "/instructor/courses.php");
    exit;
}

$title = $course['title'];
$subject = $course['subject'];
$description = $course['description'];
$status = $course['status'];

/* ✅ Update course */
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $title = trim($_POST['title'] ?? "");
    $subject = trim($_POST['subject'] ?? "");
    $description = trim($_POST['description'] ?? "");
    $status = trim($_POST['status'] ?? "draft");

    if ($title === "" || $subject === "" || $description === "") {
        $message = "Please fill all fields.";
        $message_type = "error";
    } else {
        $stmt = $conn->prepare("
            UPDATE courses
            SET title=?, subject=?, description=?, status=?
            WHERE id=? AND instructor_id=?
        ");
        $stmt->bind_param("ssssii", $title, $subject, $description, $status, $id, $instructor_id);
        $stmt->execute();
        $stmt->close();

        header("Location: " . BASE_URL . "/instructor/courses.php?success=Course updated successfully");
        exit;
    }
}
?>

<div class="main">

    <div class="top">
        <div>
            <h1>Edit Course</h1>
            <p>Update course details.</p>
        </div>

        <div style="display:flex; gap:10px;">
            <a href="<?php echo BASE_URL; ?>/instructor/courses.php" class="btn-light">← Back</a>
        </div>
    </div>

    <div class="form-card">

        <?php if (!empty($message)): ?>
            <div class="alert <?php echo ($message_type === "error") ? "alert-error" : "alert-success"; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="form-modern">

            <div class="form-row">
                <div class="form-group" style="flex:1;">
                    <label>Title *</label>
                    <input type="text" name="title" value="<?php echo htmlspecialchars($title); ?>" required>
                </div>

                <div class="form-group" style="flex:1;">
                    <label>Subject *</label>
                    <input type="text" name="subject" value="<?php echo htmlspecialchars($subject); ?>" required>
                </div>
            </div>

            <div class="form-group">
                <label>Description *</label>
                <textarea name="description" rows="5" required><?php echo htmlspecialchars($description); ?></textarea>
            </div>

            <div class="form-group">
                <label>Status *</label>
                <select name="status" required>
                    <option value="draft" <?php echo ($status === "draft") ? "selected" : ""; ?>>Draft</option>
                    <option value="published" <?php echo ($status === "published") ? "selected" : ""; ?>>Published</option>
                </select>
            </div>

            <div style="display:flex; gap:10px; justify-content:flex-end;">
                <a href="<?php echo BASE_URL; ?>/instructor/courses.php" class="btn-light">Cancel</a>
                <button type="submit" class="add-btn">Update Course</button>
            </div>

        </form>
    </div>

</div>

<?php require_once __DIR__ . "/footer.php"; ?>
