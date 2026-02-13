<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include 'config.php';

// if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
//     header("Location: login.php");
//     exit();
// }

if (isset($_GET['name'])) {
    $name= $_GET['name'];
    $query = "DELETE FROM users WHERE name='$name'";
    mysqli_query($conn, $query);
}

header("Location: manage_users.php");
exit();
?>
