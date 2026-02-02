
<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}
include '../config/db.php'; 

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
    <title>Manage Enrollments | Polymath Path</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f4f7f6; font-family: 'Poppins', sans-serif; }
        .progress { height: 10px; border-radius: 5px; }
        .table-container { background: white; border-radius: 15px; padding: 25px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
    </style>
</head>
<body>

<?php include 'admin_navbar.php'; ?>

<div class="container my-5">
    <h2 class="fw-bold mb-4" style="color: #003366;">Student Enrollments</h2>
    
    <div class="table-container">
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
                <?php while($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td class="fw-bold text-primary"><?php echo $row['student_name']; ?></td>
                    <td><?php echo $row['course_title']; ?></td>
                    <td style="width: 200px;">
                        <small><?php echo $row['progress']; ?>% Complete</small>
                        <div class="progress">
                            <div class="progress-bar bg-success" style="width: <?php echo $row['progress']; ?>%"></div>
                        </div>
                    </td>
                    <td>
                        <span class="badge <?php echo ($row['status'] == 'active') ? 'bg-info' : 'bg-success'; ?>">
                            <?php echo ucfirst($row['status']); ?>
                        </span>
                    </td>
                    <td><?php echo date('d M, Y', strtotime($row['enrolled_at'])); ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>