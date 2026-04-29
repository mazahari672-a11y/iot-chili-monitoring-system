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

// Handle form submission to update settings
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    // Get the new name from the form
    $newName = $_POST['new_name'];

    // Update the user's name in the database
    $updateQuery = "UPDATE User SET Nama = ? WHERE username = ?";
    $stmtUpdate = $conn->prepare($updateQuery);

    if ($stmtUpdate) {
        $stmtUpdate->bind_param("ss", $newName, $username);
        if ($stmtUpdate->execute()) {
            $_SESSION['Nama'] = $newName; // Update session with new name
            $nama = $newName; // Update variable with new name
            echo "<script>alert('Name updated successfully!');</script>";
        } else {
            echo "<script>alert('Failed to update name.');</script>";
        }
        $stmtUpdate->close();
    } else {
        echo "<script>alert('Database query failed.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings - IoT Chili Cultivation Dashboard</title>
    <style>
        /* Maintain the same design */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #7fbab6; /* Light greenish background */
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
            max-width: 500px;
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

        label {
            color: #2c3e50;
        }

        input[type="text"] {
            padding: 10px;
            width: 100%;
            margin: 10px 0;
            border-radius: 5px;
            border: 1px solid #bdc3c7;
        }

        button {
            padding: 10px 20px;
            background-color: #16a085; /* Teal color */
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background-color: #1abc9c; /* Lighter teal on hover */
        }

        footer {
            text-align: center;
            margin-top: 20px;
            font-size: 0.8em;
            color: #7f8c8d; /* Light gray footer text */
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
        <h1>Update Settings</h1>
        <form method="POST" action="">
            <label for="new_name">Full Name:</label>
            <input type="text" id="new_name" name="new_name" value="<?php echo $nama; ?>" required>
            <button type="submit" name="update">Update</button>
        </form>
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
</script>

</body>
</html>
