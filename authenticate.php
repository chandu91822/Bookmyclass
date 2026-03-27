<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $role = $_POST['role'];

    $query = "SELECT id, name, password, role FROM users WHERE email = ? AND role = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "ss", $email, $role);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);

    if ($user) {
        // Checking password directly (plain text)
        if ($password === $user['password']) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['user_role'] = $user['role'];

            // Redirect based on role
            if ($role === 'faculty') {
                header("Location: faculty_dashboard.php");
                exit();
            } elseif ($role === 'student') {
                header("Location: student_dashboard.php");
                exit();
            } elseif ($role === 'admin') {
                header("Location: admin_dashboard.php");
                exit();
            } else {
                echo "<script>alert('Invalid role specified!'); window.location.href='login.php';</script>";
            }
        } else {
            echo "<script>alert('Incorrect password!'); window.location.href='login.php';</script>";
        }
    } else {
        echo "<script>alert('No such user found!'); window.location.href='login.php';</script>";
    }
}
?>
