<?php
// view_students.php
include 'config.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'faculty') {
    header("Location: login.php");
    exit();
}

// Fetch Students
$students_query = "SELECT * FROM users WHERE role = 'student' ORDER BY year ASC, name ASC";
$result = mysqli_query($conn, $students_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Students | BookMyClass</title>
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
               <a href="faculty_dashboard.php">Dashboard</a>
                    <a href="check_availability.php">Check Availability</a>                   
                    <a href="book_class.php">Book Class</a>
                    <a href="faculty_schedule.php">My Schedule</a>
                    <a href="view_students.php">Students</a>
            </div>
            <div class="nav-actions">
                <div style="display: flex; flex-direction: column; align-items: flex-end; margin-right: 15px;">
                    <span style="color: white; font-weight: 600; font-size: 0.9rem;"><?php echo htmlspecialchars($_SESSION['name']); ?></span>
                    <span style="color: var(--text-muted); font-size: 0.75rem;">Faculty</span>
                </div>
                <!-- Profile Icon -->
                <a href="faculty_profile.php" style="margin-right: 15px;" title="Profile">
                    <img src="uploads/profiles/<?php echo $_SESSION['user_id']; ?>.jpg" onerror="this.src='https://ui-avatars.com/api/?name=<?php echo urlencode($_SESSION['name']); ?>&background=0D8ABC&color=fff'" alt="Profile" style="width: 40px; height: 40px; border-radius: 50%; border: 2px solid white; object-fit: cover;">
                </a>
                <a href="logout.php" class="btn btn-primary" style="padding: 8px 18px; font-size: 0.9rem;">Logout</a>
            </div>
        </div>
    </nav>

    <div class="dashboard-layout">
        <main class="main-content fade-in-up glass-panel">
            <header class="dashboard-header">
                <h2>Registered Students</h2>
            </header>

            <div class="table-container">
                <table class="dashboard-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Year</th>
                            <th>Section</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td>
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    <div style="width: 30px; height: 30px; border-radius: 50%; background: var(--primary-color); color: white; display: flex; align-items: center; justify-content: center; font-size: 0.8rem;">
                                        <?php echo strtoupper(substr($row['name'], 0, 1)); ?>
                                    </div>
                                    <?php echo htmlspecialchars($row['name']); ?>
                                </div>
                            </td>
                            <td><?php echo htmlspecialchars($row['email']); ?></td>
                            <td><?php echo htmlspecialchars($row['year']); ?></td>
                            <td><?php echo htmlspecialchars($row['section'] ?? '-'); ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</body>
</html>
