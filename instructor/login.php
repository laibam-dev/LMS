<?php session_start(); ?>
<!DOCTYPE html>
<html>
<head>
    <title>Instructor Login</title>
</head>
<body>

<h2>Instructor Login</h2>

<form method="POST" action="login_process.php">
    <label>Email</label><br>
    <input type="email" name="email" required><br><br>

    <label>Password</label><br>
    <input type="password" name="password" required><br><br>

    <button type="submit">Login</button>
</form>

</body>
</html>
