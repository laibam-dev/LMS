<?php
// Root folder ki file hai, isliye config ka path yeh hoga
include 'config/db.php'; 
session_start();

// Session saaf karein
session_unset();
session_destroy();

// Wapas login page par bhej dein
header("Location: " . BASE_URL . "admin/login.php");
exit();
?>