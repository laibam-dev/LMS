
<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}
include '../config/db.php';
include 'header.php';
include 'navbar.php';
?>
<div class="d-flex">
    <?php include 'sidebar.php'; ?>
    <div class="main-content flex-grow-1">
        <h2 class="fw-bold mb-4" style="color:#003366;">Lessons Monitoring</h2>
        <div class="mb-3 d-flex justify-content-between align-items-center">
            <input type="text" id="searchInput" class="form-control w-25" placeholder="Search lessons...">
            <select id="filterCourse" class="form-select w-25">
                <option value="">All Courses</option>
                <?php
                $courses = mysqli_query($conn, "SELECT id, title FROM courses ORDER BY title ASC");
                while($c = mysqli_fetch_assoc($courses)) {
                    echo '<option value="'.$c['id'].'">'.htmlspecialchars($c['title']).'</option>';
                }
                ?>
            </select>
            <button class="btn btn-warning ms-3" data-bs-toggle="modal" data-bs-target="#addLessonModal"><i class="fa fa-plus"></i> Add Lesson</button>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle" id="lessonsTable">
                <thead style="background-color: #003366; color: #FFD700;">
                    <tr>
                        <th>Lesson Title</th>
                        <th>Course</th>
                        <th>Instructor</th>
                        <th>Video</th>
                        <th>Date Added</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $sql = "SELECT l.id, l.title AS lesson_title, l.video_url, l.created_at, c.id AS course_id, c.title AS course_title, u.name AS instructor_name FROM lessons l JOIN courses c ON l.course_id = c.id JOIN users u ON c.instructor_id = u.id ORDER BY l.created_at DESC";
                $result = mysqli_query($conn, $sql);
                while($row = mysqli_fetch_assoc($result)) {
                    echo '<tr data-course="'.htmlspecialchars($row['course_id']).'">';
                    echo '<td>'.htmlspecialchars($row['lesson_title']).'</td>';
                    echo '<td>'.htmlspecialchars($row['course_title']).'</td>';
                    echo '<td>'.htmlspecialchars($row['instructor_name']).'</td>';
                    echo '<td>';
                    if (!empty($row['video_url'])) {
                        echo '<a href="'.htmlspecialchars($row['video_url']).'" target="_blank" class="btn btn-sm btn-outline-primary"><i class="fa fa-play"></i> Watch</a>';
                    } else {
                        echo '<span class="text-muted">N/A</span>';
                    }
                    echo '</td>';
                    echo '<td>'.date('d M, Y', strtotime($row['created_at'])).'</td>';
                    echo '<td>';
                    echo '<a href="edit_lesson.php?id='.$row['id'].'" class="btn btn-polymath btn-sm">Edit</a> ';
                    echo '<a href="delete_lesson.php?id='.$row['id'].'" class="btn btn-danger btn-sm" onclick="return confirm(\'Delete this lesson?\')">Delete</a>';
                    echo '</td>';
                    echo '</tr>';
                }
                ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Lesson Modal -->
<div class="modal fade" id="addLessonModal" tabindex="-1" aria-labelledby="addLessonModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="post" action="" id="addLessonForm">
        <div class="modal-header bg-polymath">
          <h5 class="modal-title text-white" id="addLessonModalLabel">Add New Lesson</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label for="course_id" class="form-label">Course</label>
            <select class="form-select" name="course_id" id="course_id" required>
              <option value="">Select Course</option>
              <?php
              $courses = mysqli_query($conn, "SELECT id, title FROM courses ORDER BY title ASC");
              while($c = mysqli_fetch_assoc($courses)) {
                  echo '<option value="'.$c['id'].'">'.htmlspecialchars($c['title']).'</option>';
              }
              ?>
            </select>
          </div>
          <div class="mb-3">
            <label for="lesson_title" class="form-label">Lesson Title</label>
            <input type="text" class="form-control" name="lesson_title" id="lesson_title" required>
          </div>
          <div class="mb-3">
            <label for="video_url" class="form-label">Video URL</label>
            <input type="url" class="form-control" name="video_url" id="video_url" placeholder="https://...">
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-warning" name="add_lesson">Add Lesson</button>
        </div>
      </form>
    </div>
  </div>
</div>

<?php
// Handle Add Lesson
if (isset($_POST['add_lesson'])) {
    $course_id = intval($_POST['course_id']);
    $lesson_title = trim($_POST['lesson_title']);
    $video_url = trim($_POST['video_url']);
    if ($course_id && $lesson_title) {
        $stmt = $conn->prepare("INSERT INTO lessons (course_id, title, video_url) VALUES (?, ?, ?)");
        $stmt->bind_param('iss', $course_id, $lesson_title, $video_url);
        if ($stmt->execute()) {
            echo '<script>location.reload();</script>';
        } else {
            echo '<div class="alert alert-danger m-3">Error: '.htmlspecialchars($stmt->error).'</div>';
        }
        $stmt->close();
    }
}
?>
<?php include 'footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Search & Filter functionality
const searchInput = document.getElementById('searchInput');
const filterCourse = document.getElementById('filterCourse');
const table = document.getElementById('lessonsTable').getElementsByTagName('tbody')[0];
searchInput.addEventListener('input', filterTable);
filterCourse.addEventListener('change', filterTable);
function filterTable() {
    const search = searchInput.value.toLowerCase();
    const course = filterCourse.value;
    for (let row of table.rows) {
        const title = row.cells[0].textContent.toLowerCase();
        const rowCourse = row.getAttribute('data-course');
        const matchSearch = title.includes(search);
        const matchCourse = !course || rowCourse === course;
        row.style.display = (matchSearch && matchCourse) ? '' : 'none';
    }
}
</script>
