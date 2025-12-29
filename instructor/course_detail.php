<?php
if (session_status() === PHP_SESSION_NONE) session_start();

include "../config/db.php";
include "header.php";

/* AUTH */
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'instructor') {
    header("Location: login.php");
    exit;
}

$course_id = (int)($_GET['course_id'] ?? 0);
if (!$course_id) die("Course ID missing");

$instructor_id = (int)$_SESSION['user_id'];

/* COURSE */
$course_q = mysqli_query($conn,"
    SELECT * FROM courses
    WHERE id=$course_id AND instructor_id=$instructor_id
");
if (mysqli_num_rows($course_q) !== 1) die("Access denied");
$course = mysqli_fetch_assoc($course_q);

/* DATA */
$assignments_q = mysqli_query($conn,"
    SELECT * FROM assignments WHERE course_id=$course_id ORDER BY id DESC
");

$students_q = mysqli_query($conn,"
    SELECT u.* FROM enrollments e
    JOIN users u ON u.id=e.user_id
    WHERE e.course_id=$course_id
");

$quizzes_q = mysqli_query($conn,"
    SELECT * FROM quizzes WHERE course_id=$course_id ORDER BY id DESC
");
?>

<div class="container mt-4">

<!-- COURSE CARD -->
<div class="card mb-4">
  <div class="card-body d-flex justify-content-between">
    <div>
      <h2 class="mb-1"><?= htmlspecialchars($course['title']) ?></h2>
      <p class="text-muted"><?= $course['description'] ?: 'No description available.' ?></p>
      <small>
        👥 <?= mysqli_num_rows($students_q) ?> Students &nbsp;
        📄 <?= mysqli_num_rows($assignments_q) ?> Assignments
      </small>
    </div>

    <div class="text-end">
      <a href="edit_course.php?course_id=<?= $course_id ?>"
         class="btn btn-outline-secondary btn-sm mb-2">Edit</a><br>
      <span class="badge bg-primary">CREATED ON</span><br>
      <?= date("F d, Y", strtotime($course['created_at'])) ?>
    </div>
  </div>
</div>

<!-- NAV TABS -->
<ul class="nav nav-pills mb-3" role="tablist">
  <li class="nav-item" role="presentation">
    <button class="nav-link active"
            data-bs-toggle="tab"
            data-bs-target="#assignments"
            type="button">
      Assignments
    </button>
  </li>

  <li class="nav-item" role="presentation">
    <button class="nav-link"
            data-bs-toggle="tab"
            data-bs-target="#students"
            type="button">
      Students
    </button>
  </li>

  <li class="nav-item" role="presentation">
    <button class="nav-link"
            data-bs-toggle="tab"
            data-bs-target="#quizzes"
            type="button">
      Quizzes
    </button>
  </li>
</ul>

<!-- TAB CONTENT -->
<div class="tab-content">

<!-- ASSIGNMENTS -->
<div class="tab-pane fade show active" id="assignments">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-0">Course Assignments</h5>
    <a href="add_assignment.php?course_id=<?= $course_id ?>"
       class="btn btn-primary btn-sm">
      + New Assignment
    </a>
  </div>

  <?php if (mysqli_num_rows($assignments_q) === 0): ?>
    <div class="card text-center p-5">📘 No assignments yet</div>
  <?php else: ?>
    <ul class="list-group">
      <?php while($a=mysqli_fetch_assoc($assignments_q)): ?>
        <li class="list-group-item d-flex justify-content-between align-items-center">
          <div>
            <strong><?= htmlspecialchars($a['title']) ?></strong>
            <div class="text-muted small"><?= $a['due_date'] ?></div>
          </div>
          <a href="edit_assignment.php?id=<?= $a['id'] ?>&course_id=<?= $course_id ?>"
             class="btn btn-outline-secondary btn-sm">Edit</a>
        </li>
      <?php endwhile; ?>
    </ul>
  <?php endif; ?>
</div>

<!-- STUDENTS -->
<div class="tab-pane fade" id="students">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-0">Enrolled Students</h5>
    <button class="btn btn-outline-primary btn-sm"
            data-bs-toggle="modal"
            data-bs-target="#enrollModal">
      + Enroll Student
    </button>
  </div>

  <?php if (mysqli_num_rows($students_q) === 0): ?>
    <div class="card text-center p-5">👥 No students enrolled</div>
  <?php else: ?>
    <ul class="list-group">
      <?php while($s=mysqli_fetch_assoc($students_q)): ?>
        <li class="list-group-item">
          <?= htmlspecialchars($s['name']) ?>
          <small class="text-muted">(<?= htmlspecialchars($s['email']) ?>)</small>
        </li>
      <?php endwhile; ?>
    </ul>
  <?php endif; ?>
</div>
<!-- ENROLL MODAL (FIXED – works again) -->
<div class="modal fade" id="enrollModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <form class="modal-content" method="POST" action="enroll_student_action.php">

      <div class="modal-header">
        <h5 class="modal-title">Enroll Student</h5>
        <button class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        <input type="hidden" name="course_id" value="<?= $course_id ?>">

        <div class="mb-3">
          <label class="form-label">Student ID</label>
          <input type="number" name="student_id" class="form-control" required>
        </div>

        <div class="mb-3">
          <label class="form-label">Student Email</label>
          <input type="email" name="email" class="form-control" required>
        </div>
      </div>

      <div class="modal-footer">
        <button class="btn btn-primary w-100">Enroll Student</button>
      </div>
    </form>
  </div>
</div>

<!-- QUIZZES -->
<div class="tab-pane fade" id="quizzes">

  <!-- HEADER (same as Assignments) -->
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-0">Course Quizzes</h5>

    <a href="add_quiz.php?course_id=<?= $course_id ?>"
       class="btn btn-primary btn-sm">
      + New Quiz
    </a>
  </div>

  <!-- BODY -->
  <?php if (mysqli_num_rows($quizzes_q) === 0): ?>
    <div class="card text-center p-5">
      📝 No quizzes created yet
    </div>
  <?php else: ?>
    <ul class="list-group">
      <?php while($q = mysqli_fetch_assoc($quizzes_q)): ?>
        <li class="list-group-item d-flex justify-content-between align-items-center">
          <div>
            <strong><?= htmlspecialchars($q['title']) ?></strong>
            <div class="text-muted small">
              <?= date("M d, Y", strtotime($q['created_at'])) ?>
            </div>
          </div>

          <a href="edit_quiz.php?quiz_id=<?= (int)$q['id'] ?>"
             class="btn btn-outline-secondary btn-sm">
            Edit
          </a>
        </li>
      <?php endwhile; ?>
    </ul>
  <?php endif; ?>

</div>



<?php include "footer.php"; ?>
