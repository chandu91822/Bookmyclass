<?php
include 'config.php';

$name = $_POST['name'];
$email = $_POST['email'];
$password = password_hash($_POST['password'], PASSWORD_BCRYPT);
$role = $_POST['role']; // Get role from form

$query = "INSERT INTO users (name, email, password, role) VALUES ('$name', '$email', '$password', '$role')";
if (mysqli_query($conn, $query)) {
    echo "Registration Successful. <a href='login.php'>Login Here</a>";
} else {
    echo "Error: " . mysqli_error($conn);
}
?>
