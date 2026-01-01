<?php

// Include at top of admin pages/controllers to require admin login
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$user = $_SESSION['user'] ?? null;
if (empty($user) || ($user['role'] ?? '') !== 'admin') {
    // not logged in as admin -> redirect to login
    header('Location: /LMS/admin/src/views/login.php');
    exit;
}