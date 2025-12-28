<?php
if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'instructor') {
  header("Location: login.php");
  exit;
}

$name  = $_SESSION['name']  ?? 'Instructor';
$email = $_SESSION['email'] ?? '';

$current = basename($_SERVER['PHP_SELF']); // active menu
function active($file, $current){ return $file === $current ? 'active' : ''; }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Instructor Panel</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <link href="../assets/css/instructor.css" rel="stylesheet">
</head>
<body>
<div class="app">

  <aside class="sidebar">
    <div class="brand">
      <div class="logo"><i class="bi bi-mortarboard"></i></div>
      <div>
        <p class="title mb-0">Academia</p>
        <p class="sub">Teacher Portal</p>
      </div>
    </div>

    <div class="navbox">
      <a class="navlink <?= active('index.php',$current) ?>" href="index.php">
        <i class="bi bi-grid"></i> Dashboard
      </a>
      <a class="navlink <?= active('courses.php',$current) ?>" href="courses.php">
        <i class="bi bi-book"></i> Courses
      </a>
      <a class="navlink <?= active('profile.php',$current) ?>" href="profile.php">
        <i class="bi bi-gear"></i> Profile
      </a>
    </div>

    <div class="sidebar-bottom">
      <div class="userbox">
        <div class="avatar"><?= strtoupper(substr($name,0,1)) ?></div>
        <div>
          <p class="name"><?= htmlspecialchars($name) ?></p>
          <p class="email"><?= htmlspecialchars($email) ?></p>
        </div>
      </div>
      <a class="btn logout-btn" href="logout.php"><i class="bi bi-box-arrow-left me-2"></i>Log Out</a>
    </div>
  </aside>

  <main class="main">
