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
            <h2 class="fw-bold mb-4" style="color:#003366;">Quizzes Monitoring</h2>
            <div class="mb-3 d-flex justify-content-between">
                <input type="text" id="searchInput" class="form-control w-25" placeholder="Search quizzes...">
                <select id="filterCourse" class="form-select w-25">
                    <option value="">All Courses</option>
                    <?php
                    $courses = mysqli_query($conn, "SELECT course_id, course_name FROM courses");
                    while($c = mysqli_fetch_assoc($courses)) {
                        echo '<option value="'.$c['course_id'].'">'.$c['course_name'].'</option>';
                    }
                    ?>
                </select>
            </div>
            <table class="table table-bordered table-hover" id="quizzesTable">
                <thead>
                    <tr>
                        <th>Quiz Title</th>
                        <th>Course</th>
                        <th>Instructor</th>
                        <th>Total Questions</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $sql = "SELECT q.quiz_id, q.quiz_title, c.course_name, u.full_name, COUNT(qq.question_id) as total_questions FROM quizzes q JOIN courses c ON q.course_id = c.course_id JOIN users u ON q.instructor_id = u.user_id LEFT JOIN quiz_questions qq ON q.quiz_id = qq.quiz_id GROUP BY q.quiz_id";
                $result = mysqli_query($conn, $sql);
                while($row = mysqli_fetch_assoc($result)) {
                    echo '<tr>';
                    echo '<td>'.$row['quiz_title'].'</td>';
                    echo '<td>'.$row['course_name'].'</td>';
                    echo '<td>'.$row['full_name'].'</td>';
                    echo '<td>'.$row['total_questions'].'</td>';
                    echo '<td>
                        <a href="edit_quiz.php?id='.$row['quiz_id'].'" class="btn btn-polymath btn-sm">Edit</a> 
                        <a href="delete_quiz.php?id='.$row['quiz_id'].'" class="btn btn-danger btn-sm">Delete</a> 
                        <a href="view_quiz_submissions.php?quiz_id='.$row['quiz_id'].'" class="btn btn-info btn-sm">View Submissions</a>
                    </td>';
                    echo '</tr>';
                }
                ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php include 'footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Search & Filter functionality
const searchInput = document.getElementById('searchInput');
const filterCourse = document.getElementById('filterCourse');
const table = document.getElementById('quizzesTable').getElementsByTagName('tbody')[0];
searchInput.addEventListener('input', filterTable);
filterCourse.addEventListener('change', filterTable);
function filterTable() {
    const search = searchInput.value.toLowerCase();
    const course = filterCourse.value;
    for (let row of table.rows) {
        const title = row.cells[0].textContent.toLowerCase();
        const matchSearch = title.includes(search);
        const matchCourse = !course || row.cells[1].textContent === filterCourse.options[filterCourse.selectedIndex].text;
        row.style.display = (matchSearch && matchCourse) ? '' : 'none';
    }
}
</script>
</body>
</html>
