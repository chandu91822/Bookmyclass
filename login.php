<?php
// login.php - Auto-Role Detection & Canva Purple Theme
include 'config.php';
session_start();

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $identifier = mysqli_real_escape_string($conn, $_POST['identifier'] ?? ''); // Username or Email
    $password = $_POST['password'] ?? '';

    if ($identifier === '' || $password === '') {
        $error = "Missing login details.";
    } else {

        // Query to find user by name or email
        $query = "SELECT * FROM users WHERE name = '$identifier' OR email = '$identifier'";
        $result = mysqli_query($conn, $query);

        if (mysqli_num_rows($result) == 1) {
            $user = mysqli_fetch_assoc($result);
            $stored_password = $user['password'];
            $is_valid_password = password_verify($password, $stored_password) || hash_equals($stored_password, $password);
            if ($is_valid_password) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['name'] = $user['name'];

                // Redirect based on detected role
                if ($user['role'] == 'admin') {
                    header("Location: admin_dashboard.php");
                } elseif ($user['role'] == 'faculty') {
                    header("Location: faculty_dashboard.php");
                } else {
                    header("Location: student_dashboard.php");
                }
                exit();
            } else {
                $error = "Incorrect password.";
            }
        } else {
            $error = "User not found.";
        }
    
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | BookMyClass</title>
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/landing.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="auth-body">
    
    <!-- Navbar -->
    <nav class="navbar">
        <div class="container nav-container">
            <a href="index.php" class="logo">
                <i class="fa-solid fa-shapes"></i>
                BookMyClass
            </a>
            <div class="nav-actions">
                <a href="register.php" class="btn btn-outline" style="border-width: 1px; padding: 0.5rem 1.2rem;">Sign Up</a>
            </div>
        </div>
    </nav>

    <div class="auth-container">
        <div class="form-container">
            <h2 class="form-title">Welcome Back</h2>
            <p class="form-subtitle">Login to manage your classroom bookings</p>
            
            <?php if($error): ?>
                <div style="color: #ef4444; background: rgba(239, 68, 68, 0.1); padding: 12px; border-radius: 8px; margin-bottom: 20px; font-size: 0.95rem; border: 1px solid rgba(239, 68, 68, 0.2); display: flex; align-items: center; gap: 10px;">
                    <i class="fa-solid fa-circle-exclamation"></i> <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" name="identifier" class="form-input" placeholder="e.g. Chandu" required>
                </div>
                
                <div class="form-group">
                    <div style="display:flex; justify-content:space-between;">
                        <label>Password</label>
                        <a href="#" style="font-size:0.85rem; color:var(--primary-color);">Forgot?</a>
                    </div>
                    <input type="password" name="password" class="form-input" placeholder="" required>
                </div>

                <button type="submit" class="btn btn-primary form-btn">Log In</button>
            </form>

            <div class="form-footer">
                Don't have an account? <a href="register.php">Create Free Account</a>
            </div>
        </div>
    </div>

</body>
</html>
