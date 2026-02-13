<?php
session_start();
include 'config.php'; // Database connection

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'faculty') {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $due_date = $_POST['due_date'];
    $faculty_id = $_SESSION['user_id'];

    $query = "INSERT INTO assignments (faculty_id, title, description, due_date) VALUES (?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "isss", $faculty_id, $title, $description, $due_date);

    if (mysqli_stmt_execute($stmt)) {
        echo "<script>alert('Assignment uploaded successfully!');</script>";
    } else {
        echo "<script>alert('Failed to upload assignment.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Assignment</title>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: #ecf0f3;
            text-align: center;
        }
        header {
            background: #34495e;
            color: white;
            padding: 20px;
            font-size: 24px;
        }
        .container {
            margin: 50px auto;
            width: 90%;
            max-width: 500px;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
        }
        form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        input, textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }
        button {
            background: #2980b9;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 18px;
        }
        button:hover {
            background: #1c6691;
        }
        .btn-back {
            background: #e74c3c;
            padding: 10px;
            text-decoration: none;
            color: white;
            border-radius: 5px;
            display: inline-block;
            margin-top: 15px;
        }
        .btn-back:hover {
            background: #c0392b;
        }
    </style>
</head>
<body>

<header>
    📂 Upload Assignment
</header>

<div class="container">
    <h2>Create a New Assignment</h2>
    <form method="POST">
        <input type="text" name="title" placeholder="Assignment Title" required>
        <textarea name="description" rows="5" placeholder="Assignment Description" required></textarea>
        <input type="date" name="due_date" required>
        <button type="submit">Upload Assignment</button>
    </form>
    <a href="faculty_dashboard.php" class="btn-back">Back to Dashboard</a>
</div>

</body>
</html>
