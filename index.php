<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Real LMS</title>
<link rel="stylesheet" href="assets/css/landing.css">
</head>
<body>

<!-- HEADER -->
<header class="topbar">
  <div class="brand">
    <div class="logo">RL</div>
    <div>
      <h1>Real LMS</h1>
      <p>Institute Learning Management System</p>
    </div>
  </div>
  <nav>
    <a href="#login">Login</a>
  </nav>
</header>

<!-- LOGIN CARDS SECTION -->
<section class="login-section" id="login">
  <div class="login-container">

    <div class="login-card">
      <span class="badge">Admin</span>
      <h3>Login as Admin</h3>
      <p>Manage users, courses and system settings.</p>
      <a href="admin/login.php">Login →</a>
    </div>

    <div class="login-card active">
      <span class="badge">Student</span>
      <h3>Login as Student</h3>
      <p>Access courses, assignments and results.</p>
      <a href="student_login.php">Login →</a>
    </div>

    <div class="login-card">
      <span class="badge">Teacher</span>
      <h3>Login as Teacher</h3>
      <p>Upload content and manage classes.</p>
 <a href="instructor/login.php">Login →</a>


    </div>

  </div>
</section>

<footer>
  © <?php echo date('Y'); ?> Real LMS
</footer>

</body>
</html>