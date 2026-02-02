<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}
include '../config/db.php';

// Handle Add User
$add_msg = '';
if (isset($_POST['add_user'])) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $role = $_POST['role'];
    $password = $_POST['password'];
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("INSERT INTO users (name, email, password_hash, role) VALUES (?, ?, ?, ?)");
    $stmt->bind_param('ssss', $name, $email, $hash, $role);
    if ($stmt->execute()) {
        $add_msg = '<div class="alert alert-success">User added successfully.</div>';
    } else {
        $add_msg = '<div class="alert alert-danger">Error: '.htmlspecialchars($stmt->error).'</div>';
    }
    $stmt->close();
}

// Handle Edit User
if (isset($_POST['edit_user'])) {
    $id = intval($_POST['edit_id']);
    $name = trim($_POST['edit_name']);
    $role = $_POST['edit_role'];
    $stmt = $conn->prepare("UPDATE users SET name=?, role=? WHERE id=?");
    $stmt->bind_param('ssi', $name, $role, $id);
    $stmt->execute();
    $stmt->close();
}

// Handle Delete User
$delete_msg = '';
if (isset($_POST['delete_user'])) {
    $id = intval($_POST['delete_id']);
  $admin_id = $_SESSION['admin_id'];
  $stmt = $conn->prepare("SELECT role FROM users WHERE id=?");
  $stmt->bind_param('i', $id);
  $stmt->execute();
  $stmt->bind_result($role);
  $stmt->fetch();
  $stmt->close();
  if ($role === 'admin' || $id == $admin_id) {
    $delete_msg = '<div class="alert alert-danger">You cannot delete an Admin account!</div>';
  } else {
    $stmt = $conn->prepare("DELETE FROM users WHERE id=?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->close();
  }
}

// Fetch all users
$users = $conn->query("SELECT id, name, email, role FROM users ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Manage Users | Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <style>
    body { background: #f4f7f6; font-family: 'Poppins', sans-serif; }
    .table thead { background-color: #003366; color: #FFD700; }
    .btn-polymath { background-color: #003366; color: #FFD700; border: none; }
    .btn-polymath:hover { background-color: #FFD700; color: #003366; }
    .sidebar-fixed {
      background: #003366; color: #FFD700; min-height: 100vh; width: 220px; position: fixed; top: 56px; left: 0; z-index: 1030;
      display: flex; flex-direction: column; padding: 2rem 1rem 1rem 1rem;
    }
    .main-content {
      margin-left: 220px; padding: 2rem 2rem 2rem 2rem;
    }
    @media (max-width: 991px) {
      .sidebar-fixed { position: static; width: 100%; min-height: auto; }
      .main-content { margin-left: 0; padding: 1rem; }
    }
  </style>
</head>
<body>
<?php include 'navbar.php'; ?>
<div class="d-flex">
  <?php include 'sidebar.php'; ?>
  <div class="main-content flex-grow-1" style="margin-left:220px; padding:2rem 2rem 2rem 2rem;">
            <h2 class="fw-bold mb-4" style="color:#003366;">User Management</h2>
            <?php echo $add_msg; ?>
            <?php if (!empty($delete_msg)) echo $delete_msg; ?>
            <button class="btn btn-polymath mb-3" data-bs-toggle="modal" data-bs-target="#addUserModal">Add User</button>
            <table class="table table-bordered table-hover">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Name</th>
                  <th>Email</th>
                  <th>Role</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
              <?php while($row = $users->fetch_assoc()): ?>
                <tr>
                  <td><?php echo $row['id']; ?></td>
                  <td><?php echo htmlspecialchars($row['name']); ?></td>
                  <td><?php echo htmlspecialchars($row['email']); ?></td>
                  <td><?php echo ucfirst($row['role']); ?></td>
                  <td>
                    <button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#editUserModal<?php echo $row['id']; ?>">Edit</button>
                    <?php if ($row['role'] !== 'admin' && $row['id'] != $_SESSION['admin_id']): ?>
                    <form method="post" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this user?');">
                      <input type="hidden" name="delete_id" value="<?php echo $row['id']; ?>">
                      <button type="submit" name="delete_user" class="btn btn-sm btn-danger">Delete</button>
                    </form>
                    <?php endif; ?>
                  </td>
                </tr>
                    <!-- Edit User Modal -->
                    <div class="modal fade" id="editUserModal<?php echo $row['id']; ?>" tabindex="-1" aria-labelledby="editUserModalLabel<?php echo $row['id']; ?>" aria-hidden="true">
                      <div class="modal-dialog">
                        <div class="modal-content">
                          <form method="post">
                            <div class="modal-header">
                              <h5 class="modal-title" id="editUserModalLabel<?php echo $row['id']; ?>">Edit User</h5>
                              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                              <input type="hidden" name="edit_id" value="<?php echo $row['id']; ?>">
                              <div class="mb-3">
                                <label class="form-label">Name</label>
                                <input type="text" name="edit_name" class="form-control" value="<?php echo htmlspecialchars($row['name']); ?>" required>
                              </div>
                              <div class="mb-3">
                                <label class="form-label">Role</label>
                                <select name="edit_role" class="form-select" required>
                                  <option value="admin" <?php if($row['role']=='admin') echo 'selected'; ?>>Admin</option>
                                  <option value="instructor" <?php if($row['role']=='instructor') echo 'selected'; ?>>Instructor</option>
                                  <option value="student" <?php if($row['role']=='student') echo 'selected'; ?>>Student</option>
                                </select>
                              </div>
                            </div>
                            <div class="modal-footer">
                              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                              <button type="submit" name="edit_user" class="btn btn-polymath">Save Changes</button>
                            </div>
                          </form>
                        </div>
                      </div>
                    </div>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>
      </div>
    </div>
    <!-- Add User Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="post">
        <div class="modal-header">
          <h5 class="modal-title" id="addUserModalLabel">Add New User</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Name</label>
            <input type="text" name="name" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Role</label>
            <select name="role" class="form-select" required>
              <option value="admin">Admin</option>
              <option value="instructor">Instructor</option>
              <option value="student">Student</option>
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" name="add_user" class="btn btn-polymath">Add User</button>
        </div>
      </form>
    </div>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
