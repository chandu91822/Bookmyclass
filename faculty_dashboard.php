<?php
// faculty_dashboard.php - Redesigned (Pale Blue/Purple Theme)
include 'config.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'faculty') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$name = $_SESSION['name'];

// Fetch Stats
$my_bookings_count = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM bookings WHERE user_id = '$user_id' AND date >= CURDATE()"));
$total_assignments = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM assignments WHERE faculty_id = '$user_id'"));

// Live Room Monitoring (Simulated Real-time)
// Logic: Get all rooms, check if they are currently booked
$current_time = date('H:i:s');
$current_date = date('Y-m-d');

$rooms_query = mysqli_query($conn, "SELECT * FROM classrooms WHERE status='active'");
$room_status = [];

while ($room = mysqli_fetch_assoc($rooms_query)) {
    $r_num = $room['room_number'];
    // Check if booked right now
    $chk = mysqli_query($conn, "SELECT * FROM bookings WHERE room_number = '$r_num' AND date = '$current_date' AND start_time <= '$current_time' AND end_time >= '$current_time' AND status != 'cancelled'");
    
    if (mysqli_num_rows($chk) > 0) {
        $booking_data = mysqli_fetch_assoc($chk);
        $room_status[$r_num] = ['status' => 'Occupied', 'by' => 'User ' . $booking_data['user_id']]; 
        // In real app, join users table to get name
    } else {
        $room_status[$r_num] = ['status' => 'Available', 'by' => '-'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Faculty Dashboard | BookMyClass</title>
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
                <a href="faculty_dashboard.php">Dashboard</a>
                    <a href="check_availability.php">Check Availability</a>                   
                    <a href="book_class.php">Book Class</a>
                    <a href="faculty_schedule.php">My Schedule</a>
                    <a href="view_students.php">Students</a>
            </div>
            <div class="nav-actions">
                <div style="display: flex; flex-direction: column; align-items: flex-end; margin-right: 15px;">
                    <span style="color: white; font-weight: 600; font-size: 0.9rem;"><?php echo htmlspecialchars($_SESSION['name'] ?? 'Faculty'); ?></span>
                    <span style="color: var(--text-muted); font-size: 0.75rem;">Faculty</span>
                </div>
                <!-- Profile Icon -->
                <a href="<?php echo ($_SESSION['role'] == 'admin') ? 'admin_profile.php' : (($_SESSION['role'] == 'faculty') ? 'faculty_profile.php' : 'student_profile.php'); ?>" style="margin-right: 15px;" title="Profile">
                    <?php 
                        $pro_pic = "uploads/profiles/" . $_SESSION['user_id'] . ".jpg";
                        if(!file_exists($pro_pic)) {
                            $pro_pic = "https://ui-avatars.com/api/?name=" . urlencode($_SESSION['name'] ?? 'Faculty') . "&background=0D8ABC&color=fff";
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
                     <h2 style="font-size: 1.8rem;">Hello, Prof. <?php echo htmlspecialchars($name); ?></h2>
                     <p style="color: var(--text-muted);">Manage your classes and schedule effortlessly.</p>
                </div>
                <div class="badge">Faculty Dashboard</div>
            </header>

            <?php if(isset($_GET['success'])): ?>
                <div style="background: rgba(16, 185, 129, 0.1); color: #10b981; padding: 15px; border-radius: 12px; margin-bottom: 20px;">
                    <?php echo htmlspecialchars($_GET['success']); ?>
                </div>
            <?php endif; ?>

            <div class="stats-grid-dashboard">
                <div class="stat-card">
                    <div class="icon-box">
                        <i class="fa-solid fa-chalkboard-user"></i>
                    </div>
                    <span class="stat-value"><?php echo $my_bookings_count; ?></span>
                    <span class="stat-label">Upcoming Classes</span>
                </div>
            </div>

            <div class="features-grid" style="grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); margin-bottom: 40px;">
                
              


            </div>

        

            <!-- Student Requests Section -->
            <h3>Student Booking Requests</h3>
            <?php
            // Fetch bookings assigned to this faculty
            $requests_query = mysqli_query($conn, "SELECT * FROM bookings WHERE faculty = '$name' ORDER BY date DESC, start_time ASC");
            if(mysqli_num_rows($requests_query) > 0):
            ?>
            <div class="table-container">
                <table class="dashboard-table">
                    <thead>
                        <tr>
                            <th>Student Name</th>
                            <th>Year / Section</th>
                            <th>Room</th>
                            <th>Date / Time</th>
                            <th>Subject</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($req = mysqli_fetch_assoc($requests_query)): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($req['name']); ?></td>
                            <td><?php echo htmlspecialchars($req['year'] . ' - ' . $req['section']); ?></td>
                            <td><?php echo htmlspecialchars($req['room_number']); ?></td>
                            <td>
                                <?php echo htmlspecialchars($req['date']); ?><br>
                                <span style="font-size: 0.85rem; color: var(--text-muted);">
                                    <?php echo date("g:i A", strtotime($req['start_time'])) . ' - ' . date("g:i A", strtotime($req['end_time'])); ?>
                                </span>
                            </td>
                            <td><?php echo htmlspecialchars($req['subject']); ?></td>
                            <td>
                                <?php 
                                    $st = $req['status'];
                                    $col = ($st == 'approved') ? 'green' : (($st == 'pending') ? 'orange' : 'red');
                                    echo "<span style='color: $col; font-weight: 600; text-transform: capitalize;'>$st</span>";
                                ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
                <div class="stat-card" style="text-align: center; color: var(--text-muted);">
                    No booking requests found.
                </div>
            <?php endif; ?>

        </main>
    </div>

    <script src="js/landing.js"></script>
</body>
</html>
