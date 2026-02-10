<?php
require_once "../config/db.php";
require_once "../config/base.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/* Already logged in → go dashboard */
if (isset($_SESSION['instructor_id']) && ($_SESSION['role'] ?? '') === 'instructor') {
    header("Location: " . BASE_URL . "instructor/dashboard.php");
    exit;
}

$error = "";
if (isset($_GET['error'])) {
    if ($_GET['error'] == 'invalid') $error = "Invalid email or password";
    elseif ($_GET['error'] == 'empty') $error = "Please fill all fields";
    else $error = "Login failed. Please try again.";
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Instructor Login</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>instructor/style/auth.css">
</head>
<body>

<div class="login-wrapper">
    <div class="login-box">
        <h2>Instructor Login</h2>

        <?php if ($error): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="post" action="<?php echo BASE_URL; ?>instructor/login.php">
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