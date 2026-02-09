<?php
session_start();
include '../config/db.php'; 

// Auth Check: Admin login check using BASE_URL
if (!isset($_SESSION['admin_id'])) {
    header('Location: ' . BASE_URL . 'admin/login.php');
    exit;
}

// Complex Query: Student aur Course ka naam nikalne ke liye JOIN use kiya
$query = "SELECT e.id, u.name as student_name, c.title as course_title, e.progress, e.status, e.enrolled_at 
          FROM enrollments e
          JOIN users u ON e.user_id = u.id
          JOIN courses c ON e.course_id = c.id
          ORDER BY e.enrolled_at DESC";

$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Enrollments | Polymath Path</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body { background-color: #f4f7f6; font-family: 'Poppins', sans-serif; }
        .progress { height: 10px; border-radius: 5px; background-color: #e9ecef; }
        .table-container { background: white; border-radius: 15px; padding: 25px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
        .fw-bold { color: #003366; }
    </style>
</head>
<body>

<?php include '../navbar.php'; ?>

<div class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold mb-0"><i class="fas fa-user-graduate me-2"></i>Student Enrollments</h2>
        <a href="<?php echo BASE_URL; ?>admin/index.php" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i> Back to Dashboard
        </a>
    </div>
    
    <div class="table-container">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>Student Name</th>
                        <th>Course Title</th>
                        <th>Progress</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($result) > 0): ?>
                        <?php while($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td class="fw-bold text-primary"><?php echo htmlspecialchars($row['student_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['course_title']); ?></td>
                            <td style="min-width: 180px;">
                                <div class="d-flex justify-content-between mb-1">
                                    <small class="text-muted"><?php echo $row['progress']; ?>% Complete</small>
                                </div>
                                <div class="progress">
                                    <div class="progress-bar bg-success" role="progressbar" style="width: <?php echo $row['progress']; ?>%" aria-valuenow="<?php echo $row['progress']; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </td>
                            <td>
                                <span class="badge <?php echo ($row['status'] == 'active') ? 'bg-info text-dark' : 'bg-success'; ?>">
                                    <?php echo ucfirst(htmlspecialchars($row['status'])); ?>
                                </span>
                            </td>
                            <td class="text-muted"><?php echo date('d M, Y', strtotime($row['enrolled_at'])); ?></td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">No enrollments found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://kit.fontawesome.com/your-code.js" crossorigin="anonymous"></script>
</body>
</html>