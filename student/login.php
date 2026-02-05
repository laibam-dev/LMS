<?php
session_start();
// db.php include hai, isi mein humne BASE_URL define kiya tha
require_once "../config/db.php";

$error = "";
$success = "";
$email = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = mysqli_real_escape_string($conn, trim($_POST['email']));
    $password = trim($_POST['password']);

    if ($email == "") {
        $error = "Please enter your email";
    } elseif ($password == "") {
        $error = "Please enter your password";
    } else {
        $sql = "SELECT * FROM users WHERE email='$email' AND role='student' LIMIT 1";
        $result = mysqli_query($conn, $sql);

        if (mysqli_num_rows($result) == 1) {
            $row = mysqli_fetch_assoc($result);
            if ($password === $row['password_hash']) {
                $_SESSION['student_id'] = $row['id'];
                $_SESSION['student_name'] = $row['name'];
                $_SESSION['student_email'] = $row['email'];
                $_SESSION['role'] = $row['role']; 

                $success = "Login successful! Redirecting...";
                // Yahan absolute path use karna behtar hai
                header("Location: Dashboard/dashboard.php");
                exit();
            
            } else {
                $error = "Invalid password";
            }
         } else {
            $error = "No student account found with this email";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Login - LMS</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>student/Styles/login.css?v=1.3">
</head>
<body>

<div class="login-box">
    <form method="POST">
        <h2>Login</h2>

        <?php if($error != ""){ echo "<p class='msg error'>$error</p>"; } ?>
        <?php if($success != ""){ echo "<p class='msg success'>$success</p>"; } ?>

        <div class="input-group">
            <input type="email" name="email" placeholder="Username" value="<?= htmlspecialchars($email); ?>" required>
            <i class="fa-solid fa-user"></i>
        </div>
       
        <div class="input-group">
            <input type="password" name="password" placeholder="Password" required>
            <i class="fa-solid fa-lock"></i>
        </div>

        <div class="options">
            <label><input type="checkbox" name="remember"> Remember me</label>
            <a href="#">Forgot password?</a>
        </div>

        <button type="submit" class="login-btn">Login</button>

        <div class="register-link">
            <p>Don't have an account? <a href="#">Register</a></p>
        </div>
    </form>
</div>

</body>
</html>