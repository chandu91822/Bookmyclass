<?php
include 'config.php';

// Check Bookings Table
$result = $conn->query("DESCRIBE bookings");
echo "<h3>Bookings Table</h3>";
while($row = $result->fetch_assoc()) {
    echo $row['Field'] . " - " . $row['Type'] . "<br>";
}

// Check Users Table
$result = $conn->query("DESCRIBE users");
echo "<h3>Users Table</h3>";
while($row = $result->fetch_assoc()) {
    echo $row['Field'] . " - " . $row['Type'] . "<br>";
}

// Check Assignments Table
$result = $conn->query("DESCRIBE assignments");
echo "<h3>Assignments Table</h3>";
if($result) {
    while($row = $result->fetch_assoc()) {
        echo $row['Field'] . " - " . $row['Type'] . "<br>";
    }
} else {
    echo " Assignments table not found!";
}
?>
