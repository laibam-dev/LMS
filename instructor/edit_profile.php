<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

error_reporting(E_ALL);
ini_set('display_errors', 1);

include "../config/db.php";
include "header.php";

/* AUTH */
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'instructor') {
    header("Location: login.php");
    exit;
}

$user_id = (int)$_SESSION['user_id'];

/* FETCH USER */
$q = mysqli_query($conn, "
    SELECT name, email
    FROM users
    WHERE id = $user_id
");

$user = mysqli_fetch_assoc($q);
?>

<div class="container mt-5" style="max-width:600px;">

    <h2 class="text-center mb-4">Edit Profile</h2>

    <!-- CARD -->
    <div class="card">
        <div class="card-body p-4">

            <form method="POST" action="edit_profile_action.php">

                <!-- FULL NAME -->
                <div class="mb-3">
                    <label class="form-label">Full Name</label>
                    <input type="text"
                           name="name"
                           class="form-control"
                           value="<?= htmlspecialchars($user['name']) ?>"
                           required>
                </div>

                <!-- EMAIL -->
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email"
                           name="email"
                           class="form-control"
                           value="<?= htmlspecialchars($user['email']) ?>"
                           required>
                </div>

                <!-- PASSWORD -->
                <div class="mb-4">
                    <label class="form-label">
                        New Password
                        <small class="text-muted">(leave blank to keep current)</small>
                    </label>
                    <input type="password"
                           name="password"
                           class="form-control">
                </div>

                <!-- BUTTONS -->
                <div class="d-flex justify-content-center gap-3">
                    <a href="profile.php"
                       class="btn btn-outline-secondary"
                       style="width:180px;">
                        Cancel
                    </a>

                    <button type="submit"
                            class="btn btn-primary"
                            style="width:180px;">
                        Update Profile
                    </button>
                </div>

            </form>

        </div>
    </div>

</div>

<?php include "footer.php"; ?>
