<?php
include '../config/db.php';
session_start();

// 1. Auth Check: Agar login NAHI hai, toh login page par bhej do
if (!isset($_SESSION['admin_id'])) {
    header('Location: ' . BASE_URL . 'admin/login.php');
    exit;
}

$admin_id = $_SESSION['admin_id'];
$success = '';
$error = '';

// 2. Profile Update aur Image Upload Logic
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $img_sql = "";

    // Image handling
    if (!empty($_FILES['profile_pic']['name'])) {
        $target_dir = "../assets/profiles/";
        if (!is_dir($target_dir)) mkdir($target_dir, 0777, true); 

        $file_extension = pathinfo($_FILES['profile_pic']['name'], PATHINFO_EXTENSION);
        $file_name = "admin_" . $admin_id . "_" . time() . "." . $file_extension;
        $target_file = $target_dir . $file_name;

        if (move_uploaded_file($_FILES['profile_pic']['tmp_name'], $target_file)) {
            // Database mein relative path store karein
            $db_path = "assets/profiles/" . $file_name;
            $img_sql = ", profile_pic='$db_path'";
        } else {
            $error = "Failed to upload image.";
        }
    }

    $update_query = "UPDATE users SET name='$name', email='$email' $img_sql WHERE id='$admin_id'";
    if (mysqli_query($conn, $update_query)) {
        $success = "Profile updated successfully!";
        $_SESSION['admin_name'] = $name; 
    } else {
        $error = "Database error: " . mysqli_error($conn);
    }
}

// 3. Fetch Fresh Details
$query = mysqli_query($conn, "SELECT * FROM users WHERE id = '$admin_id'");
$admin = mysqli_fetch_assoc($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile | Polymath Path</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { background: #f4f7f6; font-family: 'Poppins', sans-serif; overflow-x: hidden; }
        .main-content { margin-left: 260px; width: calc(100% - 260px); padding: 30px; margin-top: 20px; }
        .profile-card { background: white; border-radius: 20px; padding: 40px; box-shadow: 0 4px 18px rgba(30,64,175,0.07); text-align: center; border: 1px solid #eee; height: 100%; }
        .profile-img { width: 150px; height: 150px; border-radius: 50%; object-fit: cover; border: 5px solid #3b82f6; margin-bottom: 20px; background: #eee; }
        .form-control { border-radius: 12px; padding: 12px; border: 1.5px solid #e0e0e0; }
        .form-control:focus { border-color: #3b82f6; box-shadow: none; }
    </style>
</head>
<body>

<?php include '../navbar.php'; ?>

<div class="d-flex">
    <?php include 'sidebar.php'; ?>
       
    <div class="main-content">
        <h2 class="fw-bold mb-4" style="color: #1e40af;">My Profile</h2>
        
        <?php if($success): ?>
            <div class='alert alert-success alert-dismissible fade show' style="border-radius: 15px;">
                <i class="fas fa-check-circle me-2"></i> <?php echo $success; ?>
                <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
            </div>
        <?php endif; ?>

        <?php if($error): ?>
            <div class='alert alert-danger' style="border-radius: 15px;"><?php echo $error; ?></div>
        <?php endif; ?>

        <div class="row g-4">
            <div class="col-md-4">
                <div class="profile-card">
                    <?php 
                        $user_img = $admin['profile_pic']; 
                        $display_img = (!empty($user_img)) ? BASE_URL . $user_img : BASE_URL . 'assets/default-avatar.png';
                    ?>
                    <img src="<?php echo $display_img; ?>?v=<?php echo time(); ?>" class="profile-img shadow" alt="Admin">
                    
                    <h4 class="fw-bold mb-1"><?php echo htmlspecialchars($admin['name']); ?></h4>
                    <span class="badge bg-primary px-3 py-2 mb-3" style="border-radius: 20px;"><?php echo strtoupper($admin['role']); ?></span>
                    <hr>
                    <div class="text-start mt-3">
                        <p class="mb-1 small text-muted text-uppercase">Email Address</p>
                        <p class="fw-bold"><?php echo htmlspecialchars($admin['email']); ?></p>
                        <p class="mb-1 small text-muted text-uppercase">Joined Date</p>
                        <p class="fw-bold"><?php echo date('d M, Y', strtotime($admin['created_at'])); ?></p>
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <div class="profile-card text-start">
                    <h5 class="fw-bold mb-4" style="color: #1e40af;"><i class="fa fa-user-edit me-2"></i> Update Personal Information</h5>
                    
                    <form method="POST" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Full Name</label>
                                <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($admin['name']); ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Email Address</label>
                                <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($admin['email']); ?>" required>
                            </div>
                            <div class="col-md-12 mb-4">
                                <label class="form-label fw-bold">Change Profile Picture</label>
                                <input type="file" name="profile_pic" class="form-control" accept="image/*">
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary px-5 py-2 shadow-sm" style="border-radius: 10px; background-color: #1e40af; border:none;">
                            <i class="fa fa-save me-2"></i> Save Changes
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>