<?php
session_start();
require_once __DIR__ . '/../config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['user_id'])) {
    header("Location: ../page/profile.php");
    exit;
}

$user_id         = $_SESSION['user_id'];
$old_password    = $_POST['old_password'] ?? '';
$new_password    = $_POST['new_password'] ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';

if ($new_password !== $confirm_password) {
    header("Location: ../page/profile.php?error=password_mismatch");
    exit;
}

$stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user || !password_verify($old_password, $user['password'])) {
    header("Location: ../page/profile.php?error=incorrect_password");
    exit;
}

$new_hashed = password_hash($new_password, PASSWORD_DEFAULT);
$stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");

if ($stmt->execute([$new_hashed, $user_id])) {
    header("Location: ../page/profile.php?success=password_changed");
} else {
    header("Location: ../page/profile.php?error=update_failed");
}
exit;
?>
