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
        body { background-color: #f4f7f6; font-family: 'Poppins', sans-serif; overflow-x: hidden; }
        
        /* LAYOUT FIX: Main content ko sidebar ke side pe push karne ke liye */
        .main-content { 
            margin-left: 300px; /* Sidebar width + Spacing */
            padding: 30px;
            transition: 0.3s;
        }

        /* AESTHETIC FIX: Blue lines (underlines) khatam karne ke liye */
        a { text-decoration: none !important; }

        .table-container { background: white; border-radius: 15px; padding: 25px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); border: 1px solid #eee; }
        .dashboard-card { background: white; border-radius: 15px; padding: 25px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); border: 1px solid #eee; }
        
        .btn-polymath { background-color: #3b82f6; color: #fff; border: none; border-radius: 10px; padding: 10px 20px; font-weight: 500; }
        .btn-polymath:hover { background-color: #2563eb; color: #fff; }
        
        .badge-student { background-color: #e3f2fd; color: #0d6efd; border: 1px solid #0d6efd; border-radius: 8px; }
        .badge-teacher { background-color: #fff3e0; color: #ef6c00; border: 1px solid #ef6c00; border-radius: 8px; }
        
        .table thead { background-color: #1e40af; color: white; border-radius: 10px; }
        .table thead th { border: none; padding: 15px; font-weight: 500; }
        
        /* Mobile Responsive adjustment */
        @media (max-width: 992px) {
            .main-content { margin-left: 0; padding: 15px; }
        }
    </style>
</head>

<body>

<?php include '../navbar.php'; ?>

<div class="d-flex">
    <?php include 'sidebar.php'; ?>

    <div class="main-content flex-grow-1">
        
        <div class="dashboard-card mb-4">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
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
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert" style="border-radius: 12px; background-color: #dcfce7; color: #166534;">
                <i class="fas fa-check-circle me-2"></i> <?php echo $success; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <script>setTimeout(()=>{ window.location.href='manage-users.php'; }, 1200);</script>
        <?php endif; ?>

        <div class="table-container">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Full Name</th>
                            <th>Email Address</th>
                            <th>Role</th>
                            <th>Joined Date</th>
                            <th class="text-center">Actions</th>
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

<div class="modal fade" id="addUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content" style="border-radius: 20px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.1);">
            <form method="post">
                <div class="modal-header text-white border-0" style="background: #1e40af; border-radius: 20px 20px 0 0;">
                    <h5 class="modal-title fw-bold">Add New User</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Full Name</label>
                        <input type="text" class="form-control rounded-3" name="name" placeholder="Enter name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Email Address</label>
                        <input type="email" class="form-control rounded-3" name="email" placeholder="example@gmail.com" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Role</label>
                        <select class="form-select rounded-3" name="role" required>
                            <option value="student">Student</option>
                            <option value="instructor">Instructor</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Password</label>
                        <input type="password" class="form-control rounded-3" name="password" placeholder="Create password" required>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-polymath rounded-pill px-4" name="add_user">Save User</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>