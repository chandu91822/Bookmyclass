<?php
// admin_dashboard.php - Redesigned (Pale Blue/Purple Theme)
include 'config.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Fetch Stats
$total_users = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM users"));
$total_faculty = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM users WHERE role='faculty'"));
$total_students = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM users WHERE role='student'"));
$bookings_result = mysqli_query($conn, "SELECT * FROM bookings");
$total_bookings = $bookings_result ? mysqli_num_rows($bookings_result) : 0;

// Fetch Recent Logs
$logs_query = mysqli_query($conn, "SELECT * FROM activity_logs ORDER BY created_at DESC LIMIT 5");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard | BookMyClass</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/landing.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Chart.js for Analytics -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <!-- Background Theme -->
    <div class="hero-bg" style="position: fixed; z-index: -2;"></div>
    <div class="hero-overlay" style="position: fixed; z-index: -1;"></div>
    
    <!-- Top Navbar -->
    <nav class="navbar">
        <div class="container nav-container">
            <a href="index.php" class="logo">
                <i class="fa-solid fa-shapes"></i>
                BookMyClass
            </a>
            <div class="nav-links">
                <a href="admin_dashboard.php" style="color: var(--secondary);">Dashboard</a>
                <a href="view_bookings.php">All Bookings</a>
                <a href="manage_users.php">Users</a>
                <a href="export_reports.php">Reports</a>
                <a href="system_logs.php">System Logs</a>
            </div>
            <div class="nav-actions">
                <div style="display: flex; flex-direction: column; align-items: flex-end; margin-right: 15px;">
                    <span style="color: white; font-weight: 600; font-size: 0.9rem;"><?php echo htmlspecialchars($_SESSION['name'] ?? 'Admin'); ?></span>
                    <span style="color: var(--text-muted); font-size: 0.75rem;">Admin</span>
                </div>
                <!-- Profile Icon -->
                <a href="<?php echo ($_SESSION['role'] == 'admin') ? 'admin_profile.php' : (($_SESSION['role'] == 'faculty') ? 'faculty_profile.php' : 'student_profile.php'); ?>" style="margin-right: 15px;" title="Profile">
                    <?php 
                        $pro_pic = "uploads/profiles/" . $_SESSION['user_id'] . ".jpg";
                        if(!file_exists($pro_pic)) {
                            $pro_pic = "https://ui-avatars.com/api/?name=" . urlencode($_SESSION['name'] ?? 'Admin') . "&background=0D8ABC&color=fff";
                        }
                    ?>
                    <img src="<?php echo $pro_pic; ?>?t=<?php echo time(); ?>" alt="Profile" style="width: 40px; height: 40px; border-radius: 50%; border: 2px solid white; object-fit: cover;">
                </a>
                <a href="logout.php" class="btn btn-primary" style="padding: 8px 18px; font-size: 0.9rem;">Logout</a>
            </div>
            <div class="mobile-menu-toggle">
                <i class="fa-solid fa-bars" style="color:var(--text-main); font-size:1.5rem; cursor:pointer;"></i>
            </div>
        </div>
    </nav>

    <div class="dashboard-layout">
        
        <!-- Main Content -->
        <main class="main-content fade-in-up">
            <header class="dashboard-header" style="margin-bottom: 30px;">
                <div>
                     <h2 style="font-size: 1.8rem;">Welcome back, <?php echo htmlspecialchars($_SESSION['name']); ?>!</h2>
                     <p style="color: var(--text-muted);">Platform Overview & Statistics</p>
                </div>
                <div class="badge">Admin Dashboard</div>
            </header>

            <!-- Stats Grid -->
            <div class="stats-grid-dashboard">
                <div class="stat-card">
                    <div class="icon-box">
                        <i class="fa-solid fa-users"></i>
                    </div>
                    <span class="stat-value"><?php echo $total_users; ?></span>
                    <span class="stat-label">Total Users</span>
                </div>
                <div class="stat-card">
                    <div class="icon-box">
                        <i class="fa-solid fa-chalkboard-user"></i>
                    </div>
                    <span class="stat-value"><?php echo $total_faculty; ?></span>
                    <span class="stat-label">Faculty</span>
                </div>
                <div class="stat-card">
                    <div class="icon-box">
                        <i class="fa-solid fa-graduation-cap"></i>
                    </div>
                    <span class="stat-value"><?php echo $total_students; ?></span>
                    <span class="stat-label">Students</span>
                </div>
                <div class="stat-card">
                    <div class="icon-box">
                        <i class="fa-regular fa-calendar-check"></i>
                    </div>
                    <span class="stat-value"><?php echo $total_bookings; ?></span>
                    <span class="stat-label">Active Bookings</span>
                </div>
            </div>    

        </main>
    </div>
    <script src="js/landing.js"></script>
</body>
</html>
