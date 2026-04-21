<?php
$servername = 'localhost';
$username   = 'u621774021_pay';
$password   = 'Mbpay999';
$dbname     = 'u621774021_mbpay';

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
