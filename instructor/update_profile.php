<?php
require_once "session.php";
require_once "../config/db.php";

$instructor_id = $_SESSION['instructor_id'] ?? 0;

if ($instructor_id <= 0) {
    header("Location: login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: profile.php");
    exit;
}

$name  = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');

if ($name === "" || $email === "") {
    header("Location: profile.php?error=" . urlencode("Please fill all fields"));
    exit;
}

/* ✅ Update user table */
$stmt = $conn->prepare("UPDATE users SET name=?, email=? WHERE id=? AND role='instructor' LIMIT 1");
$stmt->bind_param("ssi", $name, $email, $instructor_id);

if ($stmt->execute()) {

    // ✅ Session update so sidebar/profile show updated name/email
    $_SESSION['instructor_name']  = $name;
    $_SESSION['instructor_email'] = $email;

    $stmt->close();
    header("Location: profile.php?success=" . urlencode("Profile updated successfully"));
    exit;

} else {
    $stmt->close();
    header("Location: profile.php?error=" . urlencode("Failed to update profile"));
    exit;
}
