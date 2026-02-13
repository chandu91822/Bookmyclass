<?php
include 'config.php';

// Add columns to assignments table
$sql1 = "ALTER TABLE assignments ADD COLUMN year VARCHAR(10) DEFAULT 'All', ADD COLUMN section VARCHAR(10) DEFAULT 'All'";
if(mysqli_query($conn, $sql1)) {
    echo "Added year/section to assignments.<br>";
} else {
    echo "Error updating assignments: " . mysqli_error($conn) . "<br>";
}

// Add columns to announcements table
$sql2 = "ALTER TABLE announcements ADD COLUMN year VARCHAR(10) DEFAULT 'All', ADD COLUMN section VARCHAR(10) DEFAULT 'All'";
if(mysqli_query($conn, $sql2)) {
    echo "Added year/section to announcements.<br>";
} else {
    echo "Error updating announcements: " . mysqli_error($conn) . "<br>";
}

// Ensure classrooms exist (GF-1 to GF-10, Seminar Halls) for testing
$rooms = [
    ['GF-1', 60, 'Lecture Hall'], ['GF-2', 60, 'Lecture Hall'], ['GF-3', 60, 'Lecture Hall'],
    ['GF-4', 60, 'Lecture Hall'], ['GF-5', 60, 'Lecture Hall'], ['GF-6', 60, 'Lecture Hall'],
    ['GF-7', 60, 'Lecture Hall'], ['GF-8', 60, 'Lecture Hall'], ['GF-9', 60, 'Lecture Hall'],
    ['GF-10', 60, 'Lecture Hall'], ['Small Seminar Hall', 100, 'Seminar Hall'], ['Big Seminar Hall', 200, 'Seminar Hall']
];

foreach($rooms as $r) {
    $num = $r[0];
    $cap = $r[1];
    $type = $r[2];
    $check = mysqli_query($conn, "SELECT * FROM classrooms WHERE room_number='$num'");
    if(mysqli_num_rows($check) == 0) {
        mysqli_query($conn, "INSERT INTO classrooms (room_number, capacity, type) VALUES ('$num', '$cap', '$type')");
        echo "Added room $num.<br>";
    }
}

echo "Database update complete.";
?>
