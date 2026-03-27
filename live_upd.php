<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'config.php';

// Fetch the latest 10 bookings with aliases expected by the UI.
$sql = "SELECT id, name, year, section, room_number AS classroom, date,
               start_time AS time_from, end_time AS time_to, created_at
        FROM bookings
        ORDER BY date DESC, start_time DESC
        LIMIT 10";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>📅 Recent Bookings</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            background: linear-gradient(135deg, #1c1f2b, #2a2d3f);
            color: white;
            font-family: Arial, sans-serif;
            padding: 40px 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
            animation: fadeIn 1.2s ease-in-out;
        }

        h1 {
            font-size: 36px;
            margin-bottom: 30px;
            color: #00ffcc;
            text-shadow: 0 0 10px rgba(0,255,200,0.6);
            animation: slideInDown 1s ease forwards;
        }

        .booking-container {
            width: 90%;
            max-width: 800px;
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .booking-card {
            background: rgba(255, 255, 255, 0.05);
            padding: 20px;
            border-radius: 15px;
            backdrop-filter: blur(6px);
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.3);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            animation: slideInUp 0.8s ease forwards;
        }

        .booking-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 25px rgba(0, 255, 200, 0.2);
        }

        .booking-card h2 {
            color: #00ffd5;
            margin-bottom: 10px;
        }

        .booking-details {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            font-size: 15px;
        }

        .label {
            font-weight: bold;
            color: #bbbbbb;
        }

        @keyframes slideInUp {
            from {opacity: 0; transform: translateY(30px);}
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

        @media (max-width: 600px) {
            .booking-details {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <h1>📅 Recently Booked Classes</h1>
    <div class="booking-container">
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<div class='booking-card'>
                        <h2>{$row['classroom']}</h2>
                        <div class='booking-details'>
                            <div><span class='label'>Booked By:</span> {$row['name']}</div>
                            <div><span class='label'>Year:</span> {$row['year']}</div>
                            <div><span class='label'>Section:</span> {$row['section']}</div>
                            <div><span class='label'>Date:</span> {$row['date']}</div>
                            <div><span class='label'>Time:</span> {$row['time_from']} - {$row['time_to']}</div>
                            <div><span class='label'>Booking Time:</span> {$row['created_at']}</div>
                        </div>
                    </div>";
            }
        } else {
            echo "<p>No recent bookings found.</p>";
        }
        $conn->close();
        ?>
    </div>
</body>
</html>
