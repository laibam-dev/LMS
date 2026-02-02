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
            <h2 class="fw-bold mb-4" style="color:#003366;">Assignments Tracking</h2>
            <div class="mb-3 d-flex justify-content-between">
                <input type="text" id="searchInput" class="form-control w-25" placeholder="Search assignments...">
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
            <table class="table table-bordered table-hover" id="assignmentsTable">
                <thead>
                    <tr>
                        <th>Student</th>
                        <th>Course</th>
                        <th>Assignment File</th>
                        <th>Submitted On</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $sql = "SELECT a.assignment_id, a.file_path, a.submitted_at, u.full_name, c.course_name FROM assignments a JOIN users u ON a.student_id = u.user_id JOIN courses c ON a.course_id = c.course_id ORDER BY a.submitted_at DESC";
                $result = mysqli_query($conn, $sql);
                while($row = mysqli_fetch_assoc($result)) {
                    $fileName = basename($row['file_path']);
                    echo '<tr>';
                    echo '<td>'.$row['full_name'].'</td>';
                    echo '<td>'.$row['course_name'].'</td>';
                    echo '<td><a href="../uploads/assignments/'.$fileName.'" target="_blank">'.$fileName.'</a></td>';
                    echo '<td>'.$row['submitted_at'].'</td>';
                    echo '<td>
                        <a href="../uploads/assignments/'.$fileName.'" download class="btn btn-polymath btn-sm">Download</a> 
                        <a href="delete_assignment.php?id='.$row['assignment_id'].'" class="btn btn-danger btn-sm">Delete</a>
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
const table = document.getElementById('assignmentsTable').getElementsByTagName('tbody')[0];
searchInput.addEventListener('input', filterTable);
filterCourse.addEventListener('change', filterTable);
function filterTable() {
    const search = searchInput.value.toLowerCase();
    const course = filterCourse.value;
    for (let row of table.rows) {
        const student = row.cells[0].textContent.toLowerCase();
        const matchSearch = student.includes(search);
        const matchCourse = !course || row.cells[1].textContent === filterCourse.options[filterCourse.selectedIndex].text;
        row.style.display = (matchSearch && matchCourse) ? '' : 'none';
    }
}
</script>
</body>
</html>
