<?php
session_start();

// BASE_URL aur database ke liye db.php include
require_once '../../config/db.php'; 

if (!isset($_SESSION['student_id'])) {
    header("Location: " . BASE_URL . "student/login.php");
    exit();
}
$student_id = $_SESSION['student_id'];

if (!isset($_GET['course_id'])) {
    die("Course not found");
}
$course_id = $_GET['course_id'];

// Course enrollment check aur progress access
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

// Lessons fetch (Title, Content, PDF, aur Video)
$lesson_stmt = $conn->prepare("SELECT * FROM lessons WHERE course_id = ?");
$lesson_stmt->bind_param("i", $course_id);
$lesson_stmt->execute();
$lesson_result = $lesson_stmt->get_result();
$lessons = [];
while($row = $lesson_result->fetch_assoc()){
    $lessons[] = $row;
}

// Assignments fetch
$assign_stmt = $conn->prepare("SELECT * FROM assessments WHERE course_id = ? AND type = 'assignment'");
$assign_stmt->bind_param("i", $course_id);
$assign_stmt->execute();
$assignments = $assign_stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Quizzes fetch
$quiz_stmt = $conn->prepare("SELECT * FROM quizzes WHERE course_id = ?");
$quiz_stmt->bind_param("i", $course_id);
$quiz_stmt->execute();
$quizzes = $quiz_stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($course['title']); ?></title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>student/Styles/courses.css">
    <style>
        .container { width: 85%; margin: auto; padding: 20px; font-family: Arial, sans-serif; }
        .section { margin-bottom: 40px; }
        .video-container, .assess-box { 
            margin-bottom: 20px; 
            padding: 20px; 
            border: 1px solid #ddd; 
            border-radius: 12px; 
            background: #ffffff; 
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        }
        iframe { width: 100%; height: 400px; border-radius: 8px; margin: 15px 0; }
        .pdf-btn { 
            background: #dc3545; 
            color: white; 
            padding: 10px 15px; 
            text-decoration: none; 
            border-radius: 6px; 
            display: inline-block;
            font-size: 14px;
            font-weight: bold;
            transition: 0.3s;
        }
        .pdf-btn:hover { background: #c82333; }
        .lesson-text { 
            color: #333; 
            line-height: 1.7; 
            background: #f1f8f9; 
            padding: 15px; 
            border-radius: 8px; 
            border-left: 5px solid #17a2b8; 
            margin: 15px 0; 
        }
        .btn-small { padding: 8px 12px; background: #28a745; color: white; text-decoration: none; border-radius: 5px; font-size: 14px; }
        .quiz-btn { background: #007bff; }
        .button { padding: 12px 20px; background: #17a2b8; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: bold; }
        .back-btn { background: #6c757d; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block; margin-top: 20px; }
    </style>
</head>
<body>

<div class="container">
    <div class="section">
        <h1 style="color: #2c3e50;"><?= htmlspecialchars($course['title']); ?></h1>
        <div style="background: #e9ecef; border-radius: 10px; padding: 5px 15px; display: inline-block;">
            <strong>Progress:</strong> <?= $course['progress']; ?>%
        </div>
        <p style="margin-top: 15px; color: #555;"><?= htmlspecialchars($course['description']); ?></p>
    </div>

    <hr>

    <div class="section">
        <h2>🎥 Video Lectures & Study Material</h2>
        <?php if(!empty($lessons)): ?>
            <?php foreach($lessons as $lesson): ?>
                <div class="video-container">
                    <h3 style="color: #17a2b8;"><?= htmlspecialchars($lesson['title']); ?></h3>
                    
                    <?php if(!empty($lesson['content'])): ?>
                        <div class="lesson-text">
                            <strong>About this lesson:</strong><br>
                            <?= nl2br(htmlspecialchars($lesson['content'])); ?>
                        </div>
                    <?php endif; ?>

                    <?php if(!empty($lesson['video_url'])): ?>
                        <?php 
                            $video_id = "";
                            if (strpos($lesson['video_url'], 'v=') !== false) {
                                $video_id = explode("v=", $lesson['video_url'])[1];
                                if(strpos($video_id, '&') !== false) {
                                    $video_id = explode("&", $video_id)[0];
                                }
                            }
                            if($video_id) {
                                echo '<iframe src="https://www.youtube.com/embed/'.$video_id.'" frameborder="0" allowfullscreen></iframe>';
                            }
                        ?>
                    <?php endif; ?>

                    <br>

                    <?php if(!empty($lesson['pdf_file'])): ?>
                        <div style="margin: 10px 0;">
                            <a href="<?php echo BASE_URL; ?>uploads/pdfs/<?= $lesson['pdf_file']; ?>" class="pdf-btn" target="_blank">
                                📄 Open PDF Notes
                            </a>
                        </div>
                    <?php endif; ?>

                    <div style="margin-top: 20px;">
                        <form method="POST" action="complete_lesson.php">
                            <input type="hidden" name="lesson_id" value="<?= $lesson['id'] ?>">
                            <input type="hidden" name="course_id" value="<?= $course_id ?>">
                            <button type="submit" class="button">✔ Mark Lesson as Completed</button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No study material uploaded yet.</p>
        <?php endif; ?>
    </div>

    <hr>
    <div class="section">
        <h2>📝 Assignments</h2>
        <?php if(!empty($assignments)): ?>
            <?php foreach($assignments as $assign): ?>
                <div class="assess-box">
                    <h4><?= htmlspecialchars($assign['title']); ?></h4>
                    
                    <?php if(!empty($assign['instructions'])): ?>
                        <div style="background: #f8f9fa; padding: 10px; border-radius: 5px; margin: 10px 0; border: 1px dashed #ccc;">
                            <strong>Instructions:</strong><br>
                            <?= nl2br(htmlspecialchars($assign['instructions'])); ?>
                        </div>
                    <?php else: ?>
                        <p style="color: red; font-size: 0.8em;">(No instructions found in database for this assignment)</p>
                    <?php endif; ?>

                    <p><strong>Deadline:</strong> <?= $assign['due_date'] ?? 'No deadline'; ?></p>
                    <a href="submit_assignment.php?id=<?= $assign['id'] ?>" class="btn-small">Upload Your Work</a>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No assignments assigned yet.</p>
        <?php endif; ?>
    </div>

    <div class="section">
        <h2>❓ Quizzes</h2>
        <?php if(!empty($quizzes)): ?>
            <?php foreach($quizzes as $quiz): ?>
                <div class="assess-box">
                    <h4><?= htmlspecialchars($quiz['title']); ?></h4>
                    <p><strong>Total Marks:</strong> <?= $quiz['total_marks']; ?></p>
                    <a href="take_quiz.php?id=<?= $quiz['id'] ?>" class="btn-small quiz-btn">Start Quiz</a>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <div class="section" style="text-align: center;">
        <a href="<?php echo BASE_URL; ?>student/Dashboard/dashboard.php" class="back-btn">← Back to Dashboard</a>
    </div>
</div>

</body>
</html>