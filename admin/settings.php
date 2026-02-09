<?php
include '../config/db.php';
session_start();

// 1. Auth Check using BASE_URL
if (!isset($_SESSION['admin_id'])) {
    header('Location: ' . BASE_URL . 'admin/login.php');
    exit;
}

$admin_id = $_SESSION['admin_id'];

// Fetch current settings
$settings = mysqli_query($conn, "SELECT * FROM settings LIMIT 1");
$settings_data = mysqli_fetch_assoc($settings);

$site_name = $settings_data ? $settings_data['site_name'] : 'Polymath Path';
$admin_email = $settings_data ? $settings_data['admin_email'] : '';
$contact_number = $settings_data ? $settings_data['contact_number'] : '';
$logo_path = $settings_data ? $settings_data['logo_path'] : '';

// Handle form submission
$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_name = mysqli_real_escape_string($conn, $_POST['site_name']);
    $new_email = mysqli_real_escape_string($conn, $_POST['admin_email']);
    $new_contact = mysqli_real_escape_string($conn, $_POST['contact_number']);
    $logo_sql = '';

    // Logo Upload Logic
    if (!empty($_FILES['logo']['name'])) {
        $target_dir = '../assets/';
        $file_ext = pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION);
        $new_logo_name = "site_logo_" . time() . "." . $file_ext;
        $target_file = $target_dir . $new_logo_name;

        if (move_uploaded_file($_FILES['logo']['tmp_name'], $target_file)) {
            // DB mein relative path store karenge
            $db_logo_path = 'assets/' . $new_logo_name;
            $logo_sql = ", logo_path='$db_logo_path'";
        }
    }

    $update = mysqli_query($conn, "UPDATE settings SET site_name='$new_name', admin_email='$new_email', contact_number='$new_contact' $logo_sql");
    
    if ($update) {
        $msg = '<div class="alert alert-success alert-dismissible fade show"><i class="fas fa-check-circle me-2"></i>Settings updated successfully!<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
    }

    // Update Admin Password (Security Fix: Specific Admin ID)
    if (!empty($_POST['admin_password'])) {
        $new_pass = password_hash($_POST['admin_password'], PASSWORD_DEFAULT);
        mysqli_query($conn, "UPDATE users SET password_hash='$new_pass' WHERE id='$admin_id'");
        $msg .= '<div class="alert alert-info alert-dismissible fade show"><i class="fas fa-key me-2"></i>Admin password also updated.<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
    }

    // Refresh data after update
    $settings = mysqli_query($conn, "SELECT * FROM settings LIMIT 1");
    $settings_data = mysqli_fetch_assoc($settings);
    $site_name = $settings_data['site_name'];
    $admin_email = $settings_data['admin_email'];
    $contact_number = $settings_data['contact_number'];
    $logo_path = $settings_data['logo_path'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings | Polymath Path Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body { background: #f4f7f6; font-family: 'Poppins', sans-serif; overflow-x: hidden; }
        .sidebar-fixed { background: #1e40af !important; width: 280px; position: fixed; height: 100vh; }
        .main-content { margin-left: 280px; width: calc(100% - 280px); padding: 40px; }
        .settings-card { background: white; border-radius: 15px; padding: 30px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); border: 1px solid #eee; margin-bottom: 25px; }
        .form-label { font-weight: 600; color: #1e40af; }
        .form-control { border-radius: 10px; padding: 10px 15px; border: 1.5px solid #eef2f6; }
        .form-control:focus { border-color: #3b82f6; box-shadow: none; }
        .navbar { position: sticky; top: 0; z-index: 1000; }
        @media (max-width: 992px) { .main-content { margin-left: 0; width: 100%; } }
    </style>
</head>
<body>

<?php include '../navbar.php'; ?>

<div class="d-flex">
    <?php include 'sidebar.php'; ?>

    <div class="main-content">
        <h2 class="fw-bold mb-4" style="color:#1e40af;"><i class="fas fa-cog me-2"></i>System Settings</h2>
        
        <?php echo $msg; ?>

        <form method="post" enctype="multipart/form-data">
            <div class="settings-card">
                <h5 class="fw-bold mb-4" style="color: #1e40af;"><i class="fa fa-info-circle me-2"></i> General Information</h5>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Site Name</label>
                        <input type="text" name="site_name" class="form-control" value="<?php echo htmlspecialchars($site_name); ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Support Email Address</label>
                        <input type="email" name="admin_email" class="form-control" value="<?php echo htmlspecialchars($admin_email); ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Contact Number</label>
                        <input type="text" name="contact_number" class="form-control" value="<?php echo htmlspecialchars($contact_number); ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Platform Logo</label><br>
                        <?php if ($logo_path): ?>
                            <div class="mb-2 p-2 border rounded d-inline-block bg-light">
                                <img src="<?php echo BASE_URL . $logo_path; ?>" alt="Logo" style="max-height:40px;">
                            </div>
                        <?php endif; ?>
                        <input type="file" name="logo" class="form-control" accept="image/*">
                    </div>
                </div>
            </div>

            <div class="settings-card">
                <h5 class="fw-bold mb-4" style="color: #1e40af;"><i class="fa fa-lock me-2"></i> Security & Admin Access</h5>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">New Admin Password</label>
                        <input type="password" name="admin_password" class="form-control" placeholder="Leave blank to keep current password">
                        <small class="text-muted">Updating this will change your login password immediately.</small>
                    </div>
                </div>
            </div>

            <div class="d-grid d-md-flex justify-content-md-start">
                <button type="submit" class="btn btn-primary px-5 py-3 shadow-sm" style="border-radius: 12px; background-color: #1e40af; border: none; font-weight: 600;">
                    <i class="fas fa-save me-2"></i> Save All Platform Changes
                </button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>