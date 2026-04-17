<?php
session_start();
require_once __DIR__ . '/../config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['user_id'])) {
    header("Location: ../page/profile.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$name    = trim($_POST['name'] ?? '');
$email   = trim($_POST['email'] ?? '');
$phone   = trim($_POST['phone'] ?? '');

$stmt  = $pdo->prepare("UPDATE users SET username = ?, email = ?, mobile = ? WHERE id = ?");
$result = $stmt->execute([$name, $email, $phone, $user_id]);

if ($result) {
    $_SESSION['username'] = $name;
    $_SESSION['email']    = $email;
    $_SESSION['mobile']   = $phone;
    header("Location: ../page/profile.php?success=profile_updated");
} else {
    header("Location: ../page/profile.php?error=update_failed");
}
exit;
?>
