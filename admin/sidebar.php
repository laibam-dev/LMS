<style>
    .sidebar-fixed {
        background: #1e40af; 
        color: #fff; 
        width: 240px; 
        position: fixed; 
        top: 80px; 
        left: 20px; 
        z-index: 1030;
        display: flex; 
        flex-direction: column; 
        padding: 1.5rem 1rem;
        /* Height fix taake logout bottom par dikhay */
        height: calc(100vh - 120px); 
        border-radius: 20px;
        box-shadow: 10px 0 30px rgba(0,0,0,0.05);
    }
    
    .sidebar-fixed a {
        color: #fff; 
        font-weight: 500; 
        margin-bottom: 0.5rem; 
        border-radius: 12px; 
        transition: all 0.3s ease;
        padding: 0.8rem 1rem; 
        text-decoration: none;
        display: flex; 
        align-items: center;
        gap: 12px;
    }

    .sidebar-fixed a:hover, .sidebar-fixed a.active {
        background: rgba(255, 255, 255, 0.2);
        color: #fff;
        transform: translateX(5px);
    }

    .logout-link {
        color: #ff4d4d !important; 
        font-weight: 600;
        margin-top: auto; /* Yeh magic line hai jo niche dhakelti hai */
        padding: 12px 15px;
        border-radius: 12px;
        transition: all 0.3s ease;
    }

    .logout-link:hover {
        background-color: rgba(255, 77, 77, 0.1) !important;
    }
</style>

<div class="sidebar-fixed">
    <a href="<?php echo BASE_URL; ?>admin/index.php" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'index.php') ? 'active' : ''; ?>">
        <i class="fa fa-tachometer-alt"></i> <span>Dashboard</span>
    </a>

    <a href="<?php echo BASE_URL; ?>admin/manage-users.php" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'manage-users.php') ? 'active' : ''; ?>">
        <i class="fa fa-users"></i> <span>Manage Users</span>
    </a>

    <a href="<?php echo BASE_URL; ?>admin/send_announcement.php" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'send_announcement.php') ? 'active' : ''; ?>">
        <i class="fas fa-bullhorn"></i> <span>Announcements</span>
    </a>

    <a href="<?php echo BASE_URL; ?>admin/activity_log.php" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'activity_log.php') ? 'active' : ''; ?>">
        <i class="fas fa-list-ul"></i> <span>Activity Logs</span>
    </a>

    <a href="<?php echo BASE_URL; ?>admin/profile.php" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'profile.php') ? 'active' : ''; ?>">
        <i class="fas fa-user-circle"></i> <span>My Profile</span>
    </a>

    <a href="<?php echo BASE_URL; ?>admin/settings.php" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'settings.php') ? 'active' : ''; ?>">
        <i class="fa fa-cog"></i> <span>Settings</span>
    </a>

    <div style="margin-top: auto;">
        <hr style="border-top: 1px solid rgba(255,255,255,0.1); margin-bottom: 10px;">
        <a href="<?php echo BASE_URL; ?>logout.php" class="logout-link" onclick="return confirm('Are you sure you want to logout?')">
            <i class="fas fa-sign-out-alt"></i>
            <span>Logout Account</span>
        </a>
    </div>
</div>