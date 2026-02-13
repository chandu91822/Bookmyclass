<?php
// book_class.php - Refactored with Home Page Theme & Auto-Fetch
session_start();
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

// Fetch User Details from Database
$user_query = mysqli_query($conn, "SELECT name, year, section FROM users WHERE id='$user_id'");
$user_data = mysqli_fetch_assoc($user_query);
$name = $user_data['name'];
$year = $user_data['year'];
$section = $user_data['section'];

// Fetch Classrooms & Faculty (for dropdowns)
$rooms_result = mysqli_query($conn, "SELECT * FROM classrooms WHERE status='active'");
$faculty_result = mysqli_query($conn, "SELECT name FROM users WHERE role='faculty'");

$success_msg = "";
$error_msg = "";

// Pre-fill from URL if available
$pre_classroom = $_GET['classroom'] ?? '';
$pre_date = $_GET['date'] ?? '';
$pre_start_time = $_GET['start_time'] ?? '';
$pre_end_time = $_GET['end_time'] ?? '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $classroom = $_POST['classroom'];
    $subject = $_POST['subject'];
    $faculty_input = $_POST['faculty']; // If student, this matches 'faculty' column. If faculty, they might be the faculty?
    $date = $_POST['date'];
    $time_from = $_POST['time_from'];
    $time_to = $_POST['time_to'];

    // If logged in user is faculty, they are the faculty. But if student, they selected a faculty.
    // The form will allow selecting faculty. 
    // If the user IS faculty, maybe auto-fill their name or allow them to book on behalf?
    // User request: "remove name, year, section... fetched from users table".
    // We will use the fetched $name, $year, $section for the INSERT.

    if ($time_from < "08:30" || $time_to > "16:40") {
        $error_msg = "⏰ Bookings allowed only between 08:30 AM to 04:40 PM";
    } elseif ($time_from >= $time_to) {
        $error_msg = "⚠️ End time must be after start time";
    } else {
        // Conflict Check
        $conflict_check = "SELECT * FROM bookings WHERE room_number=? AND date=? AND (
            (start_time < ? AND end_time > ?)
        ) AND status != 'cancelled'";
        $stmt = mysqli_prepare($conn, $conflict_check);
        // Overlap logic: (StartA < EndB) and (EndA > StartB) -> here (StartNew < EndExisting) and (EndNew > StartExisting)
        // Correct logic: Existing.Start < New.End AND Existing.End > New.Start
        // My query usage: existing.start < time_to AND existing.end > time_from
        // Wait, the previous logic in book_class.php was:
        // (start_time <= ? AND end_time > ?) OR (start_time < ? AND end_time >= ?)
        // Let's use the standard overlap: (StartA < EndB) and (EndA > StartB)
        // SQL: start_time < $time_to AND end_time > $time_from
        $conflict_query = "SELECT * FROM bookings WHERE room_number='$classroom' AND date='$date' AND (start_time < '$time_to' AND end_time > '$time_from') AND status != 'cancelled'";
        $conflict_result = mysqli_query($conn, $conflict_query);

        if (mysqli_num_rows($conflict_result) > 0) {
            $conflict_row = mysqli_fetch_assoc($conflict_result);
            $booked_start = date("g:i A", strtotime($conflict_row['start_time']));
            $booked_end = date("g:i A", strtotime($conflict_row['end_time']));
            $error_msg = "⚠️ Classroom already booked for this time! ($booked_start - $booked_end)";
        } else {
            // Determine Status
            // Auto-approve for everyone as per request
            $status = 'approved';

            // Insert
            $stmt = $conn->prepare("INSERT INTO bookings (user_id, name, year, section, room_number, subject, faculty, date, start_time, end_time, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("issssssssss", $user_id, $name, $year, $section, $classroom, $subject, $faculty_input, $date, $time_from, $time_to, $status);
            
            if ($stmt->execute()) {
                $success_msg = "Booking successful!";
                // Log
                mysqli_query($conn, "INSERT INTO activity_logs (user_id, action, details) VALUES ('$user_id', 'BOOKING_CREATED', 'Booked $classroom on $date')");
                
                // Redirect logic? User didn't specify. Just show success.
                echo "<script>setTimeout(() => window.location.href = '" . ($role == 'student' ? 'student_dashboard.php' : 'faculty_dashboard.php') . "', 2000);</script>";
            } else {
                $error_msg = "Database Error: " . $conn->error;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Request Slot | BookMyClass</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@500;600;700;800&display=swap" rel="stylesheet">
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
                    <a href="book_class.php" style="color: var(--secondary);">Request Slot</a>
                    <a href="my_bookings.php">My Bookings</a>
                <?php elseif(isset($_SESSION['role']) && $_SESSION['role'] == 'faculty'): ?>
                    <a href="faculty_dashboard.php">Dashboard</a>
                    <a href="check_availability.php">Check Availability</a>                   
                    <a href="book_class.php" style="color: var(--secondary);">Book Class</a>
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
                    <span style="color: white; font-weight: 600; font-size: 0.9rem;"><?php echo htmlspecialchars($_SESSION['name'] ?? $name); ?></span>
                    <span style="color: var(--text-muted); font-size: 0.75rem;"><?php echo ucfirst($_SESSION['role'] ?? ''); ?></span>
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
        
        <main class="main-content fade-in-up">
            <header class="dashboard-header">
                <h2><?php echo ($role == 'student') ? "Request a Classroom" : "Book a Classroom"; ?></h2>
            </header>

            <div class="form-container" style="margin: 0; max-width: 600px;">
                <?php if($success_msg): ?>
                    <div style="color: #10b981; background: rgba(16, 185, 129, 0.1); padding: 12px; border-radius: 8px; margin-bottom: 20px; border: 1px solid rgba(16, 185, 129, 0.2); display: flex; align-items: center; gap: 10px;">
                        <i class="fa-solid fa-check-circle"></i> <?php echo $success_msg; ?>
                    </div>
                <?php endif; ?>
                <?php if($error_msg): ?>
                     <div style="color: #ef4444; background: rgba(239, 68, 68, 0.1); padding: 12px; border-radius: 8px; margin-bottom: 20px; border: 1px solid rgba(239, 68, 68, 0.2); display: flex; align-items: center; gap: 10px;">
                        <i class="fa-solid fa-circle-exclamation"></i> <?php echo $error_msg; ?>
                    </div>
                <?php endif; ?>

                <form method="POST">
                    
                    <div class="form-group">
                        <label>Select Classroom</label>
                        <select name="classroom" class="form-input" required>
                            <option value="">-- Choose Room --</option>
                            <?php 
                            // Reset pointer just in case
                            mysqli_data_seek($rooms_result, 0);
                            while($room = mysqli_fetch_assoc($rooms_result)): 
                                $selected = ($pre_classroom == $room['room_number']) ? 'selected' : '';
                            ?>
                                <option value="<?php echo htmlspecialchars($room['room_number']); ?>" <?php echo $selected; ?>>
                                    <?php echo htmlspecialchars($room['room_number']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Subject</label>
                        <input type="text" name="subject" class="form-input" placeholder="e.g. Mathematics" required>
                    </div>

                    <div class="form-group">
                        <label>Faculty</label>
                        <?php if($role == 'faculty'): ?>
                            <input type="text" name="faculty" class="form-input" value="<?php echo htmlspecialchars($name); ?>" readonly>
                        <?php else: ?>
                            <select name="faculty" class="form-input" required>
                                <option value="">-- Select Faculty --</option>
                                <?php while($fac = mysqli_fetch_assoc($faculty_result)): ?>
                                    <option value="<?php echo htmlspecialchars($fac['name']); ?>"><?php echo htmlspecialchars($fac['name']); ?></option>
                                <?php endwhile; ?>
                            </select>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label>Date</label>
                        <input type="date" name="date" class="form-input" min="<?php echo date('Y-m-d'); ?>" required value="<?php echo htmlspecialchars($pre_date); ?>">
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                        <div class="form-group">
                            <label>Start Time</label>
                            <input type="time" name="time_from" class="form-input" min="08:30" max="16:40" required value="<?php echo htmlspecialchars($pre_start_time); ?>">
                        </div>
                        <div class="form-group">
                            <label>End Time</label>
                            <input type="time" name="time_to" class="form-input" min="08:30" max="16:40" required value="<?php echo htmlspecialchars($pre_end_time); ?>">
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary form-btn">
                        <?php echo ($role == 'student') ? "Submit Request" : "Book Class"; ?>
                    </button>
                </form>
            </div>
        </main>
    </div>
    <script src="js/landing.js"></script>
</body>
</html>
