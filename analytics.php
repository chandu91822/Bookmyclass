<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'config.php';

// Fetch total bookings per classroom
$sql = "SELECT classroom, COUNT(*) as count FROM bookings GROUP BY classroom";
$result = $conn->query($sql);
$rooms = [];
$counts = [];

while ($row = $result->fetch_assoc()) {
    $rooms[] = $row['classroom'];
    $counts[] = $row['count'];
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>📊 Classroom Analytics</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        /* Reset and base */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background: linear-gradient(135deg, #1c1f2b, #2a2d3f);
            color: #ffffff;
            min-height: 100vh;
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

        canvas {
            width: 90%;
            max-width: 700px;
            background: rgba(255, 255, 255, 0.05);
            padding: 20px;
            border-radius: 20px;
            backdrop-filter: blur(8px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.5);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            animation: slideInUp 1.2s ease forwards;
        }

        canvas:hover {
            transform: scale(1.02);
            box-shadow: 0 10px 30px rgba(0, 255, 200, 0.2);
        }

        @keyframes fadeIn {
            from {opacity: 0;}
            to {opacity: 1;}
        }

        @keyframes slideInDown {
            from {opacity: 0; transform: translateY(-30px);}
            to {opacity: 1; transform: translateY(0);}
        }

        @keyframes slideInUp {
            from {opacity: 0; transform: translateY(40px);}
            to {opacity: 1; transform: translateY(0);}
        }

        @media (max-width: 768px) {
            h1 {
                font-size: 28px;
            }

            canvas {
                width: 100%;
                padding: 15px;
            }
        }
    </style>
</head>
<body>
    <h1>📊 Classroom Booking Analytics</h1>
    <canvas id="chart"></canvas>

    <script>
        const ctx = document.getElementById('chart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($rooms); ?>,
                datasets: [{
                    label: 'Total Bookings',
                    data: <?php echo json_encode($counts); ?>,
                    backgroundColor: [
                        '#00ffd5', '#ff6b81', '#1abc9c', '#ffcc00', '#9b59b6',
                        '#e67e22', '#3498db', '#e84393', '#55efc4', '#ff7675'
                    ],
                    borderRadius: 10,
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        labels: {
                            color: '#ffffff',
                            font: {
                                size: 14
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        ticks: { color: '#ffffff' },
                        grid: { color: 'rgba(255,255,255,0.1)' }
                    },
                    y: {
                        beginAtZero: true,
                        ticks: { color: '#ffffff' },
                        grid: { color: 'rgba(255,255,255,0.1)' }
                    }
                }
            }
        });
    </script>
</body>
</html>
