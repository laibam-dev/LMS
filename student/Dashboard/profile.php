<?php
session_start();
require_once "../../config/db.php"; 


if(!isset($_SESSION['student_id'])){
    header("Location: ../login.php");
    exit();
}

// Navbar
include '../navbar.php';

$error = "";
$success = "";

// Fetch current student info
$student_id = $_SESSION['student_id'];
$sql = "SELECT * FROM account WHERE id='$student_id'";
$result = mysqli_query($conn, $sql);
$student = mysqli_fetch_assoc($result);

if($_SERVER["REQUEST_METHOD"] == "POST"){
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $cpassword = trim($_POST['cpassword']);

    if($name == "" || $email == ""){
        $error = "Name and Email are required.";
    } elseif($password != "" && strlen($password) < 6){
        $error = "Password must be at least 6 characters.";
    } elseif($password != $cpassword){
        $error = "Passwords do not match.";
    } else {
        if($password != ""){
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $sql_update = "UPDATE account SET name='$name', email='$email', password='$hashed' WHERE id='$student_id'";
        } else {
            $sql_update = "UPDATE account SET name='$name', email='$email' WHERE id='$student_id'";
        }
        if(mysqli_query($conn, $sql_update)){
            $success = "Profile updated successfully!";
            $_SESSION['student_name'] = $name;
            $_SESSION['student_email'] = $email;
        } else {
            $error = "Database error!";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Profile</title>
    <link rel="stylesheet" href="../Styles/profile.css">            <link rel="stylesheet" href="Styles/login.css">

</head>
<body>

<div class="container">
    <div class="section">
        <h2>Edit Profile</h2>

        <?php if($error != ""){ echo "<div class='error'>$error</div>"; } ?>
        <?php if($success != ""){ echo "<div class='success'>$success</div>"; } ?>

        <form method="POST">
            <input type="text" name="name" placeholder="Name" value="<?php echo $student['name']; ?>" required>
            <input type="email" name="email" placeholder="Email" value="<?php echo $student['email']; ?>" required>
            <input type="password" name="password" placeholder="New Password (leave blank if no change)">
            <input type="password" name="cpassword" placeholder="Confirm New Password">
            <input type="submit" class="button" value="Update Profile">
        </form>
    </div>

    <div class="section">
        <a href="index.php" class="button">Back to Dashboard</a>
    </div>
</div>

</body>
</html>
