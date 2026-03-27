<?php
include 'config.php';

$classroom = $_GET['classroom'];
$date = $_GET['date'];
$time = $_GET['time'];

$query = "SELECT * FROM bookings WHERE classroom='$classroom' AND date='$date' AND time='$time'";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) > 0) {
    echo "Conflict Detected!";
} else {
    echo "Available!";
}
?>
