<?php
// Gantikan ini dengan sambungan ke pangkalan data anda
require 'config.php';

// Mulakan sesi
session_start();

// Semak jika pengguna sudah log masuk
if (!isset($_SESSION['username'])) {
    header('Location: index.php'); // Redirect ke halaman log masuk jika tidak log masuk
    exit();
}

// Dapatkan nama pengguna dari sesi
$username = $_SESSION['username'];

// Dapatkan nama penuh pengguna dari pangkalan data
$query = "SELECT Nama FROM User WHERE username = ?";
$stmt = $conn->prepare($query);

if ($stmt) {
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->bind_result($nama);
    $stmt->fetch();
    $_SESSION['Nama'] = $nama; // Simpan nama dalam sesi untuk digunakan dalam halaman ini
    $stmt->close();
} else {
    echo "Ralat dalam persediaan kueri.";
}
?>

<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IoT Chili Cultivation Dashboard</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #7fbab6; /* Light gray background */
            margin: 0;
            color: #333;
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

        /* Main content */
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
            border-top: 5px solid #1abc9c; /* Accent color border on top */
        }

        h1 {
            text-align: center;
            color: #2c3e50; /* Darker blue-gray */
        }

        .data-display {
            display: flex;
            justify-content: space-around;
            margin: 20px 0;
        }

        .data-box {
            background-color: #ecf0f1; /* Light gray background for boxes */
            border: 1px solid #bdc3c7; /* Light border color */
            border-radius: 5px;
            padding: 20px;
            text-align: center;
            width: 45%;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
        }

        .control-panel {
            text-align: center;
            margin: 20px 0;
        }

        .control-panel button {
            padding: 10px 20px;
            margin: 10px;
            background-color: #16a085; /* Teal color */
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .control-panel button:hover {
            background-color: #1abc9c; /* Lighter teal on hover */
        }

        footer {
            text-align: center;
            margin-top: 20px;
            font-size: 0.8em;
            color: #7f8c8d; /* Light gray footer text */
        }

        /* Last update text centered */
        #lastUpdate {
            text-align: center;
            margin-top: 20px;
            font-size: 18px;
            color: #2c3e50; /* Darker blue-gray */
        }
    </style>
</head>
<body>

<!-- Hamburger Button -->
<button id="hamburger" class="hamburger">&#9776; Menu</button>

<!-- Include Sidebar -->
<?php include 'sidebar.php'; ?>

<!-- Main Content -->
<div class="main-content">
    <div class="container">
        <h1>IoT Chili Cultivation Dashboard</h1>

        <!-- Data Display -->
        <div class="data-display">
            <div class="data-box">
                <h2>Temperature</h2>
                <p id="temperature">Loading...</p>
            </div>
            <div class="data-box">
                <h2>Humidity</h2>
                <p id="humidity">Loading...</p>
            </div>
        </div>

        <div class="data-display">
            <div class="data-box">
                <h2>Soil Moisture 1</h2>
                <p id="soilMoisture1">Loading...</p>
            </div>
            <div class="data-box">
                <h2>Soil Moisture 2</h2>
                <p id="soilMoisture2">Loading...</p>
            </div>
        </div>

        <!-- Last Update -->
        <p id="lastUpdate">Loading last update time...</p>

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

    // Fetch and display data
    async function fetchData() {
        try {
            const response = await fetch('data.php'); // Fetch real-time data
            const data = await response.json();

            if (data.status === 'success') {
                document.getElementById('temperature').textContent = `${data.temperature}°C`;
                document.getElementById('humidity').textContent = `${data.humidity}%`;
                document.getElementById('soilMoisture1').textContent = `${data.soilMoisture1}%`;
                document.getElementById('soilMoisture2').textContent = `${data.soilMoisture2}%`;

                // Format the last update time to 24-hour format
                const lastUpdate = new Date(data.lastUpdate);
                const formattedTime = lastUpdate.toLocaleTimeString('en-GB', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
                const formattedDate = lastUpdate.toLocaleDateString('en-GB');
                document.getElementById('lastUpdate').textContent = `Last updated: ${formattedDate} ${formattedTime}`;
            } else {
                document.getElementById('lastUpdate').textContent = data.message;
            }
        } catch (error) {
            console.error('Error fetching data:', error);
            document.getElementById('lastUpdate').textContent = 'Error fetching data.';
        }
    }

    fetchData();
    setInterval(fetchData, 60000);
</script>

</body>
</html>
