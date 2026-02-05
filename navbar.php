<?php
// Current page ka naam nikalne ke liye
$current_page = basename($_SERVER['PHP_SELF']);

// Pehle check karein ke BASE_URL define hai ya nahi (safety ke liye)
if (!defined('BASE_URL')) {
    include_once 'config/db.php';
}
?>

<link rel="stylesheet" href="<?php echo BASE_URL; ?>navbar.css">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<nav class="main-navbar">
    <div class="nav-container">
        <a href="<?php echo BASE_URL; ?>index.php" class="nav-logo">
            <div class="logo-circle">LMS</div>
            <span>Learning Portal</span>
        </a>

        <ul class="nav-links">
            <li>
                <a href="<?php echo BASE_URL; ?>index.php" class="<?php echo ($current_page == 'index.php') ? 'active' : ''; ?>">Home</a>
            </li>
            
            <li>
                <a href="<?php echo BASE_URL; ?>contact.php" class="<?php echo ($current_page == 'contact.php') ? 'active' : ''; ?>">Contact</a>
            </li>            
            
            <li class="dropdown">
                <a href="javascript:void(0)" class="nav-btn dropbtn">
                    Login <i class="fas fa-caret-down"></i>
                </a>
                <div class="dropdown-content">
                    <a href="<?php echo BASE_URL; ?>student/login.php">
                        <i class="fas fa-user-graduate"></i> Student Login
                    </a>
                    <a href="<?php echo BASE_URL; ?>instructor/index.php">
                        <i class="fas fa-chalkboard-teacher"></i> Instructor Login
                    </a>
                    <a href="<?php echo BASE_URL; ?>admin/login.php">
                        <i class="fas fa-user-shield"></i> Admin Login
                    </a>
                </div>
            </li>
        </ul>
    </div>
</nav>