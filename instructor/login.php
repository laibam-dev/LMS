<?php
// instructor/login.php
session_start();
require_once "../config/db.php";

if (!isset($_POST['login'])) {
    header("Location: index.php?error=blocked");
    exit;
}

$email = trim($_POST['email'] ?? '');
$password = trim($_POST['password'] ?? '');

if ($email === "" || $password === "") {
    header("Location: index.php?error=empty");
    exit;
}

// sirf instructor
$sql = "SELECT id, role, email, password_hash, name FROM users WHERE email = ? LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if (!$user) {
    header("Location: index.php?error=invalid");
    exit;
}

if ($user['role'] !== 'instructor') {
    header("Location: index.php?error=not_instructor");
    exit;
}

$stored = $user['password_hash'] ?? '';
$ok = false;

// Case 1: stored hash (bcrypt) -> password_verify
if (is_string($stored) && strlen($stored) > 0 && str_starts_with($stored, '$2y$')) {
    $ok = password_verify($password, $stored);
} else {
    // Case 2: old/plain text fallback
    $ok = hash_equals((string)$stored, (string)$password);
    // If matched, upgrade to proper hash
    if ($ok) {
        $newHash = password_hash($password, PASSWORD_BCRYPT);
        $up = $conn->prepare("UPDATE users SET password_hash=? WHERE id=?");
        $up->bind_param("si", $newHash, $user['id']);
        $up->execute();
    }
}

if (!$ok) {
    header("Location: index.php?error=invalid");
    exit;
}

// Session secure
session_regenerate_id(true);

$_SESSION['instructor_id'] = (int)$user['id'];
$_SESSION['role'] = $user['role'];
$_SESSION['instructor_name'] = $user['name'] ?? 'Instructor';
$_SESSION['instructor_email'] = $user['email'];

header("Location: dashboard.php");
exit;
