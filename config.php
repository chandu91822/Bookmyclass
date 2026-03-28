<?php
$host = "db";   // 🔥 IMPORTANT (not localhost)
$user = "user";
$pass = "user123";
$dbname = "mydb";

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
