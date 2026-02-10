<?php
include 'config/db.php';
session_start();
session_unset();
session_destroy();

// Redirect to login page using BASE_URL
header('Location: ' . BASE_URL . 'admin/login.php');
exit();
?>