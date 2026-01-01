<?php
declare(strict_types=1);

// Simple admin router — ensure this file contains only PHP (no leading/trailing whitespace/BOM)
session_start();

// adjust base if deployed under a different path
$base = '/LMS/admin';
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// normalize and strip base
if (strpos($uri, $base) === 0) {
    $path = substr($uri, strlen($base));
} else {
    $path = $uri;
}
$path = '/' . trim($path, '/');
if ($path === '/index.php' || $path === '/') $path = '/';

// route
switch ($path) {
    case '/':
    case '/dashboard':
        require_once __DIR__ . '/src/controllers/DashboardController.php';
        $c = new DashboardController();
        $c->index();
        break;

    case '/users':
        require_once __DIR__ . '/src/middleware/auth.php';
        $admin = $_SESSION['user'] ?? null;
        ob_start();
        require __DIR__ . '/src/views/users.php';
        $content = ob_get_clean();
        $title = 'User Management';
        require __DIR__ . '/src/views/layout.php';
        break;

    case '/courses':
        require_once __DIR__ . '/src/middleware/auth.php';
        $admin = $_SESSION['user'] ?? null;
        ob_start();
        require __DIR__ . '/src/views/courses.php';
        $content = ob_get_clean();
        $title = 'Course Management';
        require __DIR__ . '/src/views/layout.php';
        break;

    case '/profile':
        require_once __DIR__ . '/src/controllers/ProfileController.php';
        $ctrl = new ProfileController();
        $ctrl->show();
        break;

    case '/login':
        require_once __DIR__ . '/src/controllers/LoginController.php';
        $ctrl = new LoginController();
        $ctrl->show();
        break;

    case '/logout':
        require_once __DIR__ . '/src/controllers/LoginController.php';
        $ctrl = new LoginController();
        $ctrl->logout();
        break;

    case '/analytics':
        require_once __DIR__ . '/src/middleware/auth.php';
        $admin = $_SESSION['user'] ?? null;
        ob_start();
        require __DIR__ . '/src/views/analytics.php';
        $content = ob_get_clean();
        $title = 'Analytics';
        require __DIR__ . '/src/views/layout.php';
        break;

    case '/logs':
        require_once __DIR__ . '/src/middleware/auth.php';
        $admin = $_SESSION['user'] ?? null;
        ob_start();
        require __DIR__ . '/src/views/logs.php';
        $content = ob_get_clean();
        $title = 'Activity Logs';
        require __DIR__ . '/src/views/layout.php';
        break;

    default:
        http_response_code(404);
        echo 'Not found';
        break;
}