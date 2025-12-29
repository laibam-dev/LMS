<?php
session_start();
require_once "../config/db.php";

if (isset($_POST['login'])) {

    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if ($email == "" || $password == "") {
        header("Location: login.php?error=empty");
        exit;
    }

    // IMPORTANT: users table + role = teacher
    $sql = "SELECT * FROM users WHERE email = ? AND role = 'teacher' LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();

        // Password verify
        if (password_verify($password, $row['password'])) {

            // Instructor session
            $_SESSION['instructor_id']   = $row['id'];
            $_SESSION['instructor_name'] = $row['name'];

            header("Location: index.php");
            exit;

        } else {
            header("Location: login.php?error=invalid");
            exit;
        }

    } else {
        header("Location: login.php?error=invalid");
        exit;
    }
}
