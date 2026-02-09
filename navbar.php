<?php
// Page ka naam nikalne ke liye
$current_page = basename($_SERVER['PHP_SELF']);

// Database aur count logic
include_once 'config/db.php'; 
$unread_count = 0;

// Agar Admin login hai toh 'unread' messages ginein
if (isset($_SESSION['admin_id'])) {
    $msg_query = "SELECT COUNT(*) as total FROM contact_messages WHERE status = 'unread'";
    $msg_result = mysqli_query($conn, $msg_query);
    if ($msg_result) {
        $msg_data = mysqli_fetch_assoc($msg_result);
        $unread_count = $msg_data['total'];
    }
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
                <a href="<?php echo (isset($_SESSION['admin_id'])) ? BASE_URL.'admin/view_messages.php' : BASE_URL.'contact.php'; ?>" 
                   class="position-relative <?php echo ($current_page == 'contact.php' || $current_page == 'view_messages.php') ? 'active' : ''; ?>">
                    Contact
                    <?php if($unread_count > 0): ?>
                        <span class="badge rounded-pill bg-danger" style="font-size: 10px; position: absolute; top: -5px; right: -10px;">
                            <?php echo $unread_count; ?>
                        </span>
                    <?php endif; ?>
                </a>
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