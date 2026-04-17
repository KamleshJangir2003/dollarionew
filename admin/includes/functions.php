<?php
// Database connection function
function dbConnect() {
    //$servername = "localhost";
    //$username = "root";
    //$password = "";
    //$dbname = "dollario_admin";  // Aapka database ka naam
 $host = 'localhost';
$dbname = 'u973762102_adming';
$username = 'u973762102_dollario12';
$password = 'Dollari@98';

    // Create connection
    $conn = new mysqli($host, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    return $conn;
}



?>
