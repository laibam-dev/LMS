<?php
session_start();
// 1. Database aur BASE_URL ke liye
require_once '../../config/db.php';

// 2. Check karein ke student logged in hai aur ID mil rahi hai
if (!isset($_SESSION['student_id']) || !isset($_GET['id'])) {
    header("Location: " . BASE_URL . "student/Dashboard/dashboard.php");
    exit();
}

$quiz_id = $_GET['id']; 
$student_id = $_SESSION['student_id'];

// --- UPDATE 1: Table ka naam 'quizzes' ---
$quiz_query = $conn->prepare("SELECT title FROM quizzes WHERE id = ?");
$quiz_query->bind_param("i", $quiz_id);
$quiz_query->execute();
$quiz = $quiz_query->get_result()->fetch_assoc();

if (!$quiz) {
    die("Quiz not found in the new quizzes table!");
}

// --- UPDATE 2 (FIXED): Column ka naam 'assessment_id' ---
$questions_query = $conn->prepare("SELECT * FROM quiz_questions WHERE assessment_id = ?");
$questions_query->bind_param("i", $quiz_id);
$questions_query->execute();
$questions = $questions_query->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Take Quiz - <?= htmlspecialchars($quiz['title']) ?></title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>student/Styles/courses.css">
    <style>
        .quiz-container { max-width: 800px; margin: 30px auto; padding: 30px; background: #fff; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        .question-box { margin-bottom: 30px; padding: 20px; border: 1px solid #f0f0f0; border-radius: 8px; }
        .question-box p { font-size: 1.1rem; color: #333; margin-bottom: 15px; }
        .options label { display: block; margin: 12px 0; cursor: pointer; padding: 12px; border: 1px solid #eee; border-radius: 6px; transition: 0.3s; }
        .options label:hover { background-color: #f8f9fa; border-color: #007bff; }
        .options input { margin-right: 12px; transform: scale(1.2); }
        .submit-btn { background: #007bff; color: white; border: none; padding: 15px 30px; border-radius: 6px; cursor: pointer; font-size: 18px; width: 100%; font-weight: bold; }
        .submit-btn:hover { background: #0056b3; }
    </style>
</head>
<body>
    <?php 
    // Navbar include karne ka sahi rasta
    include $_SERVER['DOCUMENT_ROOT'] . '/LMS/navbar.php'; 
    ?>

    <div class="quiz-container">
        <h1 style="color: #1b4f91;">Quiz: <?= htmlspecialchars($quiz['title']) ?></h1>
        <p style="color: #666;">Please answer all questions carefully.</p>
        <hr style="margin: 20px 0;">
        
        <form action="submit_quiz.php" method="POST">
            <input type="hidden" name="assessment_id" value="<?= $quiz_id ?>">
            
            <?php if ($questions->num_rows > 0): ?>
                <?php $count = 1; while($q = $questions->fetch_assoc()): ?>
                    <div class="question-box">
                        <p><strong>Question <?= $count ?>:</strong> <?= htmlspecialchars($q['question']) ?></p>
                        <div class="options">
                            <label><input type="radio" name="answer[<?= $q['id'] ?>]" value="A" required> <?= htmlspecialchars($q['option_a']) ?></label>
                            <label><input type="radio" name="answer[<?= $q['id'] ?>]" value="B"> <?= htmlspecialchars($q['option_b']) ?></label>
                            <label><input type="radio" name="answer[<?= $q['id'] ?>]" value="C"> <?= htmlspecialchars($q['option_c']) ?></label>
                            <label><input type="radio" name="answer[<?= $q['id'] ?>]" value="D"> <?= htmlspecialchars($q['option_d']) ?></label>
                        </div>
                    </div>
                <?php $count++; endwhile; ?>
                <button type="submit" class="submit-btn">Finish & Submit Quiz</button>
            <?php else: ?>
                <div style="text-align: center; padding: 20px;">
                    <p>No questions have been added to this quiz yet.</p>
                    <a href="<?php echo BASE_URL; ?>student/Dashboard/dashboard.php" class="button" style="text-decoration: none; background: #6c757d; color: white; padding: 10px 20px; border-radius: 5px;">Go Back</a>
                </div>
            <?php endif; ?>
        </form>
    </div>
</body>
</html>