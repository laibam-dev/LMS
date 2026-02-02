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
            <h2 class="fw-bold mb-4" style="color:#003366;">Certificates Log</h2>
            <div class="mb-3 d-flex justify-content-between">
                <input type="text" id="searchInput" class="form-control w-25" placeholder="Search certificates...">
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
            <table class="table table-bordered table-hover" id="certificatesTable">
                <thead>
                    <tr>
                        <th>Student</th>
                        <th>Course</th>
                        <th>Issue Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $sql = "SELECT cert.certificate_id, cert.issue_date, u.full_name, c.course_name FROM certificates cert JOIN users u ON cert.student_id = u.user_id JOIN courses c ON cert.course_id = c.course_id ORDER BY cert.issue_date DESC";
                $result = mysqli_query($conn, $sql);
                while($row = mysqli_fetch_assoc($result)) {
                    echo '<tr>';
                    echo '<td>'.$row['full_name'].'</td>';
                    echo '<td>'.$row['course_name'].'</td>';
                    echo '<td>'.$row['issue_date'].'</td>';
                    echo '<td>
                        <a href="revoke_certificate.php?id='.$row['certificate_id'].'" class="btn btn-danger btn-sm">Revoke</a> 
                        <a href="reissue_certificate.php?id='.$row['certificate_id'].'" class="btn btn-polymath btn-sm">Re-issue</a>
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
const table = document.getElementById('certificatesTable').getElementsByTagName('tbody')[0];
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
