<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

 include "header.php";

include "../config/db.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'instructor') {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['course_id'])) {
    die("Course ID missing");
}

$course_id = intval($_GET['course_id']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (!isset($_POST['title']) || !isset($_POST['content'])) {
        die("Form data missing");
    }

    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $content = mysqli_real_escape_string($conn, $_POST['content']);
    $pdf_name = "";

    if (!empty($_FILES['pdf']['name'])) {
        $pdf_name = time() . "_" . basename($_FILES['pdf']['name']);
        move_uploaded_file(
            $_FILES['pdf']['tmp_name'],
            "../uploads/pdfs/" . $pdf_name
        );
    }

    $sql = "INSERT INTO lessons (course_id, title, content, pdf_file)
            VALUES ($course_id, '$title', '$content', '$pdf_name')";

    if (!mysqli_query($conn, $sql)) {
        die("Insert error: " . mysqli_error($conn));
    }

    header("Location: course_detail.php?id=$course_id");
    exit;
}
?>

<div class="container mt-4">
    <div class="card">
        <div class="card-header bg-dark text-white">
            Add Lesson
        </div>

        <div class="card-body">
            <form method="post" enctype="multipart/form-data">

                <div class="mb-3">
                    <label class="form-label">Lesson Title</label>
                    <input type="text" name="title" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Lesson Content</label>
                    <textarea name="content" class="form-control" rows="4"></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">PDF (optional)</label>
                    <input type="file" name="pdf" class="form-control">
                </div>

                <button class="btn btn-success">Save Lesson</button>
                <a href="course_detail.php?id=<?= $course_id ?>" class="btn btn-secondary ms-2">Cancel</a>

            </form>
        </div>
    </div>
</div>

