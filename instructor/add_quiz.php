<?php
if (session_status() === PHP_SESSION_NONE) session_start();

include "../config/db.php";
include "header.php";

/* AUTH */
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'instructor') {
    header("Location: login.php");
    exit;
}

$course_id = (int)($_GET['course_id'] ?? 0);
if (!$course_id) die("Course ID missing");
?>

<div class="main">

  <!-- PAGE HEADER -->
  <div class="page-head mb-4">
    <div>
      <h1>Create Quiz</h1>
    </div>
  </div>

  <!-- CENTER CARD (LIKE ASSIGNMENT) -->
  <div class="cardx p-4" style="max-width:700px; margin:0 auto;">

    <form method="POST"
          action="add_quiz_action.php"
          enctype="multipart/form-data">

      <input type="hidden" name="course_id" value="<?= $course_id ?>">

      <!-- QUIZ TITLE -->
      <div class="mb-3">
        <label class="form-label fw-semibold">Quiz Title</label>
        <input type="text"
               name="title"
               class="form-control"
               placeholder="Enter quiz title"
               required>
      </div>

      <!-- DESCRIPTION -->
      <div class="mb-3">
        <label class="form-label fw-semibold">Description</label>
        <textarea name="description"
                  class="form-control"
                  rows="4"
                  placeholder="Optional description"></textarea>
      </div>

      <!-- PDF UPLOAD -->
      <div class="mb-4">
        <label class="form-label fw-semibold">Upload PDF (optional)</label>
        <input type="file"
               name="pdf"
               class="form-control"
               accept="application/pdf">
        <small class="text-muted">
          PDF instructions or reference material
        </small>
      </div>

      <!-- ACTION BUTTONS (SAME AS ASSIGNMENT) -->
      <div class="d-flex justify-content-end gap-2">
        <a href="course_detail.php?course_id=<?= $course_id ?>"
           class="btn btn-outline-secondary">
          Cancel
        </a>

        <button type="submit" class="btn btn-primary">
          Create Quiz
        </button>
      </div>

    </form>

  </div>
</div>

<?php include "footer.php"; ?>
