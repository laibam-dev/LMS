<?php
session_start();
include '../config/db.php';

// 1. Auth Check using BASE_URL
if (!isset($_SESSION['admin_id'])) {
    header('Location: ' . BASE_URL . 'admin/login.php');
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
        // Activity Log record karein (Check if function exists to prevent error)
        if (function_exists('log_activity')) {
            log_activity($conn, $admin_id, 'Announcement', "Admin posted: $title");
        }

        echo "<script>alert('Announcement Sent Successfully!'); window.location.href='" . BASE_URL . "admin/index.php';</script>";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Post Announcement | Polymath Path Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { background: #f4f7f6; font-family: 'Poppins', sans-serif; overflow-x: hidden; }
        .main-content { margin-left: 280px; padding: 40px; margin-top: 20px; width: calc(100% - 280px); }
        .card { border-radius: 15px; border: none; }
        .form-control, .form-select { border-radius: 10px; padding: 12px; border: 1.5px solid #eee; }
        .form-control:focus { border-color: #1e40af; box-shadow: none; }
        
        /* Mobile adjustment */
        @media (max-width: 992px) {
            .main-content { margin-left: 0; width: 100%; padding: 20px; }
        }
    </style>
</head>
<body>
    
    <?php include '../navbar.php'; ?> 
    <div class="d-flex">
        <?php include 'sidebar.php'; ?>

        <div class="main-content">
            <div class="container-fluid">
                <div class="card shadow p-4">
                    <div class="d-flex align-items-center mb-4">
                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                            <i class="fas fa-bullhorn fa-lg"></i>
                        </div>
                        <h3 class="mb-0 fw-bold" style="color: #1e40af;">Post New Announcement</h3>
                    </div>
                    
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Announcement Title</label>
                            <input type="text" name="title" class="form-control" required placeholder="e.g. Website Maintenance or Exam Schedule">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">Message Content</label>
                            <textarea name="message" class="form-control" rows="5" required placeholder="Describe the announcement in detail..."></textarea>
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label fw-bold">Target Audience</label>
                            <select name="target_role" class="form-select">
                                <option value="all">Everyone (All Users)</option>
                                <option value="student">Students Only</option>
                                <option value="instructor">Instructors Only</option>
                            </select>
                            <div class="form-text mt-2">This message will appear on the dashboards of the selected audience.</div>
                        </div>
                        
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary px-5 py-2" style="background-color: #1e40af; border: none; border-radius: 10px;">
                                <i class="fas fa-paper-plane me-2"></i> Send Announcement
                            </button>
                            <a href="<?php echo BASE_URL; ?>admin/index.php" class="btn btn-light px-4 py-2" style="border-radius: 10px;">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>