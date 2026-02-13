<?php
// manage_classrooms.php
include 'config.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$msg = "";
// Handle Add
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['room_number'])) {
    $room = mysqli_real_escape_string($conn, $_POST['room_number']);
    $capacity = mysqli_real_escape_string($conn, $_POST['capacity']);
    $type = mysqli_real_escape_string($conn, $_POST['type']);
    
    $check = mysqli_query($conn, "SELECT * FROM classrooms WHERE room_number='$room'");
    if(mysqli_num_rows($check) > 0) {
        $msg = "Room already exists!";
    } else {
        $sql = "INSERT INTO classrooms (room_number, capacity, type, status) VALUES ('$room', '$capacity', '$type', 'active')";
        if(mysqli_query($conn, $sql)) {
            $msg = "Classroom added successfully.";
        } else {
            $msg = "Error: " . mysqli_error($conn);
        }
    }
}

// Handle Delete
if (isset($_GET['delete_id'])) {
    $id = $_GET['delete_id'];
    mysqli_query($conn, "DELETE FROM classrooms WHERE id='$id'");
    header("Location: manage_classrooms.php");
    exit();
}

$rooms = mysqli_query($conn, "SELECT * FROM classrooms ORDER BY room_number ASC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Classrooms | BookMyClass</title>
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
                <a href="manage_classrooms.php" style="color: var(--secondary);">Classrooms</a>
                <a href="manage_courses.php">Courses</a>
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
                <h2>Manage Classrooms</h2>
            </header>

            <div class="form-container" style="max-width: 800px; margin: 0 auto 2rem;">
                <?php if($msg): ?>
                    <div style="color: green; margin-bottom: 20px;"><?php echo $msg; ?></div>
                <?php endif; ?>
                <form method="POST" style="display: grid; grid-template-columns: 1fr 1fr 1fr auto; gap: 1rem; align-items: end;">
                    <div class="form-group">
                        <label>Room Number</label>
                        <input type="text" name="room_number" class="form-input" required placeholder="e.g. 101">
                    </div>
                    <div class="form-group">
                        <label>Capacity</label>
                        <input type="number" name="capacity" class="form-input" required placeholder="60">
                    </div>
                    <div class="form-group">
                        <label>Type</label>
                        <select name="type" class="form-input">
                            <option value="Lecture Hall">Lecture Hall</option>
                            <option value="Lab">Lab</option>
                            <option value="Seminar Hall">Seminar Hall</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary" style="margin-bottom: 2px;">Add Room</button>
                </form>
            </div>

            <div class="table-container">
                <table class="dashboard-table">
                    <thead>
                        <tr>
                            <th>Room Number</th>
                            <th>Capacity</th>
                            <th>Type</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = mysqli_fetch_assoc($rooms)): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['room_number']); ?></td>
                            <td><?php echo htmlspecialchars($row['capacity']); ?></td>
                            <td><?php echo htmlspecialchars($row['type']); ?></td>
                            <td>
                                <a href="manage_classrooms.php?delete_id=<?php echo $row['id']; ?>" class="btn btn-sm btn-outline" style="color: #dc3545; border-color: #dc3545;" onclick="return confirm('Delete this room?')">Delete</a>
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
