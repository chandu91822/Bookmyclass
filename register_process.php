<?php
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    echo "Method not allowed.";
    exit();
}

$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$password_raw = $_POST['password'] ?? '';
$role = $_POST['role'] ?? '';

if ($name === '' || $email === '' || $password_raw === '' || $role === '') {
    http_response_code(400);
    echo "Missing required fields.";
    exit();
}

$password = password_hash($password_raw, PASSWORD_BCRYPT);

$query = "INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "ssss", $name, $email, $password, $role);

if (mysqli_stmt_execute($stmt)) {
    echo "Registration Successful. <a href='login.php'>Login Here</a>";
} else {
    echo "Error: " . mysqli_error($conn);
}

mysqli_stmt_close($stmt);
mysqli_close($conn);
?>
