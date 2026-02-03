
<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}
include '../config/db.php';
// Dashboard statistics
$student_query = "SELECT COUNT(*) as total FROM users WHERE role = 'student'";
$student_result = mysqli_query($conn, $student_query);
$student_data = mysqli_fetch_assoc($student_result);
$teacher_query = "SELECT COUNT(*) as total FROM users WHERE role = 'instructor'";
$teacher_result = mysqli_query($conn, $teacher_query);
$teacher_data = mysqli_fetch_assoc($teacher_result);
$course_query = "SELECT COUNT(*) as total FROM courses";
$course_result = mysqli_query($conn, $course_query);
$course_data = mysqli_fetch_assoc($course_result);
$enroll_query = "SELECT COUNT(*) as total FROM enrollments";
$enroll_result = mysqli_query($conn, $enroll_query);
$enroll_data = mysqli_fetch_assoc($enroll_result);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | Polymath Path Institute</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f4f7f6; }
        .stat-card { border: none; border-radius: 15px; transition: transform 0.3s; }
        .stat-card:hover { transform: translateY(-5px); }
        .bg-polymath { background-color: #1e40af; color: white; }
        .main-content { 
            margin-left: 260px;
            padding: 20px;
            width: calc(100% - 260px);
        }
        .sidebar-fixed {
            background: #1e40af !important;
            color: #FFD700;
        }
        .navbar, .navbar.bg-polymath {
            background-color: #1e40af !important;
        }
        .btn, .btn-polymath, .btn-success, .btn-outline-primary, .btn-outline-info, .btn-outline-secondary {
            background-color: #3b82f6 !important;
            color: #fff !important;
            border: none !important;
        }
        .btn:hover, .btn-polymath:hover, .btn-success:hover, .btn-outline-primary:hover, .btn-outline-info:hover, .btn-outline-secondary:hover {
            background-color: #2563eb !important;
            color: #fff !important;
        }
        .sidebar-fixed a.active, .sidebar-fixed a:hover, .nav-link.active, .nav-link:focus {
            background: #60a5fa !important;
            color: #1e40af !important;
            border-left: 4px solid #60a5fa !important;
        }
        .logo-circle {
            background: #fff !important;
            color: #1e40af !important;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
        }
        .dropdown-menu .dropdown-item {
            color: #1f2937 !important;
        }
        .dropdown-menu .dropdown-item i {
            color: #1e40af !important;
        }
        html { scroll-behavior: smooth; }
        @media (max-width: 991px) {
            .main-content {
                margin-left: 0 !important;
                padding: 1rem !important;
                width: 100% !important;
            }
            .sidebar-fixed {
                position: static !important;
                width: 100% !important;
                min-height: auto !important;
                padding: 1rem !important;
            }
        }
    </style>
</head>
<body>
<?php include '../navbar.php'; ?>
<div class="d-flex" style="min-height:100vh;">
    <?php include 'sidebar.php'; ?>
    <div class="main-content">
        <div class="row mb-4">
            <div class="col">
                <h2 class="fw-bold" style="color: #003366;">Welcome to Admin Dashboard</h2>
                <p class="text-muted">Polymath Path Institute Management System</p>
            </div>
        </div>

    <div class="row g-4">
        <div class="col-md-4">
            <div class="card stat-card shadow-sm h-100">
                <div class="card-body text-center p-4">
                    <h5 class="text-muted mb-3">Total Students</h5>
                    <h2 class="display-5 fw-bold text-primary"><?php echo $student_data['total']; ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card stat-card shadow-sm h-100 border-start border-success border-5">
                <div class="card-body text-center p-4">
                    <h5 class="text-muted mb-3">Instructors</h5>
                    <h2 class="display-5 fw-bold text-success"><?php echo $teacher_data['total']; ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card stat-card shadow-sm h-100 border-start border-warning border-5">
                <div class="card-body text-center p-4">
                    <h5 class="text-muted mb-3">Active Courses</h5>
                    <h2 class="display-5 fw-bold text-warning"><?php echo $course_data['total']; ?></h2>
                </div>
            </div>
        </div>
    </div>
        <!-- Removed Quick Actions and Total Enrollments card -->

    <!-- Recent Activity Section -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card shadow-sm border-0 p-3">
                <h5 class="fw-bold mb-3" style="color:#003366;">Recent Activity</h5>
                <table class="table table-bordered table-hover w-100 text-center align-middle mb-0">
                    <thead style="background:#003366; color:#FFD700;">
                        <tr>
                            <th class="text-start">Name</th>
                            <th>Role</th>
                            <th>Registration Date</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    $recent_users = mysqli_query($conn, "SELECT name, role, created_at FROM users ORDER BY created_at DESC LIMIT 5");
                    while($user = mysqli_fetch_assoc($recent_users)) {
                        echo '<tr>';
                        echo '<td class="text-start">' . htmlspecialchars($user['name']) . '</td>';
                        echo '<td>' . htmlspecialchars(ucfirst($user['role'])) . '</td>';
                        echo '<td>' . date('d M Y, H:i', strtotime($user['created_at'])) . '</td>';
                        echo '</tr>';
                    }
                    ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Visual Analytics Section -->
    <div class="row mt-5 justify-content-center" id="analytics-section">
        <div class="col-lg-10 mx-auto">
            <div class="card shadow-sm border-0 p-4" style="background-color:#003366; color:#FFD700;">
                <h4 class="fw-bold mb-4 text-center" style="color:#FFD700;">Visual Analytics</h4>
                <div class="row justify-content-center">
                    <div class="col-md-6 mb-4 d-flex justify-content-center">
                        <canvas id="enrollmentsBarChart" style="max-width:100%;min-width:350px;height:350px;"></canvas>
                    </div>
                    <div class="col-md-6 mb-4 d-flex justify-content-center">
                        <canvas id="studentTrendLineChart" style="max-width:100%;min-width:350px;height:350px;"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

</div>

<!-- Chart.js -->
                </div>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Bar Chart: Enrollments per Course
const enrollmentsBarChart = document.getElementById('enrollmentsBarChart').getContext('2d');
fetch('analytics_enrollments.php')
    .then(res => res.json())
    .then(data => {
        new Chart(enrollmentsBarChart, {
            type: 'bar',
            data: {
                labels: data.courses,
                datasets: [{
                    label: 'Enrollments',
                    data: data.enrollments,
                    backgroundColor: '#FFD700',
                    borderColor: '#003366',
                    borderWidth: 2
                }]
            },
            options: {
                plugins: { legend: { labels: { color: '#FFD700' } } },
                scales: {
                    x: { ticks: { color: '#FFD700' }, grid: { color: '#003366' } },
                    y: { ticks: { color: '#FFD700' }, grid: { color: '#003366' } }
                }
            }
        });
    });

// Line Chart: Student Registration Trend
const studentTrendLineChart = document.getElementById('studentTrendLineChart').getContext('2d');
fetch('analytics_registrations.php')
    .then(res => res.json())
    .then(data => {
        new Chart(studentTrendLineChart, {
            type: 'line',
            data: {
                labels: data.months,
                datasets: [{
                    label: 'Registrations',
                    data: data.registrations,
                    backgroundColor: 'rgba(0,51,102,0.2)',
                    borderColor: '#FFD700',
                    borderWidth: 3,
                    pointBackgroundColor: '#003366',
                    pointBorderColor: '#FFD700',
                    tension: 0.3
                }]
            },
            options: {
                plugins: { legend: { labels: { color: '#FFD700' } } },
                scales: {
                    x: { ticks: { color: '#FFD700' }, grid: { color: '#003366' } },
                    y: { ticks: { color: '#FFD700' }, grid: { color: '#003366' } }
                }
            }
        });
    });
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>