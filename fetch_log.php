<?php
// Start session and include database connection
session_start();
require 'config.php';

// Check if session is still valid
if (!isset($_SESSION['username']) || $_SESSION['username'] !== 'admin') {
    echo json_encode(['status' => 'session_expired', 'message' => 'Session expired. Please log in again.']);
    exit();
}

// Fetch latest user logs
$query = "SELECT username, action, timestamp FROM user_log ORDER BY timestamp DESC";
$result = $conn->query($query);

if ($result && $result->num_rows > 0) {
    $rows = [];
    while ($row = $result->fetch_assoc()) {
        $rows[] = $row;
    }
    echo json_encode(['status' => 'success', 'data' => $rows]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'No data found or query error.']);
}
?>
