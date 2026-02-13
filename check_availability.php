<?php
// check_availability.php
include 'config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$role = $_SESSION['role'];
$available_rooms = [];
$search_performed = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $date = $_POST['date'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];
    $search_performed = true;

    // Logic: Find rooms NOT in bookings for this time with strict overlap check
    $query = "SELECT * FROM classrooms WHERE status='active' AND room_number NOT IN (
                SELECT room_number FROM bookings 
                WHERE date = '$date' 
                AND start_time < '$end_time' 
                AND end_time > '$start_time'
                AND status != 'cancelled'
              )";
    $result = mysqli_query($conn, $query);
    while($row = mysqli_fetch_assoc($result)) {
        $available_rooms[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Check Availability | BookMyClass</title>
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
                    <span style="color: white; font-weight: 600; font-size: 0.9rem;"><?php echo htmlspecialchars($name); ?></span>
                </div>
                <!-- Profile Icon -->
                <a href="<?php echo ($_SESSION['role'] == 'admin') ? 'admin_profile.php' : (($_SESSION['role'] == 'faculty') ? 'faculty_profile.php' : 'student_profile.php'); ?>" style="margin-right: 15px;" title="Profile">
                    <?php 
                        $pro_pic = "uploads/profiles/" . $_SESSION['user_id'] . ".jpg";
                        if(!file_exists($pro_pic)) {
                            $pro_pic = "https://ui-avatars.com/api/?name=" . urlencode($_SESSION['name'] ?? $name) . "&background=0D8ABC&color=fff";
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
        <main class="main-content fade-in-up glass-panel">
            <header class="dashboard-header">
                <h2>Check Room Availability</h2>
            </header>

            <div class="form-container" style="max-width: 800px; margin: 0 auto;">
                <form method="POST" style="display: grid; grid-template-columns: 1fr 1fr 1fr auto; gap: 1rem; align-items: end;">
                    <div class="form-group">
                        <label>Date</label>
                        <input type="date" name="date" class="form-input" required value="<?php echo $_POST['date'] ?? ''; ?>">
                    </div>
                    <div class="form-group">
                        <label>Start Time</label>
                        <input type="time" name="start_time" class="form-input" required value="<?php echo $_POST['start_time'] ?? ''; ?>">
                    </div>
                    <div class="form-group">
                        <label>End Time</label>
                        <input type="time" name="end_time" class="form-input" required value="<?php echo $_POST['end_time'] ?? ''; ?>">
                    </div>
                    <button type="submit" class="btn btn-primary" style="height: 50px; margin-bottom: 2px;">Search</button>
                </form>
            </div>

            <?php if($search_performed): ?>
            <div style="margin-top: 3rem;">
                <h3>Available Rooms</h3>
                <?php if(empty($available_rooms)): ?>
                    <p class="text-muted">No rooms available for this time slot.</p>
                <?php else: ?>
                    <div class="features-grid" style="margin-top: 1rem;">
                        <?php foreach($available_rooms as $room): ?>
                        <a href="book_class.php?classroom=<?php echo urlencode($room['room_number']); ?>&date=<?php echo urlencode($date); ?>&start_time=<?php echo urlencode($start_time); ?>&end_time=<?php echo urlencode($end_time); ?>" style="text-decoration: none; color: inherit; display: block;">
                            <div class="feature-card" style="text-align: center; transition: transform 0.2s; cursor: pointer;">
                                <i class="fa-solid fa-door-open" style="font-size: 2rem; color: var(--primary-color); margin-bottom: 0.5rem;"></i>
                                <h4><?php echo htmlspecialchars($room['room_number']); ?></h4>
                                <p><?php echo htmlspecialchars($room['type']); ?> (Cap: <?php echo $room['capacity']; ?>)</p>
                            </div>
                        </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </main>
    </div>
</body>
</html>
