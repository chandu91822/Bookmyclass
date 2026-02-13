<?php
// student_booking.php
include 'config.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: login.php");
    exit();
}

// Fetch Classrooms
$rooms_result = mysqli_query($conn, "SELECT * FROM classrooms WHERE status='active'");

// Fetch Faculty
$faculty_result = mysqli_query($conn, "SELECT name FROM users WHERE role='faculty'");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Request Slot | BookMyClass</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/landing.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        async function submitBooking(event) {
            event.preventDefault();
            const formData = new FormData(document.getElementById('bookingForm'));
            
            const response = await fetch('booking_logic.php', {
                method: 'POST',
                body: formData
            });
            
            const result = await response.json();
            const messageDiv = document.getElementById('message');
            messageDiv.innerHTML = result.message;
            messageDiv.style.color = result.status === 'success' ? 'green' : 'red';
            
            if (result.status === 'success') {
                setTimeout(() => window.location.href = 'student_dashboard.php', 2000);
            }
        }
    </script>
</head>
<body>
    
    <!-- Top Navbar -->
    <nav class="navbar">
        <div class="container nav-container">
            <a href="index.php" class="logo">
                <i class="fa-solid fa-shapes"></i>
                BookMyClass
            </a>
            <div class="nav-links">
                <a href="student_dashboard.php">Dashboard</a>
                <a href="student_booking.php" style="color: var(--secondary);">Request Slot</a>
                <a href="my_bookings.php">My Bookings</a>
                <a href="student_profile.php">Profile</a>
            </div>
            <div class="nav-actions">
                <div style="display: flex; flex-direction: column; align-items: flex-end; margin-right: 10px;">
                    <span style="color: white; font-weight: 600; font-size: 0.9rem;"><?php echo htmlspecialchars($_SESSION['name'] ?? 'Student'); ?></span>
                    <span style="color: var(--text-muted); font-size: 0.75rem;">Student</span>
                </div>
                <!-- Mobile Logout/Profile could go here or just Logout -->
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
                <h2>Request a Classroom</h2>
            </header>

            <div class="form-container" style="margin: 0; max-width: 600px;">
                <div id="message" style="margin-bottom: 20px; font-weight: bold;"></div>
                <form id="bookingForm" onsubmit="submitBooking(event)">
                    
                    <div class="form-group">
                        <label>Select Room</label>
                        <select name="room_id" class="form-input" required>
                            <option value="">-- Choose Room --</option>
                            <?php while($room = mysqli_fetch_assoc($rooms_result)): ?>
                                <option value="<?php echo $room['room_number']; ?>">
                                    <?php echo $room['room_number'] . " (" . $room['type'] . ", Cap: " . $room['capacity'] . ")"; ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Subject</label>
                        <input type="text" name="subject" class="form-input" placeholder="e.g. Mathematics" required>
                    </div>

                    <div class="form-group">
                        <label>Select Faculty</label>
                        <select name="faculty" class="form-input" required>
                            <option value="">-- Choose Faculty --</option>
                            <?php while($fac = mysqli_fetch_assoc($faculty_result)): ?>
                                <option value="<?php echo htmlspecialchars($fac['name']); ?>"><?php echo htmlspecialchars($fac['name']); ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Date</label>
                        <input type="date" name="date" class="form-input" min="<?php echo date('Y-m-d'); ?>" required>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                        <div class="form-group">
                            <label>Start Time</label>
                            <input type="time" name="start_time" class="form-input" min="08:30" max="16:40" required>
                        </div>
                        <div class="form-group">
                            <label>End Time</label>
                            <input type="time" name="end_time" class="form-input" min="08:30" max="16:40" required>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary form-btn">Check Availability & Book</button>
                </form>
            </div>
        </main>
    </div>
    <script src="js/landing.js"></script>
</body>
</html>
