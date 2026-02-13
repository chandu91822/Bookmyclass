<?php
include 'config.php';

$rooms = [
    ['GF-1', 60, 'Lecture Hall'], ['GF-2', 60, 'Lecture Hall'], ['GF-3', 60, 'Lecture Hall'],
    ['GF-4', 60, 'Lecture Hall'], ['GF-5', 60, 'Lecture Hall'], ['GF-6', 60, 'Lecture Hall'],
    ['GF-7', 60, 'Lecture Hall'], ['GF-8', 60, 'Lecture Hall'], ['GF-9', 60, 'Lecture Hall'],
    ['GF-10', 60, 'Lecture Hall'], ['Big Seminar Hall', 200, 'Seminar Hall']
];

foreach($rooms as $r) {
    $num = $r[0];
    $cap = $r[1];
    $type = $r[2];
    
    // Check first
    $check = mysqli_query($conn, "SELECT * FROM classrooms WHERE room_number='$num'");
    if(mysqli_num_rows($check) == 0) {
        $sql = "INSERT INTO classrooms (room_number, capacity, type, status) VALUES ('$num', '$cap', '$type', 'active')";
        if(mysqli_query($conn, $sql)) {
            echo "Inserted $num.<br>";
        } else {
            echo "Failed to insert $num: " . mysqli_error($conn) . "<br>";
        }
    } else {
        echo "$num already exists.<br>";
    }
}
?>
