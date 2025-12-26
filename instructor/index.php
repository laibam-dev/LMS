<?php
session_start();

if (!isset($_SESSION['instructor_id'])) {
    header("Location: login.php");
    exit;
}
?>
<h1>Instructor Dashboard</h1>
<p>Welcome, <?php echo $_SESSION['instructor_name']; ?></p>
