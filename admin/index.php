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
        .stat-card { border: 1.5px solid #1e40af; border-radius: 15px; transition: box-shadow 0.3s, transform 0.3s; box-shadow: 0 2px 12px 0 rgba(96,165,250,0.08); }
        .stat-card:hover { transform: translateY(-5px); box-shadow: 0 4px 24px 0 #1e40af; border-color: #1e40af; }
        .bg-polymath { background-color: #1e40af; color: white; }
        .main-content { 
            margin-left: 300px;
            padding: 30px 40px 30px 30px;
            width: calc(100% - 300px);
        }
        .dashboard-card {
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 4px 18px fffrgba(30,64,175,0.07);
            margin-bottom: 20px;
            padding: 30px 25px 25px 25px;
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
            color: #fff !important;
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
        <div class="dashboard-card" style="margin-bottom:30px;">
            <h2 class="fw-bold" style="color: #1e40af;">Welcome to Admin Dashboard</h2>
            <p class="text-muted">Polymath Path Institute Management System</p>
        </div>
        <div class="dashboard-card">
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card stat-card shadow-sm h-100" style="background:#fff; border-radius:20px; box-shadow:0 2px 12px 0 rgba(96,165,250,0.08);">
                        <div class="card-body text-center p-4">
                           <h5 class="text-muted mb-3"><i class="fas fa-user-graduate me-2"></i> Total Students</h5>
                            <h2 class="display-5 fw-bold" style="color:#1e40af;">
                                <?php echo isset($student_data['total']) ? $student_data['total'] : '0'; ?>
                            </h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card stat-card shadow-sm h-100" style="background:#fff; border-radius:20px; box-shadow:0 2px 12px 0 rgba(96,165,250,0.08);">
                        <div class="card-body text-center p-4">
                            <h5 class="text-muted mb-3"><i class="fas fa-chalkboard-teacher me-2"></i> Instructors</h5>
                           
                            <h2 class="display-5 fw-bold" style="color:#1e40af;">
                                <?php echo isset($teacher_data['total']) ? $teacher_data['total'] : '0'; ?>
                            </h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card stat-card shadow-sm h-100" style="background:#fff; border-radius:20px; box-shadow:0 2px 12px 0 rgba(96,165,250,0.08);">
                        <div class="card-body text-center p-4">
                            <h5 class="text-muted mb-3"><i class="fas fa-book-open me-2"></i> Active Courses</h5>
                            <h2 class="display-5 fw-bold" style="color:#1e40af;">
                                <?php echo isset($course_data['total']) ? $course_data['total'] : '0'; ?>
                            </h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    
    <div class="mt-5">
    <h4 class="fw-bold mb-4" style="color:#1e40af;">Visual Analytics</h4> <div class="row g-4">
      <div class="row g-4">
        <div class="col-md-6">
            <div class="dashboard-card h-100 shadow-sm border-0 bg-white">
                <h5 class="fw-bold mb-4" style="color:#1e40af;">Course Enrollments</h5>
                <canvas id="enrollmentsBarChart"></canvas>
            </div>
        </div>
        <div class="col-md-6">
            <div class="dashboard-card h-100 shadow-sm border-0 bg-white">
                <h5 class="fw-bold mb-4" style="color:#1e40af;">Registration Trend</h5>
                <canvas id="studentTrendLineChart"></canvas>
            </div>
        </div>
    </div>
</div>

    <div class="dashboard-card" style="margin-top:30px;">
            <h4 class="fw-bold mb-3" style="color:#1e40af;">Recent Activity</h4>
            <table class="table table-bordered table-hover w-100 text-center align-middle mb-0">
                <thead style="background:#1e40af; color:#fff;">
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
                    backgroundColor: '#1e40af',
                    borderColor: '#60a5fa',
                    borderWidth: 2
                }]
            },
            options: {
                plugins: { legend: { labels: { color: '#333' } } },
                scales: {
                    x: { ticks: { color: '#333' }, grid: { color: '#e5e7eb' } },
                    y: { ticks: { color: '#333' }, grid: { color: '#e5e7eb' } }
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
                    backgroundColor: '#1e40af',
                    borderColor: '#60a5fa',
                    borderWidth: 3,
                    pointBackgroundColor: '#1e40af',
                    pointBorderColor: '#1e40af',
                    tension: 0.3
                }]
            },
            options: {
                plugins: { legend: { labels: { color: '#333' } } },
                scales: {
                    x: { ticks: { color: '#333' }, grid: { color: '#e5e7eb' } },
                    y: { ticks: { color: '#333' }, grid: { color: '#e5e7eb' } }
                    
                }
            }
        });
    });
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>