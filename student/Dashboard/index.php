<?php
session_start();

if(!isset($_SESSION['student_id'])){
    header("Location: ../login.php");
    exit();
}

require_once '../../config/db.php'; 
include '../navbar.php'; 

$student_id = $_SESSION['student_id'];

$sql = "
SELECT c.id, c.title, c.description, c.image, se.progress
FROM course c
JOIN student_enrollment se ON c.id = se.course_id
WHERE se.student_id = ?
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
    <link rel="stylesheet" href="../Styles/index.css">
</head>
<body>

<div class="dashboard-container">

    <!-- LEFT SIDEBAR -->
    <div class="sidebar">
        <div class="profile-box">
            <div class="profile-img"></div>
            <h3><?php echo htmlspecialchars($_SESSION['student_name']); ?></h3>
            <p><?php echo htmlspecialchars($_SESSION['student_email']); ?></p>
            <a href="profile.php" class="btn">Update Profile</a>
        </div>

    
    </div>

    
    <div class="main-content">

        
        <div class="welcome-card">
            <h2>Welcome, <?php echo htmlspecialchars($_SESSION['student_name']); ?> 👋</h2>
            <p>Here is your dashboard overview</p>
        </div>

        <div class="section">
            <h2>My Courses</h2>

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
                <h3>Certificates</h3>
                <p>View your completed certificates</p>
                <a href="certificates.php" class="button">View Certificates</a>
            </div>
        </div>

    
        <div class="section">
            <h2>Achievements</h2>
            <div class="cards-row">
                <div class="card">Badge 1</div>
                <div class="card">Badge 2</div>
            </div>
        </div>

    </div>
</div>

</body>
</html>
