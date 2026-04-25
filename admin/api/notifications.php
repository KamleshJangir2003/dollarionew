<?php
if (session_status() === PHP_SESSION_NONE) { session_name('admin_session'); session_start(); }
if (empty($_SESSION['admin_logged_in'])) { http_response_code(403); exit; }

require_once __DIR__ . '/../includes/config.php';

header('Content-Type: application/json');

$action = $_GET['action'] ?? 'list';

if ($action === 'count') {
    $stmt = $pdo->query("SELECT COUNT(*) FROM admin_notifications WHERE is_read = 0");
    echo json_encode(['count' => (int)$stmt->fetchColumn()]);
    exit;
}

if ($action === 'mark_read') {
    $pdo->exec("UPDATE admin_notifications SET is_read = 1 WHERE is_read = 0");
    echo json_encode(['ok' => true]);
    exit;
}

// list — only unread, latest 10
$rows = $pdo->query("SELECT id, title, message, type, is_read, created_at FROM admin_notifications WHERE is_read = 0 ORDER BY created_at DESC LIMIT 10")->fetchAll();
echo json_encode($rows);
