<?php
function addAdminNotif($pdo, $title, $message, $type = 'general', $refId = null) {
    try {
        $pdo->prepare("INSERT INTO admin_notifications (title, message, type, ref_id, is_read, created_at) VALUES (?, ?, ?, ?, 0, NOW())")
            ->execute([$title, $message, $type, $refId]);
    } catch (Exception $e) {
        // If ref_id column missing, insert without it
        try {
            $pdo->prepare("INSERT INTO admin_notifications (title, message, type, is_read, created_at) VALUES (?, ?, ?, 0, NOW())")
                ->execute([$title, $message, $type]);
        } catch (Exception $e2) { /* silent */ }
    }
}
