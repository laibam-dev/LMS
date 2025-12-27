<?php
session_start();
include "../config/db.php";

/* Safety check */
if (!isset($_POST['email']) || !isset($_POST['password'])) {
    die("Form data missing");
}

$email = mysqli_real_escape_string($conn, $_POST['email']);
$password = $_POST['password'];

/* Query */
$sql = "SELECT * FROM users WHERE email='$email' AND role='instructor'";
$result = mysqli_query($conn, $sql);

if ($result && mysqli_num_rows($result) === 1) {
    $user = mysqli_fetch_assoc($result);

    if (password_verify($password, $user['password_hash'])) {

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['name'] = $user['name'];
        $_SESSION['email'] = $user['email'];

        header("Location: index.php");
        exit;
    }
}

echo "Invalid email or password";
