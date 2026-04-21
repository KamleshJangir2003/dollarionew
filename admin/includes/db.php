<?php
$host     = 'localhost';
$dbname   = 'u621774021_mbpay';
$username = 'u621774021_pay';
$password = 'Mbpay999';

$conn = new mysqli($host, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
