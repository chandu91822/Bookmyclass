<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Classroom Booking</title>
    <style>
        /* Global Reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background: linear-gradient(135deg, #2c3e50, #4ca1af);
            color: white;
        }

        /* Navigation Bar */
        header {
            background: rgba(0, 0, 0, 0.8);
            padding: 15px 0;
            position: fixed;
            width: 100%;
            top: 0;
            left: 0;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.5);
            z-index: 1000;
        }

        nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 85%;
            margin: auto;
        }

        nav a {
            color: white;
            text-decoration: none;
            font-size: 1.1rem;
            padding: 10px 15px;
            transition: 0.3s;
            position: relative;
        }

        nav a:hover {
            color: #ff4757;
        }

        /* Active Page Indicator */
        nav a.active::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 0;
            width: 100%;
            height: 3px;
            background: #ff4757;
            transition: 0.3s;
        }

        /* Dropdown Menu */
        .dropdown {
            position: relative;
            display: inline-block;
        }

        .dropdown-content {
            display: none;
            position: absolute;
            background: rgba(255, 255, 255, 0.9);
            min-width: 160px;
            border-radius: 5px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
            overflow: hidden;
        }

        .dropdown-content a {
            color: black;
            padding: 10px;
            display: block;
            text-align: left;
        }

        .dropdown-content a:hover {
            background: #ff4757;
            color: white;
        }

        .dropdown:hover .dropdown-content {
            display: block;
        }

        /* Profile Section */
        .profile {
            display: flex;
            align-items: center;
        }

        .profile img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin-right: 10px;
            border: 2px solid white;
        }

        .profile span {
            font-size: 1rem;
            font-weight: bold;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            nav {
                flex-direction: column;
                align-items: center;
            }

            .dropdown-content {
                position: relative;
                width: 100%;
            }
        }
    </style>
</head>
<body>

<header>
    <nav>
        <div class="logo">
            <a href="index.php" class="active">🏫 Classroom Booking</a>
        </div>

        <div class="links">
            <a href="index.php">Home</a>
            <a href="view_bookings.php">My Bookings</a>
            
            <div class="dropdown">
                <a href="#">More ⬇️</a>
                <div class="dropdown-content">
                    <a href="schedule.php">Classroom Schedule</a>
                    <a href="announcements.php">Announcements</a>
                    <a href="help.php">Help & Support</a>
                </div>
            </div>

            <a href="logout.php" style="color: #ff4757; font-weight: bold;">Logout</a>
        </div>

        <!-- User Profile (If Logged In) -->
        <div class="profile">
            <img src="assets/profile.jpg" alt="User">
            <span>John Doe</span> <!-- Replace with PHP dynamic username -->
        </div>
    </nav>
</header>

</body>
</html>
