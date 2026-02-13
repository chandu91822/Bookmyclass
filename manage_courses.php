<?php
// manage_courses.php
include 'config.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Logic similar to classrooms
$msg = "";
// Add Course Logic here (omitted for brevity, just displaying UI)
// Assuming we create a courses table. For now, we will just simulate strict UI or create table if needed.
// "Do it your own" - I'll create a table if I have time, otherwise UI only.
// Let's make it fully functional.
$create_table = "CREATE TABLE IF NOT EXISTS courses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    course_name VARCHAR(100) NOT NULL,
    course_code VARCHAR(20) NOT NULL UNIQUE
)";
mysqli_query($conn, $create_table);

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['course_code'])) {
    $name = mysqli_real_escape_string($conn, $_POST['course_name']);
    $code = mysqli_real_escape_string($conn, $_POST['course_code']);
    
    $sql = "INSERT INTO courses (course_name, course_code) VALUES ('$name', '$code')";
    if(mysqli_query($conn, $sql)) {
        $msg = "Course added.";
    } else {
        $msg = "Error: " . mysqli_error($conn);
    }
}

if(isset($_GET['del'])) {
    $id = $_GET['del'];
    mysqli_query($conn, "DELETE FROM courses WHERE id='$id'");
    header("Location: manage_courses.php");
}

$courses = mysqli_query($conn, "SELECT * FROM courses");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Courses | BookMyClass</title>
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
                <a href="manage_classrooms.php">Classrooms</a>
                <a href="manage_courses.php" style="color: var(--secondary);">Courses</a>
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
                <h2>Manage Courses</h2>
            </header>

            <div class="form-container" style="max-width: 800px; margin: 0 auto 2rem;">
                <?php if($msg): ?>
                    <div style="color: green; margin-bottom: 20px;"><?php echo $msg; ?></div>
                <?php endif; ?>
                <form method="POST" style="display: grid; grid-template-columns: 1fr 1fr auto; gap: 1rem; align-items: end;">
                    <div class="form-group">
                        <label>Course Name</label>
                        <input type="text" name="course_name" class="form-input" required placeholder="e.g. Data Structures">
                    </div>
                    <div class="form-group">
                        <label>Course Code</label>
                        <input type="text" name="course_code" class="form-input" required placeholder="e.g. CS101">
                    </div>
                    <button type="submit" class="btn btn-primary" style="margin-bottom: 2px;">Add Course</button>
                </form>
            </div>

            <div class="table-container">
                <table class="dashboard-table">
                    <thead>
                        <tr>
                            <th>Course Name</th>
                            <th>Code</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = mysqli_fetch_assoc($courses)): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['course_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['course_code']); ?></td>
                            <td>
                                <a href="manage_courses.php?del=<?php echo $row['id']; ?>" class="btn btn-sm btn-outline" style="color: #dc3545; border-color: #dc3545;" onclick="return confirm('Delete course?')">Delete</a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</body>
</html>
