<?php
session_start();
// Database aur BASE_URL ke liye
require_once '../../config/db.php';

// Agar student login nahi hai toh login page par bhej do
if(!isset($_SESSION['student_id'])){
    header("Location: " . BASE_URL . "student/login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $assessment_id = $_POST['assessment_id'];
    $student_id = $_SESSION['student_id'];
    $answers = $_POST['answer'] ?? []; // Student ke select kiye huye options
    
    $correct_count = 0;
    $total_questions = 0;

    // 1. Database se is quiz ke saare sahi jawab nikalna
    $query = $conn->prepare("SELECT id, correct_option FROM quiz_questions WHERE assessment_id = ?");
    $query->bind_param("i", $assessment_id);
    $query->execute();
    $results = $query->get_result();

    while ($row = $results->fetch_assoc()) {
        $total_questions++;
        $q_id = $row['id'];
        
        // Check karna ke student ka jawab sahi hai ya nahi
        if (isset($answers[$q_id]) && $answers[$q_id] == $row['correct_option']) {
            $correct_count++;
        }
    }

    $percentage = ($total_questions > 0) ? ($correct_count / $total_questions) * 100 : 0;

    $insert = $conn->prepare("INSERT INTO quiz_results (assessment_id, student_id, total_questions, correct_answers, score_percentage) VALUES (?, ?, ?, ?, ?)");
    $insert->bind_param("iiiid", $assessment_id, $student_id, $total_questions, $correct_count, $percentage);
    
    if ($insert->execute()) {
        // Design aur Links ko BASE_URL ke saath set kiya
        echo "<div style='text-align:center; margin-top:50px; font-family:sans-serif;'>
                <h1 style='color:#28a745;'>Quiz Submitted!</h1>
                <div style='font-size:20px; border:1px solid #ddd; display:inline-block; padding:20px; border-radius:10px; background:#f9f9f9;'>
                    <p>Total Questions: <b>$total_questions</b></p>
                    <p>Correct Answers: <b>$correct_count</b></p>
                    <p>Your Score: <b style='color:#007bff;'>$percentage%</b></p>
                </div>
                <br><br>
                <a href='" . BASE_URL . "student/Dashboard/dashboard.php' style='padding:10px 20px; background:#007bff; color:white; text-decoration:none; border-radius:5px;'>Back to Dashboard</a>
              </div>";
    } else {
        echo "Error saving result.";
    }
}
?>