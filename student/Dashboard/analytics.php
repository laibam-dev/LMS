<?php
session_start();

// BASE_URL ke liye db.php include
require_once '../../config/db.php'; 

if(!isset($_SESSION['student_id'])){
    // Dynamic login redirect
    header("Location: " . BASE_URL . "student/login.php");
    exit();
}

$student_id = $_SESSION['student_id'];

$sql = "
SELECT co.title, e.progress
FROM courses co
JOIN enrollments e ON co.id = e.course_id
WHERE e.user_id = ?
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();

$analytics = [];
$total_progress = 0;
$course_count = 0;

while($row = $result->fetch_assoc()){
    $analytics[$row['title']] = $row['progress'];
    $total_progress += $row['progress'];
    $course_count++;
}

$overall_progress = ($course_count > 0) ? round($total_progress / $course_count) : 0;
?>

<!DOCTYPE html>
<html>
<head>
    <title>Student Analytics</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>student/Styles/analytics.css">
    <style>
        .container { max-width: 900px; margin: 30px auto; padding: 20px; 
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .section { background: white; padding: 25px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); margin-bottom: 20px; }
        .progress-bar { width: 100%; background-color: #eee; border-radius: 20px; margin: 10px 0 25px 0; height: 25px; overflow: hidden; border: 1px solid #ddd; }
        .progress-fill { height: 100%; background: linear-gradient(90deg, #007bff, #007bff); color: white; text-align: center; line-height: 25px; font-weight: bold; font-size: 14px; transition: width 0.8s ease-in-out; }
        .overall-stats { background: #eef5ff; padding: 20px; border-radius: 10px; border-left: 6px solid #007bff; margin-bottom: 30px; }
        .overall-stats h3 { margin: 0; color: #0056b3; }
        .course-title { font-size: 18px; font-weight: 600; color: black; margin-bottom: 5px; }
        .button-back { text-decoration: none; padding: 12px 25px; background: #007bff; color: #fff; border-radius: 6px; display: inline-block; transition: background 0.3s; }
        .button-back:hover { background: #007bff; }
    </style>
</head>
<body>

<div class="container">
    <div class="section">
        <h1 style="border-bottom: 2px solid #f0f0f0; padding-bottom: 10px;">Learning Analytics</h1>
        
        <div class="overall-stats">
            <h3>Overall Completion: <?php echo $overall_progress; ?>%</h3>
            <div class="progress-bar">
                <div class="progress-fill" style="width:<?php echo $overall_progress; ?>%; background: #007bff;">
                    <?php echo $overall_progress; ?>%
                </div>
            </div>
        </div>

        <h3 style="margin-top: 40px; color: #555;">Detailed Course Progress</h3>
        <?php if($course_count > 0): ?>
            <?php foreach($analytics as $course => $progress){ ?>
                <div class="course-analytics">
                    <p class="course-title"><?php echo htmlspecialchars($course); ?></p>
                    <div class="progress-bar">
                        <div class="progress-fill" style="width:<?php echo $progress; ?>%">
                            <?php echo $progress; ?>%
                        </div>
                    </div>
                </div>
            <?php } ?>
        <?php else: ?>
            <p style="color: #888; font-style: italic;">No enrolled courses found for analytics.</p>
        <?php endif; ?>
    </div>

    <div style="text-align: center; margin-top: 20px;">
        <a href="<?php echo BASE_URL; ?>student/Dashboard/dashboard.php" class="button-back">Back to Dashboard</a>
    </div>
</div>

</body>
</html>