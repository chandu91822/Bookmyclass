<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'config.php';

// Assuming you're using session to store user id
$user_id = $_SESSION['user_id'] ?? 1; // Default to 1 for testing

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);

    // Update the user's information in the database
    $stmt = $conn->prepare("UPDATE users SET name = ?, email = ? WHERE id = ?");
    $stmt->bind_param("ssi", $name, $email, $user_id);
    $stmt->execute();
    $stmt->close();

    // Set a session variable for success message
    $_SESSION['success_message'] = "Details updated successfully!";
    header("Location: settings.php"); // Redirect to the same page
    exit();
}

// Load current settings
$stmt = $conn->prepare("SELECT name, email FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($name, $email);
$stmt->fetch();
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>⚙️ Settings</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            background: linear-gradient(135deg, #1c1f2b, #2a2d3f);
            color: white;
            font-family: 'Poppins', sans-serif;
            padding: 40px 20px;
            animation: fadeIn 1s ease-in-out;
        }

        h1 {
            text-align: center;
            font-size: 36px;
            margin-bottom: 30px;
            color: #00ffcc;
            text-shadow: 0 0 10px rgba(0,255,200,0.6);
            animation: slideInDown 0.8s ease forwards;
        }

        .container {
            max-width: 600px;
            margin: auto;
            background: rgba(255, 255, 255, 0.05);
            padding: 30px;
            border-radius: 15px;
            backdrop-filter: blur(10px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.4);
            animation: slideInUp 1s ease forwards;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        label {
            font-size: 18px;
            text-align: left;
            color: #ccc;
        }

        input[type="text"], input[type="email"] {
            padding: 12px;
            font-size: 16px;
            border-radius: 8px;
            border: none;
            width: 100%;
            background: rgba(255,255,255,0.1);
            color: #fff;
            outline: none;
            transition: 0.3s;
        }

        input[type="text"]:hover, input[type="email"]:hover {
            background: rgba(255,255,255,0.2);
        }

        button {
            padding: 12px;
            border: none;
            border-radius: 8px;
            background-color: #00ffd5;
            color: #000;
            font-size: 18px;
            font-weight: bold;
            cursor: pointer;
            box-shadow: 0 4px 15px rgba(0,255,213,0.3);
            transition: 0.3s ease;
        }

        button:hover {
            background-color: #00bfa6;
            box-shadow: 0 6px 25px rgba(0,255,213,0.4);
        }

        @keyframes slideInUp {
            from {opacity: 0; transform: translateY(50px);}
            to {opacity: 1; transform: translateY(0);}
        }

        @keyframes slideInDown {
            from {opacity: 0; transform: translateY(-30px);}
            to {opacity: 1; transform: translateY(0);}
        }

        @keyframes fadeIn {
            from {opacity: 0;}
            to {opacity: 1;}
        }

        .success-message {
            background-color: #00bfa6;
            color: #fff;
            padding: 15px;
            margin-bottom: 20px;
            text-align: center;
            border-radius: 8px;
            font-size: 18px;
        }
    </style>
</head>
<body>
    <h1>⚙️ User Settings</h1>
    <div class="container">
        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="success-message" id="success-message">
                <?php
                    echo $_SESSION['success_message'];
                    unset($_SESSION['success_message']); // Clear the message after displaying
                ?>
            </div>
            <script>
                // Hide the success message after 5 seconds
                setTimeout(function() {
                    document.getElementById('success-message').style.display = 'none';
                }, 5000);
            </script>
        <?php endif; ?>
        
        <form method="POST">
            <label>👤 Update Name</label>
            <input type="text" name="name" value="<?php echo htmlspecialchars($name); ?>" required>

            <label>📧 Update Email</label>
            <input type="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>

            <button type="submit">💾 Save Settings</button>
        </form>
    </div>
</body>
</html>
