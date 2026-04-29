<?php
$host = 'localhost';
$user = 'root'; // Default user untuk localhost
$password = ''; // Biarkan kosong jika tidak ada password
$database = 'fyp_db'; // Nama database yang kamu buat

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}
?>
