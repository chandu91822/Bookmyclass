<?php
// view_bookings.php - Admin Full Control
session_start();
include 'config.php';
$user_name = $_SESSION['name'] ?? 'Admin';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Handle Cancellation/Override
if (isset($_GET['cancel_id'])) {
    $id = $_GET['cancel_id'];
    mysqli_query($conn, "UPDATE bookings SET status='cancelled' WHERE id='$id'");
    
    // Log it
    $uid = $_SESSION['user_id'];
    mysqli_query($conn, "INSERT INTO activity_logs (user_id, action, details) VALUES ('$uid', 'BOOKING_OVERRIDE', 'Admin cancelled booking #$id')");
    
    header("Location: view_bookings.php");
    exit();
}

$result = mysqli_query($conn, "SELECT b.*, u.name as user_name, u.role as user_role FROM bookings b LEFT JOIN users u ON b.user_id = u.id ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>All Bookings | BookMyClass</title>
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
                <a href="admin_dashboard.php">Dashboard</a>
                <a href="view_bookings.php">All Bookings</a>
                <a href="manage_users.php">Users</a>
                <a href="export_reports.php">Reports</a>
                <a href="system_logs.php">Logs</a>
            </div>
            <div class="nav-actions">
                <div style="display: flex; flex-direction: column; align-items: flex-end; margin-right: 15px;">
                    <span style="color: white; font-weight: 600; font-size: 0.9rem;"><?php echo htmlspecialchars($_SESSION['name'] ?? 'Admin'); ?></span>
                    <span style="color: var(--text-muted); font-size: 0.75rem;">Admin</span>
                </div>
                <!-- Profile Icon -->
                <a href="admin_profile.php" style="margin-right: 15px;" title="Profile">
                    <img src="uploads/profiles/<?php echo $_SESSION['user_id']; ?>.jpg" onerror="this.src='https://ui-avatars.com/api/?name=<?php echo urlencode($_SESSION['name'] ?? 'Admin'); ?>&background=0D8ABC&color=fff'" alt="Profile" style="width: 40px; height: 40px; border-radius: 50%; border: 2px solid white; object-fit: cover;">
                </a>
                <a href="logout.php" class="btn btn-primary" style="padding: 8px 18px; font-size: 0.9rem;">Logout</a>
            </div>
        </div>
    </nav>
    <div class="dashboard-layout">
        
        <main class="main-content fade-in-up glass-panel">
            <header class="dashboard-header">
                <h2>Manage All Bookings</h2>
            </header>

            <div class="table-container">
                <table class="dashboard-table">
                    <thead>
                        <tr>
                            <th>Booked By</th>
                            <th>Room</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td>
                                <div><?php echo htmlspecialchars($row['user_name']); ?></div>
                                <small style="color: var(--text-muted);"><?php echo ucfirst($row['user_role']); ?></small>
                            </td>
                            <td><?php echo htmlspecialchars($row['room_number']); ?></td>
                            <td><?php echo htmlspecialchars($row['date']); ?></td>
                            <td><?php echo date("g:i A", strtotime($row['start_time'])) . ' - ' . date("g:i A", strtotime($row['end_time'])); ?></td>

                            <td>
                                <?php if($row['status'] !== 'cancelled'): ?>
                                    <a href="view_bookings.php?cancel_id=<?php echo $row['id']; ?>" class="btn btn-secondary" style="padding: 5px 10px; font-size: 0.8rem; background: #fee2e2; color: #ef4444; border:none;" onclick="return confirm('Force cancel this booking?');">
                                        Override
                                    </a>
                                <?php else: ?>
                                    <span style="color: grey;">Cancelled</span>
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
