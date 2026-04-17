<?php
$servername = "localhost";
$username = "u621774021_dollario";
$password = "Copy@75970";
$dbname = "u621774021_dollario";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
