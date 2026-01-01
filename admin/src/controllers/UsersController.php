<?php
<?php
declare(strict_types=1);

require_once __DIR__ . '/../../db/db_connection.php';

class UsersController
{
    private PDO $db;

    public function __construct()
    {
        $this->db = DB::getConnection();
    }

    public function getAllUsers(?string $search = null, ?string $role = null): array
    {
        $sql = 'SELECT id, name, email, role, status, avatar, created_at FROM users';
        $conds = [];
        $params = [];

        if ($search) {
            $conds[] = '(name LIKE :q OR email LIKE :q)';
            $params[':q'] = '%' . $search . '%';
        }
        if ($role) {
            $conds[] = 'role = :role';
            $params[':role'] = $role;
        }
        if ($conds) {
            $sql .= ' WHERE ' . implode(' AND ', $conds);
        }
        $sql .= ' ORDER BY created_at DESC LIMIT 200';

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function deleteUser(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM users WHERE id = :id');
        return $stmt->execute([':id' => $id]);
    }

    public function updateUserStatus(int $id, string $status): bool
    {
        $allowed = ['active','inactive','suspended'];
        if (!in_array($status, $allowed, true)) {
            return false;
        }
        $stmt = $this->db->prepare('UPDATE users SET status = :status, updated_at = NOW() WHERE id = :id');
        return $stmt->execute([':status' => $status, ':id' => $id]);
    }

    public function roleCounts(): array
    {
        $stmt = $this->db->query("
            SELECT role, COUNT(*) AS cnt
            FROM users
            WHERE role IN ('student','instructor')
            GROUP BY role
        ");
        $rows = $stmt->fetchAll(PDO::FETCH_KEY_PAIR); // role => cnt
        return [
            'labels' => ['Students','Instructors'],
            'data' => [
                (int)($rows['student'] ?? 0),
                (int)($rows['instructor'] ?? 0)
            ]
        ];
    }

    /**
     * Fetch most recent users.
     * Returns array of rows with id, name, email, role, status, created_at
     */
    public function getRecentUsers(int $limit = 5): array
    {
        $stmt = $this->db->prepare('
            SELECT id, name, email, role, status, created_at
            FROM users
            ORDER BY created_at DESC
            LIMIT :lim
        ');
        $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

/*
 Direct JSON API handling so this controller file can be called via AJAX:
 Endpoints:
  - GET  ?action=list&search=...&role=...
  - POST ?action=delete  { id }
  - POST ?action=update_status { id, status }
  - GET  ?action=role_counts
*/
if (php_sapi_name() !== 'cli' && basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'])) {
    header('Content-Type: application/json; charset=utf-8');

    $ctrl = new UsersController();

    $action = $_REQUEST['action'] ?? 'list';

    try {
        if ($action === 'list') {
            $search = $_GET['search'] ?? null;
            $role = $_GET['role'] ?? null;
            $data = $ctrl->getAllUsers($search, $role);
            echo json_encode(['ok' => true, 'data' => $data]);
            exit;
        }

        if ($action === 'delete' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = (int)($_POST['id'] ?? 0);
            if ($id <= 0) throw new Exception('Invalid id');
            $ok = $ctrl->deleteUser($id);
            echo json_encode(['ok' => $ok]);
            exit;
        }

        if ($action === 'update_status' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = (int)($_POST['id'] ?? 0);
            $status = $_POST['status'] ?? '';
            if ($id <= 0) throw new Exception('Invalid id');
            $ok = $ctrl->updateUserStatus($id, $status);
            echo json_encode(['ok' => $ok]);
            exit;
        }

        if ($action === 'role_counts') {
            $data = $ctrl->roleCounts();
            echo json_encode(['ok' => true, 'data' => $data]);
            exit;
        }

        echo json_encode(['ok' => false, 'error' => 'Unknown action']);
    } catch (Throwable $e) {
        http_response_code(400);
        echo json_encode(['ok' => false, 'error' => $e->getMessage()]);
    }
}