<?php
session_start();
// Database aur BASE_URL ke liye
require_once '../../config/db.php';

if (!isset($_SESSION['student_id']) || !isset($_GET['id'])) {
    // Dynamic redirect
    header("Location: " . BASE_URL . "student/Dashboard/dashboard.php");
    exit();
}

$assessment_id = $_GET['id'];
$student_id = $_SESSION['student_id'];

$stmt = $conn->prepare("SELECT title FROM assessments WHERE id = ? AND type = 'assignment'");
$stmt->bind_param("i", $assessment_id);
$stmt->execute();
$assign = $stmt->get_result()->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['assignment_file'])) {
    
    // Hosting par file upload ke liye physical path zaroori hai
    $target_dir = $_SERVER['DOCUMENT_ROOT'] . "/LMS/uploads/assignments/";
    
    // Agar folder nahi hai toh bana do
    if (!is_dir($target_dir)) { 
        mkdir($target_dir, 0777, true); 
    }

    $file_name = time() . "_" . basename($_FILES["assignment_file"]["name"]);
    $target_file = $target_dir . $file_name;

    if (move_uploaded_file($_FILES["assignment_file"]["tmp_name"], $target_file)) {
        $insert = $conn->prepare("INSERT INTO submissions (assessment_id, student_id, file_path) VALUES (?, ?, ?)");
        $insert->bind_param("iis", $assessment_id, $student_id, $file_name);
        
        if ($insert->execute()) {
            echo "<script>alert('Assignment Uploaded Successfully!'); window.location.href='" . BASE_URL . "student/Dashboard/dashboard.php';</script>";
        }
    } else {
        echo "Sorry, there was an error uploading your file.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Submit Assignment</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>student/Styles/submit_assignment.css">
</head>
<body>
    <div class="upload-container">
        <h2 class="upload-title">Upload: <?= htmlspecialchars($assign['title'] ?? 'Assignment') ?></h2>
        <form action="" method="POST" enctype="multipart/form-data" class="upload-form">
            <p class="upload-info">Select your assignment file (PDF, Docx, or Image):</p>
            <input type="file" name="assignment_file" class="file-input" required>
            <div class="button-group">
                <button type="submit" class="btn-submit">Submit Assignment</button>
                <a href="<?php echo BASE_URL; ?>student/Dashboard/dashboard.php" class="btn-cancel">Cancel</a>
            </div>
        </form>
    </div>
</body>
</html>