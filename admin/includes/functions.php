<?php
function dbConnect() {
    $conn = new mysqli('localhost', 'u621774021_pay', 'Mbpay999', 'u621774021_mbpay');
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    return $conn;
}
?>

