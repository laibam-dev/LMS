<?php
session_start();
include '../config/db.php';

// Check karein ke Admin login hai
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

if (isset($_GET['id'])) {
    // Sahi variable name use karein ($id)
    $id = mysqli_real_escape_string($conn, $_GET['id']);
    
    $delete_query = "DELETE FROM users WHERE id = '$id'";

    if (mysqli_query($conn, $delete_query)) {
        // Activity record karein
        $admin_id = $_SESSION['admin_id'];
        log_activity($conn, $admin_id, 'User Deleted', "Admin deleted user with ID: $id");

        echo "<script>alert('User Deleted Successfully!'); window.location.href='manage-users.php';</script>";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
} else {
    header('Location: manage-users.php');
}
?>