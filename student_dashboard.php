<?php
// student_dashboard.php - Redesigned (Clean & Real-time)
include 'config.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$name = $_SESSION['name'];

// 1. Total Classes Booked
$total_bookings_query = mysqli_query($conn, "SELECT COUNT(*) as count FROM bookings WHERE user_id = '$user_id'");
$total_bookings = mysqli_fetch_assoc($total_bookings_query)['count'];

// 2. Ongoing Classes (Current Date and Time is between start and end)
$current_date = date('Y-m-d');
$current_time = date('H:i:s');

$ongoing_query = "SELECT * FROM bookings 
                  WHERE user_id = '$user_id' 
                  AND status = 'approved' 
                  AND date = '$current_date' 
                  AND start_time <= '$current_time' 
                  AND end_time >= '$current_time'";
$ongoing_result = mysqli_query($conn, $ongoing_query);
$ongoing_count = mysqli_num_rows($ongoing_result);

// 3. Upcoming Classes (Future Date OR (Today AND Future Time))
$upcoming_query = "SELECT * FROM bookings 
                   WHERE user_id = '$user_id' 
                   AND status = 'approved' 
                   AND (date > '$current_date' OR (date = '$current_date' AND start_time > '$current_time')) 
                   ORDER BY date ASC, start_time ASC";
$upcoming_result = mysqli_query($conn, $upcoming_query);
$upcoming_count = mysqli_num_rows($upcoming_result);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard | BookMyClass</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/landing.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
                <a href="student_dashboard.php" style="color: var(--secondary);">Dashboard</a>
                <a href="check_availability.php">Check Availability</a>
                <a href="book_class.php">Request Slot</a>
                <a href="my_bookings.php">My Bookings</a>
                
            </div>
            <div class="nav-actions">
                <div style="display: flex; flex-direction: column; align-items: flex-end; margin-right: 15px;">
                    <span style="color: white; font-weight: 600; font-size: 0.9rem;"><?php echo htmlspecialchars($name); ?></span>
                </div>
                <!-- Profile Icon -->
                <a href="<?php echo ($_SESSION['role'] == 'admin') ? 'admin_profile.php' : (($_SESSION['role'] == 'faculty') ? 'faculty_profile.php' : 'student_profile.php'); ?>" style="margin-right: 15px;" title="Profile">
                    <?php 
                        $pro_pic = "uploads/profiles/" . $_SESSION['user_id'] . ".jpg";
                        if(!file_exists($pro_pic)) {
                            $pro_pic = "https://ui-avatars.com/api/?name=" . urlencode($name) . "&background=0D8ABC&color=fff";
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
            <header class="dashboard-header" style="margin-bottom: 30px; margin-top: 20px;">
                <div>
                     <h2 style="font-size: 1.8rem;">Welcome Back, <?php echo htmlspecialchars($name); ?>!</h2>
                </div>
            </header>

            <!-- Quick Stats -->
            <div class="stats-grid-dashboard">
                 <div class="stat-card">
                    <div class="icon-box">
                        <i class="fa-solid fa-bookmark"></i>
                    </div>
                    <span class="stat-value"><?php echo $total_bookings; ?></span>
                    <span class="stat-label">Total Bookings</span>
                </div>
                <div class="stat-card">
                    <div class="icon-box" style="background: rgba(34, 197, 94, 0.1); color: #22c55e;">
                        <i class="fa-solid fa-tower-broadcast"></i>
                    </div>
                    <span class="stat-value" style="color: #22c55e;"><?php echo $ongoing_count; ?></span>
                    <span class="stat-label">Ongoing Classes</span>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr; gap: 30px; margin-top: 30px;">
                
                <!-- Ongoing Classes Section -->
                <div>
                    <h3 style="margin-bottom: 15px; display: flex; align-items: center; gap: 10px; font-size: 1.2rem;">
                        <i class="fa-solid fa-tower-broadcast" style="color: #22c55e;"></i> Ongoing Classes
                    </h3>
                    <div class="stat-card" style="min-height: auto; padding: 20px;">
                        <?php if($ongoing_count > 0): ?>
                            <div class="table-container">
                                <table class="dashboard-table">
                                    <thead>
                                        <tr>
                                            <th>Room</th>
                                            <th>Time</th>
                                            <th>Purpose</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while($row = mysqli_fetch_assoc($ongoing_result)): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($row['room_number']); ?></td>
                                            <td>
                                                <?php echo date("g:i A", strtotime($row['start_time'])) . ' - ' . date("g:i A", strtotime($row['end_time'])); ?>
                                            </td>
                                            <td><?php echo htmlspecialchars($row['subject']); ?></td>
                                            <td><span class="badge" style="background: #22c55e; color: white;">LIVE</span></td>
                                        </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <p style="color: var(--text-muted); font-size: 0.95rem;">No classes currently in session.</p>
                        <?php endif; ?>
                    </div>
                </div>

            </div>

        </main>
    </div>

    <script src="js/landing.js"></script>
</body>
</html>
