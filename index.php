<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>POLYMATH PATH | Role Selection</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@500;700&display=swap" rel="stylesheet">
    <style>
        body { background: #003366; font-family: 'Poppins', sans-serif; }
        .role-box { background: #fff; border-radius: 18px; box-shadow: 0 4px 24px rgba(0,0,0,0.08); transition: transform 0.2s, box-shadow 0.2s; cursor: pointer; border: 3px solid #FFD700; }
        .role-box:hover { transform: translateY(-8px) scale(1.03); box-shadow: 0 8px 32px rgba(0,0,0,0.12); border-color: #003366; }
        .role-label { color: #003366; font-weight: 700; font-size: 1.5rem; letter-spacing: 1px; }
        .main-title { color: #FFD700; font-size: 2.8rem; font-weight: 700; letter-spacing: 2px; }
        .subtitle { color: #fff; font-size: 1.1rem; letter-spacing: 1px; }
        .container-role { min-height: 100vh; display: flex; flex-direction: column; justify-content: center; align-items: center; }
    </style>
</head>
<body>
    <div class="container-role">
        <div class="text-center mb-5">
            <div class="main-title mb-2">POLYMATH PATH</div>
            <div class="subtitle mb-4">Learning Management System</div>
        </div>
        <div class="row justify-content-center g-4" style="width:100%; max-width: 700px;">
            <div class="col-md-4">
                <a href="admin/login.php" style="text-decoration:none;">
                    <div class="role-box p-5 text-center">
                        <img src="assets/admin_icon.svg" alt="Admin" style="height:60px; margin-bottom:18px;">
                        <div class="role-label">ADMIN PORTAL</div>
                    </div>
                </a>
            </div>
            <div class="col-md-4">
                <a href="instructor/login.php" style="text-decoration:none;">
                    <div class="role-box p-5 text-center">
                        <img src="assets/instructor_icon.svg" alt="Instructor" style="height:60px; margin-bottom:18px;">
                        <div class="role-label">INSTRUCTOR PORTAL</div>
                    </div>
                </a>
            </div>
            <div class="col-md-4">
                <a href="student/login.php" style="text-decoration:none;">
                    <div class="role-box p-5 text-center">
                        <img src="assets/student_icon.svg" alt="Student" style="height:60px; margin-bottom:18px;">
                        <div class="role-label">STUDENT PORTAL</div>
                    </div>
                </a>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
