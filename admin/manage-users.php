<?php
session_start();
include '../config/db.php'; 

// 1. Admin Auth Check using BASE_URL
if (!isset($_SESSION['admin_id'])) {
    header('Location: ' . BASE_URL . 'admin/login.php');
    exit;
}

$success = '';
// Handle Add User
if (isset($_POST['add_user'])) {
    $name = mysqli_real_escape_string($conn, trim($_POST['name']));
    $email = mysqli_real_escape_string($conn, trim($_POST['email']));
    $role = $_POST['role'];
    $password = $_POST['password'];
    $hash = password_hash($password, PASSWORD_DEFAULT);
    
    $stmt = $conn->prepare("INSERT INTO users (name, email, password_hash, role) VALUES (?, ?, ?, ?)");
    $stmt->bind_param('ssss', $name, $email, $hash, $role);
    
    if ($stmt->execute()) {
        $success = 'User added successfully!';
        // Log Activity
        log_activity($conn, $_SESSION['admin_id'], 'User Added', "Admin added $name ($role)");
    }
    $stmt->close();
}

// Database query (Admin ko exclude kar diya)
$query = "SELECT id, name, email, role, created_at FROM users WHERE role != 'admin' ORDER BY id DESC";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users | Polymath Path Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { background-color: #f4f7f6; font-family: 'Poppins', sans-serif; }
        .table-container { background: white; border-radius: 15px; padding: 25px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); border: 1px solid #eee; }
        .dashboard-card { background: white; border-radius: 15px; padding: 25px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); border: 1px solid #eee; }
        .btn-polymath { background-color: #3b82f6; color: #fff; border: none; border-radius: 10px; }
        .btn-polymath:hover { background-color: #2563eb; color: #fff; }
        .badge-student { background-color: #e3f2fd; color: #0d6efd; border: 1px solid #0d6efd; }
        .badge-teacher { background-color: #fff3e0; color: #ef6c00; border: 1px solid #ef6c00; }
        .table thead { background-color: #1e40af; color: white; }
    </style>
</head>

<body>

<?php include '../navbar.php'; ?>
<div class="d-flex">
    <?php include 'sidebar.php'; ?>
    <div class="main-content flex-grow-1 p-4">
        
        <div class="dashboard-card mb-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h2 class="fw-bold mb-1" style="color: #1e40af;">Manage Users</h2>
                    <p class="text-muted mb-0">View and manage Students & Instructors.</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="<?php echo BASE_URL; ?>admin/export_users.php" class="btn btn-outline-primary d-flex align-items-center gap-2" style="border-radius: 10px; border: 2px solid #3b82f6; color: #3b82f6; background: transparent;">
                        <i class="fa fa-download"></i> Report
                    </a>
                    <button type="button" class="btn btn-polymath d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#addUserModal">
                        <i class="fa fa-user-plus"></i> Add User
                    </button>
                </div>
            </div>
        </div>

        <?php if ($success): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert" style="border-radius: 12px;">
                <i class="fas fa-check-circle me-2"></i> <?php echo $success; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <script>setTimeout(()=>{ window.location.href='manage-users.php'; }, 1200);</script>
        <?php endif; ?>

        <div class="modal fade" id="addUserModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content" style="border-radius: 20px; overflow: hidden;">
                    <form method="post">
                        <div class="modal-header text-white" style="background: #1e40af;">
                            <h5 class="modal-title">Add New User</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body p-4">
                            <div class="mb-3">
                                <label class="form-label">Full Name</label>
                                <input type="text" class="form-control" name="name" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Email Address</label>
                                <input type="email" class="form-control" name="email" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Role</label>
                                <select class="form-select" name="role" required>
                                    <option value="student">Student</option>
                                    <option value="instructor">Instructor</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Password</label>
                                <input type="password" class="form-control" name="password" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-polymath" name="add_user">Save User</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="table-container">
            <div class="table-responsive">
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
                        <?php if(mysqli_num_rows($result) > 0): ?>
                            <?php while($row = mysqli_fetch_assoc($result)): ?>
                            <tr>
                                <td class="text-muted">#<?php echo $row['id']; ?></td>
                                <td class="fw-bold text-dark"><?php echo htmlspecialchars($row['name']); ?></td>
                                <td><?php echo htmlspecialchars($row['email']); ?></td>
                                <td>
                                    <span class="badge <?php echo ($row['role'] == 'student') ? 'badge-student' : 'badge-teacher'; ?> p-2 px-3">
                                        <?php echo strtoupper($row['role']); ?>
                                    </span>
                                </td>
                                <td><?php echo date('d M, Y', strtotime($row['created_at'])); ?></td>
                                <td class="text-center">
                                    <div class="btn-group shadow-sm">
                                        <a href="<?php echo BASE_URL; ?>admin/edit_user.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-white text-info border">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="<?php echo BASE_URL; ?>admin/delete_user.php?id=<?php echo $row['id']; ?>" 
                                           class="btn btn-sm btn-white text-danger border" 
                                           onclick="return confirm('Delete this user forever?')">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="6" class="text-center py-4">No users found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>