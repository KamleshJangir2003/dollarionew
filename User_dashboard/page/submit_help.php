<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'] ?? null;
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if (!$user_id || !$subject || !$message) {
        echo "<script>alert('Please fill all the fields.'); window.history.back();</script>";
        exit;
    }

    $pdo->prepare("INSERT INTO help_requests (user_id, subject, message, status, created_at) VALUES (?, ?, ?, 'Pending', NOW())")
        ->execute([$user_id, $subject, $message]);

    header("Location: dashboard.php?help_sent=1");
    exit;
}
?>
