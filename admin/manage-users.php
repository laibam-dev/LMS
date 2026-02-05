
<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}
include '../config/db.php';

$success = '';
// Handle Add User
if (isset($_POST['add_user'])) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $role = $_POST['role'];
    $password = $_POST['password'];
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("INSERT INTO users (name, email, password_hash, role) VALUES (?, ?, ?, ?)");
    $stmt->bind_param('ssss', $name, $email, $hash, $role);
    if ($stmt->execute()) {
        $success = 'User added successfully!';
    }
    $stmt->close();
}

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
        .sidebar-fixed {
            background: #1e40af !important;
            color:  #f4f7f6;
        }
        .dashboard-card {
    background: white; 
    border-radius: 15px; 
    padding: 25px; 
    box-shadow: 0 4px 12px rgba(0,0,0,0.05); 
    border: 1px solid #eee;
}
        .navbar, .navbar.bg-polymath {
            background-color: #1e40af !important;
        }
        .btn, .btn-polymath, .btn-success, .btn-outline-primary, .btn-outline-info, .btn-outline-secondary {
            background-color: #3b82f6 !important;
            color: #fff !important;
            border: none !important;
        }
        .btn:hover, .btn-polymath:hover, .btn-success:hover, .btn-outline-primary:hover, .btn-outline-info:hover, .btn-outline-secondary:hover {
            background-color: #2563eb !important;
            color: #fff !important;
        }
        .sidebar-fixed a.active, .sidebar-fixed a:hover, .nav-link.active, .nav-link:focus {
            background: #60a5fa !important;
            color: #1e40af !important;
            border-left: 4px solid #60a5fa !important;
        }
        .logo-circle {
            background: #fff !important;
            color: #1e40af !important;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
        }
        .dropdown-menu .dropdown-item {
            color: #1f2937 !important;
        }
        .dropdown-menu .dropdown-item i {
            color: #1e40af !important;
        }
        .badge-student { background-color: #e3f2fd; color: #0d6efd; border: 1px solid #0d6efd; }
        .badge-teacher { background-color: #fff3e0; color: #ef6c00; border: 1px solid #ef6c00; }
        .table thead { background-color: #1e40af; color: white; }
    </style>
</head>

<body>

<?php include '../navbar.php'; ?>
<div class="d-flex">
    <?php include 'sidebar.php'; ?>
    <div class="main-content flex-grow-1">
        <div class="dashboard-card mb-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h2 class="fw-bold mb-1" style="color: #1e40af;">Manage Users</h2>
            <p class="text-muted mb-0">View and manage Students & Instructors for Polymath Path Institute.</p>
        </div>
        <div style="display: flex; gap: 10px; align-items: center; white-space: nowrap;">
            <a href="add_user.php" class="btn btn-primary d-flex align-items-center gap-2" style="background-color: #3b82f6; border: none; border-radius: 10px; padding: 8px 15px; font-size: 14px;">
                <i class="fa fa-user-plus"></i> Add User
            </a>
            <a href="export_users.php" class="btn btn-outline-primary d-flex align-items-center gap-2" style="border: 2px solid #3b82f6; color: #3b82f6; border-radius: 10px; padding: 8px 15px; font-size: 14px; background: transparent;">
                <i class="fa fa-download"></i> Download Report
            </a>
        </div>
    </div>
</div>
        <?php if ($success): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo $success; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <script>setTimeout(()=>{ location.href=location.href; }, 1200);</script>
        <?php endif; ?>
                        <!-- Add User Modal -->
                        <div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form method="post" autocomplete="off">
                                        <div class="modal-header bg-polymath">
                                            <h5 class="modal-title text-white" id="addUserModalLabel">Add New User</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label for="name" class="form-label">Full Name</label>
                                                <input type="text" class="form-control" name="name" id="name" required>
                                            </div>
                                            <div class="mb-3">
                                                <label for="email" class="form-label">Email Address</label>
                                                <input type="email" class="form-control" name="email" id="email" required>
                                            </div>
                                            <div class="mb-3">
                                                <label for="role" class="form-label">Role</label>
                                                <select class="form-select" name="role" id="role" required>
                                                    <option value="student">Student</option>
                                                    <option value="instructor">Instructor</option>
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <label for="password" class="form-label">Password</label>
                                                <input type="password" class="form-control" name="password" id="password" required>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-success" name="add_user">Add User</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
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
                                        <a href="../admin/delete_user.php?id=<?php echo $row['id']; ?>" 
                                            class="btn btn-sm btn-outline-danger" 
                                            onclick="return confirm('Are you sure you want to delete this user?')">Delete</a>
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
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>