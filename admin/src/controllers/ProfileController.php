<?php
<?php
declare(strict_types=1);

require_once __DIR__ . '/../../db/db_connection.php';
require_once __DIR__ . '/../middleware/auth.php'; // ensures admin session

session_start();

class ProfileController
{
    private PDO $db;
    private int $userId;

    public function __construct()
    {
        $this->db = DB::getConnection();
        $this->userId = (int)($_SESSION['user']['id'] ?? 0);
    }

    public function show(): void
    {
        // fetch current user
        $stmt = $this->db->prepare('SELECT id, name, email, avatar FROM users WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => $this->userId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
        require __DIR__ . '/../views/profile.php';
    }

    public function update(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405); exit;
        }

        // simple CSRF
        $csrf = $_POST['csrf'] ?? '';
        if (!hash_equals((string)($_SESSION['csrf_profile'] ?? ''), (string)$csrf)) {
            $_SESSION['flash_profile'] = 'Invalid request.';
            header('Location: /LMS/admin/src/views/profile.php');
            exit;
        }

        $name = trim((string)($_POST['name'] ?? ''));
        $email = trim((string)($_POST['email'] ?? ''));
        $password = $_POST['password'] ?? '';

        if ($name === '' || $email === '') {
            $_SESSION['flash_profile'] = 'Name and email required.';
            header('Location: /LMS/admin/src/views/profile.php');
            exit;
        }

        // begin transaction
        $this->db->beginTransaction();
        try {
            $params = [':id' => $this->userId, ':name' => $name, ':email' => $email];
            $sql = 'UPDATE users SET name = :name, email = :email';

            if (!empty($password)) {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $sql .= ', password_hash = :password_hash';
                $params[':password_hash'] = $hash;
            }

            // handle avatar upload if provided
            $avatarPath = null;
            if (!empty($_FILES['avatar']['name'])) {
                $avatarPath = $this->handleAvatarUpload($_FILES['avatar']);
                if ($avatarPath === null) {
                    $this->db->rollBack();
                    $_SESSION['flash_profile'] = 'Avatar upload failed.';
                    header('Location: /LMS/admin/src/views/profile.php');
                    exit;
                }
                $sql .= ', avatar = :avatar';
                $params[':avatar'] = $avatarPath;
            }

            $sql .= ', updated_at = NOW() WHERE id = :id';
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            $this->db->commit();

            // update session
            $_SESSION['user']['name'] = $name;
            if ($avatarPath) $_SESSION['user']['avatar'] = $avatarPath;

            $_SESSION['flash_profile'] = 'Profile updated.';
            header('Location: /LMS/admin/src/views/profile.php');
            exit;
        } catch (Throwable $e) {
            $this->db->rollBack();
            $_SESSION['flash_profile'] = 'Update failed.';
            header('Location: /LMS/admin/src/views/profile.php');
            exit;
        }
    }

    private function handleAvatarUpload(array $file): ?string
    {
        if ($file['error'] !== UPLOAD_ERR_OK) return null;

        $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!isset($allowed[$mime])) return null;
        if ($file['size'] > 2 * 1024 * 1024) return null; // 2MB limit

        $ext = $allowed[$mime];
        $dir = __DIR__ . '/../../public/uploads/avatars';
        if (!is_dir($dir) && !mkdir($dir, 0755, true)) return null;

        $filename = sprintf('user_%d_%d.%s', $this->userId, time(), $ext);
        $dest = $dir . DIRECTORY_SEPARATOR . $filename;

        if (!move_uploaded_file($file['tmp_name'], $dest)) return null;

        // return web-accessible relative path
        return '/LMS/admin/public/uploads/avatars/' . $filename;
    }
}

/* Router when accessed directly */
if (php_sapi_name() !== 'cli' && basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'])) {
    $ctrl = new ProfileController();
    $action = $_REQUEST['action'] ?? 'show';
    if ($action === 'update') $ctrl->update();
    else $ctrl->show();
}