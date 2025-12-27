<?php
session_start();
include "../config/db.php";

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Instructor Login</title>
    <link rel="stylesheet" href="../assets/css/instructor.css">
</head>
<body class="login-page">

    <div class="login-container">
        <div class="login-card">
            <h2>Instructor Login</h2>

            <form method="POST" action="login_process.php">
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" required>
                </div>

                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" required>
                </div>

                <button type="submit" class="btn-primary">Login</button>
            </form>
        </div>
    </div>

</body>
</html>
