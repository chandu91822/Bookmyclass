<?php
// my_bookings.php - User's Booking History
include 'config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

// Handle Cancellation
if (isset($_GET['cancel_id'])) {
    $cancel_id = $_GET['cancel_id'];
    // Verify ownership (and implied status check if we wanted to restrict, but allowing all non-cancelled)
    $check_query = mysqli_query($conn, "SELECT * FROM bookings WHERE id='$cancel_id' AND user_id='$user_id' AND status!='cancelled'");
    
    if (mysqli_num_rows($check_query) > 0) {
        mysqli_query($conn, "UPDATE bookings SET status='cancelled' WHERE id='$cancel_id'");
        $msg = "Booking cancelled successfully.";
    } else {
        $error = "Cannot cancel this booking.";
    }
}

$query = "SELECT * FROM bookings WHERE user_id = '$user_id' ORDER BY date DESC, start_time DESC";
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Bookings | BookMyClass</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
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
                <?php if(isset($_SESSION['role']) && $_SESSION['role'] == 'student'): ?>
                <a href="student_dashboard.php" style="color: var(--secondary);">Dashboard</a>
                <a href="check_availability.php">Check Availability</a>
                <a href="book_class.php">Request Slot</a>
                <a href="my_bookings.php">My Bookings</a>
                <?php elseif(isset($_SESSION['role']) && $_SESSION['role'] == 'faculty'): ?>
                    <a href="faculty_dashboard.php">Dashboard</a>
                    <a href="check_availability.php">Check Availability</a>                   
                    <a href="book_class.php">Book Class</a>
                    <a href="faculty_schedule.php">My Schedule</a>
                    <a href="view_students.php">Students</a>
                <?php elseif(isset($_SESSION['role']) && $_SESSION['role'] == 'admin'): ?>
                    <a href="admin_dashboard.php">Dashboard</a>
                    <a href="view_bookings.php">All Bookings</a>
                    <a href="manage_users.php">Users</a>
                    <a href="export_reports.php">Reports</a>
                    <a href="system_logs.php">Logs</a>
                <?php endif; ?>
            </div>
            <div class="nav-actions">
                <div style="display: flex; flex-direction: column; align-items: flex-end; margin-right: 15px;">
                    <span style="color: white; font-weight: 600; font-size: 0.9rem;"><?php echo htmlspecialchars($_SESSION['name'] ?? 'User'); ?></span>
                    <span style="color: var(--text-muted); font-size: 0.75rem;"><?php echo ucfirst($_SESSION['role'] ?? ''); ?></span>
                </div>
                <!-- Profile Icon -->
                <?php if(isset($_SESSION['role']) && $_SESSION['role'] == 'student'): ?>
                <a href="student_profile.php" style="margin-right: 15px;" title="Profile">
                    <img src="uploads/profiles/<?php echo $_SESSION['user_id']; ?>.jpg" onerror="this.src='https://ui-avatars.com/api/?name=<?php echo urlencode($_SESSION['name']); ?>&background=0D8ABC&color=fff'" alt="Profile" style="width: 40px; height: 40px; border-radius: 50%; border: 2px solid white; object-fit: cover;">
                </a>
                <?php endif; ?>
                <a href="logout.php" class="btn btn-primary" style="padding: 8px 18px; font-size: 0.9rem;">Logout</a>
            </div>
            <div class="mobile-menu-toggle">
                <i class="fa-solid fa-bars" style="color:var(--text-main); font-size:1.5rem; cursor:pointer;"></i>
            </div>
        </div>
    </nav>

    <div class="dashboard-layout">
        <main class="main-content fade-in-up glass-panel">
            <header class="dashboard-header">
                <h2>My Bookings</h2>
            </header>

            <div class="table-container">
                <table class="dashboard-table">
                    <thead>
                        <tr>
                            <th>Room</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Subject</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['room_number']); ?></td>
                            <td><?php echo date("M d, Y", strtotime($row['date'])); ?></td>
                            <td><?php echo date("g:i A", strtotime($row['start_time'])) . ' - ' . date("g:i A", strtotime($row['end_time'])); ?></td>
                            <td><?php echo htmlspecialchars($row['subject'] ?? $row['reason']); ?></td>
                            <td>
                                <?php if(strtolower($row['status']) != 'cancelled'): ?>
                                <form method="POST" action="cancel_booking.php" style="display:inline;" onsubmit="return confirm('Are you sure you want to cancel this booking?');">
                                    <input type="hidden" name="booking_id" value="<?php echo $row['id']; ?>">
                                    <button type="submit" class="btn btn-sm btn-outline" style="color: #dc3545; border-color: #dc3545; padding: 4px 10px; font-size: 0.8rem;">Cancel</button>
                                </form>
                                <?php else: ?>
                                <span style="color: var(--text-muted);">-</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
    <script src="js/landing.js"></script>
</body>
</html>
