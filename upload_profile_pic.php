<?php
// upload_profile_pic.php
include 'config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["profile_pic"])) {
    $target_dir = "uploads/profiles/";
    $user_id = $_SESSION['user_id'];
    $target_file = $target_dir . $user_id . ".jpg";
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($_FILES["profile_pic"]["name"],PATHINFO_EXTENSION));

    // Check if image file is a actual image or fake image
    $check = getimagesize($_FILES["profile_pic"]["tmp_name"]);
    if($check !== false) {
        $uploadOk = 1;
    } else {
        echo "File is not an image.";
        $uploadOk = 0;
    }

    // Check file size (5MB limit)
    if ($_FILES["profile_pic"]["size"] > 5000000) {
        echo "Sorry, your file is too large.";
        $uploadOk = 0;
    }

    // Allow certain file formats
    if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg") {
        echo "Sorry, only JPG, JPEG, & PNG files are allowed.";
        $uploadOk = 0;
    }

    if ($uploadOk == 0) {
        echo "Sorry, your file was not uploaded.";
    } else {
        // Convert to JPG if needed or just save as ID.jpg
        // For simplicity, we just move and rename to ID.jpg. 
        // Note: Real world would handle extension conversion. 
        // Here we rely on browser ignoring extension mismatch or just forcing jpg name.
        if (move_uploaded_file($_FILES["profile_pic"]["tmp_name"], $target_file)) {
            // Redirect based on role
            if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin') {
                header("Location: admin_profile.php");
            } elseif (isset($_SESSION['role']) && $_SESSION['role'] == 'faculty') {
                header("Location: faculty_profile.php");
            } else {
                header("Location: student_profile.php");
            }
        } else {
            echo "Sorry, there was an error uploading your file.";
        }
    }
}
?>
