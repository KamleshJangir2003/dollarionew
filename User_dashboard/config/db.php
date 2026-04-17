<?php
$host     = 'localhost';
$dbname   = 'u621774021_dollario';
$username = 'u621774021_dollario';
$password = 'Copy@75970';

// PDO connection
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database Connection Failed: " . $e->getMessage());
}

// MySQLi connection (for pages that use $conn)
$conn = new mysqli($host, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
