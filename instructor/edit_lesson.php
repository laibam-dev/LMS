<?php
 include "header.php";

include "../config/db.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'instructor') {
    header("Location: login.php");
    exit;
}

$lesson_id = intval($_GET['id']);
$course_id = intval($_GET['course_id']);

/* Fetch lesson */
$sql = "SELECT * FROM lessons WHERE id = $lesson_id AND course_id = $course_id";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) !== 1) {
    die("Lesson not found");
}

$lesson = mysqli_fetch_assoc($result);

/* Update lesson */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $content = mysqli_real_escape_string($conn, $_POST['content']);

    $update = "UPDATE lessons 
               SET title = '$title', content = '$content'
               WHERE id = $lesson_id";

    mysqli_query($conn, $update);

    header("Location: course_detail.php?id=$course_id");
    exit;
}
?>

<div class="container mt-4">
    <div class="card">
        <div class="card-header bg-dark text-white">
            Edit Lesson
        </div>

        <div class="card-body">
            <form method="post">

                <div class="mb-3">
                    <label class="form-label">Lesson Title</label>
                    <input type="text"
                           name="title"
                           class="form-control"
                           value="<?= htmlspecialchars($lesson['title']) ?>"
                           required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Lesson Content</label>
                    <textarea name="content" class="form-control" rows="4"><?= htmlspecialchars($lesson['content']) ?></textarea>
                </div>

                <button class="btn btn-primary">Update Lesson</button>
                <a href="course_detail.php?id=<?= $course_id ?>" class="btn btn-secondary ms-2">Cancel</a>

            </form>
        </div>
    </div>
</div>


<br>
<a href="course_detail.php?id=<?= $course_id ?>">← Back</a>
