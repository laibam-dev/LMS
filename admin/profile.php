<?php
session_start();

// login protection
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Profile</title>
</head>
<body>

<h2>Admin Profile</h2>

<p><b>Email:</b> <?php echo $_SESSION['admin']; ?></p>

<hr>

<!-- basic profile info -->
<form>
    <label>Name</label><br>
    <input type="text" value="Admin" disabled><br><br>

    <label>Email</label><br>
    <input type="email" value="<?php echo $_SESSION['admin']; ?>" disabled><br><br>

    <button disabled>Update Profile</button>
</form>

<br>
<a href="index.php">⬅ Back to Dashboard</a>

</body>
</html>
