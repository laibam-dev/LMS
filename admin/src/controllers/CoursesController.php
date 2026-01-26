<?php
<?php
declare(strict_types=1);

require_once __DIR__ . '/../../db/db_connection.php';
require_once __DIR__ . '/../middleware/auth.php';
require_once __DIR__ . '/../helpers/ActivityLogger.php';

class CoursesController
{
    private PDO $db;
    private int $adminId;

    public function __construct()
    {
        $this->db = DB::getConnection();
        $this->adminId = (int)($_SESSION['user']['id'] ?? 0);
    }

    public function list(string $search = null, ?string $status = null): array
    {
        $sql = 'SELECT c.id, c.title, c.status, c.created_at, u.id AS instructor_id, u.name AS instructor_name FROM courses c LEFT JOIN users u ON u.id = c.instructor_id';
        $conds = [];
        $params = [];

        if ($search) {
            $conds[] = '(c.title LIKE :q OR u.name LIKE :q)';
            $params[':q'] = '%' . $search . '%';
        }
        if ($status) {
            $conds[] = 'c.status = :status';
            $params[':status'] = $status;
        }
        if ($conds) $sql .= ' WHERE ' . implode(' AND ', $conds);
        $sql .= ' ORDER BY c.created_at DESC LIMIT 500';

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function approve(int $id): bool
    {
        $stmt = $this->db->prepare('UPDATE courses SET status = "approved", updated_at = NOW() WHERE id = :id');
        $ok = $stmt->execute([':id' => $id]);
        if ($ok) {
            ActivityLogger::log($this->db, $this->adminId, 'course_approved', ['course_id' => $id]);
        }
        return $ok;
    }

    public function reject(int $id): bool
    {
        $stmt = $this->db->prepare('UPDATE courses SET status = "rejected", updated_at = NOW() WHERE id = :id');
        $ok = $stmt->execute([':id' => $id]);
        if ($ok) {
            ActivityLogger::log($this->db, $this->adminId, 'course_rejected', ['course_id' => $id]);
        }
        return $ok;
    }
}

/* Lightweight AJAX endpoints */
if (php_sapi_name() !== 'cli' && basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'])) {
    header('Content-Type: application/json; charset=utf-8');
    $ctrl = new CoursesController();
    $action = $_REQUEST['action'] ?? 'list';

    try {
        if ($action === 'list') {
            $data = $ctrl->list($_GET['search'] ?? null, $_GET['status'] ?? null);
            echo json_encode(['ok' => true, 'data' => $data]);
            exit;
        }

        if ($action === 'approve' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = (int)($_POST['id'] ?? 0);
            if ($id <= 0) throw new Exception('Invalid id');
            $ok = $ctrl->approve($id);
            echo json_encode(['ok' => $ok]);
            exit;
        }

        if ($action === 'reject' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = (int)($_POST['id'] ?? 0);
            if ($id <= 0) throw new Exception('Invalid id');
            $ok = $ctrl->reject($id);
            echo json_encode(['ok' => $ok]);
            exit;
        }

        echo json_encode(['ok' => false, 'error' => 'Unknown action']);
    } catch (Throwable $e) {
        http_response_code(400);
        echo json_encode(['ok' => false, 'error' => $e->getMessage()]);
    }
}