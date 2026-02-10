<?php
require_once "../config/db.php";
require_once "../config/base.php";
require_once "session.php";

// Sirf ek dafa '../' use karein kyunke instructor folder root ke foran baad hai
include '../navbar.php'; 


$instructor_id    = $_SESSION['instructor_id'];
$instructor_name  = $_SESSION['instructor_name'] ?? 'Instructor';
$instructor_email = $_SESSION['instructor_email'] ?? '';

require_once "header.php";
require_once "sidebar.php";

/* ✅ ACTIVE COURSES COUNT */
$stmt = $conn->prepare("SELECT COUNT(*) FROM courses WHERE instructor_id = ?");
$stmt->bind_param("i", $instructor_id);
$stmt->execute();
$stmt->bind_result($activeCourses);
$stmt->fetch();
$stmt->close();

/* ✅ TOTAL STUDENTS */
$stmt = $conn->prepare("
    SELECT COUNT(DISTINCT enrollments.user_id)
    FROM enrollments
    JOIN courses ON courses.id = enrollments.course_id
    WHERE courses.instructor_id = ?
");
$stmt->bind_param("i", $instructor_id);
$stmt->execute();
$stmt->bind_result($totalStudents);
$stmt->fetch();
$stmt->close();

/* ✅ UPCOMING TASKS (Quizzes + Assessments) */
$upcomingTasks = [];

$stmt = $conn->prepare("
    SELECT t.type, t.title, t.due_date, t.course_title
    FROM (
        SELECT 
            'Quiz' AS type,
            q.title AS title,
            q.due_date AS due_date,
            c.title AS course_title
        FROM quizzes q
        JOIN courses c ON c.id = q.course_id
        WHERE c.instructor_id = ? 
          AND q.due_date IS NOT NULL
          AND q.due_date != '0000-00-00'

        UNION ALL

        SELECT 
            'Assessment' AS type,
            a.title AS title,
            a.due_date AS due_date,
            c.title AS course_title
        FROM assessments a
        JOIN courses c ON c.id = a.course_id
        WHERE c.instructor_id = ? 
          AND a.due_date IS NOT NULL
          AND a.due_date != '0000-00-00'
    ) t
    WHERE t.due_date >= CURDATE()
    ORDER BY t.due_date ASC
    LIMIT 3
");
$stmt->bind_param("ii", $instructor_id, $instructor_id);
$stmt->execute();
$resultTasks = $stmt->get_result();

while ($r = $resultTasks->fetch_assoc()) {
    $upcomingTasks[] = $r;
}
$stmt->close();

/* ✅ COURSES LIST + enrolled students per course */
$stmt = $conn->prepare("
    SELECT 
        c.id, c.title, c.subject, c.description, c.created_at,
        (SELECT COUNT(*) FROM enrollments e WHERE e.course_id = c.id) AS enrolled_students
    FROM courses c
    WHERE c.instructor_id = ?
    ORDER BY c.created_at DESC
");
$stmt->bind_param("i", $instructor_id);
$stmt->execute();
$courses = $stmt->get_result();
$stmt->close();
?>

<div class="main">

    <div class="top">
        <div>
            <h1>Welcome back, <?php echo htmlspecialchars($instructor_name); ?></h1>
            <p>Manage your courses and students from here.</p>
        </div>
        <a href="<?php echo BASE_URL; ?>instructor/add_course.php" class="add-btn">+ Add Course</a>
    </div>

    <div class="cards">

        <div class="card blue">
            <div>
                <small>Active Courses</small>
                <h2><?php echo (int)$activeCourses; ?></h2>
            </div>
            <div class="card-icon">📘</div>
        </div>

        <div class="card">
            <div>
                <small>Total Students</small>
                <h2><?php echo (int)$totalStudents; ?></h2>
            </div>
            <div class="card-icon">👥</div>
        </div>

        <div class="card">
            <div>
                <small>Upcoming Tasks</small>

                <?php if (!empty($upcomingTasks)): ?>
                    <h2><?php echo count($upcomingTasks); ?></h2>

                    <div style="margin-top:8px; font-size:13px; color:#475569; line-height:1.5;">
                        <?php foreach ($upcomingTasks as $task): ?>
                            <div style="margin-bottom:8px;">
                                <b><?php echo htmlspecialchars($task['type']); ?>:</b>
                                <?php echo htmlspecialchars($task['title']); ?>
                                <br>
                                <span style="font-size:12px;">
                                    📚 <?php echo htmlspecialchars($task['course_title']); ?> |
                                    📅 <?php echo date("M d, Y", strtotime($task['due_date'])); ?>
                                </span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <h2>--</h2>
                    <div style="margin-top:8px; font-size:13px; color:#64748b;">
                        No upcoming quizzes or assessments.
                    </div>
                <?php endif; ?>

            </div>
            <div class="card-icon">📅</div>
        </div>

    </div>

    <h2 class="section-title">Your Courses</h2>

    <div class="course-grid-modern">
        <?php if ($courses->num_rows > 0): ?>
            <?php while ($row = $courses->fetch_assoc()): ?>
                <div class="course-card-modern">

                    <span class="course-tag">
                        <?php echo htmlspecialchars($row['subject'] ?: 'General'); ?>
                    </span>

                    <h3><?php echo htmlspecialchars($row['title']); ?></h3>

                    <p>
                        <?php echo $row['description']
                            ? htmlspecialchars($row['description'])
                            : "No description provided."; ?>
                    </p>

                    <p style="margin-top:8px; font-size:13px; color:#475569;">
                        👥 Enrolled Students: <?php echo (int)$row['enrolled_students']; ?>
                    </p>

                    <div class="course-footer">
                        <span class="course-date">
                            📅 Created <?php echo date("m/d/Y", strtotime($row['created_at'])); ?>
                        </span>

                        <a class="course-link"
                           href="<?php echo BASE_URL; ?>instructor/course_overview.php?course_id=<?php echo (int)$row['id']; ?>">
                            View Details →
                        </a>
                    </div>

                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No courses found.</p>
        <?php endif; ?>
    </div>

</div>

<?php require_once "footer.php"; ?>