<?php
session_start();
// Dashboard folder ke andar hone ki wajah se path 2 level piche jaye ga (../../)
require_once '../../config/db.php'; 

if(!isset($_SESSION['student_id'])){
    // Login redirect using BASE_URL
    header("Location: " . BASE_URL . "student/login.php");
    exit();
}

// Navbar include path
include $_SERVER['DOCUMENT_ROOT'] . '/LMS/navbar.php'; 

$student_id = $_SESSION['student_id'];

// SQL Query for enrolled courses
$sql = "
SELECT c.id, c.title, c.subject, c.description, e.progress
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

// Announcements fetch karna (Sirf top 2 dashboard par dikhane ke liye)
$ann_query = "SELECT * FROM announcements WHERE target_role IN ('all', 'student') ORDER BY created_at DESC LIMIT 2";
$ann_result = mysqli_query($conn, $ann_query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Student Dashboard</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>student/Styles/dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* Naya Announcements Style (White Box with Blue/Black Text) */
        .announcement-box { 
            background: #ffffff; 
            border: 1px solid #e2e8f0; 
            border-left: 5px solid #1e40af; 
            padding: 20px; 
            border-radius: 12px; 
            margin-top: 20px; 
            margin-bottom: 30px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        }
        .announcement-box h4 { color: #1e40af; margin-bottom: 10px; font-weight: bold; }
        .announcement-box p { color: #334155; margin: 5px 0; font-size: 0.95rem; }
        .announcement-box a { color: #3b82f6; text-decoration: none; font-weight: 600; font-size: 0.9rem; }
        .announcement-box a:hover { text-decoration: underline; }
    </style>
</head>
<body>

<div class="dashboard-container">
    <div class="sidebar">
        <div class="profile-box">
            <div class="profile-img">
                <img src="<?php echo BASE_URL; ?>assets/pic4.jpg" alt="Profile" style="width: 100%; height: 100%; border-radius: 50%; object-fit: cover;">
            </div>
            <h3><?php echo htmlspecialchars($_SESSION['student_name']); ?></h3>
            <p><?php echo htmlspecialchars($_SESSION['student_email']); ?></p>
            <a href="<?php echo BASE_URL; ?>student/Dashboard/profile.php" class="btn">Update Profile</a>
        </div>

        <div class="sidebar-menu" style="margin-top: 20px;">
            <a href="<?php echo BASE_URL; ?>student/Dashboard/announcements.php" class="btn" style="background: #1e40af; color: white; text-align: center; margin-bottom: 10px;">
                <i class="fas fa-bullhorn"></i> Announcements
            </a>
            
            <a href="<?php echo BASE_URL; ?>student/logout.php" class="btn" style="background: white; color: black; text-align: center;">Logout</a>
        </div>
    </div>

    <div class="main-content">
        <div class="welcome-card">
            <h2>Welcome <?php echo htmlspecialchars($_SESSION['student_name']); ?> </h2>
            <p>Here is your dashboard overview</p>
        </div>

        <?php if(mysqli_num_rows($ann_result) > 0): ?>
            <div class="announcement-box">
                <h4><i class="fas fa-bullhorn"></i> Latest Announcements</h4>
                <?php while($ann = mysqli_fetch_assoc($ann_result)): ?>
                    <p><strong><?php echo htmlspecialchars($ann['title']); ?>:</strong> 
                    <?php echo substr(htmlspecialchars($ann['message']), 0, 100); ?>...</p>
                <?php endwhile; ?>
                <hr style="border: 0.5px solid #e2e8f0; margin: 15px 0;">
                <a href="<?php echo BASE_URL; ?>student/Dashboard/announcements.php">Read All Announcements <i class="fas fa-arrow-right"></i></a>
            </div>
        <?php endif; ?>

        <div class="section">
            <h2>Available Courses to Join</h2>
            <div class="cards-row">
                <?php
                $all_sql = "SELECT * FROM courses WHERE status = 'published' AND id NOT IN (SELECT course_id FROM enrollments WHERE user_id = ?)";
                $all_stmt = $conn->prepare($all_sql);
                $all_stmt->bind_param("i", $student_id);
                $all_stmt->execute();
                $all_res = $all_stmt->get_result();
                
                if($all_res->num_rows > 0){
                    while($c = $all_res->fetch_assoc()){
                        echo '<div class="card">';
                        echo '<h4>'.htmlspecialchars($c['title']).'</h4>';
                        echo '<p style="color: #17a2b8; font-weight: bold; margin: 5px 0;">'.htmlspecialchars($c['subject']).'</p>';
                        echo '<p style="font-size: 0.9em; color: #666;">'.substr(htmlspecialchars($c['description']), 0, 100).'...</p>';
                        echo '<a href="'.BASE_URL.'student/Dashboard/enroll_process.php?course_id='.$c['id'].'" class="button">Join Course</a>';
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
                            <p style="color: #666; font-size: 0.8em;"><?php echo htmlspecialchars($course['subject']); ?></p>
                            <p>Progress: <?php echo $course['progress']; ?>%</p>
                            <a href="<?php echo BASE_URL; ?>student/Dashboard/courses.php?course_id=<?php echo $course['id']; ?>" class="button">
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
                <a href="<?php echo BASE_URL; ?>student/Dashboard/analytics.php" class="button">View Analytics</a>
            </div>
            <div class="card">
                <h3>Attendance</h3>
                <p>Current Attendance: 85%</p> 
                <a href="<?php echo BASE_URL; ?>student/Dashboard/attendance_details.php" class="button">View History</a>
            </div>
            <div class="card :">
                <h3>Certificates</h3>
                <p>View your completed certificates</p>
                <a href="<?php echo BASE_URL; ?>student/Dashboard/certificates.php" class="button">View Certificates</a>
            </div>
        </div>
    </div>
</div>
</body>
</html>