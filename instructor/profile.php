<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

error_reporting(E_ALL);
ini_set('display_errors', 1);

include "../config/db.php";
include "header.php";

/* AUTH CHECK */
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'instructor') {
    header("Location: login.php");
    exit;
}

$user_id = (int)$_SESSION['user_id'];

/* FETCH TEACHER */
$q = mysqli_query($conn, "
    SELECT id, name, email, created_at
    FROM users
    WHERE id = $user_id
");

$user = mysqli_fetch_assoc($q);
?>

<div class="container mt-5">

    <h2 class="mb-4">Teacher Profile</h2>

    <!-- PROFILE CARD -->
    <div class="card">
        <div class="card-body d-flex justify-content-between align-items-center">

            <!-- LEFT SIDE -->
            <div class="d-flex gap-4 align-items-center">

                <!-- Avatar -->
                <div class="rounded-circle bg-light d-flex align-items-center justify-content-center"
                     style="width:72px;height:72px;font-size:26px;font-weight:600;">
                    <?= strtoupper(substr($user['name'], 0, 1)) ?>
                </div>

                <!-- Info -->
                <div>
                    <h5 class="mb-0"><?= htmlspecialchars($user['name']) ?></h5>
                    <p class="text-muted mb-2"><?= htmlspecialchars($user['email']) ?></p>

                    <small class="text-muted">Account ID</small><br>
                    <strong><?= $user['id'] ?></strong>
                </div>

            </div>

            <!-- RIGHT SIDE -->
            <div class="text-end">
                <a href="edit_profile.php"
                   class="btn btn-outline-primary btn-sm mb-2">
                    Edit Profile
                </a>

                <div>
                    <small class="text-muted">Member Since</small><br>
                    <strong><?= date("m/d/Y", strtotime($user['created_at'])) ?></strong>
                </div>
            </div>

        </div>
    </div>

</div>

<?php include "footer.php"; ?>
