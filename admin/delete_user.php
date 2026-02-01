<?php
include '../config/db.php';

if(isset($_GET['id'])) {
    $id = $_GET['id'];
    $delete_query = "DELETE FROM users WHERE id = $id";
    
    if(mysqli_query($conn, $delete_query)) {
        header("Location: manage-users.php?msg=User Deleted Successfully");
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>