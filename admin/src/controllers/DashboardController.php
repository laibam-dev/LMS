<?php
declare(strict_types=1);

require_once __DIR__ . '/../../db/db_connection.php';
require_once __DIR__ . '/../middleware/auth.php'; // ensure admin
require_once __DIR__ . '/../helpers/ActivityLogger.php';

class DashboardController
{
    private PDO $db;

    public function __construct()
    {
        $this->db = DB::getConnection();
    }

    public function index(): void
    {
        // current admin for layout
        $admin = $_SESSION['user'] ?? ['name' => 'Administrator', 'avatar' => 'https://i.pravatar.cc/48'];

        // Real-time counts
        $stats = [
            'total_students' => $this->countByRole('student'),
            'total_instructors' => $this->countByRole('instructor'),
            'total_courses' => $this->countCourses(),
            'monthly_revenue' => $this->sumMonthlyRevenue()
        ];

        // Recent users: last 5 registered
        $recentUsers = $this->fetchRecentUsers(5);

        // Recent activity logs
        $activityLogs = ActivityLogger::fetchRecent($this->db, 6);

        // Render: prepare $content via output buffer and include layout
        ob_start();
        require __DIR__ . '/../views/dashboard_content.php'; // content-only partial
        $content = ob_get_clean();

        $title = 'Dashboard';
        require __DIR__ . '/../views/layout.php';
    }

    private function countByRole(string $role): int
    {
        $stmt = $this->db->prepare('SELECT COUNT(*) FROM users WHERE role = :role');
        $stmt->execute([':role' => $role]);
        return (int)$stmt->fetchColumn();
    }

    private function countCourses(): int
    {
        try {
            $stmt = $this->db->query('SELECT COUNT(*) FROM courses');
            return (int)$stmt->fetchColumn();
        } catch (PDOException $e) {
            return 0;
        }
    }

    private function sumMonthlyRevenue(): float
    {
        try {
            // assume payments table with amount and created_at
            $stmt = $this->db->prepare('SELECT IFNULL(SUM(amount),0) FROM payments WHERE MONTH(created_at)=MONTH(CURDATE()) AND YEAR(created_at)=YEAR(CURDATE())');
            $stmt->execute();
            return (float)$stmt->fetchColumn();
        } catch (PDOException $e) {
            return 0.0;
        }
    }

    private function fetchRecentUsers(int $limit = 5): array
    {
        $stmt = $this->db->prepare('SELECT id, name, email, role, status, created_at FROM users ORDER BY created_at DESC LIMIT :lim');
        $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * API: enrollments per month for last 12 months.
     * If enrollments table missing, fallback to users grouped by month.
     */
    public function apiEnrollmentsPerMonth(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        $months = [];
        $labels = [];
        $data = [];

        // build last 12 months keys yyyy-mm
        for ($i = 11; $i >= 0; $i--) {
            $dt = new DateTime("first day of -{$i} month");
            $key = $dt->format('Y-m');
            $months[] = $key;
            $labels[] = $dt->format('M Y');
            $data[$key] = 0;
        }

        $queryEnrollments = "
            SELECT DATE_FORMAT(created_at, '%Y-%m') AS ym, COUNT(*) AS cnt
            FROM enrollments
            WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 11 MONTH)
            GROUP BY ym
        ";

        $queryUsersFallback = "
            SELECT DATE_FORMAT(created_at, '%Y-%m') AS ym, COUNT(*) AS cnt
            FROM users
            WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 11 MONTH)
            GROUP BY ym
        ";

        try {
            $stmt = $this->db->query($queryEnrollments);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if (empty($rows)) {
                // try fallback
                $stmt = $this->db->query($queryUsersFallback);
                $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            }
        } catch (PDOException $e) {
            // fallback
            try {
                $stmt = $this->db->query($queryUsersFallback);
                $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            } catch (PDOException $e2) {
                $rows = [];
            }
        }

        foreach ($rows as $r) {
            if (isset($data[$r['ym']])) $data[$r['ym']] = (int)$r['cnt'];
        }

        $out = [
            'labels' => $labels,
            'data' => array_values($data)
        ];

        echo json_encode(['ok' => true, 'data' => $out]);
    }
}

/* Simple endpoint routing when file is requested directly */
if (php_sapi_name() !== 'cli' && basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'])) {
    $ctrl = new DashboardController();
    $action = $_GET['action'] ?? 'index';
    if ($action === 'chart_enrollments') {
        $ctrl->apiEnrollmentsPerMonth();
        exit;
    }
    // default: show index (will enforce auth)
    $ctrl->index();
}