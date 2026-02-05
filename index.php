<?php
// 1. db.php ko include kiya taake BASE_URL mil sake
include 'config/db.php'; 

// 2. Navbar include karne ke liye PHP tag
include 'navbar.php'; 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LMS | Welcome to Learning Portal</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>index.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>

    <header class="hero-section">
        <div class="hero-content">
            <h1 class="hero-title">Elevate Your Learning Journey</h1>
            <p class="hero-description">A professional platform designed for students to excel and instructors to inspire. Join our community today and start exploring.</p>
            <div class="hero-buttons">
                <a href="#portals" class="btn-primary">Get Started</a>
            </div>
        </div>
    </header>

    <section class="features-section">
        <div class="features-wrapper">
            <div class="mini-card">
                <i class="fas fa-play-circle"></i>
                <h4>Video Lessons</h4>
                <p>High quality video content for better learning.</p>
            </div>
            <div class="mini-card">
                <i class="fas fa-edit"></i>
                <h4>Easy Quizzes</h4>
                <p>Test your skills with interactive assessments.</p>
            </div>
            <div class="mini-card">
                <i class="fas fa-award"></i>
                <h4>Certificates</h4>
                <p>Earn certificates on course completion.</p>
            </div>
            <div class="mini-card">
                <i class="fas fa-chart-bar"></i>
                <h4>Track Progress</h4>
                <p>Monitor your performance daily.</p>
            </div>
        </div>
    </section>

    <section class="portals-container" id="portals">
        <h2 class="section-heading">Select Your Portal</h2>
        <div class="cards-wrapper">
            <div class="portal-card">
                <div class="card-icon"><i class="fas fa-user-graduate"></i></div>
                <h3 class="card-title">Student Portal</h3>
                <p class="card-text">View your enrolled courses, track progress, and download certificates.</p>
                <a href="<?php echo BASE_URL; ?>student/login.php" class="portal-link">Login as Student</a>
            </div>

            <div class="portal-card">
                <div class="card-icon"><i class="fas fa-chalkboard-teacher"></i></div>
                <h3 class="card-title">Instructor Portal</h3>
                <p class="card-text">Create new content, manage your students, and review assignments.</p>
                <a href="<?php echo BASE_URL; ?>instructor/index.php" class="portal-link">Login as Instructor</a>
            </div>

            <div class="portal-card">
                <div class="card-icon"><i class="fas fa-user-shield"></i></div>
                <h3 class="card-title">Admin Portal</h3>
                <p class="card-text">Manage users, oversee system configurations, and generate reports.</p>
                <a href="<?php echo BASE_URL; ?>admin/Login.php" class="portal-link">Login as Admin</a>
            </div>
        </div>
    </section>
         
   <section id="about" class="about-section">
        <h2 class="section-heading">Your Gateway to Excellence</h2>
        <div class="about-content">
            <div class="about-text">
                <p>Our Learning Management System is a bridge between students and educators. We provide a seamless environment for course management, interactive learning, and skill development.</p>
                <ul class="about-list">
                    <li><i class="fas fa-check-circle"></i> User-friendly Dashboard for everyone.</li>
                    <li><i class="fas fa-check-circle"></i> Secure and Private Data Management.</li>
                    <li><i class="fas fa-check-circle"></i> 24/7 Access to Learning Materials.</li>
                </ul>
            </div>
        </div>
    </section>

   <footer class="main-footer">
    <div class="footer-container">
        <div class="footer-col">
            <h3 class="footer-logo">LMS Portal</h3>
            <p class="footer-about">A modern platform dedicated to empowering students and instructors through digital learning.</p>
            <div class="social-links">
    <a href="https://www.facebook.com" target="_blank" title="Visit Facebook">
        <i class="fab fa-facebook"></i>
    </a>
    
    <a href="https://www.linkedin.com" target="_blank" title="Visit LinkedIn">
        <i class="fab fa-linkedin"></i>
    </a>
    
    <a href="https://www.github.com" target="_blank" title="Visit GitHub">
        <i class="fab fa-github"></i>
    </a>
</div>        </div>

        <div class="footer-col">
            <h4>Quick Links</h4>
            <ul>
                <li><a href="<?php echo BASE_URL; ?>index.php">Home</a></li>
                <li><a href="<?php echo BASE_URL; ?>index.php#about">About Us</a></li>
                <li><a href="<?php echo BASE_URL; ?>contact.php">Contact Us</a></li>
            </ul>
        </div>

        <div class="footer-col">
            <h4>User Portals</h4>
            <ul>
                <li><a href="<?php echo BASE_URL; ?>student/login.php">Student Login</a></li>
                <li><a href="<?php echo BASE_URL; ?>instructor/index.php">Instructor Login</a></li>
                <li><a href="<?php echo BASE_URL; ?>admin/login.php">Admin Login</a></li>
            </ul>
        </div>
    </div>
    <div class="footer-bottom">
        <p>&copy; 2026 Laiba LMS. All rights reserved.</p>
    </div>
</footer>

</body>
</html>