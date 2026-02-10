<?php
require_once "../config/db.php";
require_once "../config/base.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/* Must come from form */
if (!isset($_POST['login'])) {
    header("Location: " . BASE_URL . "instructor/index.php?error=blocked");
    exit;
}

$email = trim($_POST['email'] ?? '');
$password = trim($_POST['password'] ?? '');

if ($email === "" || $password === "") {
    header("Location: " . BASE_URL . "instructor/index.php?error=empty");
    exit;
}

/* Instructor only */
$stmt = $conn->prepare("
    SELECT id, role, email, password_hash, name
    FROM users 
    WHERE email = ? 
    LIMIT 1
");
$stmt->bind_param("s", $email);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$user || $user['role'] !== 'instructor') {
    header("Location: " . BASE_URL . "instructor/index.php?error=invalid");
    exit;
}

$stored = $user['password_hash'] ?? '';
$ok = false;

/* hashed password */
if (is_string($stored) && str_starts_with($stored, '$2y$')) {
    $ok = password_verify($password, $stored);
} else {
    /* fallback old plain text */
    $ok = hash_equals((string)$stored, (string)$password);

    if ($ok) {
        $newHash = password_hash($password, PASSWORD_BCRYPT);
        $up = $conn->prepare("UPDATE users SET password_hash=? WHERE id=?");
        $up->bind_param("si", $newHash, $user['id']);
        $up->execute();
        $up->close();
    }
}

if (!$ok) {
    header("Location: " . BASE_URL . "instructor/index.php?error=invalid");
    exit;
}

/* Secure session */
session_regenerate_id(true);

$_SESSION['instructor_id'] = (int)$user['id'];
$_SESSION['role'] = 'instructor';
$_SESSION['instructor_name'] = $user['name'] ?? 'Instructor';
$_SESSION['instructor_email'] = $user['email'];

/* Success */
header("Location: " . BASE_URL . "instructor/dashboard.php");
exit;