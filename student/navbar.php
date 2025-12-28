<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if(!isset($_SESSION['student_id'])){
    header("Location: ../login.php");
    exit();
}
?>

<style>
/* Navbar container */
nav{
    background:#007bff;
    color:#fff;
    padding:10px 20px;
    display:flex;
    justify-content:space-between;
    align-items:center;
}

/* Left side: logo + name */
nav .left{
    display:flex;
    align-items:center;
}

nav .left img{
    height:40px;
    width:40px;
    margin-right:10px;
}

nav .left span{
    font-weight:bold;
    font-size:18px;
}

/* Right side: links */
nav .right a{
    color:#fff;
    text-decoration:none;
    margin-left:20px;
    font-weight:bold;
}

nav .right a:hover{
    text-decoration:underline;
}
</style>

<nav>
    <!-- Left: Logo + LMS Name -->
    <div class="left">
        <img src="../assets/logo.jpeg" alt="Logo">
        <span>LMS</span>
    </div>

    <!-- Right: Navigation Links -->
    <div class="right">
        <a href="Dashboard/index.php">Dashboard</a>
        <a href="Dashboard/courses.php">Courses</a>
        <a href="Dashboard/analytics.php">Analytics</a>
        <a href="Dashboard/certificates.php">Certificates</a>
        <a href="Dashboard/achievements.php">Achievements</a>
        <a href="/LMS/student/logout.php">Logout</a>
    </div>
</nav>
