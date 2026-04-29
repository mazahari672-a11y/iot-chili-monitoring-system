<?php
// Start the session
session_start();

// Include the database connection
include('config.php');

// Check if the user is logged in and is an admin
if (!isset($_SESSION['username']) || $_SESSION['username'] !== 'admin') {
    header('Location: index.php'); // Redirect to login page if not logged in as admin
    exit();
}

// Fetch user log data from the database
$log_query = "SELECT username, action, timestamp FROM user_log ORDER BY timestamp DESC LIMIT 10";
$log_result = $conn->query($log_query);
?>

<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ADMIN-User Log</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            background-color: #f4f4f9;
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

        .container {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
        }

        h1 {
            color: #333;
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 8px;
            text-align: center;
        }

        th {
            background-color: #f2f2f2;
            color: #333;
        }

        footer {
            text-align: center;
            margin-top: 20px;
            color: #ddd;
        }

        body {
            background: linear-gradient(to right, #6a11cb, #2575fc);
            background-size: cover;
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
        <h1>User Log</h1>
        <table id="logTable">
            <tr>
                <th>Username</th>
                <th>Action</th>
                <th>Timestamp</th>
            </tr>
            <?php if ($log_result->num_rows > 0): ?>
                <?php while($row = $log_result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['username']); ?></td>
                        <td><?php echo htmlspecialchars($row['action']); ?></td>
                        <td><?php echo htmlspecialchars($row['timestamp']); ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="3">No user logs found.</td>
                </tr>
            <?php endif; ?>
        </table>
    </div>

    <footer>
        <p>&copy; 2024 HAA.co. All rights reserved.</p>
    </footer>
</div>

<script>
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        sidebar.classList.toggle('open');
    }
</script>

</body>
</html>
