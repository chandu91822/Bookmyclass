<?php
// student_assignments.php
include 'config.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: login.php");
    exit();
}

// Fetch Assignments
$user_id = $_SESSION['user_id'];
// Fetch student details
$u_query = mysqli_query($conn, "SELECT year, section FROM users WHERE id='$user_id'");
$u_data = mysqli_fetch_assoc($u_query);
$my_year = $u_data['year'];
$my_sec = $u_data['section'];

// Fetch relevant assignments (Match Year & Section OR 'All')
$assignments_query = "SELECT a.*, u.name as faculty_name FROM assignments a JOIN users u ON a.faculty_id = u.id WHERE (a.year='$my_year' OR a.year='All' OR a.year='') AND (a.section='$my_sec' OR a.section='All' OR a.section='') ORDER BY a.uploaded_at DESC";
$result = mysqli_query($conn, $assignments_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Assignments | BookMyClass</title>
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
                <a href="student_dashboard.php">Dashboard</a>
                <a href="student_booking.php">Request Slot</a>
                <a href="my_bookings.php">My Bookings</a>
                <a href="student_assignments.php" style="color: var(--secondary);">Assignments</a>
                <a href="student_announcements.php">Announcements</a>
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
                <h2>Assignments</h2>
            </header>

            <div class="table-container">
                <table class="dashboard-table">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Description</th>
                            <th>Faculty</th>
                            <th>Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['title']); ?></td>
                            <td><?php echo htmlspecialchars($row['description']); ?></td>
                            <td><?php echo htmlspecialchars($row['faculty_name']); ?></td>
                            <td><?php echo date("M d, Y", strtotime($row['uploaded_at'])); ?></td>
                            <td>
                                <a href="uploads/<?php echo htmlspecialchars($row['file_path']); ?>" class="btn btn-sm btn-outline" download>
                                    <i class="fa-solid fa-download"></i> Download
                                </a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                        
                        <?php if(mysqli_num_rows($result) == 0): ?>
                        <tr>   
                            <td colspan="5" style="text-align:center; padding: 2rem;">No assignments unavailable.</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</body>
</html>
