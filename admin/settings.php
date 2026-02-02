<?php
include '../config/db.php';
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}
include 'admin_navbar.php';

// Fetch current settings
$settings = mysqli_query($conn, "SELECT * FROM settings LIMIT 1");
$settings_data = mysqli_fetch_assoc($settings);
$institute_name = $settings_data ? $settings_data['institute_name'] : 'Polymath Path';
$logo_path = $settings_data ? $settings_data['logo_path'] : '';

// Handle form submission
$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Update Institute Name
    $new_name = mysqli_real_escape_string($conn, $_POST['institute_name']);
    $logo_sql = '';
    if (!empty($_FILES['logo']['name'])) {
        $target_dir = '../assets/';
        $logo_file = $target_dir . basename($_FILES['logo']['name']);
        if (move_uploaded_file($_FILES['logo']['tmp_name'], $logo_file)) {
            $logo_sql = ", logo_path='".mysqli_real_escape_string($conn, $logo_file)."'";
        }
    }
    $update = mysqli_query($conn, "UPDATE settings SET institute_name='$new_name' $logo_sql");
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
    $institute_name = $settings_data ? $settings_data['institute_name'] : 'Polymath Path';
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
        .sidebar { background: #003366; color: #FFD700; min-height: 100vh; }
        .btn-polymath { background-color: #003366; color: #FFD700; border: none; }
        .btn-polymath:hover { background-color: #FFD700; color: #003366; }
    </style>
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-2 sidebar p-4">
            <?php include 'sidebar.php'; ?>
        </div>
        <div class="col-md-10 p-4">
            <h2 class="fw-bold mb-4" style="color:#003366;">Settings</h2>
            <?php echo $msg; ?>
            <form method="post" enctype="multipart/form-data" class="mb-4">
                <div class="mb-3">
                    <label class="form-label">Institute Name</label>
                    <input type="text" name="institute_name" class="form-control" value="<?php echo htmlspecialchars($institute_name); ?>" required>
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
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
