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
    <a href="index.php"><i class="fa fa-tachometer-alt" style="color:#fff;"></i> <span style="color:#fff;">Dashboard</span></a>
    <a href="manage-users.php"><i class="fa fa-users" style="color:#fff;"></i> <span style="color:#fff;">Manage Users</span></a>
    <a href="settings.php"><i class="fa fa-cog" style="color:#fff;"></i> <span style="color:#fff;">Settings</span></a>
   <li class="nav-item list-unstyled">
    <a href="profile.php" class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'profile.php') ? 'active' : ''; ?>" style="color:#fff;">
        <i class="fas fa-user-circle me-2"></i>
        <span>My Profile</span>
    </a>
    <li class="nav-item list-unstyled mt-3">
    <a href="logout.php" class="nav-link text-danger fw-bold">
        <i class="fas fa-sign-out-alt me-2"></i>
        <span>Logout</span>
    </a>
</li>
</li>
</div>
