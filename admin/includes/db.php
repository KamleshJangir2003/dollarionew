<?php
$host = 'localhost';
$dbname = 'u973762102_admin';
$username = 'root';
$password = '';// Your database name

// Create connection
$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
