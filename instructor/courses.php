<?php

include "../config/db.php";
include "header.php";

$instructor_id = (int)$_SESSION['user_id'];
$courses = mysqli_query($conn, "SELECT * FROM courses WHERE instructor_id=$instructor_id ORDER BY id DESC");
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="mb-0">My Courses</h1>

    <button
        class="btn btn-primary px-4 py-2"
        style="width:auto; display:inline-flex; align-items:center;"
        data-bs-toggle="modal"
        data-bs-target="#createCourseModal"
    >
        + Create New Course
    </button>
</div>

<div class="cardx">
  <?php while($c = mysqli_fetch_assoc($courses)): ?>
    <div class="list-row">
      <div class="d-flex align-items-center gap-3">
        <div class="fw-bold"><?= htmlspecialchars($c['title']) ?></div>

        <?php if(($c['status'] ?? '') === 'published'): ?>
          <span class="badge-soft">Published</span>
        <?php else: ?>
          <span class="badge-draft">Draft</span>
        <?php endif; ?>
      </div>

      <div class="d-flex gap-2">
        <a class="btn btn-outline-primary btn-sm" href="course_detail.php?course_id=<?= (int)$c['id'] ?>">View Details</a>

        <?php if(($c['status'] ?? '') === 'published'): ?>
          <a class="btn btn-outline-secondary btn-sm" href="toggle_status.php?id=<?= (int)$c['id'] ?>&status=draft">Unpublish</a>
        <?php else: ?>
          <a class="btn btn-outline-success btn-sm" href="toggle_status.php?id=<?= (int)$c['id'] ?>&status=published">Publish</a>
        <?php endif; ?>
      </div>
    </div>
    <hr class="m-0">
  <?php endwhile; ?>
</div>

<!-- Modal -->
<div class="modal fade" id="createCourseModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <form class="modal-content cardx" method="POST" action="create_course_action.php">
      <div class="modal-header border-0">
        <div>
          <h4 class="modal-title fw-bold">Create New Course</h4>
          <div class="text-secondary">Add a new course.</div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body pt-0">
        <div class="mb-3">
          <label class="form-label fw-bold">Course Title</label>
          <input class="form-control form-control-lg" name="title" required>
        </div>

        <div class="mb-3">
          <label class="form-label fw-bold">Subject</label>
          <input class="form-control" name="subject">
        </div>

        <div class="mb-2">
          <label class="form-label fw-bold">Description</label>
          <textarea class="form-control" rows="4" name="description"></textarea>
        </div>
      </div>

      <div class="modal-footer border-0">
        <button class="btn btn-primary px-4 py-2 rounded-3" type="submit">
          Create Course
        </button>
      </div>
    </form>
  </div>
</div>

<?php
if (($_GET['open'] ?? '') === 'create') {
  echo "<script>
    window.addEventListener('load', () => {
      new bootstrap.Modal(document.getElementById('createCourseModal')).show();
    });
  </script>";
}
?>

<?php include "footer.php"; ?>
