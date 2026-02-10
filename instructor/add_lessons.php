<?php
require_once "../config/db.php";
require_once "../config/base.php";
require_once "session.php";


$course_id = isset($_GET['course_id']) ? (int)$_GET['course_id'] : 0;
if ($course_id <= 0) {
    die("Invalid course");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $title     = trim($_POST['title']);
    $content   = trim($_POST['content']);
    $video_url = trim($_POST['video_url']);

    // PDF upload
    $pdf_file = null;
    if (!empty($_FILES['pdf_file']['name'])) {
        $uploadDir = "../uploads/pdfs/";
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $pdf_file = time() . "_" . basename($_FILES['pdf_file']['name']);
        move_uploaded_file($_FILES['pdf_file']['tmp_name'], $uploadDir . $pdf_file);
    }

    $stmt = $conn->prepare("
        INSERT INTO lessons (course_id, title, content, pdf_file, video_url, created_at)
        VALUES (?, ?, ?, ?, ?, NOW())
    ");
    $stmt->bind_param("issss", $course_id, $title, $content, $pdf_file, $video_url);
    $stmt->execute();

    header("Location: lessons.php?course_id=" . $course_id);
    exit;
}

include "header.php";
include "sidebar.php";
?>

<div class="main">
    <h1 class="page-title">Add Lesson</h1>

    <div class="form-card">
        <form method="post" enctype="multipart/form-data">

            <div class="form-group">
                <label>Lesson Title <span class="req">*</span></label>
                <input type="text" name="title" required>
            </div>

            <div class="form-group">
                <label>Lesson Content</label>
                <textarea name="content" rows="4"></textarea>
            </div>

            <div class="form-group">
                <label>PDF File</label>
                <input type="file" name="pdf_file" accept="application/pdf">
            </div>

            <div class="form-group">
                <label>Video URL (YouTube)</label>
                <input type="url" name="video_url" placeholder="https://www.youtube.com/watch?v=...">
            </div>

          <div class="form-actions">
    <a href="lessons.php?course_id=<?php echo $course_id; ?>" class="btn-light">
        Cancel
    </a>

    <button type="submit" class="btn-primary">
        Save Lesson
    </button>
</div>


        </form>
    </div>
</div>
