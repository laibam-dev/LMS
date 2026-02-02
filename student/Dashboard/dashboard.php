<?php
session_start();

if(!isset($_SESSION['student_id'])){
    header("Location: ../login.php");
    exit();
}

require_once '../../config/db.php'; 

// '../../' ka matlab hai 2 folder bahar nikal kar LMS folder mein jao
include '../../navbar.php'; 


$student_id = $_SESSION['student_id'];

// --- UPDATE 1: Yahan 'user_id' aur 'progress' add kiya hai ---
$sql = "
SELECT c.id, c.title, e.progress
FROM courses c
JOIN enrollments e ON c.id = e.course_id
WHERE e.user_id = ?
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();

$courses = [];
while($row = $result->fetch_assoc()){
    $courses[] = $row;
}

$total_progress = 0;
$course_count = count($courses);

foreach($courses as $course){
    $total_progress += $course['progress'];
}

$overall_progress = ($course_count > 0) 
    ? round($total_progress / $course_count) 
    : 0;
?>

<!DOCTYPE html>
<html>
<head>
    <title>Student Dashboard</title>
    <link rel="stylesheet" href="../Styles/dashboard.css">
</head>
<body>

<div class="dashboard-container">
    <div class="sidebar">
        <div class="profile-box">
            <div class="profile-img">
                <img src="../Certificates/pic4.jpg" alt="Profile" style="width: 100%; height: 100%; border-radius: 50%; object-fit: cover;">
            </div>
            <h3><?php echo htmlspecialchars($_SESSION['student_name']); ?></h3>
            <p><?php echo htmlspecialchars($_SESSION['student_email']); ?></p>
            <a href="profile.php" class="btn">Update Profile</a>
        </div>

        <div class="sidebar-menu" style="margin-top: 20px;">
            <a href="../logout.php" class="btn" style="background: white; color: black; text-align: center;">Logout</a>
        </div>
    </div>

    <div class="main-content">
        <div class="welcome-card">
            <h2>Welcome <?php echo htmlspecialchars($_SESSION['student_name']); ?> </h2>
            <p>Here is your dashboard overview</p>
        </div>

        <div class="section">
            <h2>Available Courses to Join</h2>
            <div class="cards-row">
                <?php
                
                $all_sql = "SELECT * FROM courses WHERE id NOT IN (SELECT course_id FROM enrollments WHERE user_id = ?)";
                $all_stmt = $conn->prepare($all_sql);
                $all_stmt->bind_param("i", $student_id);
                $all_stmt->execute();
                $all_res = $all_stmt->get_result();
                
                if($all_res->num_rows > 0){
                    while($c = $all_res->fetch_assoc()){
                        echo '<div class="card">';
                        echo '<h4>'.htmlspecialchars($c['title']).'</h4>';
                        echo '<p>'.htmlspecialchars($c['subject']).'</p>';
                        // Join button enroll_process.php ki taraf le jayega
                        echo '<a href="enroll_process.php?course_id='.$c['id'].'" class="button">Join Course</a>';
                        echo '</div>';
                    }
                } else { echo "<p>No new courses available.</p>"; }
                ?>
            </div>
        </div>

        <div class="section">
            <h2>My Enrolled Courses</h2>
            <?php if(!empty($courses)): ?>
                <div class="cards-row">
                    <?php foreach($courses as $course): ?>
                        <div class="card">
                            <h4><?php echo htmlspecialchars($course['title']); ?></h4>
                            <p>Progress: <?php echo $course['progress']; ?>%</p>
                            <a href="courses.php?course_id=<?php echo $course['id']; ?>" class="button">
                                View Details
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p>You are not enrolled in any courses yet.</p>
            <?php endif; ?>
        </div>

        <div class="cards-row">
            <div class="card">
                <h3>Analytics</h3>
                <p>Overall progress: <?php echo $overall_progress; ?>%</p>
                <a href="analytics.php" class="button">View Analytics</a>
            </div>
            <div class="card">
                <h3>Attendance</h3>
                <p>Current Attendance: 85%</p> 
                <a href="attendance_details.php" class="button">View History</a>
            </div>
            <div class="card">
                <h3>Certificates</h3>
                <p>View your completed certificates</p>
                <a href="certificates.php" class="button">View Certificates</a>
            </div>
        </div>
    </div>
</div>
</body>
</html>