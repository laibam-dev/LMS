<?php
session_start();
include '../config/db.php'; // Top par include kiya taake BASE_URL har jagah milay

// Auth Check using BASE_URL
if (!isset($_SESSION['admin_id'])) {
    header('Location: ' . BASE_URL . 'admin/login.php');
    exit;
}

$message = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];

    $query = "INSERT INTO users (name, email, password_hash, role) VALUES ('$name', '$email', '$password', '$role')";
        
    if (mysqli_query($conn, $query)) {
            $admin_id = $_SESSION['admin_id'];
            log_activity($conn, $admin_id, 'User Added', "Admin added a new user with email: $email");
            
            // Redirect using dynamic BASE_URL in JS
            echo "<script>alert('User Added Successfully!'); window.location.href='" . BASE_URL . "admin/manage-users.php';</script>";
        } else {
            $message = "Error: " . mysqli_error($conn);
        }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add User | Polymath Path Institute</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f4f7f6; }
        .main-content { margin-left: 300px; padding: 40px; width: calc(100% - 300px); }
        .form-card { background: #fff; border-radius: 20px; box-shadow: 0 4px 18px rgba(30,64,175,0.07); padding: 40px; border: none; }
        .btn-polymath { background-color: #1e40af; color: white; border-radius: 10px; padding: 10px 25px; transition: 0.3s; }
        .btn-polymath:hover { background-color: #2563eb; color: white; transform: translateY(-2px); }
        .form-label { font-weight: 600; color: #1e40af; }
        .form-control { border-radius: 10px; border: 1px solid #dee2e6; padding: 12px; }
        .form-control:focus { border-color: #1e40af; box-shadow: 0 0 0 0.2rem rgba(30,64,175,0.1); }
        @media (max-width: 991px) { .main-content { margin-left: 0; width: 100%; padding: 20px; } }
    </style>
</head>
<body>
    <?php include '../navbar.php'; ?>
    <div class="d-flex">
        <?php include 'sidebar.php'; ?>
        <div class="main-content">
            <div class="container-fluid">
                <div class="row justify-content-center">
                    <div class="col-lg-8">
                        <div class="form-card shadow-sm">
                            <div class="d-flex align-items-center mb-4">
                                <a href="<?php echo BASE_URL; ?>admin/manage-users.php" class="text-decoration-none me-3" style="color: #1e40af;">
                                    <i class="fas fa-arrow-left fa-lg"></i>
                                </a>
                                <h2 class="fw-bold mb-0" style="color: #1e40af;">Add New User</h2>
                            </div>
                            
                            <?php if($message != ""): ?>
                                <div class="alert alert-danger" style="border-radius: 12px;"><?php echo $message; ?></div>
                            <?php endif; ?>

                            <form method="POST">
                                <div class="row g-3">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label"><i class="fas fa-user me-2"></i>Full Name</label>
                                        <input type="text" name="name" class="form-control" placeholder="Enter full name" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label"><i class="fas fa-envelope me-2"></i>Email Address</label>
                                        <input type="email" name="email" class="form-control" placeholder="example@polymath.com" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label"><i class="fas fa-lock me-2"></i>Password</label>
                                        <input type="password" name="password" class="form-control" placeholder="Create a strong password" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label"><i class="fas fa-user-tag me-2"></i>User Role</label>
                                        <select name="role" class="form-select form-control" required style="border-radius: 10px;">
                                            <option value="" selected disabled>Select Role</option>
                                            <option value="student">Student</option>
                                            <option value="instructor">Instructor</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="mt-4 text-end">
                                    <button type="reset" class="btn btn-light me-2" style="border-radius: 10px; border: 1px solid #ddd;">Clear Form</button>
                                    <button type="submit" class="btn btn-polymath">
                                        <i class="fas fa-save me-2"></i>Save User
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>