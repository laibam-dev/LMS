<?php
session_start();
include '../config/db.php';

// Agar admin login nahi hai toh login page par bhej dein
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $message_text = mysqli_real_escape_string($conn, $_POST['message']);
    $role = $_POST['target_role'];
    $admin_id = $_SESSION['admin_id'];

    $query = "INSERT INTO announcements (title, message, target_role, admin_id) 
              VALUES ('$title', '$message_text', '$role', '$admin_id')";

    if (mysqli_query($conn, $query)) {
        // Activity Log record karein
        log_activity($conn, $admin_id, 'Announcement', "Admin posted: $title");

        echo "<script>alert('Announcement Sent Successfully!'); window.location.href='index.php';</script>";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Post Announcement | Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-light">
    
    <?php include '../navbar.php'; ?> 
    <?php include 'sidebar.php'; ?>

    <div style="margin-left: 280px; padding: 40px; margin-top: 20px;">
        <div class="container-fluid">
            <div class="card shadow border-0 p-4" style="border-radius: 15px;">
                <h3 class="mb-4 fw-bold" style="color: #1e40af;">📢 Post New Announcement</h3>
                
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Announcement Title</label>
                        <input type="text" name="title" class="form-control shadow-sm" required placeholder="e.g. Website Maintenance">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Message Content</label>
                        <textarea name="message" class="form-control shadow-sm" rows="5" required placeholder="Write your message here..."></textarea>
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label fw-bold">Target Audience</label>
                        <select name="target_role" class="form-select shadow-sm">
                            <option value="all">Everyone (All Users)</option>
                            <option value="student">Students Only</option>
                            <option value="instructor">Instructors Only</option>
                        </select>
                    </div>
                    
                    <button type="submit" class="btn btn-primary px-5 py-2 shadow-sm" style="background-color: #1e40af; border: none;">
                        <i class="fas fa-paper-plane me-2"></i> Send Announcement
                    </button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>