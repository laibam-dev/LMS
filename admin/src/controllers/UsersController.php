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
        // select only known columns; avoid created_at/updated_at to be compatible with schemas lacking timestamps
        $sql = "SELECT id, name, email, role, '' AS avatar, 'active' AS status FROM users";
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
        $sql .= ' ORDER BY id DESC LIMIT 200';

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
        // The current `users` table does not include a `status` column.
        // For now, respond as not-implemented to avoid SQL errors.
        return false;
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
     * Monthly student signups for the last N months (default 12)
     * Returns ['labels'=>[], 'data'=>[]]
     */
    public function monthlyStudents(int $months = 12): array
    {
        // build a map of year-month -> 0
        $labels = [];
        $data = [];
        $dt = new DateTimeImmutable('first day of this month');
        for ($i = $months - 1; $i >= 0; $i--) {
            $m = $dt->sub(new DateInterval('P' . $i . 'M'));
            $labels[] = $m->format('M Y');
            $data[$m->format('Y-m')] = 0;
        }

        $sql = "SELECT DATE_FORMAT(created_at, '%Y-%m') AS ym, COUNT(*) AS cnt
                FROM users
                WHERE role = 'student' AND created_at >= DATE_SUB(CURDATE(), INTERVAL :months MONTH)
                GROUP BY ym
                ORDER BY ym";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':months' => $months - 1]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($rows as $r) {
            $key = $r['ym'];
            if (isset($data[$key])) $data[$key] = (int)$r['cnt'];
        }

        // map data in same order as labels
        $out = [];
        foreach ($labels as $lbl) {
            // convert label back to key YYYY-mm
            $dt = DateTimeImmutable::createFromFormat('M Y', $lbl);
            $key = $dt ? $dt->format('Y-m') : null;
            $out[] = $key && isset($data[$key]) ? $data[$key] : 0;
        }

        return ['labels' => $labels, 'data' => $out];
    }

    /**
     * Fetch most recent users.
     * Returns array of rows with id, name, email, role, status, created_at
     */
    public function getRecentUsers(int $limit = 5): array
    {
        $sql = "SELECT id, name, email, role, '' AS avatar, 'active' AS status
            FROM users
            ORDER BY id DESC
            LIMIT :lim";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getUserById(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT id, name, email, role, status FROM users WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function createUser(string $name, string $email, string $password, string $role = 'student'): bool
    {
        // check for existing email
        $stmt = $this->db->prepare('SELECT COUNT(*) FROM users WHERE email = :email');
        $stmt->execute([':email' => $email]);
        $cnt = (int)$stmt->fetchColumn();
        if ($cnt > 0) {
            throw new Exception('Email already exists');
        }

        $hash = password_hash($password, PASSWORD_DEFAULT);
        // Restrict roles to only student or instructor to prevent admin creation via dashboard
        $allowed = ['student', 'instructor'];
        $role = in_array(strtolower($role), $allowed, true) ? strtolower($role) : 'student';

        // Insert only into known columns: name, email, password, role, status
        $stmt = $this->db->prepare('INSERT INTO users (name, email, password, role, status) VALUES (:name, :email, :pwd, :role, :status)');
        return $stmt->execute([':name' => $name, ':email' => $email, ':pwd' => $hash, ':role' => $role, ':status' => 'active']);
    }

    public function updateUser(int $id, string $name, string $email, string $role, string $status, ?string $password = null): bool
    {
        // check duplicate email for other users
        $stmt = $this->db->prepare('SELECT COUNT(*) FROM users WHERE email = :email AND id <> :id');
        $stmt->execute([':email' => $email, ':id' => $id]);
        if ((int)$stmt->fetchColumn() > 0) {
            throw new Exception('Email already exists');
        }

        // Restrict roles on update as well
        $allowed = ['student', 'instructor'];
        $role = in_array(strtolower($role), $allowed, true) ? strtolower($role) : 'student';

        $params = [':name' => $name, ':email' => $email, ':role' => $role, ':status' => $status, ':id' => $id];
        $sql = 'UPDATE users SET name = :name, email = :email, role = :role, status = :status';
        if ($password) {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $sql .= ', password = :pwd';
            $params[':pwd'] = $hash;
        }
        $sql .= ' WHERE id = :id';

        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
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
        if ($action === 'monthly_students') {
            $months = isset($_GET['months']) ? (int)$_GET['months'] : 12;
            if ($months <= 0) $months = 12;
            $data = $ctrl->monthlyStudents($months);
            echo json_encode(['ok' => true, 'data' => $data]);
            exit;
        }

        if ($action === 'create' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $role = $_POST['role'] ?? 'student';
            if ($name === '' || $email === '' || $password === '') {
                throw new Exception('Missing required fields');
            }
            $ok = $ctrl->createUser($name, $email, $password, $role);
            echo json_encode(['ok' => $ok]);
            exit;
        }

        if ($action === 'update' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
            $name = trim($_POST['name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $role = $_POST['role'] ?? 'student';
            $status = $_POST['status'] ?? 'active';
            $password = $_POST['password'] ?? null;
            if ($id <= 0 || $name === '' || $email === '') {
                throw new Exception('Missing required fields');
            }
            $ok = $ctrl->updateUser($id, $name, $email, $role, $status, $password ?: null);
            echo json_encode(['ok' => $ok]);
            exit;
        }

        echo json_encode(['ok' => false, 'error' => 'Unknown action']);
    } catch (Throwable $e) {
        http_response_code(400);
        echo json_encode(['ok' => false, 'error' => $e->getMessage()]);
    }
}