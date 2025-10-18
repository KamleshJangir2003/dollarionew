<?php
session_start();
include '../../config/db.php'; // ✅ Path check kare

$userId = $_SESSION['user_id'] ?? 0;
$message = '';

if (!$userId) {
    die("User not logged in!");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $amount = floatval($_POST['amount']);
    $walletAddress = htmlspecialchars($_POST['wallet']);
    $chain = htmlspecialchars($_POST['chain']);

    // ✅ Check current USDT balance from wallets table
    $stmt = $pdo->prepare("SELECT usdt_balance FROM wallets WHERE user_id = ?");
    $stmt->execute([$userId]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $currentBalance = floatval($result['usdt_balance'] ?? 0);

    if ($amount <= 0) {
        $message = "<div style='color:red; text-align:center;'>❌ Invalid amount!</div>";
    } elseif ($amount > $currentBalance) {
        $message = "<div style='color:red; text-align:center;'>❌ Insufficient USDT balance!</div>";
    } else {
        // Deduct USDT
        $newBalance = $currentBalance - $amount;
        $stmt = $pdo->prepare("UPDATE wallets SET usdt_balance = ? WHERE user_id = ?");
        $stmt->execute([$newBalance, $userId]);

        // Insert transaction
        $stmt = $pdo->prepare("INSERT INTO user_transactions (user_id, type, amount, currency, chain, wallet_address, description, created_at) VALUES (?, 'withdraw', ?, 'USDT', ?, ?, 'USDT Withdrawal', NOW())");
        $stmt->execute([$userId, $amount, $chain, $walletAddress]);

        // Success message
        $message = "
        <div style='
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            background: #e6ffe6;
            color: #006600;
            padding: 12px 18px;
            border-radius: 8px;
            border: 1px solid #00cc66;
            font-size: 15px;
            line-height: 1.5;
            text-align: center;
            box-shadow: 0 2px 8px rgba(0,0,0,0.15);
            z-index: 9999;
        '>
        ✅ <b>USDT Withdrawal of $amount USDT</b> via <b>$chain</b> network has been initiated.<br>
        ⏳ Your withdrawal will be processed and credited within <b>24 hours</b>.
        </div>
        ";
    }
}
?>







<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Withdraw USDT</title>
  <link rel="stylesheet" href="usdt.css">
  <style>
    body {
      font-family: "Segoe UI", Arial, sans-serif;
      background: #f1f3f6;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      margin: 0;
    }

    .withdraw-box {
      background: #fff;
      padding: 30px;
      border-radius: 12px;
      box-shadow: 0 4px 14px rgba(0,0,0,0.1);
      width: 380px;
    }

    h2 {
      text-align: center;
      color: #222;
      margin-bottom: 10px;
    }

    p.subtitle {
      text-align: center;
      font-size: 14px;
      color: #666;
      margin-bottom: 20px;
    }

    label {
      font-weight: 600;
      display: block;
      margin-top: 12px;
      color: #333;
    }

    input, select {
      width: 100%;
      padding: 9px;
      margin-top: 5px;
      border: 1px solid #ccc;
      border-radius: 6px;
      font-size: 14px;
      outline: none;
      transition: border-color 0.3s;
    }

    input:focus, select:focus {
      border-color: #007bff;
    }

    button {
      width: 100%;
      padding: 10px;
      margin-top: 20px;
      background-color: #007bff;
      color: white;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      font-size: 16px;
      font-weight: bold;
      transition: background-color 0.3s;
    }

    button:hover {
      background-color: #0056b3;
    }

    .note {
      font-size: 12px;
      color: #888;
      margin-top: 8px;
      text-align: center;
    }
  </style>
</head>
<body>
  <?= $message ?> <!-- ✅ Message top me show hoga -->

  <div class="withdraw-box">
    <h2>USDT Withdrawal</h2>
    <p class="subtitle">Withdraw your USDT securely to your crypto wallet.</p>

    <form method="POST">
      <label>Amount (USDT):</label>
      <input type="number" name="amount" min="1" step="0.01" placeholder="Enter amount" required>

      <label>Wallet Address:</label>
      <input type="text" name="wallet" placeholder="Enter your USDT wallet address" required>

      <label>Select Blockchain Network:</label>
      <select name="chain" required>
        <option value="">-- Select Network --</option>
        <option value="TRON (TRC20)">TRON (TRC20)</option>
        <option value="BNB Smart Chain (BEP20)">BNB Smart Chain (BEP20)</option>
        <option value="Ethereum (ERC20)">Ethereum (ERC20)</option>
        <option value="Solana (SOL)">Solana (SOL)</option>
      </select>

      <button type="submit">Withdraw Now</button>

      <p class="note">⚠️ Please double-check your wallet address. Withdrawals cannot be reversed once initiated.</p>
    </form>
  </div>

  <script>
    // Auto-hide success message after 5 seconds
    setTimeout(() => {
      const msg = document.querySelector('div[style*="position: fixed"]');
      if (msg) msg.style.display = 'none';
    }, 5000);
  </script>
</body>
</html>
