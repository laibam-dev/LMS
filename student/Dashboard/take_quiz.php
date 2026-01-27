<?php
session_start();
include '../../config/db.php';

if (!isset($_SESSION['student_id']) || !isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$assessment_id = $_GET['id'];
$student_id = $_SESSION['student_id'];


$quiz_query = $conn->prepare("SELECT title FROM assessments WHERE id = ? AND type = 'quiz'");
$quiz_query->bind_param("i", $assessment_id);
$quiz_query->execute();
$quiz = $quiz_query->get_result()->fetch_assoc();

if (!$quiz) {
    die("Quiz not found!");
}

// Sawalaat fetch karna
$questions_query = $conn->prepare("SELECT * FROM quiz_questions WHERE assessment_id = ?");
$questions_query->bind_param("i", $assessment_id);
$questions_query->execute();
$questions = $questions_query->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Take Quiz - <?= htmlspecialchars($quiz['title']) ?></title>
    <link rel="stylesheet" href="../Styles/courses.css">
    <style>
        .quiz-container { max-width: 800px; margin: 20px auto; padding: 20px; background: #fff; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .question-box { margin-bottom: 25px; padding: 15px; border-bottom: 1px solid #eee; }
        .options label { display: block; margin: 10px 0; cursor: pointer; padding: 10px; border: 1px solid #ddd; border-radius: 5px; }
        .options input { margin-right: 10px; }
        .submit-btn { background: #007bff; color: white; border: none; padding: 12px 25px; border-radius: 5px; cursor: pointer; font-size: 16px; }
    </style>
</head>
<body>
    <?php include '../navbar.php'; ?>

    <div class="quiz-container">
        <h1>Quiz: <?= htmlspecialchars($quiz['title']) ?></h1>
        <hr>
        
        <form action="submit_quiz.php" method="POST">
            <input type="hidden" name="assessment_id" value="<?= $assessment_id ?>">
            
            <?php if ($questions->num_rows > 0): ?>
                <?php $count = 1; while($q = $questions->fetch_assoc()): ?>
                    <div class="question-box">
                        <p><strong>Q<?= $count ?>:</strong> <?= htmlspecialchars($q['question']) ?></p>
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
                <p>No questions added to this quiz yet.</p>
                <a href="index.php" class="button">Go Back</a>
            <?php endif; ?>
        </form>
    </div>
</body>
</html>