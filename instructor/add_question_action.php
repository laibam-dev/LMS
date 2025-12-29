<?php
if (session_status() === PHP_SESSION_NONE) session_start();
include "../config/db.php";

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'instructor') {
  header("Location: login.php");
  exit;
}

$quiz_id = (int)$_POST['quiz_id'];

$question = mysqli_real_escape_string($conn, $_POST['question']);
$a = mysqli_real_escape_string($conn, $_POST['option_a']);
$b = mysqli_real_escape_string($conn, $_POST['option_b']);
$c = mysqli_real_escape_string($conn, $_POST['option_c']);
$d = mysqli_real_escape_string($conn, $_POST['option_d']);
$correct = $_POST['correct_option'];

mysqli_query($conn, "
  INSERT INTO quiz_questions
  (quiz_id, question, option_a, option_b, option_c, option_d, correct_option)
  VALUES
  ($quiz_id,'$question','$a','$b','$c','$d','$correct')
");

header("Location: add_questions.php?quiz_id=$quiz_id");
exit;
