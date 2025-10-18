<?php
// Database connection function
function dbConnect() {
    //$servername = "localhost";
    //$username = "root";
    //$password = "";
    //$dbname = "dollario_admin";  // Aapka database ka naam
 $host = 'localhost';
$dbname = 'u973762102_admin';
$username = 'root';
$password = '';

    // Create connection
    $conn = new mysqli($host, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    return $conn;
}



?>
