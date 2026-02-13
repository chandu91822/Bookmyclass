<?php
// student_profile.php
include 'config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$user_query = mysqli_query($conn, "SELECT * FROM users WHERE id='$user_id'");
$user = mysqli_fetch_assoc($user_query);

$msg = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_pass = $_POST['password'];
    // In production, hash password
    mysqli_query($conn, "UPDATE users SET password='$new_pass' WHERE id='$user_id'");
    $msg = "Password updated successfully!";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Profile | BookMyClass</title>
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
                    <a href="student_dashboard.php">Dashboard</a>
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
                    <a href="manage_classrooms.php">Classrooms</a>
                    <a href="manage_courses.php">Courses</a>
                    <a href="export_reports.php">Reports</a>
                    <a href="system_logs.php">Logs</a>
                <?php endif; ?>
            </div>
            <div class="nav-actions">
                <div style="display: flex; flex-direction: column; align-items: flex-end; margin-right: 15px;">
                    <span style="color: white; font-weight: 600; font-size: 0.9rem;"><?php echo htmlspecialchars($_SESSION['name'] ?? $user['name']); ?></span>
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
                <h2>My Profile</h2>
            </header>

            <div class="profile-layout" style="display: flex; gap: 2rem; flex-wrap: wrap;">
                <div class="profile-image-section" style="text-align: center; flex: 1; min-width: 250px;">
                    <?php 
                        $profile_pic = "uploads/profiles/" . $_SESSION['user_id'] . ".jpg";
                        if(!file_exists($profile_pic)) {
                            $profile_pic = "https://ui-avatars.com/api/?name=" . urlencode($_SESSION['name']) . "&background=0D8ABC&color=fff";
                        }
                    ?>
                    <img src="<?php echo $profile_pic; ?>?t=<?php echo time(); ?>" alt="Profile" style="width: 150px; height: 150px; border-radius: 50%; object-fit: cover; border: 4px solid white; box-shadow: var(--card-shadow); margin-bottom: 1rem;">
                    
                    <form action="upload_profile_pic.php" method="POST" enctype="multipart/form-data">
                        <label for="profile_pic" class="btn btn-sm btn-outline" style="cursor: pointer; display: inline-block;">Change Photo</label>
                        <input type="file" name="profile_pic" id="profile_pic" style="display: none;" onchange="this.form.submit()">
                    </form>
                </div>

                <div class="form-container" style="margin: 0; flex: 2;">
                <?php if($msg): ?>
                    <div style="color: green; margin-bottom: 20px;"><?php echo $msg; ?></div>
                <?php endif; ?>
                
                <div class="form-group">
                    <label>Name</label>
                    <input type="text" class="form-input" value="<?php echo htmlspecialchars($user['name']); ?>" disabled style="background: #f1f5f9;">
                </div>
                
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" class="form-input" value="<?php echo htmlspecialchars($user['email']); ?>" disabled style="background: #f1f5f9;">
                </div>

                <?php if(isset($_SESSION['role']) && $_SESSION['role'] == 'student'): ?>
                <div style="display: flex; gap: 20px;">
                    <div class="form-group" style="flex:1;">
                        <label>Year</label>
                        <input type="text" class="form-input" value="<?php echo htmlspecialchars($user['year']); ?>" disabled style="background: #f1f5f9;">
                    </div>
                    <div class="form-group" style="flex:1;">
                        <label>Section</label>
                        <input type="text" class="form-input" value="<?php echo htmlspecialchars($user['section']); ?>" disabled style="background: #f1f5f9;">
                    </div>
                </div>
                <?php endif; ?>

                <form method="POST">
                    <div class="form-group">
                        <label>New Password</label>
                        <input type="password" name="password" class="form-input" placeholder="Enter new password to change">
                    </div>
                    <button type="submit" class="btn btn-primary form-btn">Update Password</button>
                </form>
            </div>
            </div>
        </main>
    </div>
    <script src="js/landing.js"></script>
</body>
</html>
