<?php
require 'User_dashboard/config/db.php';

$queries = [
    "CREATE TABLE IF NOT EXISTS `user_transactions` (
        `id`             int(11)       NOT NULL AUTO_INCREMENT,
        `user_id`        int(11)       NOT NULL,
        `type`           varchar(30)   NOT NULL,
        `amount`         decimal(15,2) NOT NULL,
        `currency`       varchar(10)   NOT NULL DEFAULT 'INR',
        `description`    text          DEFAULT NULL,
        `status`         varchar(20)   NOT NULL DEFAULT 'pending',
        `chain`          varchar(50)   DEFAULT NULL,
        `wallet_address` varchar(100)  DEFAULT NULL,
        `created_at`     datetime      NOT NULL DEFAULT current_timestamp(),
        PRIMARY KEY (`id`),
        KEY `user_id` (`user_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

    "CREATE TABLE IF NOT EXISTS `inr_deposits` (
        `id`          int(11)       NOT NULL AUTO_INCREMENT,
        `user_id`     int(11)       NOT NULL,
        `amount`      decimal(15,2) NOT NULL,
        `method`      varchar(50)   DEFAULT NULL,
        `utr_number`  varchar(100)  DEFAULT NULL,
        `bank_id`     int(11)       DEFAULT NULL,
        `status`      enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
        `approved_at` datetime      DEFAULT NULL,
        `created_at`  datetime      NOT NULL DEFAULT current_timestamp(),
        PRIMARY KEY (`id`),
        KEY `user_id` (`user_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
];

$success = [];
$errors  = [];

foreach ($queries as $sql) {
    try {
        $pdo->exec($sql);
        preg_match('/`(\w+)`/', $sql, $m);
        $success[] = "✅ Table `{$m[1]}` created (or already exists).";
    } catch (PDOException $e) {
        $errors[] = "❌ Error: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>DB Setup</title>
  <style>
    body { font-family: sans-serif; padding: 40px; background: #f8fafc; }
    .box { background: #fff; border-radius: 12px; padding: 30px; max-width: 500px; box-shadow: 0 4px 12px rgba(0,0,0,0.08); }
    h2 { margin-bottom: 20px; color: #1e293b; }
    p { margin: 8px 0; font-size: 15px; }
    .ok { color: #16a34a; }
    .err { color: #dc2626; }
    a { display: inline-block; margin-top: 20px; background: #6366f1; color: #fff; padding: 10px 20px; border-radius: 8px; text-decoration: none; }
  </style>
</head>
<body>
  <div class="box">
    <h2>🛠️ Database Setup</h2>
    <?php foreach ($success as $s): ?><p class="ok"><?= $s ?></p><?php endforeach; ?>
    <?php foreach ($errors  as $e): ?><p class="err"><?= $e ?></p><?php endforeach; ?>
    <?php if (empty($errors)): ?>
      <p style="margin-top:16px; color:#64748b;">All tables ready! You can delete this file now.</p>
      <a href="User_dashboard/page/dashboard.php">Go to Dashboard →</a>
    <?php endif; ?>
  </div>
</body>
</html>
