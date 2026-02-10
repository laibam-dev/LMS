<style>
    .sidebar-fixed {
        background: #1e40af; color: #fff; 
        min-height: 100vh;
        width: 220px; 
        position: fixed; 
        top: 64px; 
        left: 0; 
        z-index: 1030;
        display: flex; flex-direction: column; 
        padding: 2rem 1rem 1rem 1rem;
        margin-top: 25px;
        margin-left: 25px;
        margin-right: 25px;
        height: calc(100vh - 120px);
        border-radius: 20px;
        box-shadow: 10px 0 30px rgba(0,0,0,0.05);
    }
    .sidebar-fixed a {
        color: #fff; font-weight: 500; margin-bottom: 0.5rem; border-radius: 8px; transition: background 0.2s, color 0.2s;
        padding: 0.75rem 1rem; text-decoration: none;
        display: flex; align-items: center;
        gap: 10px;
    }
    .sidebar-fixed a.active, .sidebar-fixed a:hover {
        background: #60a5fa; color: #fff;
    }
    .sidebar-section-title {
        color: #60a5fa; font-size: 13px; font-weight: 600; margin: 1.5rem 0 0.5rem 0.5rem; letter-spacing: 1px;
        opacity: 0.7;
    }
    .main-content {
        margin-left: 260px;
        margin-top: 20px;
        padding: 2rem 2rem 2rem 2rem;
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
        <i class="fas fa-bullhorn"></i> <span>Post Announcement</span>
    </a>

    <a href="<?php echo BASE_URL; ?>admin/activity_log.php" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'activity_log.php') ? 'active' : ''; ?>">
        <i class="fas fa-list-ul"></i> <span>Activity Logs</span>
    </a>

    <a href="<?php echo BASE_URL; ?>admin/settings.php" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'settings.php') ? 'active' : ''; ?>">
        <i class="fa fa-cog"></i> <span>Settings</span>
    </a>

    <li class="nav-item list-unstyled">
        <a href="<?php echo BASE_URL; ?>admin/profile.php" class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'profile.php') ? 'active' : ''; ?>">
            <i class="fas fa-user-circle"></i>
            <span>My Profile</span>
        </a>
    </li>

    <hr class="text-white-50">
<a href="<?php echo BASE_URL; ?>logout.php" class="nav-link text-danger fw-bold mt-auto" onclick="return confirm('Are you sure you want to logout?')">
    <i class="fas fa-power-off me-2"></i> Logout
</a>
</div>