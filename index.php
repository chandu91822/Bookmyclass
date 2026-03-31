<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BookMyClass | Smart Academic Scheduling</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@500;600;700;800&display=swap" rel="stylesheet">
    <!-- Styles -->
    <link rel="stylesheet" href="css/landing.css">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

    <!-- Header / Navbar -->
    <nav class="navbar">
        <div class="container nav-container">
            <a href="index.php" class="logo">
                <i class="fa-solid fa-shapes"></i>
                BookMyClass
            </a>
            <div class="nav-links">
                <a href="index.php">Home</a>
                <a href="#features">Features</a>
                <a href="#how-it-works">Demo</a>
                <a href="#contact">Contact</a>
            </div>
            <div class="nav-actions">
                <a href="login.php" class="btn btn-outline">Login</a>
                <a href="register.php" class="btn btn-primary">Register</a>
            </div>
            <div class="mobile-menu-toggle">
                <i class="fa-solid fa-bars" style="color:var(--text-main); font-size:1.5rem; cursor:pointer;"></i>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <header class="hero">
        <div class="hero-bg"></div>
        <div class="hero-overlay"></div>
        
        <div class="container hero-grid">
            <div class="hero-content">
                <h1>Smart Classroom Booking. <br><span class="gradient-text">Zero Conflicts.</span></h1>
                <p>Real-time scheduling platform designed for modern campuses. Eliminate double bookings and streamline academic logistics.</p>
                <div class="hero-buttons">
                    <a href="register.php" class="btn btn-primary">Get Started <i class="fa-solid fa-arrow-right"></i></a>
                </div>
            </div>
            
            <div class="hero-visual">
                <!-- Floating Glass Cards -->
                 <div class="floating-card card-1">
                    <div class="float-icon"><i class="fa-solid fa-check-circle"></i></div>
                    <div class="float-text">
                        <h4>0% Conflicts</h4>
                        <p>Double bookings eliminated</p>
                    </div>
                 </div>
                 
                 <div class="floating-card card-2">
                    <div class="float-icon"><i class="fa-solid fa-clock"></i></div>
                    <div class="float-text">
                        <h4>Real-Time</h4>
                        <p>Live availability updates</p>
                    </div>
                 </div>
                 
                 <div class="floating-card card-3">
                    <div class="float-icon"><i class="fa-solid fa-chart-line"></i></div>
                    <div class="float-text">
                        <h4>Analytics</h4>
                        <p>Usage insights & reports</p>
                    </div>
                 </div>
            </div>
        </div>
    </header>

    <!-- Core Features Section (10 Cards) -->
    <section id="features" class="section">
        <div class="container">
            <h2 class="section-title">Everything you need to <br>manage campus spaces</h2>
            <p class="section-subtitle">Powerful features designed to make classroom management effortless for everyone.</p>

            <div class="features-grid">
                <!-- 1. Real-Time Conflict Prevention -->
                <div class="feature-card">
                    <div class="feature-icon-circle"><i class="fa-solid fa-shield-halved"></i></div>
                    <h3>Conflict Detection</h3>
                    <p>Smart engine automatically blocks double bookings before they happen.</p>
                </div>

                <!-- 2. Role-Based Dashboards -->
                <div class="feature-card">
                    <div class="feature-icon-circle"><i class="fa-solid fa-users-gear"></i></div>
                    <h3>Role-Based Access</h3>
                    <p>Dedicated dashboards for Admins, Faculty, and Students.</p>
                </div>

                <!-- 3. Mobile Responsive UI -->
                <div class="feature-card">
                    <div class="feature-icon-circle"><i class="fa-solid fa-mobile-screen-button"></i></div>
                    <h3>Fully Responsive</h3>
                    <p>Seamless experience on mobile, tablet, and desktop devices.</p>
                </div>

                <!-- 4. Secure Authentication -->
                <div class="feature-card">
                    <div class="feature-icon-circle"><i class="fa-solid fa-lock"></i></div>
                    <h3>Secure Auth</h3>
                    <p>Enterprise-grade security to protect user data and schedules.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works Section -->
    <section id="how-it-works" class="section" style="background: white;">
        <div class="container">
            <h2 class="section-title">Booking in 3 Simple Steps</h2>
            <p class="section-subtitle">Streamlined process to get you into the classroom faster.</p>
            
            <div class="timeline">
                <!-- Step 1 -->
                <div class="timeline-step">
                    <div class="step-marker">1</div>
                    <div class="step-content">
                        <h3>Login</h3>
                        <p>Access your secure dashboard.</p>
                    </div>
                </div>

                <!-- Step 2 -->
                <div class="timeline-step">
                    <div class="step-marker">2</div>
                    <div class="step-content">
                        <h3>Select Room & Time</h3>
                        <p>Choose your ideal slot.</p>
                    </div>
                </div>

                <!-- Step 3 -->
                <div class="timeline-step">
                    <div class="step-marker">3</div>
                    <div class="step-content">
                        <h3>Confirm Booking</h3>
                        <p>Instant confirmation.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <!-- Footer -->
    <footer id="contact" class="footer">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-brand">
                    <a href="#" class="logo">
                        <i class="fa-solid fa-shapes"></i>
                        BookMyClass
                    </a>
                    <p>Empowering education through smart space management.</p>
                    <div class="social-icons" style="margin-top: 1.5rem;">
                        <a href="#"><i class="fa-brands fa-twitter"></i></a>
                        <a href="#"><i class="fa-brands fa-linkedin-in"></i></a>
                        <a href="#"><i class="fa-brands fa-instagram"></i></a>
                    </div>
                </div>
                <div class="footer-links">
                    <h4>Product</h4>
                    <ul>
                        <li><a href="#">Features</a></li>
                        <li><a href="#">Pricing</a></li>
                        <li><a href="#">API</a></li>
                    </ul>
                </div>
                <div class="footer-links">
                    <h4>Resources</h4>
                    <ul>
                        <li><a href="#">Documentation</a></li>
                        <li><a href="#">Support Center</a></li>
                        <li><a href="#">Community</a></li>
                    </ul>
                </div>
                <div class="footer-links">
                    <h4>Contact</h4>
                    <ul>
                        <li><a href="#">hello@bookmyclass.com</a></li>
                        <li><a href="#">(555) 123-4567</a></li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; <?php echo date('Y'); ?> BookMyClass. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="js/landing.js"></script>
</body>
</html>
