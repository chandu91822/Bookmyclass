<?php
// upload_assignment.php
include 'config.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'faculty') {
        die("Unauthorized");
    }

    $faculty_id = $_SESSION['user_id'];
    $title = $_POST['title'];
    $year = $_POST['year'];
    $section = $_POST['section'];
    $deadline = $_POST['deadline'];
    
    // File Upload
    $target_dir = "uploads/assignments/";
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    
    $file_name = basename($_FILES["assignment_file"]["name"]);
    $target_file = $target_dir . time() . "_" . $file_name;
    $uploadOk = 1;

    // Check file size (limit 5MB)
    if ($_FILES["assignment_file"]["size"] > 5000000) {
        $uploadOk = 0;
        $error = "File too large.";
    }

    if ($uploadOk && move_uploaded_file($_FILES["assignment_file"]["tmp_name"], $target_file)) {
        $sql = "INSERT INTO assignments (faculty_id, title, year, section, file_path, deadline) 
                VALUES ('$faculty_id', '$title', '$year', '$section', '$target_file', '$deadline')";
        
        if (mysqli_query($conn, $sql)) {
            // Log action
            mysqli_query($conn, "INSERT INTO activity_logs (user_id, action, details) VALUES ('$faculty_id', 'ASSIGNMENT_UPLOAD', 'Uploaded assignment: $title')");
            header("Location: faculty_dashboard.php?success=Assignment uploaded");
        } else {
            echo "Error: " . mysqli_error($conn);
        }
    } else {
        echo "Error uploading file.";
    }
}
?>
