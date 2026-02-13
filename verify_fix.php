<?php
// Simulate a POST request for Faculty registration without year/section
// This mimics the condition that caused the error.

$_SERVER['REQUEST_METHOD'] = 'POST';
$_POST['name'] = 'Test Faculty';
$_POST['email'] = 'faculty_test@example.com';
$_POST['password'] = 'password123';
$_POST['role'] = 'faculty';
$_POST['subject'] = 'Mathematics';
$_POST['year'] = ''; // Empty string, was causing data truncated
$_POST['section'] = ''; // Empty string

// Include the register script
// Note: We need to capture output to prevent it from displaying HTML
ob_start();
include 'register.php';
$output = ob_get_clean();

// Check if registration was successful by looking for success message in output
if (strpos($output, 'Registration successful!') !== false) {
    echo "SUCCESS: Faculty registration worked.\n";
} else {
    echo "FAILURE: Faculty registration failed.\n";
    // echo output for debugging if needed, but it's HTML
    // Strip tags to see error message
    echo strip_tags($output);
}

// Clean up: delete the test user
include 'config.php';
$conn->query("DELETE FROM users WHERE email = 'faculty_test@example.com'");
$conn->close();
?>
