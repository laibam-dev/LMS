<?php
session_start();
// Path theek kiya: 3 level piche config folder tak jane ke liye
// 3 ki jagah sirf 2 baar ../ use karein
require_once __DIR__ . '/../../config/db.php';
// Check karein ke student login hai ya nahi. Redirect ke liye BASE_URL use kiya.
if (!isset($_SESSION['student_id'])) {
    header("Location: " . BASE_URL . "student/login.php");
    exit;
}

// Announcements fetch karein jo 'all' ya sirf 'student' ke liye hon
$query = "SELECT * FROM announcements WHERE target_role IN ('all', 'student') ORDER BY created_at DESC";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Announcements | Polymath Path</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { background: #f4f7f6; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .announcement-card { 
            background: white;
            border: none;
            border-left: 5px solid #1e40af; 
            border-radius: 12px; 
            margin-bottom: 25px; 
            box-shadow: 0 4px 12px rgba(0,0,0,0.08); 
            transition: transform 0.2s;
        }
        .announcement-card:hover { transform: translateY(-3px); }
        .badge-all { background-color: #10b981; }
        .badge-student { background-color: #3b82f6; }
        .back-link { text-decoration: none; color: #1e40af; font-weight: 600; }
    </style>
</head>
<body>
    <div class="container mt-5 mb-5" style="max-width: 800px;">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 style="color: #1e40af; font-weight: bold; margin: 0;">📢 Recent Announcements</h2>
            <a href="dashboard.php" class="back-link"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
        </div>
        
        <?php if(mysqli_num_rows($result) > 0): ?>
            <?php while($row = mysqli_fetch_assoc($result)): ?>
                <div class="card announcement-card">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4 class="card-title fw-bold m-0" style="color: #0f172a;"><?php echo htmlspecialchars($row['title']); ?></h4>
                            <span class="badge rounded-pill <?php echo $row['target_role'] == 'all' ? 'badge-all' : 'badge-student'; ?>">
                                <?php echo ucfirst($row['target_role']); ?>
                            </span>
                        </div>
                        <p class="card-text" style="color: #334155; line-height: 1.6;">
                            <?php echo nl2br(htmlspecialchars($row['message'])); ?>
                        </p>
                        <hr style="border-top: 1px solid #e2e8f0; margin: 15px 0;">
                        <small class="text-secondary">
                            <i class="far fa-clock"></i> Posted on: <?php echo date('d M Y, h:i A', strtotime($row['created_at'])); ?>
                        </small>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="alert alert-info text-center">No announcements found.</div>
        <?php endif; ?>
    </div>
</body>
</html>