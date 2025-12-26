<?php
include "header.php";
include "../config/db.php";

$instructor_id = $_SESSION['user_id'];

$sql = "SELECT * FROM courses WHERE instructor_id = $instructor_id";
$result = mysqli_query($conn, $sql);
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>My Courses</h3>

        <a href="create_course.php" class="btn btn-primary">
            + Create New Course
        </a>
    </div>

<?php if (mysqli_num_rows($result) > 0) { ?>
    <div class="list-group">

    <?php while ($course = mysqli_fetch_assoc($result)) { ?>
        <div class="list-group-item d-flex justify-content-between align-items-center">

            <div>
                <strong><?= htmlspecialchars($course['title']) ?></strong>

                <?php if ($course['status'] === 'published') { ?>
                    <span class="badge bg-success ms-2">Published</span>
                <?php } else { ?>
                    <span class="badge bg-secondary ms-2">Draft</span>
                <?php } ?>
            </div>

            <div>
                <a href="course_detail.php?id=<?= $course['id'] ?>"
                   class="btn btn-sm btn-outline-primary me-2">
                    View
                </a>

                <a href="toggle_status.php?id=<?= $course['id'] ?>"
                   class="btn btn-sm btn-outline-warning">
                    <?= $course['status'] === 'published' ? 'Unpublish' : 'Publish' ?>
        <div class="d-inline-flex gap-2">

    <a href="course_detail.php?id=<?= $course['id'] ?>"
       class="btn btn-sm btn-outline-primary">
        View
    </a>

    <?php if ($course['status'] === 'published') { ?>
        <a href="toggle_status.php?id=<?= $course['id'] ?>&status=draft"
           class="btn btn-sm btn-outline-warning">
            Unpublish
        </a>
    <?php } else { ?>
        <a href="toggle_status.php?id=<?= $course['id'] ?>&status=published"
           class="btn btn-sm btn-outline-success">
            Publish
        </a>
    <?php } ?>

</div>


        </div>
    <?php } ?>

    </div>

<?php } else { ?>
    <p>No courses yet.</p>
<?php } ?>
</div>

<?php include "footer.php"; ?>
