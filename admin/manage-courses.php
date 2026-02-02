
<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}
include '../config/db.php';
include 'header.php';
include 'navbar.php';
$query = "SELECT c.id, c.title, c.subject, c.status, c.created_at, u.name as instructor_name 
          FROM courses c
          JOIN users u ON c.instructor_id = u.id
          ORDER BY c.created_at DESC";
$result = mysqli_query($conn, $query);
?>
<div class="d-flex">
    <?php include 'sidebar.php'; ?>
    <div class="main-content flex-grow-1">
        <h2 class="fw-bold mb-4" style="color: #003366;">Manage All Courses</h2>
        <p class="text-muted">Monitor all courses offered by Polymath Path instructors.</p>
        <div class="table-container bg-white rounded-3 p-4 shadow-sm">
            <table class="table table-hover align-middle">
                <thead style="background-color: #003366; color: #FFD700;">
                    <tr>
                        <th>Title</th>
                        <th>Instructor</th>
                        <th>Subject</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td class="fw-bold"><?php echo htmlspecialchars($row['title']); ?></td>
                        <td><span class="text-muted">👨‍🏫</span> <?php echo htmlspecialchars($row['instructor_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['subject'] ?: 'General'); ?></td>
                        <td>
                            <span class="badge rounded-pill <?php echo ($row['status'] == 'active') ? 'bg-success' : 'bg-warning text-dark'; ?>">
                                <?php echo ucfirst($row['status']); ?>
                            </span>
                        </td>
                        <td><?php echo date("d M, Y", strtotime($row['created_at'])); ?></td>
                        <td class="text-center">
                            <a href="change_status.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-outline-primary">Toggle Status</a>
                            <a href="delete_course.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this course permanently?')">Delete</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php include 'footer.php'; ?>