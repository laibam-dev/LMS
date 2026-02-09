<?php
session_start();
include '../config/db.php'; // BASE_URL yahan se define ho jayega

// Agar admin pehle se logged in hai, toh seedha dashboard bhej do
if (isset($_SESSION['admin_id'])) {
    header('Location: ' . BASE_URL . 'admin/index.php');
    exit;
}

$error = ''; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    $sql = "SELECT id, name, email, password_hash, role FROM users WHERE email=? LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        if ($row['role'] !== 'admin') {
            $error = 'Access Denied: User is not an admin.';
        } elseif (!password_verify($password, $row['password_hash'])) {
            $error = 'Invalid password.';
        } else {
            $_SESSION['admin_id'] = $row['id'];
            $_SESSION['admin_name'] = $row['name'];
            
            // Success redirect using BASE_URL
            header('Location: ' . BASE_URL . 'admin/index.php');
            exit;
        }
    } else {
        $error = 'User not found.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login | Polymath Path</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* Aapka Glass-morphism CSS (No changes) */
        body { 
            background: radial-gradient(circle at top right, #1e40af, #1e1b4b, #0f172a);
            font-family: 'Plus Jakarta Sans', sans-serif;
            height: 100vh; display: flex; align-items: center; justify-content: center; margin: 0;
        }
        .glass-card {
            background: rgba(255, 255, 255, 0.07); backdrop-filter: blur(25px) saturate(180%);
            border-radius: 35px; border: 1px solid rgba(255, 255, 255, 0.15);
            box-shadow: 0 30px 60px rgba(0, 0, 0, 0.5); width: 100%; max-width: 380px; padding: 40px 35px; text-align: center;
        }
        .main-title { 
            color: #fff; font-size: 2.2rem; font-weight: 800; margin-bottom: 5px;
            background: linear-gradient(to right, #fff, #93c5fd); -webkit-background-clip: text; -webkit-text-fill-color: transparent;
        }
        .subtitle { color: rgba(255, 255, 255, 0.5); font-size: 0.85rem; margin-bottom: 30px; }
        .input-box { position: relative; margin-bottom: 20px; }
        .input-box i { position: absolute; left: 18px; top: 50%; transform: translateY(-50%); color: rgba(255, 255, 255, 0.4); font-size: 0.9rem; }
        .form-control { background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 18px; padding: 14px 14px 14px 48px; color: #fff; font-size: 0.9rem; transition: 0.3s; }
        .form-control:focus { background: rgba(255, 255, 255, 0.1); border-color: #3b82f6; box-shadow: 0 0 15px rgba(59, 130, 246, 0.3); color: #fff; }
        .btn-premium { background: linear-gradient(135deg, #3b82f6 0%, #1e40af 100%); color: white; border: none; padding: 14px; border-radius: 18px; font-weight: 700; width: 100%; margin-top: 10px; box-shadow: 0 10px 20px rgba(30, 64, 175, 0.3); transition: 0.4s; }
        .btn-premium:hover { transform: translateY(-2px); box-shadow: 0 15px 25px rgba(30, 64, 175, 0.5); }
        .error-msg { background: rgba(239, 68, 68, 0.15); color: #fca5a5; padding: 10px; border-radius: 12px; font-size: 0.8rem; margin-bottom: 20px; border: 1px solid rgba(239, 68, 68, 0.2); }
        .back-btn { display: inline-block; margin-top: 25px; color: rgba(255, 255, 255, 0.4); text-decoration: none; font-size: 0.8rem; transition: 0.3s; }
        .back-btn:hover { color: #fff; }
    </style>
</head>
<body>

    <div class="glass-card">
        <h1 class="main-title">Admin</h1>
        <p class="subtitle">Polymath Path Portal</p>

        <?php if (!empty($error)): ?>
            <div class="error-msg">
                <i class="fas fa-circle-exclamation me-2"></i> <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form method="post" autocomplete="off">
            <div class="input-box">
                <i class="fas fa-envelope"></i>
                <input type="email" name="email" class="form-control" placeholder="Admin Email" required autofocus>
            </div>
            
            <div class="input-box">
                <i class="fas fa-lock"></i>
                <input type="password" name="password" class="form-control" placeholder="Password" required>
            </div>

            <button type="submit" class="btn-premium">
                ENTER <i class="fas fa-arrow-right ms-2"></i>
            </button>
        </form>

        <a href="<?php echo BASE_URL; ?>index.php" class="back-btn">Exit to Selection</a>
    </div>

</body>
</html>