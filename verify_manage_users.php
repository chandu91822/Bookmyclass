<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
include 'config.php';

echo "Testing SELECT query from manage_users.php...\n";

// The corrected query uses 'id' instead of 'user_id'
$query = "SELECT id, name, email, password, role FROM users";

if ($result = mysqli_query($conn, $query)) {
    echo "Query SUCCESS!\n";
    echo "Found " . mysqli_num_rows($result) . " users.\n";
    while ($row = mysqli_fetch_assoc($result)) {
        echo "User: " . $row['name'] . " (ID: " . $row['id'] . ")\n";
    }
} else {
    echo "Query FAILED: " . mysqli_error($conn) . "\n";
}

$conn->close();
?>
