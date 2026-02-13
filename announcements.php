<?php
session_start();
if (!isset($_SESSION['name']) || !isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

$name = $_SESSION['name'];
$email = $_SESSION['email'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Faculty Dashboard</title>
    <style>
        /* 🌟 Global Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background: linear-gradient(135deg, #1e1e2e, #25253a);
            color: white;
            display: flex;
            height: 100vh;
            overflow: hidden;
        }

        /* 🔥 Sidebar - Ultra Pro Max */
        .sidebar {
            width: 80px;
            height: 100%;
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(15px);
            transition: width 0.4s ease-in-out, box-shadow 0.3s ease-in-out;
            position: fixed;
            left: 0;
            top: 0;
            padding-top: 20px;
            z-index: 1000;
            box-shadow: 5px 0 20px rgba(0, 0, 0, 0.5);
            border-right: 2px solid rgba(255, 255, 255, 0.2);
        }

        .sidebar:hover {
            width: 260px;
            box-shadow: 10px 0 25px rgba(255, 255, 255, 0.2);
        }

        .sidebar ul {
            list-style: none;
            padding: 10px;
        }

        .sidebar ul li {
            padding: 18px;
            cursor: pointer;
            display: flex;
            align-items: center;
            transition: background 0.3s ease-in-out, transform 0.3s ease-in-out;
            font-size: 18px;
            border-radius: 10px;
        }

        .sidebar ul li:hover {
            background: rgba(255, 255, 255, 0.15);
            transform: translateX(7px) scale(1.05);
        }

        .sidebar-link {
            text-decoration: none;
            color: white;
            display: inline-block;
            opacity: 0;
            transition: opacity 0.4s ease-in-out, transform 0.3s ease-in-out;
        }

        .sidebar:hover .sidebar-link {
            opacity: 1;
            transform: translateX(5px);
        }

        /* 🚀 Main Content */
        .main-content {
            margin-left: 80px;
            padding: 30px;
            width: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            transition: margin-left 0.3s ease-in-out;
        }

        .sidebar:hover ~ .main-content {
            margin-left: 260px;
        }

        /* 👤 Welcome Message */
        .top-right {
            position: absolute;
            top: 20px;
            right: 30px;
            font-size: 18px;
            font-weight: bold;
        }

        /* 📅 Real-Time Bookings */
        .bookings {
            background: rgba(255, 255, 255, 0.1);
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            width: 80%;
            max-width: 700px;
            backdrop-filter: blur(10px);
        }

        /* 📜 Announcements Modal */
        .modal {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 400px;
            background: #1e1e2e;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(255, 255, 255, 0.2);
            z-index: 1000;
        }

        .modal h2 {
            margin-bottom: 15px;
        }

        .modal select, .modal input, .modal textarea {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
            border-radius: 5px;
            border: none;
            outline: none;
        }

        .modal button {
            margin-top: 10px;
            padding: 10px;
            width: 100%;
            background: cyan;
            border: none;
            color: black;
            font-weight: bold;
            cursor: pointer;
            border-radius: 5px;
        }

        .close-btn {
            background: red;
            color: white;
        }

    </style>
</head>
<body>

<!-- 🚀 Sidebar -->
<div class="sidebar">
    <ul>
        <li class="sidebar-item">
            <span class="icon">🏠</span>
            <a href="faculty_dashboard.php" class="sidebar-link"><b>Dashboard</b></a>
        </li>
        <li class="sidebar-item" onclick="openAnnouncementsModal()">
            <span class="icon">📢</span>
            <span class="sidebar-link"><b>Announcements</b></span>
        </li>
    </ul>
</div>

<!-- 👤 Welcome Message -->
<div class="top-right">
    👤 Welcome, <?php echo $name; ?> | 📧 <?php echo $email; ?>
</div>

<!-- 🚀 Main Content -->
<div class="main-content">
    <div class="bookings" id="bookings">
        <h3>📅 Real-Time Bookings</h3>
        <p>Loading bookings...</p>
    </div>
</div>

<!-- 📜 Announcements Modal -->
<div class="modal" id="announcementModal">
    <h2>📢 Post Announcement</h2>
    <form action="post_announcement.php" method="POST">
        <select name="year" required>
            <option value="">Select Year</option>
            <option value="1">1st Year</option>
            <option value="2">2nd Year</option>
            <option value="3">3rd Year</option>
            <option value="4">4th Year</option>
        </select>

        <select name="section" required>
            <option value="">Select Section</option>
            <option value="A">A</option>
            <option value="B">B</option>
            <option value="C">C</option>
            <option value="D">D</option>
            <option value="E">E</option>
        </select>

        <input type="text" name="title" placeholder="Title" required>
        <textarea name="description" placeholder="Description" required></textarea>

        <button type="submit">Submit</button>
        <button type="button" class="close-btn" onclick="closeAnnouncementsModal()">Cancel</button>
    </form>
</div>

<!-- 🎯 JavaScript -->
<script>
    function openAnnouncementsModal() {
        document.getElementById("announcementModal").style.display = "block";
    }

    function closeAnnouncementsModal() {
        document.getElementById("announcementModal").style.display = "none";
    }
</script>

</body>
</html>
