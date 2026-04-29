<?php
// Start the session
session_start();

// Include the database connection file
include('config.php');

// Check if the user is logged in and is an admin
if (!isset($_SESSION['username']) || $_SESSION['username'] !== 'admin') {
    header('Location: index.php'); // Redirect to login page if not logged in as admin
    exit();
}

// Fetch all users from the database
$query = "SELECT id, username, nama FROM user";
$result = $conn->query($query);

// Add New User
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_user'])) {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);  // Hash password
    $nama = $_POST['nama'];

    $addQuery = "INSERT INTO user (username, password, nama) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($addQuery);
    $stmt->bind_param("sss", $username, $password, $nama);
    $stmt->execute();
    $stmt->close();
    $_SESSION['message'] = "User added successfully!";
    header('Location: user_management.php'); // Reload the page to see the changes
    exit();
}

// Delete User
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];

    $deleteQuery = "DELETE FROM user WHERE id = ?";
    $stmt = $conn->prepare($deleteQuery);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    $_SESSION['message'] = "User deleted successfully!";
    header('Location: user_management.php'); // Reload the page to see the changes
    exit();
}

// Edit User
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $editQuery = "SELECT username, nama FROM user WHERE id = ?";
    $stmt = $conn->prepare($editQuery);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($username, $nama);
    $stmt->fetch();
    $stmt->close();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit_user'])) {
    $id = $_POST['id'];
    $username = $_POST['username'];
    $password = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : null; // Password optional
    $nama = $_POST['nama'];

    if ($password) {
        $editQuery = "UPDATE user SET username = ?, password = ?, nama = ? WHERE id = ?";
        $stmt = $conn->prepare($editQuery);
        $stmt->bind_param("sssi", $username, $password, $nama, $id);
    } else {
        $editQuery = "UPDATE user SET username = ?, nama = ? WHERE id = ?";
        $stmt = $conn->prepare($editQuery);
        $stmt->bind_param("ssi", $username, $nama, $id);
    }

    $stmt->execute();
    $stmt->close();
    $_SESSION['message'] = "User updated successfully!";
    header('Location: user_management.php'); // Reload the page to see the changes
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ADMIN-User Management</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            background-color: #f1f1f1;
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

        /* User Management Styles */
        .container {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
        }

        h1 {
            color: #333;
            text-align: center;
            margin-bottom: 30px;
        }

        h3 {
            color: #333;
            margin-bottom: 20px;
        }

        form {
            margin-bottom: 30px;
        }

        input[type="text"], input[type="password"] {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-sizing: border-box;
        }

        input[type="text"]:focus, input[type="password"]:focus {
            border-color: #007bff;
            outline: none;
        }

        button {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 12px 20px;
            cursor: pointer;
            border-radius: 5px;
            font-size: 16px;
        }

        button:hover {
            background-color: #0056b3;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background-color: #f9f9f9;
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 8px;
            text-align: center;
        }

        th {
            background-color: #007bff;
            color: white;
        }

        .edit, .delete {
            background-color: #f0ad4e;
            color: white;
            padding: 5px 10px;
            border-radius: 5px;
            text-decoration: none;
        }

        .delete {
            background-color: #d9534f;
        }

        .edit:hover, .delete:hover {
            opacity: 0.8;
        }

        .cancel-button {
            background-color: #6c757d;
            color: white;
            padding: 5px 10px;
            text-decoration: none;
            border-radius: 5px;
        }

        .cancel-button:hover {
            opacity: 0.8;
        }

        footer {
            text-align: center;
            margin-top: 20px;
            color: #ddd;
        }

        .alert {
            background-color: #28a745;
            color: white;
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 5px;
            text-align: center;
        }
        .cancel-button {
        background-color: red;
        color: white;
        padding: 12px 20px;
        text-decoration: none;
        border-radius: 5px;
        }

        .cancel-button:hover {
         opacity: 0.8;
        }


        body {
            background: linear-gradient(to right, #6a11cb, #2575fc);
            background-size: cover;
        }

        @media (max-width: 768px) {
            .sidebar {
                left: -250px; /* Collapse sidebar on mobile */
            }
            .sidebar.open {
                left: 0;
            }
            .main-content {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>

<!-- Sidebar -->
<?php include('sidebar_admin.php'); ?>

<!-- Main Content -->
<div class="main-content">
    <button class="toggle-btn" onclick="toggleSidebar()">&#9776; Menu</button>

    <!-- User Management Section -->
    <div class="container">
        <h1>User Management</h1>

        <!-- Success/Error Message -->
        <?php
        if (isset($_SESSION['message'])) {
            echo "<div class='alert'>{$_SESSION['message']}</div>";
            unset($_SESSION['message']);
        }
        ?>

        <!-- Add New User Form -->
        <h3>Add New User</h3>
        <form action="user_management.php" method="POST">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <input type="text" name="nama" placeholder="Full Name" required>
            <button type="submit" name="add_user">Add User</button>
        </form>

        <!-- Edit User Form -->
        <?php if (isset($_GET['edit'])): ?>
        <h3>Edit User</h3>
        <form action="user_management.php" method="POST">
        <input type="hidden" name="id" value="<?php echo $id; ?>">
        <input type="text" name="username" value="<?php echo $username; ?>" required>
        <input type="password" name="password" placeholder="Leave empty to keep current password">
        <input type="text" name="nama" value="<?php echo $nama; ?>" required>
        <button type="submit" name="edit_user">Update User</button>
        <a href="user_management.php" class="cancel-button">Cancel</a> <!-- Cancel Button -->
        </form>
        
        <?php endif; ?>


        <h3>Existing Users</h3>
        <table>
            <thead>
                <tr>
                    
                    <th>Username</th>
                    <th>Full Name</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        
                        <td><?php echo $row['username']; ?></td>
                        <td><?php echo $row['nama']; ?></td>
                        <td>
                            <a href="user_management.php?edit=<?php echo $row['id']; ?>" class="edit">Edit</a>
                            <a href="user_management.php?delete=<?php echo $row['id']; ?>" class="delete">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Footer -->
<footer>
    <p>&copy; 2024 HAA.co. All rights reserved.</p>
</footer>

<!-- Toggle Sidebar Script -->
<script>
    function toggleSidebar() {
        document.getElementById('sidebar').classList.toggle('open');
    }
</script>

</body>
</html>