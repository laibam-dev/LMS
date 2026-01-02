<?php
declare(strict_types=1);

require_once __DIR__ . '/../../db/db_connection.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

class LoginController
{
    private PDO $db;

    public function __construct()
    {
        $this->db = DB::getConnection();
    }

    public function show(): void
    {
        // generate simple CSRF token for the form
        if (empty($_SESSION['csrf_login'])) {
            $_SESSION['csrf_login'] = bin2hex(random_bytes(16));
        }
        require __DIR__ . '/../views/login.php';
    }

    public function login(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            exit('Method not allowed');
        }

        $csrf = $_POST['csrf'] ?? '';
        if (!hash_equals((string)($_SESSION['csrf_login'] ?? ''), (string)$csrf)) {
            $_SESSION['flash_error'] = 'Invalid request.';
            header('Location: /LMS/admin/login');
            exit;
        }

        $email = trim((string)($_POST['email'] ?? ''));
        $password = (string)($_POST['password'] ?? '');

        if ($email === '' || $password === '') {
            $_SESSION['flash_error'] = 'Email and password are required.';
            header('Location: /LMS/admin/login');
            exit;
        }

        try {
            $stmt = $this->db->prepare('SELECT id, name, email, password_hash, role, status, avatar FROM users WHERE email = :email LIMIT 1');
            $stmt->execute([':email' => $email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $_SESSION['flash_error'] = 'Database error. Please try again later.';
            header('Location: /LMS/admin/login');
            exit;
        }

        if (!$user || !password_verify($password, $user['password_hash'] ?? '')) {
            $_SESSION['flash_error'] = 'Invalid credentials.';
            header('Location: /LMS/admin/login');
            exit;
        }

        if (($user['role'] ?? '') !== 'admin') {
            $_SESSION['flash_error'] = 'Access denied.';
            header('Location: /LMS/admin/login');
            exit;
        }

        if (($user['status'] ?? '') !== 'active') {
            $_SESSION['flash_error'] = 'Account not active.';
            header('Location: /LMS/admin/login');
            exit;
        }

        // login success
        session_regenerate_id(true);
        $_SESSION['user'] = [
            'id' => (int)$user['id'],
            'name' => $user['name'],
            'email' => $user['email'],
            'role' => $user['role'],
            'avatar' => $user['avatar'] ?? null
        ];

        // clear csrf / flash
        unset($_SESSION['csrf_login'], $_SESSION['flash_error']);

        header('Location: /LMS/admin/');
        exit;
    }

    public function logout(): void
    {
        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        session_destroy();
        header('Location: /LMS/admin/src/views/login.php');
        exit;
    }
}

/* Simple router when this file is accessed directly */
if (php_sapi_name() !== 'cli' && basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'])) {
    $c = new LoginController();
    $action = $_REQUEST['action'] ?? ($_SERVER['REQUEST_METHOD'] === 'POST' ? 'login' : 'show');
    if ($action === 'login') $c->login();
    elseif ($action === 'logout') $c->logout();
    else $c->show();
}