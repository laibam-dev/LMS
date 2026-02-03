<style>
    .sidebar-fixed {
        background: #003366; color: #FFD700; min-height: 100vh; width: 220px; position: fixed; top: 64px; left: 0; z-index: 1030;
        display: flex; flex-direction: column; padding: 2rem 1rem 1rem 1rem;
        box-shadow: 2px 0 8px rgba(0,0,0,0.04);
    }
    .sidebar-fixed a {
        color: #FFD700; font-weight: 500; margin-bottom: 0.5rem; border-radius: 8px; transition: background 0.2s, color 0.2s;
        padding: 0.75rem 1rem; text-decoration: none;
        display: flex; align-items: center;
        gap: 10px;
    }
    .sidebar-fixed a.active, .sidebar-fixed a:hover {
        background: #FFD700; color: #003366;
    }
    .sidebar-section-title {
        color: #FFD700; font-size: 13px; font-weight: 600; margin: 1.5rem 0 0.5rem 0.5rem; letter-spacing: 1px;
        opacity: 0.7;
    }
    .main-content {
        margin-left: 220px;
        margin-top: 64px;
        padding: 2rem 2rem 2rem 2rem;
    }
</style>
<div class="sidebar-fixed">
    <a href="index.php"><i class="fa fa-tachometer-alt"></i> Dashboard</a>
    <a href="manage-users.php"><i class="fa fa-users"></i> Manage Users</a>
    <div class="sidebar-section-title">Analytics</div>
    <a href="index.php#analytics-section"><i class="fa fa-chart-line"></i> Visual Analytics</a>
    <a href="settings.php"><i class="fa fa-cog"></i> Settings</a>
</div>
