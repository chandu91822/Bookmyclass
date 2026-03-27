<?php
// cancel_booking.php
include 'config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['booking_id'])) {
    $booking_id = $_POST['booking_id'];
    $user_id = $_SESSION['user_id'];
    
    // Validate ownership before cancelling
    // Allow admin to cancel any, user only their own
    if ($_SESSION['role'] == 'admin') {
         $query = "UPDATE bookings SET status='cancelled' WHERE id='$booking_id'";
    } else {
         $query = "UPDATE bookings SET status='cancelled' WHERE id='$booking_id' AND user_id='$user_id'";
    }

    if (mysqli_query($conn, $query)) {
        // Redirect back to referring page
        header("Location: " . $_SERVER['HTTP_REFERER']);
    } else {
        echo "Error cancelling booking: " . mysqli_error($conn);
    }
} else {
    header("Location: index.php");
}
?>
