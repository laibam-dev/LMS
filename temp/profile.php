<?php
include 'session.php';       // Session check
include 'header.php';        // Top header




include '../config/db.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

$admin_id = $_SESSION['admin_id'];

// Fetch admin data
$sql = "SELECT name, email FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$admin = $stmt->get_result()->fetch_assoc();


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Profile</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background:#f5f6fa;
        }
        .container {
            width: 500px;
            margin: 60px auto;
            background:#fff;
            padding:30px;
            border-radius:10px;
            box-shadow:0 5px 15px rgba(0,0,0,0.1);
        }
        h2 {
            text-align:center;
            margin-bottom:20px;
        }
        input {
            width:100%;
            padding:10px;
            margin:8px 0;
            border-radius:5px;
            border:1px solid #ccc;
        }
        button {
            width:100%;
            padding:10px;
            background:#40739e;
            color:#fff;
            border:none;
            border-radius:5px;
            cursor:pointer;
        }
        button:hover {
            background:#2f3640;
        }
        .info {
            font-size:14px;
            color:#555;
            margin-bottom:10px;
        }
        .success {
            color:green;
            text-align:center;
        }
        .nav {
            text-align:center;
            margin-bottom:20px;
        }
        .nav a {
            margin:0 10px;
            text-decoration:none;
            color:#40739e;
        }
    </style>
</head>
<body>

<div class="container">

    <div class="nav">
        <a href="dashboard.php">Dashboard</a>
        <a href="profile.php">Profile</a>
        <a href="logout.php">Logout</a>
    </div>

    <h2>Admin Profile</h2>

    <?php if (isset($success)) { ?>
        <p class="success"><?php echo $success; ?></p>
    <?php } ?>

    <form method="POST">
        <label>Name</label>
        <input type="text" name="name" value="<?php echo htmlspecialchars($admin['name']); ?>" required>

        <label>Email (cannot be changed)</label>
        <input type="email" value="<?php echo htmlspecialchars($admin['email']); ?>" disabled>

        <label>New Password (leave blank if not changing)</label>
        <input type="password" name="password">

        <button type="submit" name="update">Update Profile</button>
    </form>

</div>

</body>
</html>