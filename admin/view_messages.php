<?php
session_start();
include '../config/db.php';

// 1. Auth Check using BASE_URL
if (!isset($_SESSION['admin_id'])) {
    header('Location: ' . BASE_URL . 'admin/login.php');
    exit;
}

// 2. Mark all as read when page is opened
mysqli_query($conn, "UPDATE contact_messages SET status = 'read' WHERE status = 'unread'");

// 3. Fetch all messages
$query = "SELECT * FROM contact_messages ORDER BY created_at DESC";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Messages | Polymath Path Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body { background: #f4f7f6; font-family: 'Poppins', sans-serif; }
        .sidebar-fixed { background: #1e40af !important; width: 280px; position: fixed; height: 100vh; }
        .main-content { margin-left: 280px; width: calc(100% - 280px); padding: 40px; }
        .message-card { background: white; border-radius: 15px; padding: 25px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); border: 1px solid #eee; }
        .table thead { background-color: #1e40af; color: white; }
        .status-badge { font-size: 0.8rem; padding: 5px 12px; border-radius: 20px; }
        @media (max-width: 992px) { .main-content { margin-left: 0; width: 100%; } }
    </style>
</head>
<body>

<?php include '../navbar.php'; ?>

<div class="d-flex">
    <?php include 'sidebar.php'; ?>

    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold mb-0" style="color: #1e40af;"><i class="fas fa-envelope-open-text me-2"></i> Contact Requests</h2>
            <a href="<?php echo BASE_URL; ?>admin/index.php" class="btn btn-outline-primary shadow-sm" style="border-radius: 10px;">
                <i class="fas fa-arrow-left me-1"></i> Dashboard
            </a>
        </div>
        
        <div class="message-card">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th class="py-3">Sender Name</th>
                            <th class="py-3">Email Address</th>
                            <th class="py-3">Message Snippet</th>
                            <th class="py-3">Status</th>
                            <th class="py-3">Date Received</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(mysqli_num_rows($result) > 0): ?>
                            <?php while($row = mysqli_fetch_assoc($result)): ?>
                            <tr>
                                <td class="fw-bold"><?php echo htmlspecialchars($row['full_name']); ?></td>
                                <td><a href="mailto:<?php echo $row['email']; ?>" class="text-decoration-none text-primary"><?php echo htmlspecialchars($row['email']); ?></a></td>
                                <td>
                                    <span class="text-muted" title="<?php echo htmlspecialchars($row['message']); ?>">
                                        <?php echo substr(htmlspecialchars($row['message']), 0, 50) . (strlen($row['message']) > 50 ? '...' : ''); ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge status-badge <?php echo ($row['status'] == 'unread') ? 'bg-warning text-dark' : 'bg-success'; ?>">
                                        <i class="fas <?php echo ($row['status'] == 'unread') ? 'fa-clock' : 'fa-check-double'; ?> me-1"></i>
                                        <?php echo ucfirst($row['status']); ?>
                                    </span>
                                </td>
                                <td class="small text-muted">
                                    <?php echo date('d M Y, h:i A', strtotime($row['created_at'])); ?>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">
                                    <i class="fas fa-inbox fa-3x mb-3"></i><br>No messages found in the database.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>