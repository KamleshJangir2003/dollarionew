<?php
if (session_status() === PHP_SESSION_NONE) session_start();
include '../includes/db.php';

$activeUserId = intval($_GET['user_id'] ?? 0);
$afterId      = intval($_GET['after_id'] ?? 0);

$newMsgs = [];
if ($activeUserId && $afterId) {
    $r = $conn->query("SELECT id, sender, message, admin_reply, reply_to_id, created_at FROM help_requests WHERE user_id=$activeUserId AND id > $afterId ORDER BY created_at ASC");
    while ($row = $r->fetch_assoc()) $newMsgs[] = $row;
}

// Unread counts per user (for sidebar badges)
$unreadMap = [];
$ur = $conn->query("SELECT user_id, COUNT(*) as cnt FROM help_requests WHERE sender='user' AND admin_reply IS NULL GROUP BY user_id");
while ($row = $ur->fetch_assoc()) $unreadMap[$row['user_id']] = $row['cnt'];

header('Content-Type: application/json');
echo json_encode(['messages' => $newMsgs, 'unread' => $unreadMap]);
