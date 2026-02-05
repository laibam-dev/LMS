<?php
session_start();
require_once '../../config/db.php';

if (!isset($_SESSION['student_id'])) {
    header("Location: " . BASE_URL . "student/login.php");
    exit();
}

include '../../navbar.php';

$student_id = $_SESSION['student_id'];
$message = "";

// Fetch student data
$sql = "SELECT * FROM account WHERE id='$student_id'";
$result = mysqli_query($conn, $sql);
$student = mysqli_fetch_assoc($result);

// Update profile
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];
    $cpassword = $_POST['cpassword'];

    if (!empty($password)) {
        if ($password === $cpassword) {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $update = "UPDATE account 
                       SET name='$name', email='$email', password='$hashed' 
                       WHERE id='$student_id'";
        } else {
            $message = "Passwords do not match!";
        }
    } else {
        $update = "UPDATE account 
                   SET name='$name', email='$email' 
                   WHERE id='$student_id'";
    }

    if (isset($update) && mysqli_query($conn, $update)) {
        $message = "Profile updated successfully!";
        $student['name'] = $name;
        $student['email'] = $email;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Profile - LMS</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>student/Styles/profile.css">
</head>
<body>

<div class="container">

    <div class="section">
        <h2>Edit Profile</h2>

        <?php if (!empty($message)) { ?>
            <p style="text-align:center; margin-bottom:15px; color:#4a90e2;">
                <?php echo $message; ?>
            </p>
        <?php } ?>

        <form method="POST">

            <label>Full Name</label>
            <input type="text" name="name"
                   value="<?php echo htmlspecialchars($student['name']); ?>" required>

            <label>Email Address</label>
            <input type="email" name="email"
                   value="<?php echo htmlspecialchars($student['email']); ?>" required>

            <label>New Password</label>
            <input type="password" name="password"
                   placeholder="Leave blank if no change">

            <label>Confirm Password</label>
            <input type="password" name="cpassword"
                   placeholder="Confirm new password">

            <input type="submit" value="Update Profile" class="button">

        </form>
    </div>

    <div style="text-align:center; margin-top:20px;">
        <a href="<?php echo BASE_URL; ?>student/Dashboard/dashboard.php"
           class="button" style="background:#6c757d;">
           Back to Dashboard
        </a>
    </div>

</div>

</body>
</html>