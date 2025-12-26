<?php
session_start();
require_once "../config/db.php";

$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

if ($email === '' || $password === '') {
    die("Email or password missing");
}

$stmt = $pdo->prepare(
    "SELECT * FROM users 
     WHERE email = :email AND role = 'instructor'
     LIMIT 1"
);
$stmt->execute(['email' => $email]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die("Instructor not found");
}

if (!password_verify($password, $user['password_hash'])) {
    die("Wrong password");
}

$_SESSION['instructor_id'] = $user['id'];
$_SESSION['instructor_name'] = $user['name'];

header("Location: index.php");
exit;
