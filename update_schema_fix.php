<?php
// update_schema_fix.php - Fix database schema
include 'config.php';

function run_query($conn, $sql, $msg) {
    if ($conn->query($sql) === TRUE) {
        echo "<p style='color: green;'>Success: $msg</p>";
    } else {
        echo "<p style='color: red;'>Error ($msg): " . $conn->error . "</p>";
    }
}

// 1. Fix Bookings Table
echo "<h2>Fixing Bookings Table...</h2>";
// Check if 'room_number' exists, if not, we might need to migrate 'classroom'
$check = $conn->query("SHOW COLUMNS FROM bookings LIKE 'room_number'");
if ($check->num_rows == 0) {
    // Rename/Change columns
    run_query($conn, "ALTER TABLE bookings CHANGE classroom room_number VARCHAR(50)", "Change classroom to room_number");
    run_query($conn, "ALTER TABLE bookings CHANGE time_from start_time TIME", "Change time_from to start_time");
    run_query($conn, "ALTER TABLE bookings CHANGE time_to end_time TIME", "Change time_to to end_time");
    
    // Add missing columns
    run_query($conn, "ALTER TABLE bookings ADD COLUMN reason TEXT", "Add reason column");
    run_query($conn, "ALTER TABLE bookings ADD COLUMN status ENUM('pending', 'approved', 'rejected', 'cancelled') DEFAULT 'pending'", "Add status column");
} else {
    echo "<p>Bookings table already updated.</p>";
}

// 2. Create Assignments Table
echo "<h2>Creating Assignments Table...</h2>";
$sql_assignments = "CREATE TABLE IF NOT EXISTS assignments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    faculty_id INT NOT NULL,
    year VARCHAR(10) NOT NULL,
    section VARCHAR(10) NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    file_path VARCHAR(255),
    deadline DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
run_query($conn, $sql_assignments, "Create assignments table");


// 3. Create Announcements Table
echo "<h2>Creating Announcements Table...</h2>";
$sql_announcements = "CREATE TABLE IF NOT EXISTS announcements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    faculty_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    year VARCHAR(10),
    section VARCHAR(10),
    type ENUM('info', 'alert', 'event') DEFAULT 'info',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
run_query($conn, $sql_announcements, "Create announcements table");

// 4. Create Activity Logs Table
echo "<h2>Creating Activity Logs Table...</h2>";
$sql_logs = "CREATE TABLE IF NOT EXISTS activity_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    action VARCHAR(255) NOT NULL,
    details TEXT,
    ip_address VARCHAR(45),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
run_query($conn, $sql_logs, "Create activity_logs table");

// 5. Create Classrooms Table
echo "<h2>Creating Classrooms Table...</h2>";
$sql_classrooms = "CREATE TABLE IF NOT EXISTS classrooms (
    id INT AUTO_INCREMENT PRIMARY KEY,
    room_number VARCHAR(50) NOT NULL UNIQUE,
    capacity INT DEFAULT 60,
    type ENUM('lecture_hall', 'lab', 'seminar_hall') DEFAULT 'lecture_hall',
    equipment TEXT,
    status ENUM('active', 'maintenance') DEFAULT 'active'
)";
run_query($conn, $sql_classrooms, "Create classrooms table");

// Seed Classrooms if empty
$chk_rooms = $conn->query("SELECT * FROM classrooms");
if ($chk_rooms && $chk_rooms->num_rows == 0) {
    $rooms_sql = "INSERT INTO classrooms (room_number, capacity, type, equipment) VALUES 
        ('AB1-101', 60, 'lecture_hall', 'Projector,Whiteboard'),
        ('AB1-102', 60, 'lecture_hall', 'Projector,Whiteboard'),
        ('CSE-LAB-1', 40, 'lab', 'Computers,Internet'),
        ('Small Seminar Hall', 100, 'seminar_hall', 'Audio,AC')";
    run_query($conn, $rooms_sql, "Seed classrooms");
}

echo "<h3>Database Fix Complete.</h3>";
?>
