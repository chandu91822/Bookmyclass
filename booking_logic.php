<?php
// booking_logic.php - enhanced booking system
include 'config.php';
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $role = $_SESSION['role'];
    $room_id = $_POST['room_id']; 
    $date = $_POST['date'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];
    $subject = $_POST['subject']; // Changed from reason to subject
    $faculty = $_POST['faculty']; 

    // Fetch Student Details (Year, Section, Name)
    $user_query = mysqli_query($conn, "SELECT name, year, section FROM users WHERE id='$user_id'");
    $user_data = mysqli_fetch_assoc($user_query);
    $name = $user_data['name'];
    $year = $user_data['year'];
    $section = $user_data['section'];

    // 0. Time Range Validation (Book Class Logic)
    if ($start_time < "08:30" || $end_time > "16:40") {
        echo json_encode(['status' => 'error', 'message' => 'Bookings allowed only between 08:30 AM to 04:40 PM']);
        exit();
    }

    // 1. Basic Validation
    if (strtotime($start_time) >= strtotime($end_time)) {
        echo json_encode(['status' => 'error', 'message' => 'End time must be after start time.']);
        exit();
    }

    // 2. Conflict Detection
    $conflict_query = "SELECT * FROM bookings WHERE room_number = '$room_id' AND date = '$date' AND 
                       (('$start_time' < end_time) AND ('$end_time' > start_time)) AND status != 'cancelled'";
    
    $conflict_result = mysqli_query($conn, $conflict_query);

    if (mysqli_num_rows($conflict_result) > 0) {
        // Log conflict attempt
        $log_sql = "INSERT INTO activity_logs (user_id, action, details) VALUES ('$user_id', 'BOOKING_CONFLICT', 'Attempted to book $room_id on $date ($start_time-$end_time)')";
        mysqli_query($conn, $log_sql);

        echo json_encode(['status' => 'error', 'message' => 'Conflict detected! Room is already booked for this slot.']);
        exit();
    }

    // 3. Faculty Priority / Auto-Approval
    $status = 'pending'; 
    $status = 'approved'; 
    if ($role == 'faculty' || $role == 'admin') {
        $status = 'approved';
    }

    // 4. Insert Booking with Subject
    // Assuming 'subject' column exists in bookings as per book_class.php
    $stmt = $conn->prepare("INSERT INTO bookings (user_id, name, year, section, room_number, date, start_time, end_time, subject, faculty, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("issssssssss", $user_id, $name, $year, $section, $room_id, $date, $start_time, $end_time, $subject, $faculty, $status);

    if ($stmt->execute()) {
        // Log success
        $log_sql = "INSERT INTO activity_logs (user_id, action, details) VALUES ('$user_id', 'BOOKING_CREATED', 'Booked $room_id on $date')";
        mysqli_query($conn, $log_sql);

        echo json_encode(['status' => 'success', 'message' => 'Booking request submitted successfully!']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $conn->error]);
    }
}
?>
