<!-- sidebar_admin.php -->
<style>
    .sidebar {
        width: 300px; /* Increased width from 250px to 300px */
        background-color: #1f2a44; /* Dark blue */
        color: white;
        height: 100vh;
        position: fixed;
        top: 0;
        left: -300px; /* Adjusted to match the increased width */
        transition: left 0.3s;
        padding-top: 20px;
        box-shadow: 2px 0 8px rgba(0, 0, 0, 0.2);
    }

    .sidebar.open {
        left: 0;
    }

    .sidebar h2 {
        text-align: center;
        margin: 20px 0;
        font-size: 30px;
        font-weight: bold;
        color: #f1f1f1;
    }

    .sidebar ul {
        list-style-type: none;
        padding: 0;
    }

    .sidebar ul li {
        padding: 20px; /* Adjusted padding to make it more spaced out */
        text-align: left;
    }

    .sidebar ul li:hover {
        background-color: #2980b9;
    }

    .sidebar ul li a {
        color: white;
        text-decoration: none;
        display: block;
        font-size: 18px;
    }
</style>

<div id="sidebar" class="sidebar">
    <br>
    <h2>Hi, Admin</h2>
    <ul>
        <li><a href="admin_dashboard.php">Dashboard</a></li>
        <li><a href="admin_dataHistory.php">Data History</a></li>
        <li><a href="user_log.php">User Log</a></li>
        <li><a href="user_management.php">User Management</a></li>
        <li><a href="habis.php">TAHNIAH</a></li>
        <li>
            <form action="logout.php" method="POST">
                <button type="submit" style="background-color: #d9534f; width: 100%; border: none; padding: 10px; color: white;">Log Out</button>
            </form>
        </li>
    </ul>
</div>
