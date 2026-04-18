<?php
session_start();
require_once __DIR__ . '/../config/db.php';

if (!isset($_SESSION['user_id'])) { header("Location: ../auth/login.php"); exit; }
$userId = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bank_id'])) {
    $bankId = intval($_POST['bank_id']);

    // Reset all to not primary
    $pdo->prepare("UPDATE bank_accounts SET is_primary = 0 WHERE user_id = ?")->execute([$userId]);

    // Set selected as primary
    $pdo->prepare("UPDATE bank_accounts SET is_primary = 1 WHERE id = ? AND user_id = ?")->execute([$bankId, $userId]);

    header("Location: ../page/profile.php?success=primary_updated");
    exit;
}

header("Location: ../page/profile.php");
exit;
?>
