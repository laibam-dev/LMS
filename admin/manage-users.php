
<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}
include '../config/db.php'; 

// Database se sirf Students aur Instructors nikalna (Admin ko exclude kar diya)
$query = "SELECT id, name, email, role, created_at FROM users WHERE role != 'admin' ORDER BY id DESC";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Users | Polymath Path Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body { background-color: #f4f7f6; font-family: 'Poppins', sans-serif; }
        .table-container { background: white; border-radius: 15px; padding: 25px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); border: 1px solid #eee; }
        /* Role based colors */
        .badge-student { background-color: #e3f2fd; color: #0d6efd; border: 1px solid #0d6efd; }
        .badge-teacher { background-color: #fff3e0; color: #ef6c00; border: 1px solid #ef6c00; }
        .table thead { background-color: #003366; color: white; }
    </style>
</head>
<body>

<?php include 'admin_navbar.php'; ?>

<div class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold" style="color: #003366;">Manage Users</h2>
            <p class="text-muted small">View and manage Students & Instructors for Polymath Path Institute.</p>
        </div>
        <button class="btn btn-dark shadow-sm" onclick="window.print()">
            <i class="bi bi-printer"></i> Download Report
        </button>
    </div>

    <div class="table-container">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th class="py-3">ID</th>
                    <th class="py-3">Full Name</th>
                    <th class="py-3">Email Address</th>
                    <th class="py-3">Role</th>
                    <th class="py-3">Joined Date</th>
                    <th class="text-center py-3">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                if(mysqli_num_rows($result) > 0) {
                    while($row = mysqli_fetch_assoc($result)) { 
                ?>
                <tr>
                    <td>#<?php echo $row['id']; ?></td>
                    <td class="fw-bold text-dark"><?php echo $row['name']; ?></td>
                    <td><?php echo $row['email']; ?></td>
                    <td>
                        <span class="badge <?php echo ($row['role'] == 'student') ? 'badge-student' : 'badge-teacher'; ?> p-2 px-3">
                            <?php echo strtoupper($row['role']); ?>
                        </span>
                    </td>
                    <td><?php echo date('d M, Y', strtotime($row['created_at'])); ?></td>
                    <td class="text-center">
                        <div class="btn-group">
                            <a href="edit_user.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-outline-info">Edit</a>
                            <a href="delete_user.php?id=<?php echo $row['id']; ?>" 
                               class="btn btn-sm btn-outline-danger" 
                               onclick="return confirm('Kya aap waqai is user ko delete karna chahte hain?')">Delete</a>
                        </div>
                    </td>
                </tr>
                <?php 
                    } 
                } else {
                    echo "<tr><td colspan='6' class='text-center py-4'>No users found besides admin.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>