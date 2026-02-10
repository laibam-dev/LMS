<?php
require_once "../config/db.php";
require_once "../config/base.php";
require_once "session.php";


$instructor_id = $_SESSION['instructor_id'] ?? 0;

if ($instructor_id <= 0) {
    header("Location: login.php");
    exit;
}

/* Fetch instructor data */
$stmt = $conn->prepare("SELECT id, name, email, created_at FROM users WHERE id=? AND role='instructor' LIMIT 1");
$stmt->bind_param("i", $instructor_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$user) {
    header("Location: logout.php");
    exit;
}

$instructor_name  = $user['name'];
$instructor_email = $user['email'];
$member_since     = !empty($user['created_at']) ? date("m/d/Y", strtotime($user['created_at'])) : "--";

$success = $_GET['success'] ?? '';
$error   = $_GET['error'] ?? '';

require_once "header.php";
require_once "sidebar.php";
?>

<div class="main">

    <div class="top">
        <div>
            <h1>Profile</h1>
            <p>Manage your teacher profile</p>
        </div>
    </div>

    <?php if (!empty($success)): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>

    <?php if (!empty($error)): ?>
        <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <div class="profile-grid">

        <!-- LEFT CARD -->
        <div class="profile-card-left">
            <h2>Teacher Profile</h2>

            <div class="profile-user">
                <div class="profile-avatar">
                    <?php echo strtoupper(substr($instructor_name, 0, 1)); ?>
                </div>

                <div>
                    <h3 style="margin:0;"><?php echo htmlspecialchars($instructor_name); ?></h3>
                    <p style="margin:4px 0 0 0; color:#6b7280;"><?php echo htmlspecialchars($instructor_email); ?></p>
                </div>
            </div>

            <div class="profile-meta">
                <div class="meta-row">
                    <span>Account ID</span>
                    <b><?php echo (int)$user['id']; ?></b>
                </div>

                <div class="meta-row">
                    <span>Member Since</span>
                    <b><?php echo htmlspecialchars($member_since); ?></b>
                </div>
            </div>
        </div>

        <!-- RIGHT WHITE CARD (FIXED ✅) -->
        <div class="profile-card-right">

            <h2>Update Profile</h2>

            <form method="POST" action="update_profile.php" class="form-modern">
                <div class="form-group">
                    <label>Name</label>
                    <input type="text" name="name" value="<?php echo htmlspecialchars($instructor_name); ?>" required>
                </div>

                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" value="<?php echo htmlspecialchars($instructor_email); ?>" required>
                </div>

                <button type="submit" class="btn-primary">Save Changes</button>
            </form>

            <hr class="profile-divider">

            <h2>Update Password</h2>

            <form method="POST" action="update_password.php" class="form-modern">
                <div class="form-row">
                    <div class="form-group">
                        <label>Current Password</label>
                        <input type="password" name="current_password" required>
                    </div>

                    <div class="form-group">
                        <label>New Password</label>
                        <input type="password" name="new_password" required>
                    </div>

                    <div class="form-group">
                        <label>Confirm New Password</label>
                        <input type="password" name="confirm_password" required>
                    </div>
                </div>

                <button type="submit" class="btn-primary">Update Password</button>
            </form>

        </div>

    </div>

</div>

<style>
/* ✅ Layout */
.profile-grid{
    display: grid;
    grid-template-columns: 360px 1fr;
    gap: 22px;
    align-items: start;
}

/* ✅ Left card */
.profile-card-left{
    background:#fff;
    padding:22px;
    border-radius:18px;
    box-shadow:0 10px 28px rgba(0,0,0,0.06);
    border:1px solid #eef2ff;
}

.profile-user{
    display:flex;
    gap:14px;
    align-items:center;
    margin-top:16px;
}

.profile-avatar{
    width:54px;
    height:54px;
    border-radius:16px;
    background:#eef2ff;
    display:flex;
    align-items:center;
    justify-content:center;
    font-weight:700;
    font-size:18px;
    color:#2563eb;
}

.profile-meta{
    margin-top:18px;
    display:flex;
    flex-direction:column;
    gap:10px;
}

.meta-row{
    display:flex;
    justify-content:space-between;
    padding:12px 14px;
    border:1px solid #eef2ff;
    border-radius:14px;
    background:#f8fafc;
}

/* ✅ Right card (THIS IS YOUR FIX ✅) */
.profile-card-right{
    background:#fff;
    padding:22px;
    border-radius:18px;
    box-shadow:0 10px 28px rgba(0,0,0,0.06);
    border:1px solid #eef2ff;
}

/* Divider */
.profile-divider{
    border:none;
    height:1px;
    background:#eef2ff;
    margin:18px 0;
}
</style>

<?php require_once "footer.php"; ?>
