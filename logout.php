<?php
session_start();

// Check if the user is logged in
if (isset($_SESSION['username'])) {
    $username = $_SESSION['username'];

    // Database connection
    include 'config.php';

    // Log logout action
    $stmt = $conn->prepare("INSERT INTO user_log (username, action) VALUES (?, 'logout')");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->close();
}

// Destroy session to log out the user
session_destroy();
header("Location: index.php"); // Redirect to login page
exit();

?>
