<?php
session_start();

// BASE_URL ke liye db.php include karna zaroori hai
require_once '../../config/db.php'; 

if(!isset($_SESSION['student_id'])){
    // Dynamic login redirect
    header("Location: " . BASE_URL . "student/login.php");
    exit();
}
// Achievements array mein badges ke paths ko BASE_URL ke saath dynamic kiya
$achievements = [
    ["title" => "Completed Web Development", "badge" => BASE_URL . "assets/badge1.png", "date" => "2025-12-01"],
    ["title" => "PHP & MySQL Expert", "badge" => BASE_URL . "assets/badge2.png", "date" => "2025-12-10"],
    ["title" => "Python Programming Master", "badge" => BASE_URL . "assets/badge3.png", "date" => "2025-12-15"]
];
?>
<!DOCTYPE html>
<html>
<head>
    <title>Student Achievements</title>
    <style>
        body{ font-family: Arial, sans-serif; background:#f2f2f2; margin:0; padding:0; }
        .container{ width:90%; margin:20px auto; }
        .section{ background:#fff; padding:20px; margin-bottom:20px; border-radius:8px; box-shadow:0 2px 5px rgba(0,0,0,0.1); }
        h2{ margin-top:0; }
        .achievement-card{
            display:inline-block;
            width:200px;
            margin:10px;
            padding:15px;
            background:#e8f0fe;
            border-radius:8px;
            text-align:center;
        }
        .achievement-card img{
            width:80px;
            height:80px;
        }
        .button{ padding:10px 20px; background:#007bff; color:#fff; text-decoration:none; border-radius:5px; }
    </style>
</head>
<body>

<div class="container">

    <div class="section">
        <h2>My Achievements</h2>
        <?php foreach($achievements as $ach){ ?>
            <div class="achievement-card">
                <img src="<?php echo $ach['badge']; ?>" alt="Badge">
                <h4><?php echo $ach['title']; ?></h4>
                <p>Date: <?php echo $ach['date']; ?></p>
            </div>
        <?php } ?>
    </div>

    <div class="section">
        <a href="<?php echo BASE_URL; ?>student/Dashboard/dashboard.php" class="button">Back to Dashboard</a>
    </div>

</div>

</body>
</html>