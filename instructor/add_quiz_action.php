<?php
session_start();
include "../config/db.php";

if ($_SESSION['role'] !== 'instructor') exit;

$course_id = (int)$_POST['course_id'];
$title = mysqli_real_escape_string($conn, $_POST['title']);
$desc  = mysqli_real_escape_string($conn, $_POST['description']);

$pdf_name = null;

if (!empty($_FILES['pdf']['name'])) {
    $pdf_name = time().'_'.$_FILES['pdf']['name'];
    move_uploaded_file(
        $_FILES['pdf']['tmp_name'],
        "../uploads/quizzes/".$pdf_name
    );
}

mysqli_query($conn,"
  INSERT INTO quizzes (course_id, title, description, pdf_file, created_at)
  VALUES ($course_id, '$title', '$desc', '$pdf_name', NOW())
");

header("Location: course_detail.php?course_id=$course_id");
exit;
