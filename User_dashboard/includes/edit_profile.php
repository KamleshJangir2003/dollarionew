<?php
session_start();  // session start karna zaroori hai

require_once __DIR__ . '/../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Session variable check
    if (!isset($_SESSION['user_id'])) {
        die("User not logged in.");
    }

    $user_id = $_SESSION['user_id'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];

    // PDO prepared statement
   $stmt = $pdo->prepare("UPDATE users SET username = ?, email = ?, mobile = ? WHERE id = ?");
 $result = $stmt->execute([$name, $email, $phone, $user_id]);

    if ($result) {
        header("Location: ../profile.php?success=profile_updated");
        exit;
    } else {
        header("Location: ../profile.php?error=update_failed");
        exit;
    }
}
?>
