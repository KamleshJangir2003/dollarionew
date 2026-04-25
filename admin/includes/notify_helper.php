<?php
// Insert a notification into admin_notifications table
// Uses PDO ($pdo) - pass the PDO connection
function addAdminNotification($pdo, $title, $message, $type = 'user_alert') {
    try {
        $pdo->exec("CREATE TABLE IF NOT EXISTS `admin_notifications` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `title` varchar(255) NOT NULL,
            `message` text NOT NULL,
            `type` varchar(50) NOT NULL DEFAULT 'user_alert',
            `is_read` tinyint(1) NOT NULL DEFAULT 0,
            `created_at` datetime NOT NULL DEFAULT current_timestamp(),
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        $stmt = $pdo->prepare("INSERT INTO admin_notifications (title, message, type, is_read, created_at) VALUES (?, ?, ?, 0, NOW())");
        $stmt->execute([$title, $message, $type]);
    } catch (Exception $e) { /* silent fail */ }
}
