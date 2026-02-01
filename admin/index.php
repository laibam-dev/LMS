<?php 
include '../config/db.php'; 

// Database se Counts nikalna (SQL Queries)
// 1. Total Students Count
$student_query = "SELECT COUNT(*) as total FROM users WHERE role = 'student'";
$student_result = mysqli_query($conn, $student_query);
$student_data = mysqli_fetch_assoc($student_result);

// 2. Total Teachers Count
$teacher_query = "SELECT COUNT(*) as total FROM users WHERE role = 'instructor'";
$teacher_result = mysqli_query($conn, $teacher_query);
$teacher_data = mysqli_fetch_assoc($teacher_result);

// 3. Total Courses Count
$course_query = "SELECT COUNT(*) as total FROM courses";
$course_result = mysqli_query($conn, $course_query);
$course_data = mysqli_fetch_assoc($course_result);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | Polymath Path Institute</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f4f7f6; }
        .stat-card { border: none; border-radius: 15px; transition: transform 0.3s; }
        .stat-card:hover { transform: translateY(-5px); }
        .bg-polymath { background-color: #003366; color: white; }
    </style>
</head>
<body>

<?php include 'admin_navbar.php'; ?>

<div class="container my-5">
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

    <div class="row mt-5">
        <div class="col-md-12">
            <div class="card shadow-sm border-0 p-4">
                <h5 class="fw-bold mb-3">Quick Actions</h5>
                <div class="d-flex gap-2">
                    <a href="manage-users.php" class="btn btn-outline-primary">View User List</a>
                    <a href="manage-courses.php" class="btn btn-outline-secondary">Review Courses</a>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>