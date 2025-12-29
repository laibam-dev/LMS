<?php
session_start();
include "../config/db.php";
include "header.php";

$quiz_id = (int)$_GET['quiz_id'];

$quiz = mysqli_fetch_assoc(mysqli_query($conn,"
  SELECT * FROM quizzes WHERE id=$quiz_id
"));
?>

<div class="container mt-4">

<h3><?= htmlspecialchars($quiz['title']) ?></h3>

<form method="POST" action="add_question_action.php">

<input type="hidden" name="quiz_id" value="<?= $quiz_id ?>">

<div class="mb-3">
  <label>Question</label>
  <input type="text" name="question" class="form-control" required>
</div>

<?php for($i=1;$i<=4;$i++): ?>
<div class="mb-2">
  <input type="radio" name="correct" value="<?= $i ?>" required>
  <input type="text" name="answers[]" class="form-control d-inline w-75" required>
</div>
<?php endfor; ?>

<button class="btn btn-primary mt-3">Add Question</button>
</form>

</div>

<?php include "footer.php"; ?>
