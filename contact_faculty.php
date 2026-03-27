<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'student') {
    header("Location: login.php");
    exit();
}

// Email Sending Logic
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $faculty_email = $_POST["faculty_email"];
    $message = $_POST["message"];
    $subject = "Message from Student";
    $headers = "From: chandukadiri96420@gmail.com\r\n"; // Replace with your email

    if (mail($faculty_email, $subject, $message, $headers)) {
        echo "<script>alert('Email sent successfully to $faculty_email!');</script>";
    } else {
        echo "<script>alert('Failed to send email. Please try again.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Faculty</title>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: #ecf0f3;
            text-align: center;
        }

        header {
            background: #2c3e50;
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
            background: #27ae60;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 18px;
        }

        button:hover {
            background: #219150;
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
    📞 Contact Faculty
</header>

<div class="container">
    <h2>Send a Message to Faculty</h2>
    <form method="POST">
        <input type="email" name="faculty_email" placeholder="Faculty Email" required>
        <textarea name="message" rows="5" placeholder="Your Message" required></textarea>
        <button type="submit">Send Message</button>
    </form>
    <a href="student_dashboard.php" class="btn-back">Back to Dashboard</a>
</div>

</body>
</html>
