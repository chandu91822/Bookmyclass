<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include 'config.php';

// Fetch User Bookings
$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM bookings WHERE user_id = '$user_id' ORDER BY date DESC, start_time DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Bookings</title>
    <style>
        body {
            background: #121212;
            color: white;
            font-family: 'Poppins', sans-serif;
            text-align: center;
        }

        .container {
            margin-top: 80px;
            width: 80%;
            margin: auto;
            padding: 20px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            backdrop-filter: blur(10px);
            box-shadow: 0 10px 30px rgba(0, 255, 255, 0.3);
        }

        h1 {
            text-shadow: 0 0 10px cyan;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            overflow: hidden;
        }

        th, td {
            padding: 12px;
            text-align: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
        }

        th {
            background: rgba(0, 255, 255, 0.2);
        }

        .status {
            padding: 5px 10px;
            border-radius: 5px;
            font-weight: bold;
        }

        .status.confirmed {
            background: limegreen;
            color: black;
        }

        .status.pending {
            background: orange;
            color: black;
        }

        .status.cancelled {
            background: red;
        }

        .cancel-btn {
            padding: 8px 15px;
            background: red;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 5px;
        }

        .cancel-btn:hover {
            background: darkred;
        }

    </style>
</head>
<body>

<div class="container">
    <h1>📅 Your Bookings</h1>
    <table>
        <tr>
            <th>Booking ID</th>
            <th>Classroom</th>
            <th>Date</th>
            <th>Time Slot</th>
            <th>Status</th>
            <th>Action</th>
        </tr>

        <?php while ($row = $result->fetch_assoc()) { ?>
            <tr>
                <td><?php echo $row['id']; ?></td>
                <td><?php echo htmlspecialchars($row['room_number']); ?></td>
                <td><?php echo htmlspecialchars($row['date']); ?></td>
                <td><?php echo date("g:i A", strtotime($row['start_time'])) . ' - ' . date("g:i A", strtotime($row['end_time'])); ?></td>
                <td>
                    <span class="status <?php echo strtolower($row['status']); ?>">
                        <?php echo ucfirst($row['status']); ?>
                    </span>
                </td>
                <td>
                    <?php if (strtolower($row['status']) === 'pending') { ?>
                        <form method="POST" action="cancel_booking.php">
                            <input type="hidden" name="booking_id" value="<?php echo $row['id']; ?>">
                            <button type="submit" class="cancel-btn">Cancel</button>
                        </form>
                    <?php } else { echo "N/A"; } ?>
                </td>
            </tr>
        <?php } ?>
    </table>
</div>

</body>
</html>
