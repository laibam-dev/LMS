<?php
require_once "../config/db.php";
require_once "../config/base.php";
require_once "session.php";

?>
<aside class="sidebar">

    <div>
        <div class="brand">
            <div class="logo">🎓</div>
            <div class="brand-text">
                <h3>LMS</h3>
                <p>Teacher Portal</p>
            </div>
        </div>

        <nav class="nav">
            <a href="dashboard.php" class="active">
                📊 Dashboard
            </a>
            <a href="courses.php">
                📘 Courses
            </a>
             <!-- ✅ Analytics Button -->
        <a href="analytics.php" class="menu-item <?php echo basename($_SERVER['PHP_SELF']) == 'analytics.php' ? 'active' : ''; ?>">
            📈 Analytics
        </a>
            <a href="profile.php">
                ⚙️ Profile
            </a>
        </nav>
    </div>

    <div class="sidebar-bottom">
        <div class="user-card">
            <div class="avatar">
                <?php echo strtoupper(substr($instructor_name,0,1)); ?>
            </div>
            <div>
                <strong><?php echo $instructor_name; ?></strong><br>
                <small><?php echo $instructor_email; ?></small>
            </div>
        </div>

        <a href="logout.php" class="logout-btn">Log Out</a>
    </div>

    

</aside>
