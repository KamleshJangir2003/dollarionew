<?php
if (session_status() === PHP_SESSION_NONE) {
    session_name('user_session');
    session_start();
}
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}
?>
