<?php
session_start();
include '../config/db.php';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $sql = "SELECT id, name, email, password_hash, role FROM users WHERE email=? LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        if ($row['role'] !== 'admin') {
            $error = 'User is not an admin.';
        } elseif (!password_verify($password, $row['password_hash'])) {
            $error = 'Password mismatch.';
        } else {
            $_SESSION['admin_id'] = $row['id'];
            $_SESSION['admin_name'] = $row['name'];
            header('Location: index.php');
            exit;
        }
    } else {
        $error = 'User not found.';
    }
    $stmt->close();
    if ($error) {
        echo '<div class="alert alert-danger py-2" style="margin:0;">'.$error.'</div>';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login | Polymath Path</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@500;700&display=swap" rel="stylesheet">
    <style>
        body { background: #003366; font-family: 'Poppins', sans-serif; }
        .login-box { background: #fff; border-radius: 18px; box-shadow: 0 4px 24px rgba(0,0,0,0.10); max-width: 400px; margin: 80px auto; padding: 40px 32px; border: 3px solid #FFD700; }
        .main-title { color: #003366; font-size: 2rem; font-weight: 700; letter-spacing: 1px; }
        .btn-polymath { background: #003366; color: #FFD700; border: none; font-weight: 600; }
        .btn-polymath:hover { background: #FFD700; color: #003366; }
        .form-label { color: #003366; font-weight: 600; }
        .back-link { color: #FFD700; text-decoration: underline; font-size: 1rem; }
        .back-link:hover { color: #003366; }
    </style>
</head>
<body>
    <div class="login-box">
        <div class="text-center mb-4">
            <div class="main-title">Admin Login</div>
            <div class="mb-2" style="color:#FFD700; font-weight:600;">Polymath Path</div>
        </div>
        <?php if ($error): ?>
            <div class="alert alert-danger py-2"><?php echo $error; ?></div>
        <?php endif; ?>
        <form method="post" autocomplete="off">
            <div class="mb-3">
                <label class="form-label">Email Address</label>
                <input type="email" name="email" class="form-control" required autofocus>
            </div>
            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-polymath w-100">Login</button>
        </form>
        <div class="text-center mt-3">
            <a href="../index.php" class="back-link">&larr; Back to Selection</a>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
