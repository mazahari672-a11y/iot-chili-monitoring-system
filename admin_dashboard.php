<?php
// Ensure user is logged in as admin
session_start();

if (!isset($_SESSION['username']) || $_SESSION['username'] !== 'admin') {
    header('Location: index.php'); // Redirect to login if not admin
    exit();
}
?>

<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ADMIN-Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            background-color: #f2f4f7;
            color: #333;
        }

        .main-content {
            margin-left: 0;
            padding: 20px;
            transition: margin-left 0.3s;
        }

        .sidebar.open + .main-content {
            margin-left: 300px;
        }

        .toggle-btn {
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

        .toggle-btn:hover {
            background-color: #0056b3;
        }

        /* Dashboard Content Styles */
        .container {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.1);
            margin-top: 50px;
        }

        h1 {
            color: #2e3d54;
            text-align: center;
            font-size: 28px;
        }

        .data-display {
            display: flex;
            justify-content: space-around;
            margin-top: 20px;
        }

        .data-box {
            background-color: #e3f2fd; /* Light blue */
            border: 1px solid #bbdefb;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            width: 45%;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .data-box h3 {
            font-size: 20px;
            color: #1f2a44;
        }

        .data-box p {
            font-size: 18px;
            color: #333;
            font-weight: bold;
        }

        footer {
            text-align: center;
            margin-top: 20px;
            color: #777;
        }

        #lastUpdate {
            text-align: center;
            margin-top: 20px;
            font-size: 18px;
            color: #444;
        }

        /* Add gradient background to the page */
        body {
            background: linear-gradient(to right, #6a11cb, #2575fc);
            background-size: cover;
        }

        footer {
            font-size: 14px;
            color: #ddd;
        }

    </style>
</head>
<body>

<!-- Include Sidebar -->
<?php include('sidebar_admin.php'); ?>

<!-- Main Content -->
<div class="main-content">
    <button class="toggle-btn" onclick="toggleSidebar()">&#9776; Menu</button>

    <div class="container">
        <h1>IoT Chili Cultivation Admin Dashboard</h1>

        <div class="data-display">
            <div class="data-box">
                <h3>Temperature</h3>
                <p id="temperature">Loading...</p>
            </div>
            <div class="data-box">
                <h3>Humidity</h3>
                <p id="humidity">Loading...</p>
            </div>
        </div>

        <div class="data-display">
            <div class="data-box">
                <h3>Soil Moisture 1</h3>
                <p id="soilMoisture1">Loading...</p>
            </div>
            <div class="data-box">
                <h3>Soil Moisture 2</h3>
                <p id="soilMoisture2">Loading...</p>
            </div>
        </div>

        <p id="lastUpdate">Loading last update time...</p>
    </div>

    <footer>
        <p>&copy; 2024 HAA.co. All rights reserved.</p>
    </footer>
</div>

<script>
    // Function to toggle sidebar visibility
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        sidebar.classList.toggle('open');
    }

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


    setInterval(fetchData, 1000);
    fetchData();
</script>

</body>
</html>
