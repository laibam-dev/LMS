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

// Course enrollment check
$check = $conn->prepare("
    SELECT c.id, c.title, c.description, e.progress
    FROM enrollments e
    JOIN courses c ON e.course_id = c.id
    WHERE e.user_id = ? AND e.course_id = ?
");
$check->bind_param("ii", $student_id, $course_id);
$check->execute();
$result = $check->get_result();

if ($result->num_rows == 0) {
    die("You are not enrolled in this course");
}
$course = $result->fetch_assoc();

// Lessons fetch
$lesson_stmt = $conn->prepare("SELECT * FROM lessons WHERE course_id = ?");
$lesson_stmt->bind_param("i", $course_id);
$lesson_stmt->execute();
$lesson_result = $lesson_stmt->get_result();
$lessons = [];
while($row = $lesson_result->fetch_assoc()){
    $lessons[] = $row;
}

// --- UPDATE 1: Sirf Assignments fetch karna (assessments table se) ---
$assign_stmt = $conn->prepare("SELECT * FROM assessments WHERE course_id = ? AND type = 'assignment'");
$assign_stmt->bind_param("i", $course_id);
$assign_stmt->execute();
$assignments = $assign_stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// --- UPDATE 2: Quizzes naye 'quizzes' table se fetch karna ---
$quiz_stmt = $conn->prepare("SELECT * FROM quizzes WHERE course_id = ?");
$quiz_stmt->bind_param("i", $course_id);
$quiz_stmt->execute();
$quizzes = $quiz_stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title><?= htmlspecialchars($course['title']); ?></title>
    <link rel="stylesheet" href="../Styles/courses.css">
    <style>
        .video-container, .assess-box { margin-bottom: 20px; padding: 15px; border: 1px solid #ddd; border-radius: 8px; background: #f9f9f9; }
        iframe { width: 100%; height: 350px; border-radius: 5px; }
        .btn-small { padding: 5px 10px; background: #28a745; color: white; text-decoration: none; border-radius: 4px; font-size: 14px; }
        .quiz-btn { background: #007bff; }
        .container { width: 80%; margin: auto; padding: 20px; }
    </style>
</head>
<body>

 


<div class="container">
    <div class="section">
        <h1><?= htmlspecialchars($course['title']); ?></h1>
        <p><strong>Your Progress:</strong> <?= $course['progress']; ?>%</p>
        <p><?= htmlspecialchars($course['description']); ?></p>
    </div>

    <div class="section">
        <h2>🎥 Video Lectures</h2>
        <?php if(!empty($lessons)): ?>
            <?php foreach($lessons as $lesson): ?>
                <div class="video-container">
                    <h3><?= htmlspecialchars($lesson['title']); ?></h3>
                    <?php if(!empty($lesson['video_url'])): ?>
                        <?php 
                            $video_id = explode("v=", $lesson['video_url'])[1] ?? '';
                            if($video_id) {
                                echo '<iframe src="https://www.youtube.com/embed/'.$video_id.'" frameborder="0" allowfullscreen></iframe>';
                            }
                        ?>
                    <?php endif; ?>
                    <br><br>
                    <form method="POST" action="complete_lesson.php">
                        <input type="hidden" name="lesson_id" value="<?= $lesson['id'] ?>">
                        <button type="submit" class="button">Mark as Completed</button>
                    </form>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No lessons yet.</p>
        <?php endif; ?>
    </div>

    <hr>

    <div class="section">
        <h2>📝 Assignments</h2>
        <?php if(!empty($assignments)): ?>
            <?php foreach($assignments as $assign): ?>
                <div class="assess-box">
                    <h4><?= htmlspecialchars($assign['title']); ?></h4>
                    <p>Deadline: <?= $assign['due_date'] ?? 'No deadline'; ?></p>
                    <a href="submit_assignment.php?id=<?= $assign['id'] ?>" class="btn-small">Upload Assignment</a>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No assignments posted by instructor.</p>
        <?php endif; ?>
    </div>

    <div class="section">
        <h2>❓ Quizzes</h2>
        <?php if(!empty($quizzes)): ?>
            <?php foreach($quizzes as $quiz): ?>
                <div class="assess-box">
                    <h4><?= htmlspecialchars($quiz['title']); ?></h4>
                    <p>Total Marks: <?= $quiz['total_marks']; ?></p>
                    <a href="take_quiz.php?id=<?= $quiz['id'] ?>" class="btn-small quiz-btn">Start Quiz</a>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No quizzes available yet.</p>
        <?php endif; ?>
    </div>

    <div class="section">
        <a href="dashboard.php" class="button" style="background: #666; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;">Back to Dashboard</a>
    </div>
</div>

</body>
</html>