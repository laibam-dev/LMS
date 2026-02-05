<?php
session_start();
include "../config/db.php";

/*
  Student ko sirf published courses dikhane hain
*/
$sql = "SELECT id, title, description 
        FROM courses 
        WHERE status = 'published'";

$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Available Courses</title>
    <link href="<?php echo BASE_URL; ?>assets/css/instructor.css" rel="stylesheet">
</head>
<body style="padding:20px">

<h2>Available Courses</h2>

<?php if (mysqli_num_rows($result) > 0) { ?>
    <?php while ($course = mysqli_fetch_assoc($result)) { ?>
        <div style="border:1px solid #ddd; padding:15px; margin-bottom:10px; border-radius:6px;">
            <h3><?= htmlspecialchars($course['title']) ?></h3>
            <p><?= nl2br(htmlspecialchars($course['description'])) ?></p>

            <a href="<?php echo BASE_URL; ?>student/course_detail.php?id=<?= $course['id'] ?>">
                View Course
            </a>
        </div>
    <?php } ?>
<?php } else { ?>
    <p>No courses available right now.</p>
<?php } ?>

</body>
</html>