<?php
include 'config.php';
$result = mysqli_query($conn, "DESCRIBE bookings");
while ($row = mysqli_fetch_assoc($result)) {
    print_r($row);
    echo "\n";
}
?>
