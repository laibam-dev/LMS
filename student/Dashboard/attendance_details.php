<?php
session_start();

// BASE_URL ke liye db.php include karna zaroori hai
require_once '../../config/db.php'; 

if(!isset($_SESSION['student_id'])){
    // Dynamic login redirect
    header("Location: " . BASE_URL . "student/login.php");
    exit();
}

// Dummy data
$attendance_data = [
    "English" => [
        ['date' => '2023-10-25', 'status' => 'Present'],
        ['date' => '2023-10-22', 'status' => 'Absent'],
    ],
    "Programming Fundamental" => [
        ['date' => '2023-10-24', 'status' => 'Present'],
    ],
    "Math" => [
        ['date' => '2023-10-23', 'status' => 'Present'],
    ]
];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Attendance History</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>student/Styles/attendance_details.css">
    
</head>
<body>

<div class="main-wrapper">
    <div class="welcome-card">
        <h2>Attendance History</h2>
        <p>View your presence record for all enrolled courses</p>
        <a href="<?php echo BASE_URL; ?>student/Dashboard/dashboard.php" class="back-link">← Back to Dashboard</a>
    </div>

    <div class="attendance-content">
        <?php foreach($attendance_data as $subject => $records): ?>
        <div class="subject-section">
            <h3 class="subject-title"><?php echo $subject; ?></h3>
            <div class="white-card">
                <table class="attendance-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($records as $row): ?>
                        <tr>
                            <td><?php echo date('d M, Y', strtotime($row['date'])); ?></td>
                            <td>
                                <span class="badge <?php echo strtolower($row['status']); ?>">
                                    <?php echo $row['status']; ?>
                                </span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

</body>
</html>