<?php
// delete_announcement.php
include 'config.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'faculty') {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'])) {
    $id = $_POST['id'];
    // In real app, check if this faculty owns the announcement
    $query = "DELETE FROM announcements WHERE id='$id'";
    mysqli_query($conn, $query);
    header("Location: post_announcement.php");
} else {
    header("Location: index.php");
}
?>
