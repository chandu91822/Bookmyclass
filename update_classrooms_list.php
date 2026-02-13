<?php
include 'config.php';

// 1. Truncate table
$truncate = "TRUNCATE TABLE classrooms";
if ($conn->query($truncate) === TRUE) {
    echo "Classrooms table truncated.<br>";
} else {
    echo "Error truncating table: " . $conn->error . "<br>";
}

// 2. Prepare new rooms
$rooms = [];
// GF-1 to GF-10
for ($i = 1; $i <= 10; $i++) {
    $rooms[] = "('GF-$i', 60, 'lecture_hall', 'active')";
}
// Seminar Halls
$rooms[] = "('Small Seminar Hall', 100, 'seminar_hall', 'active')";
$rooms[] = "('Big Seminar Hall', 200, 'seminar_hall', 'active')";

// 3. Insert new rooms
$sql = "INSERT INTO classrooms (room_number, capacity, type, status) VALUES " . implode(',', $rooms);

if ($conn->query($sql) === TRUE) {
    echo "New classrooms inserted successfully.<br>";
} else {
    echo "Error inserting classrooms: " . $conn->error . "<br>";
}

$conn->close();
?>
