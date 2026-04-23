<?php
if (session_status() === PHP_SESSION_NONE) { session_name('admin_session'); session_start(); }
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    http_response_code(403); exit();
}
require_once '../includes/db.php';

header('Content-Type: application/json');

$q = trim($_GET['q'] ?? '');
if (strlen($q) < 2) { echo json_encode([]); exit(); }

$like = '%' . $conn->real_escape_string($q) . '%';

$sql = "SELECT t.id, t.amount, t.currency, t.type, t.status, t.created_at, u.username
        FROM user_transactions t
        LEFT JOIN users u ON t.user_id = u.id
        WHERE t.id LIKE '$like'
           OR u.username LIKE '$like'
           OR t.type LIKE '$like'
           OR t.status LIKE '$like'
           OR t.amount LIKE '$like'
        ORDER BY t.created_at DESC
        LIMIT 10";

$result = $conn->query($sql);
$rows = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $rows[] = [
            'id'       => $row['id'],
            'txn_id'   => '#TXN' . str_pad($row['id'], 5, '0', STR_PAD_LEFT),
            'username' => $row['username'] ?? 'User #' . $row['id'],
            'amount'   => ($row['currency'] === 'INR' ? '₹' : '') . number_format($row['amount'], 2) . ($row['currency'] === 'USDT' ? ' USDT' : ''),
            'type'     => ucfirst($row['type']),
            'status'   => ucfirst($row['status'] ?? 'pending'),
            'date'     => date('d M, h:i A', strtotime($row['created_at'])),
        ];
    }
}
echo json_encode($rows);
