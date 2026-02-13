<?php
include 'config.php';
$result = mysqli_query($conn, "SELECT * FROM classrooms");
echo "<h2>Classrooms in DB:</h2>";
echo "<table border='1'><tr><th>ID</th><th>Room Number</th><th>Type</th><th>Status</th></tr>";
while($row = mysqli_fetch_assoc($result)) {
    echo "<tr>";
    echo "<td>" . $row['id'] . "</td>";
    echo "<td>'" . $row['room_number'] . "'</td>"; // Single quotes to see whitespace
    echo "<td>" . $row['type'] . "</td>";
    echo "<td>" . $row['status'] . "</td>";
    echo "</tr>";
}
echo "</table>";

echo "<h3>Target List Check:</h3>";
$targets = ['GF-1','GF-2','GF-3','GF-4','GF-5','GF-6','GF-7','GF-8','GF-9','GF-10','Small Seminar Hall','Big Seminar Hall'];
foreach($targets as $t) {
    $c = mysqli_query($conn, "SELECT * FROM classrooms WHERE room_number = '$t'");
    echo "Room '$t': " . (mysqli_num_rows($c) > 0 ? "Found" : "NOT FOUND") . "<br>";
}
?>
