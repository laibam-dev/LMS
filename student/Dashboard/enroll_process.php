<?php
session_start();
require_once '../../config/db.php'; 


if(!isset($_SESSION['student_id'])){
    header("Location: ../login.php");
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
        
    
        $insert_sql = "INSERT INTO enrollments (user_id, course_id, progress, status) VALUES (?, ?, 0, 'active')";
        $insert_stmt = $conn->prepare($insert_sql);
        $insert_stmt->bind_param("ii", $student_id, $course_id);
        
        if($insert_stmt->execute()){
            
            header("Location: index.php?msg=enrolled");
            exit();
        } else {
            echo "Error: Enrollment failed.";
        }
    } else {
        
        header("Location: index.php?msg=already_enrolled");
        exit();
    }
} else {
    header("Location: index.php");
    exit();
}
?>