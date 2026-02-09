<?php
session_start();
include '../config/db.php';

// 1. Admin login check using BASE_URL
if (!isset($_SESSION['admin_id'])) {
    header('Location: ' . BASE_URL . 'admin/login.php');
    exit;
}

if (isset($_GET['id'])) {
    
    $id = mysqli_real_escape_string($conn, $_GET['id']);
    
    $delete_query = "DELETE FROM users WHERE id = '$id'";

    if (mysqli_query($conn, $delete_query)) {
        // Activity record for user deletion
        $admin_id = $_SESSION['admin_id'];
        log_activity($conn, $admin_id, 'User Deleted', "Admin deleted user with ID: $id");

        // 2. JavaScript redirect using BASE_URL
        echo "<script>
                alert('User Deleted Successfully!'); 
                window.location.href='" . BASE_URL . "admin/manage-users.php';
              </script>";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
} else {
    // 3. Header redirect if no ID is found
    header('Location: ' . BASE_URL . 'admin/manage-users.php');
}
?>