<?php
// Sidebar with styling
?>

<style>
    /* Sidebar Styling */
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
</style>

<div id="sidebar" class="sidebar">
    <br>
    <h2>Hi, <?php echo $nama; ?></h2>
    <ul>
        <li><a href="dashboard.php">Dashboard</a></li>
        <li><a href="data_history.php">Data History</a></li>
        <li><a href="settings.php">Settings</a></li>
        <li>
            <form action="logout.php" method="POST">
                <button type="submit" style="background-color: #e74c3c; width: 100%; border: none; padding: 10px; color: white;">Log Out</button>
            </form>
        </li>
    </ul>
</div>
