<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Sahi file name 'db.php' include karein
require_once 'config/db.php'; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Form se data lena aur clean karna
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $message = mysqli_real_escape_string($conn, $_POST['message']);

    // SQL query jo 'status' column ke saath hai
    // Table columns: full_name, email, message, status
    $sql = "INSERT INTO contact_messages (full_name, email, message, status) 
            VALUES ('$name', '$email', '$message', 'unread')";

    if (mysqli_query($conn, $sql)) {
        // Redirect use karke refresh par duplicate message rokein
        echo "<script>alert('Thank you! Message sent.'); window.location.href='contact.php';</script>";
        exit;
    } else {
        // Error handling
        echo "Error: " . mysqli_error($conn);
    }
}

// Navbar include karein
include 'navbar.php'; 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us | LMS</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <style>
        body { background-color: #f8fafc; font-family: 'Segoe UI', Arial, sans-serif; margin: 0; color: #1e293b; }
        .contact-wrapper { max-width: 900px; margin: 60px auto; padding: 20px; display: grid; grid-template-columns: 1fr 1.5fr; gap: 40px; background: white; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.08); overflow: hidden; }
        .contact-info { background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%); color: white; padding: 40px; display: flex; flex-direction: column; justify-content: center; }
        .contact-info h2 { margin-bottom: 20px; font-size: 2rem; }
        .info-item { display: flex; align-items: center; gap: 15px; margin-bottom: 25px; }
        .info-item i { font-size: 1.5rem; color: #bfdbfe; }
        .contact-form-container { padding: 40px; }
        .contact-form-container h3 { margin-bottom: 20px; color: #0f172a; }
        .input-group { margin-bottom: 20px; }
        .input-group label { display: block; margin-bottom: 8px; font-weight: 500; font-size: 0.9rem; }
        .input-group input, .input-group textarea { width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 8px; outline: none; transition: 0.3s; }
        .input-group input:focus, .input-group textarea:focus { border-color: #3b82f6; box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1); }
        .submit-btn { background: #1e40af; color: white; border: none; padding: 12px 30px; border-radius: 8px; font-weight: 600; cursor: pointer; width: 100%; transition: 0.3s; }
        .submit-btn:hover { background: #1e3a8a; transform: translateY(-2px); }
        @media (max-width: 768px) { .contact-wrapper { grid-template-columns: 1fr; margin: 20px; } .contact-info { padding: 30px; } }
    </style>
</head>
<body>

    <div class="contact-wrapper">
        <div class="contact-info">
            <h2>Let's Talk!</h2>
            <p style="margin-bottom: 30px; opacity: 0.9;">Have questions about our courses or portals? We are here to help.</p>
            
            <div class="info-item">
                <i class="fas fa-envelope"></i>
                <span>support@laibalms.com</span>
            </div>
            <div class="info-item">
                <i class="fas fa-phone-alt"></i>
                <span>+92 300 1234567</span>
            </div>
            <div class="info-item">
                <i class="fas fa-map-marker-alt"></i>
                <span>Punjab, Pakistan</span>
            </div>
        </div>

        <div class="contact-form-container">
            <h3>Send us a Message</h3>
            <form action="<?php echo BASE_URL; ?>contact.php" method="POST">
                <div class="input-group">
                    <label>Full Name</label>
                    <input type="text" name="name" placeholder="Enter your name" required>
                </div>
                <div class="input-group">
                    <label>Email Address</label>
                    <input type="email" name="email" placeholder="Enter your email" required>
                </div>
                <div class="input-group">
                    <label>Message</label>
                    <textarea name="message" rows="4" placeholder="How can we help you?" required></textarea>
                </div>
                <button type="submit" class="submit-btn">Send Message</button>
            </form>
        </div>
    </div>

    <footer style="text-align: center; padding: 20px; color: #64748b; font-size: 0.9rem;">
        &copy; 2026 LMS. All rights reserved.
    </footer>

</body>
</html>