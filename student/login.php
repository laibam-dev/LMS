<?php
session_start();
require_once "../config/db.php";

$error = "";
$success = "";
$email = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Input data ko saaf (sanitize) karna zaroori hai
    $email = mysqli_real_escape_string($conn, trim($_POST['email']));
    $password = trim($_POST['password']);

    // VALIDATIONS
    if ($email == "") {
        $error = "Please enter your email";
    } elseif ($password == "") {
        $error = "Please enter your password";
    } else {

        // Table badal kar 'users' kiya aur role check lagaya
        $sql = "SELECT * FROM users WHERE email='$email' AND role='student' LIMIT 1";
        $result = mysqli_query($conn, $sql);

        if (mysqli_num_rows($result) == 1) {
            $row = mysqli_fetch_assoc($result);

            // Password column ka naam 'password_hash' use kiya
            if ($password === $row['password_hash']) {
                // Sessions mein data save karna
                $_SESSION['student_id'] = $row['id'];
                $_SESSION['student_name'] = $row['name'];
                $_SESSION['student_email'] = $row['email'];
                $_SESSION['role'] = $row['role']; 

                $success = "Login successful! Redirecting...";
                header("refresh:2;url=Dashboard/index.php");
            } else {
                $error = "Invalid password";
            }
         } 
        else {
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
    <title>Student Login</title>
    <link rel="stylesheet" href="Styles/login.css">
</head>
<body>

<div class="login-container">
    <form method="POST">
        <h3>Student Login</h3>

        <?php if($error != ""){ ?>
            <div class="error" style="color: red; margin-bottom: 10px;"><?php echo $error; ?></div>
        <?php } ?>

        <?php if($success != ""){ ?>
            <div class="success" style="color: green; margin-bottom: 10px;"><?php echo $success; ?></div>
        <?php } ?>

        <input type="email" name="email" placeholder="Email" value="<?php echo htmlspecialchars($email); ?>" required>
        <input type="password" name="password" placeholder="Password" required>
        <input type="submit" value="Login">
    </form>
</div>

</body>
</html>