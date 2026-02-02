<style>
    .sidebar-fixed {
        background: #003366; color: #FFD700; min-height: 100vh; width: 220px; position: fixed; top: 56px; left: 0; z-index: 1030;
        display: flex; flex-direction: column; padding: 2rem 1rem 1rem 1rem;
    }
    .sidebar-fixed a {
        color: #FFD700; font-weight: 500; margin-bottom: 0.5rem; border-radius: 8px; transition: background 0.2s, color 0.2s;
        padding: 0.75rem 1rem; text-decoration: none;
    }
    .sidebar-fixed a.active, .sidebar-fixed a:hover {
        background: #FFD700; color: #003366;
    }
</style>
<div class="sidebar-fixed">
    <a href="manage_users.php"><i class="fa-solid fa-users"></i> Manage Users</a>
    <a href="manage-courses.php"><i class="fa-solid fa-book"></i> Manage Courses</a>
    <a href="manage-lessons.php"><i class="fa-solid fa-chalkboard-user"></i> Manage Lessons</a>
    <a href="manage-quizzes.php"><i class="fa-solid fa-question"></i> Manage Quizzes</a>
    <a href="manage-assignments.php"><i class="fa-solid fa-file-arrow-up"></i> Manage Assignments</a>
    <a href="manage-attendance.php"><i class="fa-solid fa-user-check"></i> Manage Attendance</a>
    <a href="manage-certificates.php"><i class="fa-solid fa-certificate"></i> Manage Certificates</a>
    <a href="settings.php"><i class="fa-solid fa-gear"></i> Settings</a>
</div>
