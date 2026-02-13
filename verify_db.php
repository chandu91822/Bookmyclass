<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "book_class";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "DESCRIBE users";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo "Table 'users' structure:\n";
    while($row = $result->fetch_assoc()) {
        echo $row["Field"] . " - " . $row["Type"] . "\n";
    }
} else {
    echo "0 results";
}

$sql_bookings = "DESCRIBE bookings";
$result_bookings = $conn->query($sql_bookings);

if ($result_bookings->num_rows > 0) {
    echo "\nTable 'bookings' structure:\n";
    while($row = $result_bookings->fetch_assoc()) {
        echo $row["Field"] . " - " . $row["Type"] . "\n";
    }
} else {
    echo "0 results for bookings";
}

$conn->close();
?>
