<?php
require_once "../config/db.php";
require_once "../config/base.php";
require_once "session.php";


$instructor_id = $_SESSION['instructor_id'] ?? 0;

if ($instructor_id <= 0) {
    header("Location: login.php");
    exit;
}

require_once "header.php";
require_once "sidebar.php";

/* ✅ Helper function */
function fetchCount($conn, $sql, $instructor_id) {
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $instructor_id);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    return (int)($row[array_key_first($row)] ?? 0);
}

/* ✅ Numbers */
$total_courses = fetchCount($conn, "SELECT COUNT(*) AS total FROM courses WHERE instructor_id = ?", $instructor_id);

$published_courses = fetchCount(
    $conn,
    "SELECT COUNT(*) AS total FROM courses WHERE instructor_id = ? AND status = 'published'",
    $instructor_id
);

$draft_courses = fetchCount(
    $conn,
    "SELECT COUNT(*) AS total FROM courses WHERE instructor_id = ? AND status = 'draft'",
    $instructor_id
);

$total_lessons = fetchCount(
    $conn,
    "SELECT COUNT(*) AS total
     FROM lessons l
     JOIN courses c ON c.id = l.course_id
     WHERE c.instructor_id = ?",
    $instructor_id
);

$total_assessments = fetchCount(
    $conn,
    "SELECT COUNT(*) AS total
     FROM assessments a
     JOIN courses c ON c.id = a.course_id
     WHERE c.instructor_id = ?",
    $instructor_id
);

$total_quizzes = fetchCount(
    $conn,
    "SELECT COUNT(*) AS total
     FROM quizzes q
     JOIN courses c ON c.id = q.course_id
     WHERE c.instructor_id = ?",
    $instructor_id
);

$total_enrollments = fetchCount(
    $conn,
    "SELECT COUNT(*) AS total
     FROM enrollments e
     JOIN courses c ON c.id = e.course_id
     WHERE c.instructor_id = ?",
    $instructor_id
);

$completed_enrollments = fetchCount(
    $conn,
    "SELECT COUNT(*) AS total
     FROM enrollments e
     JOIN courses c ON c.id = e.course_id
     WHERE c.instructor_id = ? AND e.status = 'completed'",
    $instructor_id
);

