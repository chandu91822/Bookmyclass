<?php
// register.php - Canva Purple Theme
include 'config.php';
session_start();

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $role = $_POST['role'];

    // Additional fields
    $year = isset($_POST['year']) ? $_POST['year'] : null;
    $section = isset($_POST['section']) ? $_POST['section'] : null;
    $subject = isset($_POST['subject']) ? $_POST['subject'] : null;

    if ($password !== $confirm_password) {
        $error = "Passwords do not match!";
    } else {
        // Enforce logical NULLs based on role
        if ($role !== 'student') { $year = null; $section = null; }
        if ($role !== 'faculty') { $subject = null; }

        // Prepare SQL (Use prepared statements in prod)
        $year_val = $year ? "'$year'" : "NULL";
        $section_val = $section ? "'$section'" : "NULL";
        $subject_val = $subject ? "'$subject'" : "NULL";

        $sql = "INSERT INTO users (name, email, password, role, year, section, subject) 
                VALUES ('$name', '$email', '$password', '$role', $year_val, $section_val, $subject_val)";
        
        if (mysqli_query($conn, $sql)) {
            $success = "Registration successful! Redirecting to login...";
            echo "<script>setTimeout(function(){ window.location.href = 'login.php'; }, 2000);</script>";
        } else {
            $error = "Error: " . mysqli_error($conn);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | BookMyClass</title>
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/landing.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        function toggleFields() {
            var role = document.getElementById("role").value;
            var studentFields = document.getElementById("student_fields");
            var facultyFields = document.getElementById("faculty_fields");
            
            if (role === "student") {
                studentFields.style.display = "block";
                studentFields.classList.add('fade-in');
            } else {
                studentFields.style.display = "none";
            }
            
            if (role === "faculty") {
                facultyFields.style.display = "block";
                facultyFields.classList.add('fade-in');
            } else {
                facultyFields.style.display = "none";
            }
        }
    </script>
    <style>
        .fade-in { animation: fadeIn 0.3s ease-in; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(5px); } to { opacity: 1; transform: translateY(0); } }
    </style>
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
                <span style="color:var(--text-muted); font-size:0.9rem; margin-right:10px;">Already have an account?</span>
                <a href="login.php" class="btn btn-primary" style="padding: 0.5rem 1.2rem;">Log In</a>
            </div>
        </div>
    </nav>

    <div class="auth-container">
        <div class="form-container" style="max-width: 550px;">
            <h2 class="form-title">Get Started</h2>
            <p class="form-subtitle">Create your account to start booking</p>

            <?php if($error): ?>
                <div style="color: #ef4444; background: rgba(239, 68, 68, 0.1); padding: 12px; border-radius: 8px; margin-bottom: 20px; font-size: 0.95rem; border: 1px solid rgba(239, 68, 68, 0.2); display: flex; align-items: center; gap: 10px;">
                    <i class="fa-solid fa-circle-exclamation"></i> <?php echo $error; ?>
                </div>
            <?php endif; ?>
            <?php if($success): ?>
                <div style="color: #10b981; background: rgba(16, 185, 129, 0.1); padding: 12px; border-radius: 8px; margin-bottom: 20px; font-size: 0.95rem; border: 1px solid rgba(16, 185, 129, 0.2); display: flex; align-items: center; gap: 10px;">
                    <i class="fa-solid fa-check-circle"></i> <?php echo $success; ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <div style="display: flex; flex-direction: column; gap: 15px;">
                    <div class="form-group">
                        <label>Full Name</label>
                        <input type="text" name="name" class="form-input" placeholder="John Doe" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Email Address</label>
                        <input type="email" name="email" class="form-input" placeholder="john@university.edu" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>I am a...</label>
                    <select name="role" id="role" class="form-input" onchange="toggleFields()" required style="background-image: url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%237b61ff%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E'); background-repeat: no-repeat; background-position: right 1rem center; background-size: 0.65em auto; appearance: none; padding-right: 2.5rem;">
                        <option value="" disabled selected>Select Role</option>
                        <option value="student">Student</option>
                        <option value="faculty">Faculty</option>
                    </select>
                </div>

                <!-- Dynamic Fields -->
                <div id="student_fields" style="display: none;">
                    <div style="display: flex; flex-direction: column; gap: 15px;">
                        <div class="form-group">
                             <label>Year</label>
                             <select name="year" class="form-input">
                                 <option value="E1">E1 (Freshman)</option>
                                 <option value="E2">E2 (Sophomore)</option>
                                 <option value="E3">E3 (Junior)</option>
                                 <option value="E4">E4 (Senior)</option>
                             </select>
                        </div>
                        <div class="form-group">
                             <label>Section</label>
                             <select name="section" class="form-input">
                                 <option value="A">Section A</option>
                                 <option value="B">Section B</option>
                                 <option value="C">Section C</option>
                                 <option value="D">Section D</option>
                                 <option value="E">Section E</option>
                             </select>
                        </div>
                    </div>
                </div>

                <div id="faculty_fields" style="display: none;">
                    <div class="form-group">
                        <label>Department / Subject</label>
                        <input type="text" name="subject" class="form-input" placeholder="e.g. Computer Science">
                    </div>
                </div>

                <div style="display: flex; flex-direction: column; gap: 15px;">
                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" name="password" class="form-input" required>
                    </div>
    
                    <div class="form-group">
                        <label>Confirm Password</label>
                        <input type="password" name="confirm_password" class="form-input" required>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary form-btn">Create Account</button>
            </form>

            <div class="form-footer">
                By signing up, you agree to our <a href="#">Terms of Service</a> & <a href="#">Privacy Policy</a>.
            </div>
        </div>
    </div>

</body>
</html>
