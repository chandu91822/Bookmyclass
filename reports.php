<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'config.php';

// Fetch past bookings (Older than today)
$sql = "SELECT classroom, date, time_from, time_to FROM bookings WHERE date < CURDATE() ORDER BY date DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>📜 Reports</title>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #1e1e2e, #25253a);
            color: white;
            text-align: center;
        }
        h1 {
            margin-top: 20px;
            text-shadow: 0 0 10px rgba(255, 255, 255, 0.7);
        }
        table {
            width: 80%;
            margin: 20px auto;
            border-collapse: collapse;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 10px;
            overflow: hidden;
        }
        th, td {
            padding: 12px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
        }
        th {
            background: rgba(255, 255, 255, 0.2);
            font-size: 18px;
        }
        td {
            font-size: 16px;
        }
        tr:hover {
            background: rgba(255, 255, 255, 0.2);
        }
        .no-data {
            margin: 20px;
            font-size: 18px;
        }
    </style>
</head>
<body>
    <h1>📜 Past Booking Reports</h1>

    <?php if ($result->num_rows > 0) { ?>
        <table>
            <tr>
                <th>🏫 Classroom</th>
                <th>📅 Date</th>
                <th>⏰ Time From</th>
                <th>⏳ Time To</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['classroom']); ?></td>
                    <td><?php echo htmlspecialchars($row['date']); ?></td>
                    <td><?php echo htmlspecialchars($row['time_from']); ?></td>
                    <td><?php echo htmlspecialchars($row['time_to']); ?></td>
                </tr>
            <?php } ?>
        </table>
    <?php } else { ?>
        <p class="no-data">❌ No past bookings found.</p>
    <?php } ?>

</body>
</html>

<?php
$conn->close();
?>