/* ✅ Avg progress */
$stmt = $conn->prepare("
    SELECT AVG(e.progress) AS avg_progress
    FROM enrollments e
    JOIN courses c ON c.id = e.course_id
    WHERE c.instructor_id = ?
");
$stmt->bind_param("i", $instructor_id);
$stmt->execute();
$avg_progress = (float)($stmt->get_result()->fetch_assoc()['avg_progress'] ?? 0);
$stmt->close();

$avg_progress = max(0, min(100, $avg_progress));

/* ✅ completion rate */
$completion_rate = 0;
if ($total_enrollments > 0) {
    $completion_rate = ($completed_enrollments / $total_enrollments) * 100;
}
$completion_rate = max(0, min(100, $completion_rate));

/* ✅ Course performance table */
$stmt = $conn->prepare("
    SELECT 
        c.title,
        COUNT(e.id) AS enrollments,
        AVG(e.progress) AS avg_progress
    FROM courses c
    LEFT JOIN enrollments e ON c.id = e.course_id
    WHERE c.instructor_id = ?
    GROUP BY c.id, c.title
    ORDER BY enrollments DESC
    LIMIT 10
");
$stmt->bind_param("i", $instructor_id);
$stmt->execute();
$course_performance = $stmt->get_result();
$stmt->close();
?>

<div class="main">

    <div class="top">
        <div>
            <h1>Analytics</h1>
            <p>Track your courses, quizzes, lessons and student engagement.</p>
        </div>

        <a href="dashboard.php" class="btn-light">← Back</a>
    </div>

    <!-- ✅ Summary Cards -->
    <div class="cards analytics-cards">

        <div class="card">
            <div>
                <small>Total Courses</small>
                <h2><?php echo $total_courses; ?></h2>
                <div class="mini-text">
                    <span class="pill success"><?php echo $published_courses; ?> Published</span>
                    <span class="pill muted"><?php echo $draft_courses; ?> Draft</span>
                </div>
            </div>
            <div class="card-icon">📚</div>
        </div>

        <div class="card">
            <div>
                <small>Content Created</small>
                <h2><?php echo $total_lessons; ?></h2>
                <div class="mini-text">
                    <span class="pill primary"><?php echo $total_quizzes; ?> Quizzes</span>
                    <span class="pill orange"><?php echo $total_assessments; ?> Assessments</span>
                </div>
            </div>
            <div class="card-icon">📝</div>
        </div>

        <div class="card">
            <div>
                <small>Total Enrollments</small>
                <h2><?php echo $total_enrollments; ?></h2>
                <div class="mini-text">
                    <span class="pill success"><?php echo $completed_enrollments; ?> Completed</span>
                    <span class="pill muted"><?php echo round($avg_progress, 1); ?>% Avg Progress</span>
                </div>
            </div>
            <div class="card-icon">👥</div>
        </div>

    </div>

    <!-- ✅ Charts Row -->
    <div class="charts-row">

        <!-- ✅ Avg Progress -->
        <div class="chart-card">
            <div class="chart-top">
                <div>
                    <h3>Average Progress</h3>
                    <p>Overall student progress in your courses</p>
                </div>
                <div class="big-number"><?php echo round($avg_progress, 1); ?>%</div>
            </div>

            <div class="progress-wrap">
                <div class="progress-bar">
                    <div class="progress-fill" style="width: <?php echo round($avg_progress, 1); ?>%;"></div>
                </div>
                <div class="progress-meta">
                    <span>0%</span>
                    <span>100%</span>
                </div>
            </div>
        </div>

        <!-- ✅ Completion Rate -->
        <div class="chart-card">
            <div class="chart-top">
                <div>
                    <h3>Completion Rate</h3>
                    <p>How many enrollments are completed</p>
                </div>
                <div class="big-number"><?php echo round($completion_rate, 1); ?>%</div>
            </div>

            <div class="donut-wrap">
                <div class="donut" style="--p: <?php echo round($completion_rate, 1); ?>;">
                    <div class="donut-center">
                        <div class="donut-percent"><?php echo round($completion_rate, 0); ?>%</div>
                        <div class="donut-label">Completed</div>
                    </div>
                </div>

                <div class="donut-stats">
                    <div class="stat-line">
                        <span>Total</span>
                        <b><?php echo $total_enrollments; ?></b>
                    </div>
                    <div class="stat-line">
                        <span>Completed</span>
                        <b><?php echo $completed_enrollments; ?></b>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- ✅ Table -->
    <div class="table-card" style="margin-top:18px;">
        <div style="display:flex; align-items:center; justify-content:space-between; gap:12px;">
            <div>
                <h2 style="margin:0;">Top Courses (Performance)</h2>
                <p style="margin:6px 0 0; color:#64748b; font-size:13px;">
                    Based on enrollments and average progress.
                </p>
            </div>
        </div>

        <div style="margin-top:14px;">
            <?php if ($course_performance->num_rows > 0): ?>
                <table class="modern-table">
                    <thead>
                        <tr>
                            <th style="width:60%;">Course Title</th>
                            <th style="width:20%;">Enrollments</th>
                            <th style="width:20%;">Avg Progress</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($course = $course_performance->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($course['title']); ?></td>
                                <td><?php echo (int)$course['enrollments']; ?></td>
                                <td>
                                    <span class="pill muted">
                                        <?php echo round($course['avg_progress'] ?? 0, 1); ?>%
                                    </span>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div style="padding:18px; color:#64748b;">
                    No course performance data available.
                </div>
            <?php endif; ?>
        </div>
    </div>

</div>

<style>
/* ✅ Analytics extra styles */
.analytics-cards { gap: 16px; margin-top: 14px; }

.mini-text { margin-top: 10px; display: flex; gap: 8px; flex-wrap: wrap; }

.pill {
    display: inline-flex;
    align-items: center;
    padding: 6px 10px;
    border-radius: 999px;
    font-size: 12px;
    font-weight: 600;
    border: 1px solid #e5e7eb;
    background: #f8fafc;
    color: #334155;
}
.pill.success { background: #ecfdf5; border-color: #a7f3d0; color: #065f46; }
.pill.primary { background: #eff6ff; border-color: #bfdbfe; color: #1d4ed8; }
.pill.orange  { background: #fff7ed; border-color: #fed7aa; color: #9a3412; }
.pill.muted   { background: #f1f5f9; border-color: #e2e8f0; color: #475569; }

/* ✅ Charts */
.charts-row{
    margin-top: 16px;
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 16px;
}

.chart-card{
    background: #ffffff;
    border: 1px solid #e5e7eb;
    border-radius: 18px;
    padding: 16px;
    box-shadow: 0 10px 25px rgba(15, 23, 42, 0.06);
}

.chart-top{
    display:flex;
    align-items:flex-start;
    justify-content:space-between;
    gap: 12px;
}

.chart-top h3{
    margin:0;
    font-size: 16px;
}
.chart-top p{
    margin:6px 0 0;
    font-size: 13px;
    color:#64748b;
}

.big-number{
    font-size: 22px;
    font-weight: 800;
    color: #0f172a;
}

/* Progress bar */
.progress-wrap{ margin-top: 16px; }
.progress-bar{
    width: 100%;
    height: 12px;
    background: #eef2ff;
    border-radius: 999px;
    overflow:hidden;
}
.progress-fill{
    height: 100%;
    background: #2563eb;
    border-radius: 999px;
}
.progress-meta{
    display:flex;
    justify-content:space-between;
    margin-top: 8px;
    font-size: 12px;
    color: #64748b;
}

/* Donut chart */
.donut-wrap{
    margin-top: 16px;
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap: 14px;
}

.donut{
    width: 120px;
    height: 120px;
    border-radius: 50%;
    background: conic-gradient(#2563eb calc(var(--p) * 1%), #e2e8f0 0);
    display:flex;
    align-items:center;
    justify-content:center;
    position: relative;
}

.donut::after{
    content:"";
    width: 84px;
    height: 84px;
    background: #fff;
    border-radius: 50%;
    position:absolute;
}

.donut-center{
    position: relative;
    z-index: 2;
    text-align:center;
}

.donut-percent{
    font-weight: 800;
    font-size: 18px;
    color: #0f172a;
}
.donut-label{
    font-size: 12px;
    color:#64748b;
    margin-top: 2px;
}

.donut-stats{
    flex:1;
    display:flex;
    flex-direction:column;
    gap: 10px;
}
.stat-line{
    display:flex;
    align-items:center;
    justify-content:space-between;
    font-size: 13px;
    color:#475569;
}
.stat-line b{
    color:#0f172a;
    font-weight: 800;
}

/* Responsive */
@media (max-width: 900px){
    .charts-row{
        grid-template-columns: 1fr;
    }
}
</style>

<?php require_once "footer.php"; ?>
