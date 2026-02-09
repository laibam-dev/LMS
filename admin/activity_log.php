<?php
session_start();
include '../config/db.php';

// Auth Check: Agar admin logged in nahi hai toh login page par bhejo
if (!isset($_SESSION['admin_id'])) {
    header('Location: ' . BASE_URL . 'admin/login.php');
    exit;
}

// Join users table to get names
$query = "SELECT al.*, u.name as admin_name 
          FROM activity_log al 
          JOIN users u ON al.user_id = u.id 
          ORDER BY al.timestamp DESC";

$result = mysqli_query($conn, $query);

if (!$result) {
    die("Query Failed: " . mysqli_error($conn));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Activity Log | Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body { background-color: #f4f7f6; font-family: 'Poppins', sans-serif; }
        .card { border-radius: 20px; }
        .table thead { background: #1e40af; color: #fff; }
        .badge-action { background: #60a5fa !important; color: #fff; font-weight: 500; }
    </style>
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="card shadow border-0 p-4">
            <h2 class="fw-bold mb-4" style="color: #1e40af;">
                <i class="fas fa-history me-2"></i> System Activity Logs
            </h2>
            
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>User (Admin)</th>
                            <th>Action</th>
                            <th>Description</th>
                            <th>Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td class="fw-bold"><?php echo htmlspecialchars($row['admin_name']); ?></td>
                            <td><span class="badge badge-action"><?php echo htmlspecialchars($row['action']); ?></span></td>
                            <td><?php echo htmlspecialchars($row['description']); ?></td>
                            <td class="text-muted"><?php echo date('d M Y, h:i A', strtotime($row['timestamp'])); ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                <a href="<?php echo BASE_URL; ?>admin/index.php" class="btn btn-outline-primary shadow-sm" style="border-radius: 12px; padding: 10px 25px;">
                    <i class="fas fa-arrow-left me-2"></i> Back to Dashboard
                </a>
            </div>
        </div>
    </div>
</body>
</html>