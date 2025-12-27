<?php
include "header.php";
include "../config/db.php";

$instructor_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $title = trim($_POST['title']);
    $description = trim($_POST['description']);

    if ($title === '') {
        $error = "Title is required";
    } else {
        $sql = "INSERT INTO courses (instructor_id, title, description, status)
                VALUES ($instructor_id, '$title', '$description', 'draft')";

        mysqli_query($conn, $sql);

        header("Location: courses.php");
        exit;
    }
}
?>

<h2>Create New Course</h2>

<?php if (!empty($error)) { ?>
    <div class="alert alert-danger"><?= $error ?></div>
<?php } ?>

<form method="POST">
    <div class="mb-3">
        <label class="form-label">Course Title</label>
        <input type="text" name="title" class="form-control" required>
    </div>

    <div class="mb-3">
        <label class="form-label">Description</label>
        <textarea name="description" class="form-control"></textarea>
    </div>

    <button type="submit" class="btn btn-primary">Create Course</button>
    <a href="courses.php" class="btn btn-secondary">Cancel</a>
</form>
