<?php
// manage_users.php
include 'config.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Handle User Deletion
if (isset($_GET['delete_id'])) {
    $id = $_GET['delete_id'];
    mysqli_query($conn, "DELETE FROM users WHERE id='$id'");
    header("Location: manage_users.php");
    exit();
}

// Fetch Users
$result = mysqli_query($conn, "SELECT * FROM users ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Users | BookMyClass</title>
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
                    <span style="color: white; font-weight: 600; font-size: 0.9rem;"><?php echo htmlspecialchars($user['name']); ?></span>
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
                <h2>Manage Users</h2>
                <!-- Add User Button could go here linking to a register style form or modal -->
            </header>

            <div class="table-container">
                <table class="dashboard-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Year/Section</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td>#<?php echo $row['id']; ?></td>
                            <td><?php echo htmlspecialchars($row['name']); ?></td>
                            <td><?php echo htmlspecialchars($row['email']); ?></td>
                            <td>
                                <span class="badge" style="background: <?php echo ($row['role']=='admin'?'#fee2e2':'rgba(123,97,255,0.1)'); ?>; color: <?php echo ($row['role']=='admin'?'#ef4444':'#7b61ff'); ?>;">
                                    <?php echo ucfirst($row['role']); ?>
                                </span>
                            </td>
                            <td>
                                <?php echo ($row['year'] ? $row['year'] . '-' . $row['section'] : '-'); ?>
                            </td>
                            <td>
                                <a href="manage_users.php?delete_id=<?php echo $row['id']; ?>" class="btn btn-secondary" style="padding: 5px 10px; font-size: 0.8rem; background: #fee2e2; color: #ef4444; border:none;" onclick="return confirm('Delete this user?');">
                                    <i class="fa-solid fa-trash"></i>
                                </a>
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
