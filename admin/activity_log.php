<?php
session_start();
include '../config/db.php';

// Refined Query: Join users table to get names
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
    <title>System Activity Log | Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="card shadow border-0 p-4">
            <h2 class="fw-bold mb-4" style="color: #1e40af;"><i class="fas fa-history"></i> System Activity Logs</h2>
            <table class="table table-striped">
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
                        <td><?php echo $row['admin_name']; ?></td>
                        <td><span class="badge bg-info text-dark"><?php echo $row['action']; ?></span></td>
                        <td><?php echo $row['description']; ?></td>
                        <td><?php echo $row['timestamp']; ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            <a href="index.php" class="btn btn-secondary mt-3">Back to Dashboard</a>
        </div>
    </div>
</body>
</html>