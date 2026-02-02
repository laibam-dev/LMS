<?php
include '../config/db.php';
$new_password = 'admin123';
$new_hash = password_hash($new_password, PASSWORD_DEFAULT);
$sql = "UPDATE users SET password_hash = '$new_hash' WHERE role = 'admin'";
if (mysqli_query($conn, $sql)) {
    echo 'Success! Password hash updated. Now delete this file and login with admin123.';
} else {
    echo 'Error updating: ' . mysqli_error($conn);
}
?>
