<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}
include '../config/db.php';
include 'admin_navbar.php';
$quiz_id = isset($_GET['quiz_id']) ? intval($_GET['quiz_id']) : 0;
if ($quiz_id <= 0) {
    die('<div class="alert alert-danger">Invalid Quiz ID.</div>');
}
// Fetch quiz title
$quiz_title = '';
$q = mysqli_query($conn, "SELECT quiz_title FROM quizzes WHERE quiz_id = $quiz_id");
if ($row = mysqli_fetch_assoc($q)) {
    $quiz_title = $row['quiz_title'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Quiz Submissions | Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f4f7f6; font-family: 'Poppins', sans-serif; }
        .table thead { background-color: #003366; color: #FFD700; }
        .btn-polymath { background-color: #003366; color: #FFD700; border: none; }
        .btn-polymath:hover { background-color: #FFD700; color: #003366; }
        .sidebar { background: #003366; color: #FFD700; min-height: 100vh; }
    </style>
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-2 sidebar p-4">
            <?php include 'sidebar.php'; ?>
        </div>
        <div class="col-md-10 p-4">
            <h2 class="fw-bold mb-4" style="color:#003366;">Quiz Results: <?php echo htmlspecialchars($quiz_title); ?></h2>
            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>Student Name</th>
                        <th>Score</th>
                        <th>Total Marks</th>
                        <th>Date of Submission</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $sql = "SELECT u.full_name, r.score, r.total_marks, r.submitted_at FROM quiz_results r JOIN users u ON r.student_id = u.user_id WHERE r.quiz_id = $quiz_id ORDER BY r.submitted_at DESC";
                $result = mysqli_query($conn, $sql);
                if (mysqli_num_rows($result) > 0) {
                    while($row = mysqli_fetch_assoc($result)) {
                        echo '<tr>';
                        echo '<td>'.$row['full_name'].'</td>';
                        echo '<td>'.$row['score'].'</td>';
                        echo '<td>'.$row['total_marks'].'</td>';
                        echo '<td>'.$row['submitted_at'].'</td>';
                        echo '</tr>';
                    }
                } else {
                    echo '<tr><td colspan="4" class="text-center">No submissions found.</td></tr>';
                }
                ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
