<?php
session_start();

include '../../config/db.php'; 
if (!isset($_SESSION['student_id'])) {
    header("Location: ../login.php");
    exit();
}
$student_id = $_SESSION['student_id'];

if (!isset($_GET['course_id'])) {
    die("Course not found");
}
$course_id = $_GET['course_id'];


$check = $conn->prepare("
    SELECT c.id, c.title, c.description, se.progress
    FROM student_enrollment se
    JOIN course c ON se.course_id = c.id
    WHERE se.student_id = ? AND se.course_id = ?
");
$check->bind_param("ii", $student_id, $course_id);
$check->execute();
$result = $check->get_result();

if ($result->num_rows == 0) {
    die("You are not enrolled in this course");
}
$course = $result->fetch_assoc();


$lesson_stmt = $conn->prepare("SELECT * FROM lessons WHERE course_id = ?");
$lesson_stmt->bind_param("i", $course_id);
$lesson_stmt->execute();
$lesson_result = $lesson_stmt->get_result();
$lessons = [];
while($row = $lesson_result->fetch_assoc()){
    $lessons[] = $row;
}


$completed_stmt = $conn->prepare("SELECT lesson_id FROM lesson_completion WHERE student_id = ?");
$completed_stmt->bind_param("i", $_SESSION['student_id']);
$completed_stmt->execute();
$completed_lessons_result = $completed_stmt->get_result();
$completed_lessons = [];
while($row = $completed_lessons_result->fetch_assoc()){
    $completed_lessons[] = $row['lesson_id'];
}


$assess_stmt = $conn->prepare("SELECT * FROM assessments WHERE course_id = ?");
$assess_stmt->bind_param("i", $course_id);
$assess_stmt->execute();
$assess_result = $assess_stmt->get_result();
$assessments = [];
while($row = $assess_result->fetch_assoc()){
    $assessments[] = $row;
}

?>

<!DOCTYPE html>
<html>
<head>
    <title><?= $course['title']; ?></title>
    <link rel="stylesheet" href="../Styles/courses.css">
</head>

<body>

<?php include '../navbar.php'; ?>
<div class="container">

    
    <div class="section">
        <h2><?= $course['title']; ?></h2>
        <p><strong>Progress:</strong> <?= $course['progress']; ?>%</p>
        <p><?= $course['description']; ?></p>
    </div>



<div class="section">
    <h2>Videos / Lessons</h2>
    <?php if(!empty($lessons)): ?>
    <ul>
        <?php foreach($lessons as $lesson): ?>
            <li><?= htmlspecialchars($lesson['title']); ?></li>
          <li><?= htmlspecialchars($lesson['video_url']); ?></li>
           <?php if(in_array($lesson['id'], $completed_lessons)): ?>
    <span>Completed ✅</span>
<?php else: ?>
    <form method="POST" action="complete_lesson.php">
        <input type="hidden" name="lesson_id" value="<?= $lesson['id'] ?>">
        <input type="hidden" name="course_id" value="<?= $course_id ?>">
        <button type="submit">Mark as Complete</button>
    </form>
<?php endif; ?>
        <?php endforeach; ?>
    </ul>
    <?php else: ?>
        <p>No lessons added yet.</p>
    <?php endif; ?>
</div>


<div class="section">
    <h2>Assessments</h2>
    <?php if(!empty($assessments)): ?>
        <ul>
        <?php foreach($assessments as $assess): ?>
            <li><?= ucfirst($assess['type']) . ": " . htmlspecialchars($assess['title']); ?></li>
        <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>No assessments added yet.</p>
    <?php endif; ?>
</div>

    
    <div class="section">
        <a href="index.php" class="button">Back to Dashboard</a>
    </div>

</div>

</body>
</html>
