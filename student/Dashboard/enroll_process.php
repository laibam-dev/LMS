<?php
session_start();
// db.php include kar rahe hain taake BASE_URL aur $conn mil jaye
require_once '../../config/db.php'; 

// Agar student login nahi hai toh login page par bhej do
if(!isset($_SESSION['student_id'])){
    header("Location: " . BASE_URL . "student/login.php");
    exit();
}

if(isset($_GET['course_id'])){
    $course_id = $_GET['course_id'];
    $student_id = $_SESSION['student_id']; 

    $check_sql = "SELECT * FROM enrollments WHERE user_id = ? AND course_id = ?";
    $stmt = $conn->prepare($check_sql);
    $stmt->bind_param("ii", $student_id, $course_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows == 0){
        // Naya enrollment insert karna
        $insert_sql = "INSERT INTO enrollments (user_id, course_id, progress, status) VALUES (?, ?, 0, 'active')";
        $insert_stmt = $conn->prepare($insert_sql);
        $insert_stmt->bind_param("ii", $student_id, $course_id);
        
        if($insert_stmt->execute()){
            // Success redirect using BASE_URL
            header("Location: " . BASE_URL . "student/Dashboard/dashboard.php?msg=enrolled");
            exit();
        } else {
            echo "Error: Enrollment failed.";
        }
    } else {
        // Already enrolled redirect
        header("Location: " . BASE_URL . "student/Dashboard/dashboard.php?msg=already_enrolled");
        exit();
    }
} else {
    // Agar course_id missing hai toh dashboard par wapas
    header("Location: " . BASE_URL . "student/Dashboard/dashboard.php");
    exit();
}
?>