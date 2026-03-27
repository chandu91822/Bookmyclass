<?php
session_start();
require_once "config.php"; // Ensure database connection is correct

header('Content-Type: application/json');

// Debug: Check if faculty name exists in the session
if (!isset($_SESSION['name'])) {
    echo json_encode(["error" => "Faculty not logged in"]);
    exit;
}

$faculty_name = $_SESSION['name']; // Fetch faculty name from session

// Debug: Log session faculty name
error_log("Faculty Name from Session: " . $faculty_name);

// Fetch bookings where faculty name in `bookings` matches session name
$query = "
    SELECT year, section, room_number as classroom, date, start_time as time_from, end_time as time_to 
    FROM bookings 
    WHERE faculty = ?
";

$stmt = $conn->prepare($query);
$stmt->bind_param("s", $faculty_name);
$stmt->execute();
$result = $stmt->get_result();

$bookings = [];
while ($row = $result->fetch_assoc()) {
    $bookings[] = $row;
}

// Debug: Log fetched bookings
error_log("Bookings Data: " . json_encode($bookings));

echo json_encode($bookings);
?>
