<?php 
include '../config/db.php'; 

// Admin ke liye query: Saaray courses aur unke teachers ka naam
$query = "SELECT c.id, c.title, c.subject, c.status, c.created_at, u.name as instructor_name 
          FROM courses c
          JOIN users u ON c.instructor_id = u.id
          ORDER BY c.created_at DESC";

$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Courses | Polymath Path Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f4f7f6; font-family: 'Poppins', sans-serif; }
        .table-container { background: white; border-radius: 15px; padding: 25px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
        .status-active { color: #28a745; fw-bold; }
        .status-pending { color: #ffc107; fw-bold; }
        thead { background-color: #003366; color: white; }
    </style>
</head>
<body>

<?php include 'admin_navbar.php'; ?>

<div class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold" style="color: #003366;">Manage All Courses</h2>
            <p class="text-muted">Monitor all courses offered by Polymath Path instructors.</p>
        </div>
    </div>

    <div class="table-container">
        <table class="table table-hover align-middle">
            <thead>
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>