<?php
session_start();

// BASE_URL aur DB connection ke liye
require_once '../../config/db.php';

if(!isset($_SESSION['student_id'])){
    // Dynamic login redirect
    header("Location: " . BASE_URL . "student/login.php");
    exit();
}

$student_id = $_SESSION['student_id'];

// SQL query for certificates
$sql = "
SELECT 
    c.title AS course_title,
    cert.status,
    cert.course_id
FROM certificates cert
JOIN course c ON cert.course_id = c.id
WHERE cert.student_id = ?
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();

$certificates = [];
while($row = $result->fetch_assoc()){
    $certificates[] = $row;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Student Certificates</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>student/Styles/certificates.css">
</head>
<body>
    <div class="container">
    <div class="section">
        <h2>My Certificates</h2>
        <table>
            <tr>
                <th>Course</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
            <?php foreach($certificates as $cert){ ?>
                <tr>
                    <td><?php echo htmlspecialchars($cert['course_title']); ?></td>
                    <td><?php echo $cert['status']; ?></td>
                    <td>
                        <?php if($cert['status'] == "Generated"){ ?>
                            <a href="<?php echo BASE_URL; ?>certificates/certificate_<?php echo $student_id; ?>_<?php echo $cert['course_id']; ?>.pdf" target="_blank" class="button">View / Download</a>
                        <?php } else { ?>
                            <span class="button disabled">Not Ready</span>
                        <?php } ?>
                    </td>
                </tr>
            <?php } ?>
        </table>
    </div>
    <div class="section">
        <a href="<?php echo BASE_URL; ?>student/Dashboard/dashboard.php" class="button" style="text-decoration:none; padding:10px 20px; background:#007bff; color:white; border-radius:5px;">Back to Dashboard</a>
    </div>

</div>

</body>
</html>