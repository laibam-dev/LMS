<?php
session_start();

if (isset($_SESSION['instructor_id']) && ($_SESSION['role'] ?? '') === 'instructor') {
    header("Location: dashboard.php");
    exit;
}

$error = "";
if (isset($_GET['error'])) {
    $error = "Invalid email or password";
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Instructor Login</title>
    <link rel="stylesheet" href="/LMS/instructor/style/auth.css">
</head>
<body>

<div class="login-wrapper">
    <div class="login-box">
        <h2>Instructor Login</h2>

        <?php if ($error): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="post" action="login.php">
            <label>Email</label>
            <input type="email" name="email" required>

            <label>Password</label>
            <input type="password" name="password" required>

            <button type="submit" name="login">Login</button>
        </form>
    </div>
</div>

</body>
</html>
