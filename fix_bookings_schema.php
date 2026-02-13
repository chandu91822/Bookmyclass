<?php
include 'config.php';

// Allow NULLs for year and section in bookings table
$queries = [
    "ALTER TABLE bookings MODIFY year ENUM('E1', 'E2', 'E3', 'E4') NULL",
    "ALTER TABLE bookings MODIFY section ENUM('A', 'B', 'C', 'D', 'E') NULL"
];

foreach ($queries as $sql) {
    if ($conn->query($sql) === TRUE) {
        echo "Success: $sql\n";
    } else {
        echo "Error: " . $conn->error . "\n";
    }
}
?>
