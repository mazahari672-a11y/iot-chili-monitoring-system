<?php
// Include the database configuration file
require 'config.php';

// Start the session
session_start();

if (!isset($_SESSION['username']) || $_SESSION['username'] !== 'admin') {
    header('Location: index.php'); // Redirect to login if not admin
    exit();
}

// Get the logged-in user's name from the session
$username = $_SESSION['username'];

// Fetch the full name of the user from the database
$query = "SELECT Nama FROM User WHERE username = ?";
$stmt = $conn->prepare($query);

if ($stmt) {
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->bind_result($nama);
    $stmt->fetch();
    $stmt->close();
} else {
    echo "Error in query preparation.";
}

// Fetch the 10 latest records from datahistory table
$dataQuery = "SELECT time, temperature, humidity, soilMoisture1, soilMoisture2, latency, rssi FROM datahistory ORDER BY time DESC LIMIT 100";
$dataResult = $conn->query($dataQuery);

// Prepare arrays for graph data
$labels = [];
$temperatureData = [];
$humidityData = [];
$soilMoisture1Data = [];
$soilMoisture2Data = [];
$latencyData = [];
$rssiData = [];

if ($dataResult && $dataResult->num_rows > 0) {
    while ($row = $dataResult->fetch_assoc()) {
        $labels[] = $row['time'];
        $temperatureData[] = $row['temperature'];
        $humidityData[] = $row['humidity'];
        $soilMoisture1Data[] = $row['soilMoisture1'];
        $soilMoisture2Data[] = $row['soilMoisture2'];
        $latencyData[] = $row['latency'];
        $rssiData[] = $row['rssi'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ADMIN-Data History</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        /* Design tetap sama seperti versi awal */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            background: linear-gradient(to right, #6a11cb, #2575fc);
            color: #333;
        }
        .hamburger {
            position: absolute;
            top: 20px;
            left: 20px;
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px;
            cursor: pointer;
            border-radius: 5px;
        }
        .hamburger:hover {
            background-color: #0056b3;
        }
        .sidebar.open {
            left: 0;
        }
        .main-content {
            margin-left: 0;
            padding: 20px;
            transition: margin-left 0.3s ease;
        }
        .sidebar.open + .main-content {
            margin-left: 100px;
        }

        .container {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.1);
            margin-top: 50px;
        }
        h1 {
            text-align: center;
            color: #2c3e50;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 10px;
            text-align: center;
        }
        th {
            background-color: #007bff;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        .toggle-buttons {
            text-align: center;
            margin: 20px 0;
        }
        .toggle-buttons button {
            padding: 10px 20px;
            margin: 0 10px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .toggle-buttons button:hover {
            background-color: #0056b3;
        }
        .graph-container {
            display: none;
        }
        .graph-container.active {
            display: block;
            margin-top: 20px;
        }
        canvas {
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 5px;
            margin: 10px auto;
            width: 80%;
            height: 300px;
        }
        footer {
            text-align: center;
            margin-top: 20px;
            color: #ddd;
        }
    </style>
</head>
<body>

<!-- Sidebar -->
<?php include('sidebar_admin.php'); ?>

<!-- Main Content -->
<div class="main-content">
    <button id="hamburger" class="hamburger">&#9776; Menu</button>
    <div class="container">
        <h1>Data History</h1>
        <div class="toggle-buttons">
            <button id="viewTable">View Table</button>
            <button id="viewGraph">View Graph</button>
        </div>
        <div id="dataTable">
            <table>
                <tr>
                    <th>Time</th>
                    <th>Temperature (°C)</th>
                    <th>Humidity (%)</th>
                    <th>Soil Moisture 1 (%)</th>
                    <th>Soil Moisture 2 (%)</th>
                    <th>Latency (ms)</th>
                    <th>Wifi Signal Strength (dBm)</th>
                </tr>
                <?php
                if (!empty($temperatureData)) {
                    foreach (array_reverse($temperatureData) as $index => $temp) {
                        echo "<tr>";
                        echo "<td>" . $labels[$index] . "</td>";
                        echo "<td>" . $temp . "</td>";
                        echo "<td>" . $humidityData[$index] . "</td>";
                        echo "<td>" . $soilMoisture1Data[$index] . "</td>";
                        echo "<td>" . $soilMoisture2Data[$index] . "</td>";
                        echo "<td>" . $latencyData[$index] . "</td>";
                    
                        // Logik pewarnaan untuk RSSI
                        $rssiValue = $rssiData[$index];
                        $rssiColor = ''; // Default
                    
                        if ($rssiValue >= -70) {
                            $rssiColor = 'green'; // Kuat
                        } elseif ($rssiValue >= -80 && $rssiValue < -70) {
                            $rssiColor = 'yellow'; // Sederhana
                        } else {
                            $rssiColor = 'red'; // Lemah
                        }
                    
                        echo "<td style='background-color: $rssiColor; color: white;'>" . $rssiValue . "</td>";
                        echo "</tr>";
                    }
                    
                } else {
                    echo "<tr><td colspan='6'>No data available.</td></tr>";
                }
                ?>
            </table>
        </div>
        <div id="dataGraph" class="graph-container">
        <canvas id="temperatureGraph"></canvas>
        <canvas id="humidityGraph"></canvas>
        <canvas id="soilMoisture1Graph"></canvas>
        <canvas id="soilMoisture2Graph"></canvas>
        <canvas id="latencyGraph"></canvas>
        <canvas id="rssiGraph"></canvas>
        </div>
    </div>
    <footer>
        <p>&copy; 2024 HAA.co. All rights reserved.</p>
    </footer>
</div>

<script>
     // Toggle sidebar
     const hamburger = document.getElementById('hamburger');
    const sidebar = document.getElementById('sidebar');
    hamburger.addEventListener('click', () => {
        sidebar.classList.toggle('open');
    });  
    // Toggle table and graph view
    document.getElementById('viewTable').addEventListener('click', function () {
        document.getElementById('dataTable').style.display = 'block';
        document.getElementById('dataGraph').classList.remove('active');
    });
    document.getElementById('viewGraph').addEventListener('click', function () {
        document.getElementById('dataTable').style.display = 'none';
        document.getElementById('dataGraph').classList.add('active');
        drawLatencyGraph();
        drawRssiGraph();
    });

    // Graph data
    // Data untuk grafik
    const temperatureData = <?php echo json_encode($temperatureData); ?>;
    const humidityData = <?php echo json_encode($humidityData); ?>;
    const soilMoisture1Data = <?php echo json_encode($soilMoisture1Data); ?>;
    const soilMoisture2Data = <?php echo json_encode($soilMoisture2Data); ?>;
    const latencyData = <?php echo json_encode($latencyData); ?>;
    const rssiData = <?php echo json_encode($rssiData); ?>;
    const labels = <?php echo json_encode($labels); ?>;

    function drawTemperatureGraph() {
        new Chart(document.getElementById('temperatureGraph'), {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Temperature (°C)',
                    data: temperatureData,
                    borderColor: '#ff6384',
                    fill: false,
                    tension: 0.1
                }]
            },
            options: {
                scales: {
                    x: { title: { display: true, text: 'Time' } },
                    y: { title: { display: true, text: 'Temperature (°C)' } }
                }
            }
        });
    }

    function drawHumidityGraph() {
        new Chart(document.getElementById('humidityGraph'), {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Humidity (%)',
                    data: humidityData,
                    borderColor: '#36a2eb',
                    fill: false,
                    tension: 0.1
                }]
            },
            options: {
                scales: {
                    x: { title: { display: true, text: 'Time' } },
                    y: { title: { display: true, text: 'Humidity (%)' } }
                }
            }
        });
    }

    function drawSoilMoisture1Graph() {
        new Chart(document.getElementById('soilMoisture1Graph'), {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Soil Moisture 1 (%)',
                    data: soilMoisture1Data,
                    borderColor: '#4bc0c0',
                    fill: false,
                    tension: 0.1
                }]
            },
            options: {
                scales: {
                    x: { title: { display: true, text: 'Time' } },
                    y: { title: { display: true, text: 'Soil Moisture 1 (%)' } }
                }
            }
        });
    }

    function drawSoilMoisture2Graph() {
        new Chart(document.getElementById('soilMoisture2Graph'), {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Soil Moisture 2 (%)',
                    data: soilMoisture2Data,
                    borderColor: '#9966ff',
                    fill: false,
                    tension: 0.1
                }]
            },
            options: {
                scales: {
                    x: { title: { display: true, text: 'Time' } },
                    y: { title: { display: true, text: 'Soil Moisture 2 (%)' } }
                }
            }
        });
    }

    function drawLatencyGraph() {
        new Chart(document.getElementById('latencyGraph'), {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Latency (ms)',
                    data: latencyData,
                    borderColor: '#ff9f40',
                    fill: false,
                    tension: 0.1
                }]
            },
            options: {
                scales: {
                    x: { title: { display: true, text: 'Time' } },
                    y: { title: { display: true, text: 'Latency (ms)' } }
                }
            }
        });
    }

    function drawRssiGraph() {
        new Chart(document.getElementById('rssiGraph'), {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Wifi Signal Strength (dBm)',
                    data: rssiData,
                    borderColor: '#ff40ac',
                    fill: false,
                    tension: 0.1
                }]
            },
            options: {
                scales: {
                    x: { title: { display: true, text: 'Time' } },
                    y: { title: { display: true, text: 'RSSI (dBm)' } }
                }
            }
        });
    }

    document.getElementById('viewTable').addEventListener('click', function () {
        document.getElementById('dataTable').style.display = 'block';
        document.getElementById('dataGraph').classList.remove('active');
    });

    document.getElementById('viewGraph').addEventListener('click', function () {
        document.getElementById('dataTable').style.display = 'none';
        document.getElementById('dataGraph').classList.add('active');
        drawTemperatureGraph();
        drawHumidityGraph();
        drawSoilMoisture1Graph();
        drawSoilMoisture2Graph();
        drawLatencyGraph();
        drawRssiGraph();
    });
</script>

</body>
</html>