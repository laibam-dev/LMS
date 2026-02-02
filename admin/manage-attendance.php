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
            <h2 class="fw-bold mb-4" style="color:#003366;">Attendance Records</h2>
            <div class="mb-3 d-flex justify-content-between">
                <input type="date" id="filterDate" class="form-control w-25" placeholder="Filter by date...">
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
            <table class="table table-bordered table-hover" id="attendanceTable">
                <thead>
                    <tr>
                        <th>Student</th>
                        <th>Course</th>
                        <th>Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $sql = "SELECT a.attendance_id, a.attendance_date, a.status, u.full_name, c.course_name FROM attendance a JOIN users u ON a.student_id = u.user_id JOIN courses c ON a.course_id = c.course_id ORDER BY a.attendance_date DESC";
                $result = mysqli_query($conn, $sql);
                while($row = mysqli_fetch_assoc($result)) {
                    echo '<tr>';
                    echo '<td>'.$row['full_name'].'</td>';
                    echo '<td>'.$row['course_name'].'</td>';
                    echo '<td>'.$row['attendance_date'].'</td>';
                    echo '<td>'.$row['status'].' ';
                    echo '<button class="btn btn-polymath btn-sm ms-2" data-bs-toggle="modal" data-bs-target="#editModal" 
                        data-id="'.$row['attendance_id'].'" data-status="'.$row['status'].'">Edit</button>';
                    echo '</td>';
                    echo '</tr>';
                }
                ?>
                </tbody>
            </table>

                </div>
        </div>
</div>

<!-- Edit Attendance Modal -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="post" action="">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Edit Attendance Status</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="attendance_id" id="modalAttendanceId">
                    <div class="mb-3">
                        <label for="modalStatus" class="form-label">Status</label>
                        <select class="form-select" name="status" id="modalStatus">
                            <option value="Present">Present</option>
                            <option value="Absent">Absent</option>
                            <option value="Late">Late</option>
                            <option value="Excused">Excused</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-polymath">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Search & Filter functionality
const filterDate = document.getElementById('filterDate');
const filterCourse = document.getElementById('filterCourse');
const table = document.getElementById('attendanceTable').getElementsByTagName('tbody')[0];
filterDate.addEventListener('change', filterTable);
filterCourse.addEventListener('change', filterTable);
function filterTable() {
        const date = filterDate.value;
        const course = filterCourse.value;
        for (let row of table.rows) {
                const rowDate = row.cells[2].textContent;
                const matchDate = !date || rowDate === date;
                const matchCourse = !course || row.cells[1].textContent === filterCourse.options[filterCourse.selectedIndex].text;
                row.style.display = (matchDate && matchCourse) ? '' : 'none';
        }
}

// Modal logic for editing attendance
var editModal = document.getElementById('editModal');
editModal.addEventListener('show.bs.modal', function (event) {
    var button = event.relatedTarget;
    var attendanceId = button.getAttribute('data-id');
    var status = button.getAttribute('data-status');
    document.getElementById('modalAttendanceId').value = attendanceId;
    document.getElementById('modalStatus').value = status;
});
</script>
<?php
// Handle attendance update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['attendance_id'], $_POST['status'])) {
        $aid = intval($_POST['attendance_id']);
        $status = mysqli_real_escape_string($conn, $_POST['status']);
        $update = mysqli_query($conn, "UPDATE attendance SET status='$status' WHERE attendance_id=$aid");
        if ($update) {
                echo "<script>location.href=location.href;</script>";
        } else {
                echo '<div class="alert alert-danger mt-3">Failed to update attendance.</div>';
        }
}
?>
<?php include 'footer.php'; ?>
</body>
</html>
