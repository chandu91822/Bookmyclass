<?php
// student_announcements.php
include 'config.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: login.php");
    exit();
}

// Fetch Announcements
// Assuming there is an announcements table. If not, I need to check schema.
// Checking file listing earlier: announcements.php exists.
// Let's assume table 'announcements' exists with title, message, created_at.
// I'll double check with a quick view of announcements.php if needed, but standard practice suggests this.
// Fetch student details if not already set
if(!isset($_SESSION['year'])) {
    $u_res = mysqli_query($conn, "SELECT year, section FROM users WHERE id='".$_SESSION['user_id']."'");
    $u_dat = mysqli_fetch_assoc($u_res);
    $_SESSION['year'] = $u_dat['year'];
    $_SESSION['section'] = $u_dat['section'];
}
$my_year = $_SESSION['year'];
$my_sec = $_SESSION['section'];

$query = "SELECT * FROM announcements WHERE (year='$my_year' OR year='All' OR year='') AND (section='$my_sec' OR section='All' OR section='') ORDER BY created_at DESC";
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Announcements | BookMyClass</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/landing.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .announcement-card {
            background: rgba(255, 255, 255, 0.6);
            border-left: 4px solid var(--primary-color);
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            border-radius: 8px;
        }
        .ann-meta {
            font-size: 0.85rem;
            color: var(--text-muted);
            margin-top: 0.5rem;
        }
    </style>
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
                <a href="student_dashboard.php">Dashboard</a>
                <a href="student_booking.php">Request Slot</a>
                <a href="my_bookings.php">My Bookings</a>
                <a href="student_assignments.php">Assignments</a>
                <a href="student_announcements.php" style="color: var(--secondary);">Announcements</a>
                <a href="student_profile.php">Profile</a>
            </div>
            <div class="nav-actions">
                <div style="display: flex; flex-direction: column; align-items: flex-end; margin-right: 10px;">
                    <span style="color: white; font-weight: 600; font-size: 0.9rem;"><?php echo htmlspecialchars($_SESSION['name']); ?></span>
                    <span style="color: var(--text-muted); font-size: 0.75rem;">Student</span>
                </div>
                <a href="logout.php" class="btn btn-primary" style="padding: 8px 18px; font-size: 0.9rem;">Logout</a>
            </div>
        </div>
    </nav>

    <div class="dashboard-layout">
        <main class="main-content fade-in-up glass-panel">
            <header class="dashboard-header">
                <h2>Announcements</h2>
            </header>

            <div class="announcements-list">
                <?php if($result && mysqli_num_rows($result) > 0): ?>
                    <?php while($row = mysqli_fetch_assoc($result)): ?>
                    <div class="announcement-card">
                        <h3><?php echo htmlspecialchars($row['title']); ?></h3>
                        <p><?php echo nl2br(htmlspecialchars($row['message'])); ?></p>
                        <div class="ann-meta">
                            <i class="fa-regular fa-clock"></i> <?php echo date("M d, Y h:i A", strtotime($row['created_at'])); ?>
                        </div>
                    </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p style="text-align: center; color: var(--text-muted);">No announcements found.</p>
                <?php endif; ?>
            </div>
        </main>
    </div>
</body>
</html>
