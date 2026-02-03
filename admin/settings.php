<?php
include '../config/db.php';
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}
include '../navbar.php';

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
    if (!empty($_FILES['logo']['name'])) {
        $target_dir = '../assets/';
        $logo_file = $target_dir . basename($_FILES['logo']['name']);
        if (move_uploaded_file($_FILES['logo']['tmp_name'], $logo_file)) {
            $logo_sql = ", logo_path='".mysqli_real_escape_string($conn, $logo_file)."'";
        }
    }
    $update = mysqli_query($conn, "UPDATE settings SET site_name='$new_name', admin_email='$new_email', contact_number='$new_contact' $logo_sql");
    if ($update) $msg = '<div class="alert alert-success">Settings updated.</div>';

    // Update Admin Password
    if (!empty($_POST['admin_password'])) {
        $new_pass = password_hash($_POST['admin_password'], PASSWORD_DEFAULT);
        $update_pass = mysqli_query($conn, "UPDATE users SET password='$new_pass' WHERE role='admin'");
        if ($update_pass) $msg .= '<div class="alert alert-success">Admin password updated.</div>';
    }
    // Refresh settings
    $settings = mysqli_query($conn, "SELECT * FROM settings LIMIT 1");
    $settings_data = mysqli_fetch_assoc($settings);
    $site_name = $settings_data ? $settings_data['site_name'] : 'Polymath Path';
    $admin_email = $settings_data ? $settings_data['admin_email'] : '';
    $contact_number = $settings_data ? $settings_data['contact_number'] : '';
    $logo_path = $settings_data ? $settings_data['logo_path'] : '';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Settings | Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f4f7f6; font-family: 'Poppins', sans-serif; }
        .sidebar-fixed, .sidebar { background: #1e40af !important; color: #FFD700; min-height: 100vh; }
        .navbar, .navbar.bg-polymath {
            background-color: #1e40af !important;
        }
        .btn, .btn-polymath, .btn-success, .btn-outline-primary, .btn-outline-info, .btn-outline-secondary {
            background-color: #3b82f6 !important;
            color: #fff !important;
            border: none !important;
        }
        .btn:hover, .btn-polymath:hover, .btn-success:hover, .btn-outline-primary:hover, .btn-outline-info:hover, .btn-outline-secondary:hover {
            background-color: #2563eb !important;
            color: #fff !important;
        }
        .sidebar-fixed a.active, .sidebar-fixed a:hover, .nav-link.active, .nav-link:focus {
            background: #60a5fa !important;
            color: #1e40af !important;
            border-left: 4px solid #60a5fa !important;
        }
        .logo-circle {
            background: #fff !important;
            color: #1e40af !important;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
        }
        .dropdown-menu .dropdown-item {
            color: #1f2937 !important;
        }
        .dropdown-menu .dropdown-item i {
            color: #1e40af !important;
        }
    </style>
</head>
<body>
<div class="d-flex" style="min-height:100vh;">
    <?php include 'sidebar.php'; ?>
    <div class="main-content" style="margin-left:260px; width:calc(100% - 260px); padding:20px; background:#f4f7f6;">
        <h2 class="fw-bold mb-4" style="color:#003366;">Settings</h2>
        <?php echo $msg; ?>
        <form method="post" enctype="multipart/form-data" class="mb-4">
            <div class="mb-3">
                <label class="form-label">Site Name</label>
                <input type="text" name="site_name" class="form-control" value="<?php echo htmlspecialchars($site_name); ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Admin Email</label>
                <input type="email" name="admin_email" class="form-control" value="<?php echo htmlspecialchars($admin_email); ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Contact Number</label>
                <input type="text" name="contact_number" class="form-control" value="<?php echo htmlspecialchars($contact_number); ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">Logo</label><br>
                <?php if ($logo_path) echo '<img src="'.$logo_path.'" alt="Logo" style="max-height:60px;">'; ?>
                <input type="file" name="logo" class="form-control mt-2">
            </div>
            <div class="mb-3">
                <label class="form-label">New Admin Password</label>
                <input type="password" name="admin_password" class="form-control" placeholder="Leave blank to keep unchanged">
            </div>
            <button type="submit" class="btn btn-polymath">Save Changes</button>
        </form>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
