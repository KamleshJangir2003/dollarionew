<?php include "../includes/db.php"; ?>
<?php include '../templates/sidebar.php'; ?>
<?php include '../templates/header.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>USDT Deposits - Admin Panel</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<style>
    * { box-sizing: border-box; }
    html, body { overflow-x: hidden; }
    .container {
        margin-left: 260px;
        max-width: calc(100vw - 260px);
    }
    @media (max-width: 767px) {
        .container { margin-left: 0; max-width: 100%; padding: 12px; }
    }
</style>
<body>
<div class="container mt-5">
    <h2 class="mb-4">USDT Deposits</h2>
    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>#</th>
                <th>Tx Hash</th>
                <th>Wallet Address</th>
                <th>Amount</th>
                <th>Confirmations</th>
                <th>Time</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $result = $conn->query("SELECT * FROM usdt_deposits WHERE confirmations >= 3 ORDER BY timestamp DESC");
            if ($result->num_rows > 0) {
                $count = 1;
                while($row = $result->fetch_assoc()) {
                    echo "<tr>
                        <td>{$count}</td>
                        <td>{$row['tx_hash']}</td>
                        <td>{$row['wallet_address']}</td>
                        <td>{$row['amount']}</td>
                        <td>{$row['confirmations']}</td>
                        <td>{$row['timestamp']}</td>
                    </tr>";
                    $count++;
                }
            } else {
                echo "<tr><td colspan='6' class='text-center'>No confirmed deposits found.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>
</body>
</html>
