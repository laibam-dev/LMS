<?php
session_start();
require_once '../../config/db.php'; // Apne database connection ka sahi path check karlein

// Check karein ke student login hai ya nahi
if(!isset($_SESSION['student_id'])){
    header("Location: ../login.php");
    exit();
}

// Check karein ke URL mein course_id mili hai ya nahi
if(isset($_GET['course_id'])){
    $course_id = $_GET['course_id'];
    $student_id = $_SESSION['student_id']; // Aapki ID 3 yahan use hogi

    // 1. Pehle check karein ke student pehle se enroll toh nahi hai
    $check_sql = "SELECT * FROM enrollments WHERE user_id = ? AND course_id = ?";
    $stmt = $conn->prepare($check_sql);
    $stmt->bind_param("ii", $student_id, $course_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows == 0){
        // 2. Agar enroll nahi hai, toh naya record insert karein
        // Hum progress 0 se shuru karenge
        $insert_sql = "INSERT INTO enrollments (user_id, course_id, progress, status) VALUES (?, ?, 0, 'active')";
        $insert_stmt = $conn->prepare($insert_sql);
        $insert_stmt->bind_param("ii", $student_id, $course_id);
        
        if($insert_stmt->execute()){
            // Success: Wapas dashboard par bhej dein
            header("Location: index.php?msg=enrolled");
            exit();
        } else {
            echo "Error: Enrollment failed.";
        }
    } else {
        // Pehle se enrolled hai
        header("Location: index.php?msg=already_enrolled");
        exit();
    }
} else {
    // Agar course_id nahi mili
    header("Location: index.php");
    exit();
}
?>