<?php
// Include the database configuration file
require 'config.php';

// Start the session
session_start();

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header('Location: index.php'); // Redirect to login page if not logged in
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
}

// Fetch the 10 latest records from datahistory table
$dataQuery = "SELECT time, temperature, humidity, soilMoisture1, soilMoisture2 FROM datahistory ORDER BY time DESC LIMIT 10";
$dataResult = $conn->query($dataQuery);

// Prepare arrays for graph data
$labels = [];
$temperatureData = [];
$humidityData = [];
$soilMoisture1Data = [];
$soilMoisture2Data = [];

if ($dataResult && $dataResult->num_rows > 0) {
    while ($row = $dataResult->fetch_assoc()) {
        $labels[] = $row['time'];
        $temperatureData[] = $row['temperature'];
        $humidityData[] = $row['humidity'];
        $soilMoisture1Data[] = $row['soilMoisture1'];
        $soilMoisture2Data[] = $row['soilMoisture2'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data History - IoT Chili Cultivation Dashboard</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        /* General Styling */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #7fbab6;
            margin: 0;
            color: #333;
        }

        .hamburger {
            position: fixed;
            top: 10px;
            left: 10px;
            background-color: #16a085;
            color: white;
            border: none;
            padding: 10px 15px;
            cursor: pointer;
            border-radius: 5px;
            z-index: 1100;
        }

        .hamburger:hover {
            background-color: #1abc9c;
        }
    
        /* Sidebar styling */
        .sidebar {
            width: 250px;
            background-color: #34495e; /* Darker blue-gray */
            color: white;
            height: 100vh;
            position: fixed;
            top: 0;
            left: -250px;
            transition: left 0.3s ease;
            z-index: 1000;
            padding: 20px 0;
        }

        .sidebar.open {
            left: 0;
        }

        .sidebar h2 {
            text-align: center;
            margin-bottom: 20px;
            font-size: 1.5em;
            color: #ecf0f1; /* Light grayish white */
        }

        .sidebar ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .sidebar ul li {
            padding: 15px 20px;
            cursor: pointer;
        }

        .sidebar ul li:hover {
            background-color: #2980b9; /* Blue color on hover */
        }

        .sidebar ul li a {
            text-decoration: none;
            color: white;
            display: block;
        }

        /* Hamburger menu button */
        .hamburger {
            position: fixed;
            top: 10px;
            left: 10px;
            background-color: #16a085; /* Teal color */
            color: white;
            border: none;
            padding: 10px 15px;
            cursor: pointer;
            border-radius: 5px;
            z-index: 1100;
        }

        .hamburger:hover {
            background-color: #1abc9c; /* Lighter teal on hover */
        }

        .main-content {
            margin-left: 0;
            padding: 20px;
            transition: margin-left 0.3s ease;
        }

        .sidebar.open + .main-content {
            margin-left: 250px;
        }

        .container {
            max-width: 900px;
            margin: 20px auto;
            background: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0px 6px 15px rgba(0, 0, 0, 0.1);
            border-top: 5px solid #1abc9c;
        }

        h1 {
            text-align: center;
            color: #2c3e50;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background-color: #fff;
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 10px;
            text-align: center;
        }

        th {
            background-color: #16a085;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        footer {
            text-align: center;
            margin-top: 20px;
            font-size: 0.8em;
            color:#7f8c8d;
        }

        .toggle-buttons {
            text-align: center;
            margin-bottom: 20px;
        }

        .toggle-buttons button {
            padding: 10px 20px;
            margin: 0 10px;
            background-color: #16a085;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .toggle-buttons button:hover {
            background-color: #1abc9c;
        }

        .graph-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            gap: 20px;
        }

        canvas {
            background:rgb(224, 231, 226);
            border: 1px solid #ddd;
            border-radius: 5px;
            width: 20%; /* Adjusted for smaller size */
            height: 20px;
        }
    </style>
</head>
<body>

<!-- Hamburger Button -->
<button id="hamburger" class="hamburger">&#9776; Menu</button>

<!-- Sidebar -->
<?php include('sidebar.php'); ?>

<!-- Main Content -->
<div class="main-content">
    <div class="container">
        <h1>Data History</h1>
        <div class="toggle-buttons">
            <button id="viewTable">View Table</button>
            <button id="viewGraph">View Graph</button>
        </div>
        <div id="dataTable" style="display: block;">
            <table>
                <tr>
                    <th>Time</th>
                    <th>Temperature (°C)</th>
                    <th>Humidity (%)</th>
                    <th>Soil Moisture 1 (%)</th>
                    <th>Soil Moisture 2 (%)</th>
                </tr>
                <?php
                if ($dataResult && $dataResult->num_rows > 0) {
                    foreach (array_reverse($temperatureData) as $index => $temp) {
                        echo "<tr>";
                        echo "<td>" . $labels[$index] . "</td>";
                        echo "<td>" . $temp . "</td>";
                        echo "<td>" . $humidityData[$index] . "</td>";
                        echo "<td>" . $soilMoisture1Data[$index] . "</td>";
                        echo "<td>" . $soilMoisture2Data[$index] . "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='5'>No data available.</td></tr>";
                }
                ?>
            </table>
        </div>
        <div id="dataGraph" style="display: none;">
            <div class="graph-container">
                <canvas id="temperatureGraph"></canvas>
                <canvas id="humidityGraph"></canvas>
                <canvas id="soilMoisture1Graph"></canvas>
                <canvas id="soilMoisture2Graph"></canvas>
            </div>
        </div>
        <footer>
        <p>&copy; 2024 HAA.co. All rights reserved.</p>
        <p>Version 1.0</p>
        </footer>
    </div>
</div>

<script>
  // Toggle sidebar
  const hamburger = document.getElementById('hamburger');
    const sidebar = document.getElementById('sidebar');
    hamburger.addEventListener('click', () => {
        sidebar.classList.toggle('open');
    });

    // Toggle between table and graph views
    document.getElementById('viewTable').addEventListener('click', () => {
        document.getElementById('dataTable').style.display = 'block';
        document.getElementById('dataGraph').style.display = 'none';
    });

    document.getElementById('viewGraph').addEventListener('click', () => {
        document.getElementById('dataTable').style.display = 'none';
        document.getElementById('dataGraph').style.display = 'block';
        loadGraphs();
    });

    // Data for graphs (passed from PHP)
    const labels = <?php echo json_encode($labels); ?>;
    const temperatureData = <?php echo json_encode($temperatureData); ?>;
    const humidityData = <?php echo json_encode($humidityData); ?>;
    const soilMoisture1Data = <?php echo json_encode($soilMoisture1Data); ?>;
    const soilMoisture2Data = <?php echo json_encode($soilMoisture2Data); ?>;

    function loadGraphs() {
    // Temperature Graph
    new Chart(document.getElementById('temperatureGraph'), {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Temperature (°C)',
                data: temperatureData,
                borderColor: '#FF6384',
                tension: 0.1
            }]
        },
        options: {
            scales: {
                x: {
                    title: {
                        display: true,
                        text: 'Time',
                    },
                    ticks: {
                        color: '#000' // Warna hitam untuk nombor axis x
                    }
                },
                y: {
                    title: {
                        display: true,
                        text: 'Temperature (°C)'
                    },
                    ticks: {
                        color: '#000' // Warna hitam untuk nombor axis y
                    }
                }
            }
        }
    });

    // Humidity Graph
    new Chart(document.getElementById('humidityGraph'), {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Humidity (%)',
                data: humidityData,
                borderColor: '#36A2EB',
                tension: 0.1
            }]
        },
        options: {
            scales: {
                x: {
                    title: {
                        display: true,
                        text: 'Time'
                    },
                    ticks: {
                        color: '#000' // Warna hitam untuk nombor axis x
                    }
                },
                y: {
                    title: {
                        display: true,
                        text: 'Humidity (%)'
                    },
                    ticks: {
                        color: '#000' // Warna hitam untuk nombor axis y
                    }
                }
            }
        }
    });

    // Soil Moisture 1 Graph
    new Chart(document.getElementById('soilMoisture1Graph'), {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Soil Moisture 1 (%)',
                data: soilMoisture1Data,
                borderColor: '#FFCE56',
                tension: 0.1
            }]
        },
        options: {
            scales: {
                x: {
                    title: {
                        display: true,
                        text: 'Time'
                    },
                    ticks: {
                        color: '#000' // Warna hitam untuk nombor axis x
                    }
                },
                y: {
                    title: {
                        display: true,
                        text: 'Soil Moisture 1 (%)'
                    },
                    ticks: {
                        color: '#000' // Warna hitam untuk nombor axis y
                    }
                }
            }
        }
    });

    // Soil Moisture 2 Graph
    new Chart(document.getElementById('soilMoisture2Graph'), {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Soil Moisture 2 (%)',
                    data: soilMoisture2Data,
                    borderColor: '#4BC0C0',
                    tension: 0.1
                }]
            },
            options: {
            scales: {
                x: {
                    title: {
                        display: true,
                        text: 'Time'
                    },
                    ticks: {
                        color: '#000' // Warna hitam untuk nombor axis x
                    }
                },
                y: {
                    title: {
                        display: true,
                        text: 'Soil Moisture 2 (%)'
                    },
                    ticks: {
                        color: '#000' // Warna hitam untuk nombor axis y
                    }
                }
            }
        }
        });
}

</script>

</body>
</html>
