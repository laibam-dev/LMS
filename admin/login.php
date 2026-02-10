<?php
include '../config/db.php';
session_start();

// LOGIN PAGE LOGIC: Agar pehle se login HAI, toh dashboard bhej do
if (isset($_SESSION['admin_id'])) {
    header('Location: ' . BASE_URL . 'admin/index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    // Yahan check karein ke role 'admin' ho
    $query = "SELECT * FROM users WHERE email='$email' AND role='admin'";
    $result = mysqli_query($conn, $query);

    if ($row = mysqli_fetch_assoc($result)) {
        // Password check (agar aap password_hash use kar rahi hain)
        if (password_verify($password, $row['password_hash'])) {
            $_SESSION['admin_id'] = $row['id'];
            $_SESSION['admin_name'] = $row['name'];
            header('Location: ' . BASE_URL . 'admin/index.php');
            exit;
        } else {
            $error = "Invalid password!";
        }
    } else {
        $error = "Admin account not found!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Login | Polymath Path</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f4f7f6; display: flex; align-items: center; justify-content: center; height: 100vh; }
        .login-card { background: white; padding: 40px; border-radius: 20px; box-shadow: 0 4px 18px rgba(0,0,0,0.1); width: 100%; max-width: 400px; }
    </style>
</head>
<body>
    <div class="login-card">
        <h3 class="text-center fw-bold mb-4" style="color: #1e40af;">Admin Login</h3>
        <?php if($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Email Address</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary w-100 py-2" style="background: #1e40af; border:none;">Login</button>
        </form>
    </div>
</body>
</html>