<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'instructor') {
    header("Location: login.php");
    exit;
}

$name  = $_SESSION['name']  ?? 'Instructor';
$email = $_SESSION['email'] ?? '';
?>

<!DOCTYPE html>
<html>
<head>
    <title>Instructor Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body { margin:0; background:#f4f6f9; }
        .sidebar {
            width:240px; min-height:100vh;
            background:#212529; color:#adb5bd;
        }
        .sidebar a {
            color:#adb5bd; text-decoration:none;
            display:block; padding:12px 20px;
        }
        .sidebar a:hover { background:#343a40; color:#fff; }
        .content { padding:25px; width:100%; }
    </style>
</head>
<body>

<div class="d-flex">

    <!-- SIDEBAR -->
    <div class="sidebar d-flex flex-column justify-content-between">

        <div>
            <h5 class="text-white px-3 pt-3 mb-1">LMS</h5>
            <small class="text-secondary px-3">Teacher Portal</small>
            <hr>

            <a href="index.php">Dashboard</a>
            <a href="courses.php">Courses</a>
        </div>

        <div class="px-3 pb-3">
            <hr>
            <div class="d-flex align-items-center mb-2">
                <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center"
                     style="width:40px;height:40px;">
                    <?= strtoupper(substr($name,0,1)) ?>
                </div>
                <div class="ms-2">
                    <div class="text-white fw-bold"><?= htmlspecialchars($name) ?></div>
                    <small class="text-secondary"><?= htmlspecialchars($email) ?></small>
                </div>
            </div>

            <a href="logout.php" class="btn btn-outline-danger w-100 btn-sm">Log Out</a>
        </div>
    </div>

    <!-- PAGE CONTENT START -->
    <div class="content">
