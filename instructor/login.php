<?php
session_start();
include "../config/db.php";
?>

<!DOCTYPE html>
<html>
<head>
    <title>Instructor Login</title>
</head>
<body>

<h2>Instructor Login</h2>

<form method="post" action="login_process.php">
    <input type="email" name="email" placeholder="Email" required><br><br>
    <input type="password" name="password" placeholder="Password" required><br><br>
    <button type="submit">Login</button>
</form>

</body>
</html>
