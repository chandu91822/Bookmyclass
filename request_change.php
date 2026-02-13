<?php
session_start();
include '../config.php';

if ($_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

$id = $_GET['id'];
$query = "DELETE FROM bookings WHERE id='$id'";
mysqli_query($conn, $query);
header("Location: dashboard.php");
?>
