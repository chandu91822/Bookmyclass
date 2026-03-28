<?php
// system_logs.php
include 'config.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$result = mysqli_query($conn, "SELECT al.*, u.name as user_name FROM activity_logs al LEFT JOIN users u ON al.user_id = u.id ORDER BY al.created_at DESC LIMIT 100");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>System Logs | BookMyClass</title>
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
                <h2>System Activity Logs</h2>
            </header>

            <div class="table-container">
                <table class="dashboard-table">
                    <thead>
                        <tr>
                            <th>Time</th>
                            <th>User</th>
                            <th>Action</th>
                            <th>Details</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td style="color: var(--text-muted); font-size: 0.9rem;"><?php echo $row['created_at']; ?></td>
                            <td><?php echo htmlspecialchars($row['user_name'] ? $row['user_name'] : 'Unknown'); ?></td>
                            <td><span style="font-weight: 600; color:var(--primary-color);"><?php echo $row['action']; ?></span></td>
                            <td><?php echo htmlspecialchars($row['details']); ?></td>
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
