<?php
if (session_status() === PHP_SESSION_NONE) session_start();

include "../config/db.php";
include "header.php";

/* AUTH */
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'instructor') {
  header("Location: login.php");
  exit;
}

$quiz_id = (int)($_GET['quiz_id'] ?? 0);
if (!$quiz_id) die("Quiz ID missing");
?>

<div class="main">

  <div class="page-head mb-4">
    <div>
      <h1>Add Quiz Questions</h1>
      <p>Create MCQs for this quiz</p>
    </div>
  </div>

  <div class="cardx p-4" style="max-width:800px;margin:0 auto;">

    <form method="POST" action="add_quiz_question_action.php">
      <input type="hidden" name="quiz_id" value="<?= $quiz_id ?>">

      <div class="mb-3">
        <label class="form-label fw-semibold">Question</label>
        <textarea name="question" class="form-control" rows="3" required></textarea>
      </div>

      <div class="row">
        <div class="col-md-6 mb-3">
          <label class="form-label">Option A</label>
          <input type="text" name="option_a" class="form-control" required>
        </div>
        <div class="col-md-6 mb-3">
          <label class="form-label">Option B</label>
          <input type="text" name="option_b" class="form-control" required>
        </div>
        <div class="col-md-6 mb-3">
          <label class="form-label">Option C</label>
          <input type="text" name="option_c" class="form-control" required>
        </div>
        <div class="col-md-6 mb-3">
          <label class="form-label">Option D</label>
          <input type="text" name="option_d" class="form-control" required>
        </div>
      </div>

      <div class="mb-4">
        <label class="form-label fw-semibold">Correct Answer</label>
        <select name="correct_option" class="form-select" required>
          <option value="">Select correct option</option>
          <option value="A">Option A</option>
          <option value="B">Option B</option>
          <option value="C">Option C</option>
          <option value="D">Option D</option>
        </select>
      </div>

      <div class="d-flex justify-content-end gap-2">
        <a href="course_detail.php" class="btn btn-outline-secondary">
          Back
        </a>
        <button class="btn btn-primary">
          Save Question
        </button>
      </div>

    </form>

  </div>
</div>

<?php include "footer.php"; ?>
