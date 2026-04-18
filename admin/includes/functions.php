<?php
function dbConnect() {
    $conn = new mysqli('localhost', 'root', '', 'dollario_admin');
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    return $conn;
}
?>
