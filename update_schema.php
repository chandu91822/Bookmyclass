<?php
// update_schema.php - Add new tables for dashboard expansion
include 'config.php';

$queries = [];

// 1. Assignments Table
$queries[] = "CREATE TABLE IF NOT EXISTS assignments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    faculty_id INT NOT NULL,
    year VARCHAR(10) NOT NULL,
    section VARCHAR(10) NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    file_path VARCHAR(255),
    deadline DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (faculty_id) REFERENCES users(id) ON DELETE CASCADE
)";

// 2. Announcements Table
$queries[] = "CREATE TABLE IF NOT EXISTS announcements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    faculty_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    year VARCHAR(10), -- NULL means all years
    section VARCHAR(10), -- NULL means all sections
    type ENUM('info', 'alert', 'event') DEFAULT 'info',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (faculty_id) REFERENCES users(id) ON DELETE CASCADE
)";

// 3. Activity Logs Table
$queries[] = "CREATE TABLE IF NOT EXISTS activity_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    action VARCHAR(255) NOT NULL,
    details TEXT,
    ip_address VARCHAR(45),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    -- No foreign key on user_id to keep logs even if user is deleted
)";

// 4. Classrooms Table (For better management than hardcoding)
$queries[] = "CREATE TABLE IF NOT EXISTS classrooms (
    id INT AUTO_INCREMENT PRIMARY KEY,
    room_number VARCHAR(50) NOT NULL UNIQUE,
    capacity INT DEFAULT 60,
    type ENUM('lecture_hall', 'lab', 'seminar_hall') DEFAULT 'lecture_hall',
    equipment TEXT, -- JSON or comma-separated list
    status ENUM('active', 'maintenance') DEFAULT 'active'
)";

// Execute Queries
echo "<h2>Updating Database Schema...</h2>";
foreach ($queries as $sql) {
    if ($conn->query($sql) === TRUE) {
        echo "<p style='color: green;'>Success: " . substr($sql, 0, 50) . "...</p>";
    } else {
        echo "<p style='color: red;'>Error: " . $conn->error . "</p>";
    }
}

// Populate basic classrooms if empty
$check_rooms = $conn->query("SELECT * FROM classrooms");
if ($check_rooms->num_rows == 0) {
    $rooms = [
        "('AB1-101', 60, 'lecture_hall', 'Projector,Whiteboard')",
        "('AB1-102', 60, 'lecture_hall', 'Projector,Whiteboard')",
        "('AB1-201', 120, 'seminar_hall', 'Audio System,Projector,AC')",
        "('CSE-LAB-1', 40, 'lab', 'Computers,Internet')",
    ];
    $insert_rooms = "INSERT INTO classrooms (room_number, capacity, type, equipment) VALUES " . implode(",", $rooms);
    if ($conn->query($insert_rooms) === TRUE) {
        echo "<p style='color: green;'>Seeded initial classrooms.</p>";
    } else {
        echo "<p style='color: red;'>Error seeding rooms: " . $conn->error . "</p>";
    }
}

echo "<h3>Database Update Complete.</h3>";
?>
