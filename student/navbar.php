<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Redirect to login if student not logged in
if(!isset($_SESSION['student_id'])){
    header("Location: ../login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>LMS Navbar</title>
    <style>
    /* Navbar container */
    nav {
            background:linear-gradient(135deg,#1e40af,#1e40af);
        color: #fff;
        padding: 12px 30px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        border-radius: 0 0 10px 10px;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    /* Left side: logo + LMS Name */
    nav .left {
        display: flex;
        align-items: center;
    }

    nav .left img {
        height: 45px;
        width: 45px;
        margin-right: 12px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid #fff;
    }

    nav .left span {
        font-weight: 700;
        font-size: 20px;
        letter-spacing: 1px;
    }

    /* Right side: links */
    nav .right a {
        color: #fff;
        text-decoration: none;
        margin-left: 25px;
        font-weight: 600;
        padding: 8px 12px;
        border-radius: 6px;
        transition: all 0.3s ease;
    }

    nav .right a:hover {
        background: rgba(255,255,255,0.2);
        text-decoration: none;
    }

    /* Active link style */
    nav .right a.active {
        background: rgba(255,255,255,0.3);
    }

    /* Responsive: collapse links on small screens */
    @media (max-width: 768px) {
        nav {
            flex-direction: column;
            align-items: flex-start;
        }

        nav .right {
            margin-top: 10px;
            display: flex;
            flex-wrap: wrap;
        }

        nav .right a {
            margin: 5px 10px 5px 0;
        }
    }
    </style>
</head>
<body>

<nav>
    <div class="left">
        <img src="Certificates/logo1.png" alt="LMS Logo"> 
        <span>LMS</span>
    </div>
    <!-- Right: Navigation Links -->
    <div class="right">
        <a href="Dashboard/index.php" class="<?= basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : '' ?>">Dashboard</a>
        <a href="Dashboard/courses.php" class="<?= basename($_SERVER['PHP_SELF']) == 'courses.php' ? 'active' : '' ?>">Courses</a>
        <a href="Dashboard/analytics.php" class="<?= basename($_SERVER['PHP_SELF']) == 'analytics.php' ? 'active' : '' ?>">Analytics</a>
        <a href="Dashboard/certificates.php" class="<?= basename($_SERVER['PHP_SELF']) == 'certificates.php' ? 'active' : '' ?>">Certificates</a>
        <a href="../logout.php">Logout</a>
    </div>
</nav>

</body>
</html>
