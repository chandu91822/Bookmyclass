<?php
include "config.php";

// Get current date and time
$current_time = new DateTime();

// 🔔 Fetch Latest 5 Booking Notifications (Future Only)
$notif_sql = "SELECT classroom, date, time_from, time_to 
              FROM bookings 
              ORDER BY date DESC, time_from DESC LIMIT 5";
$notif_result = $conn->query($notif_sql);
$notifications = [];

while ($row = $notif_result->fetch_assoc()) {
    $booking_start = new DateTime($row['date'] . ' ' . $row['time_from']);
    if ($booking_start > $current_time) {
        $formatted_date = date('d-m-Y', strtotime($row['date']));
        $formatted_from = date('h:i A', strtotime($row['time_from']));
        $formatted_to = date('h:i A', strtotime($row['time_to']));
        
        $notifications[] = "📌 " . $row['classroom'] . " booked on " . $formatted_date . 
                           " from " . $formatted_from . " to " . $formatted_to;
    }
}

// 📊 Fetch Booking Data for Graph (Last 7 Days, Future Only)
$graph_sql = "SELECT classroom, date, time_from, COUNT(*) AS usage_count 
              FROM bookings 
              WHERE date >= CURDATE() - INTERVAL 7 DAY
              GROUP BY classroom, date, time_from";
$graph_result = $conn->query($graph_sql);
$rooms = [];
$counts = [];
$times = [];

while ($row = $graph_result->fetch_assoc()) {
    $booking_start = new DateTime($row['date'] . ' ' . $row['time_from']);
    if ($booking_start > $current_time) {
        $rooms[] = $row['classroom'];
        $counts[] = $row['usage_count'];
        $times[] = $row['date'] . ' ' . $row['time_from'];
    }
}

// 🆕 NEW: Classroom Booking Count (Total Per Classroom)
$room_count_sql = "SELECT classroom, COUNT(*) AS total_count FROM bookings GROUP BY classroom";
$room_count_result = $conn->query($room_count_sql);
$classroomLabels = [];
$classroomCounts = [];

while ($row = $room_count_result->fetch_assoc()) {
    $classroomLabels[] = $row['classroom'];
    $classroomCounts[] = $row['total_count'];
}

// 🚀 Return Full JSON Response
echo json_encode([
    "notifications" => $notifications,
    "rooms" => $rooms,
    "counts" => $counts,
    "times" => $times,
    "classroomLabels" => $classroomLabels,
    "classroomCounts" => $classroomCounts
]);

$conn->close();
?>
