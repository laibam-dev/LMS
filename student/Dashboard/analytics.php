<?php
session_start();
if(!isset($_SESSION['student_id'])){
    header("Location: ../login.php");
    exit();
}

include '../navbar.php';
require_once '../../config/db.php'; 

$student_id = $_SESSION['student_id'];

$sql = "
SELECT c.title, se.progress
FROM course c
JOIN student_enrollment se ON c.id = se.course_id
WHERE se.student_id = ?
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
        <link rel="stylesheet" href="../Styles/analytics.css">

</head>
<body>

<div class="container">

    <div class="section">
        <h2>Student Analytics</h2>
        <p>Overall progress in enrolled courses:</p>

        <?php foreach($analytics as $course => $progress){ ?>
            <p><strong><?php echo $course; ?>:</strong> <?php echo $progress; ?>%</p>
            <div class="progress-bar">
                <div class="progress-fill" style="width:<?php echo $progress; ?>%">
                    <?php echo $progress; ?>%
                </div>
            </div>
        <?php } ?>

    </div>

    <div class="section">
        <a href="index.php" class="button" style="text-decoration:none; padding:10px 20px; background:#007bff; color:#fff; border-radius:5px;">Back to Dashboard</a>
    </div>

</div>

</body>
</html>
