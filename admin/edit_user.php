<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}
include '../config/db.php';

$id = $_GET['id'];
$user_query = "SELECT * FROM users WHERE id = $id";
$user_result = mysqli_query($conn, $user_query);
$user = mysqli_fetch_assoc($user_result);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $role = $_POST['role'];
    
    // Agar password field bhari hai toh update karein, warna purana rehne dein
    if (!empty($_POST['password'])) {
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $update_query = "UPDATE users SET email='$email', password_hash='$password', role='$role' WHERE id=$id";
    } else {
        $update_query = "UPDATE users SET email='$email', role='$role' WHERE id=$id";
    }

    if (mysqli_query($conn, $update_query)) {
        echo "<script>alert('User Updated Successfully!'); window.location.href='manage-users.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Edit User | Polymath Path Institute</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f4f7f6; }
        .form-card { background: #fff; border-radius: 20px; padding: 40px; box-shadow: 0 4px 18px rgba(30,64,175,0.07); margin-top: 50px; }
        .btn-polymath { background-color: #1e40af; color: white; border-radius: 10px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="form-card">
                    <h2 class="fw-bold mb-4" style="color: #1e40af;">Edit User</h2>
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label">Email Address</label>
                            <input type="email" name="email" class="form-control" value="<?php echo $user['email']; ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">New Password (Leave blank to keep current)</label>
                            <input type="password" name="password" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Role</label>
                            <select name="role" class="form-select">
                                <option value="student" <?php if($user['role'] == 'student') echo 'selected'; ?>>Student</option>
                                <option value="instructor" <?php if($user['role'] == 'instructor') echo 'selected'; ?>>Instructor</option>
                                <option value="admin" <?php if($user['role'] == 'admin') echo 'selected'; ?>>Admin</option>
                            </select>
                        </div>
                        <div class="mt-4">
                            <button type="submit" class="btn btn-polymath w-100 p-2">Update User</button>
                            <a href="manage-users.php" class="btn btn-light w-100 mt-2">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>