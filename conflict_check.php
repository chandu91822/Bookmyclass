<?php
include 'config.php';

header('Content-Type: text/plain');

$classroom = $_GET['classroom'] ?? '';
$date = $_GET['date'] ?? '';
$start_time = $_GET['start_time'] ?? ($_GET['time'] ?? '');
$end_time = $_GET['end_time'] ?? '';

if ($classroom === '' || $date === '' || $start_time === '') {
    http_response_code(400);
    echo "Invalid request";
    exit();
}

if ($end_time === '') {
    // Backward-compatible mode when only one time value is passed.
    $stmt = $conn->prepare("SELECT id FROM bookings WHERE room_number = ? AND date = ? AND start_time = ? LIMIT 1");
    $stmt->bind_param("sss", $classroom, $date, $start_time);
} else {
    // Overlap check with current schema.
    $stmt = $conn->prepare(
        "SELECT id FROM bookings
         WHERE room_number = ?
           AND date = ?
           AND (? < end_time AND ? > start_time)
           AND status != 'cancelled'
         LIMIT 1"
    );
    $stmt->bind_param("ssss", $classroom, $date, $start_time, $end_time);
}

$stmt->execute();
$result = $stmt->get_result();
echo mysqli_num_rows($result) > 0 ? "Conflict Detected!" : "Available!";
if ($stmt) {
    $stmt->close();
}
if ($conn) {
    $conn->close();
}
?>
