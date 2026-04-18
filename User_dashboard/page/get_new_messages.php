<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require '../config/db.php';
if (!isset($_SESSION['user_id'])) { http_response_code(403); exit; }

$userId  = $_SESSION['user_id'];
$afterId = intval($_GET['after_id'] ?? 0);

$stmt = $pdo->prepare("SELECT id, sender, message, admin_reply, reply_to_id, created_at FROM help_requests WHERE user_id = ? AND id > ? ORDER BY created_at ASC");
$stmt->execute([$userId, $afterId]);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

header('Content-Type: application/json');
echo json_encode($rows);
