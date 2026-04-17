<?php include('../sidebar.php'); ?>
<?php include('submit_help.php'); ?>
<?php
require '../config/db.php';

$userId = $_SESSION['user_id'] ?? 0;
if (!$userId) {
    header('Location: ../auth/login.php');
    exit;
}

// Pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$records_per_page = 10;
$start = ($page - 1) * $records_per_page;

// Total count for this user only
$countStmt = $pdo->prepare("SELECT COUNT(*) FROM user_transactions WHERE user_id = ?");
$countStmt->execute([$userId]);
$totalTransactions = $countStmt->fetchColumn();

// Fetch paginated records for this user only
$stmt = $pdo->prepare("SELECT * FROM user_transactions WHERE user_id = ? ORDER BY created_at DESC LIMIT :start, :limit");
$stmt->bindValue(':start', $start, PDO::PARAM_INT);
$stmt->bindValue(':limit', $records_per_page, PDO::PARAM_INT);
$stmt->bindValue(1, $userId, PDO::PARAM_INT);
$stmt->execute();
$transactions = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Transaction History</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Inter', sans-serif;
      background: #f1f5f9;
      margin: 0;
      padding: 0;
    }

    .container {
      background: #ffffff;
      padding: 30px;
      border-radius: 12px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.05);
      margin-left: 260px;
    }

    .page-header {
      font-size: 26px;
      font-weight: 600;
      margin-bottom: 10px;
      color: #111827;
    }

    .total-count {
      font-size: 16px;
      margin-bottom: 25px;
      color: #374151;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 30px;
    }

    table thead {
      background-color: #f3f4f6;
    }

    table th, table td {
      padding: 14px 16px;
      border-bottom: 1px solid #e5e7eb;
      text-align: left;
      font-size: 14px;
      color: #374151;
    }

    table tbody tr:hover {
      background-color: #f9fafb;
    }

    .pagination {
      text-align: center;
    }

    .pagination a {
      margin: 0 4px;
      padding: 8px 14px;
      font-size: 14px;
      text-decoration: none;
      color: #374151;
      background-color: #e5e7eb;
      border-radius: 6px;
      transition: background-color 0.3s, color 0.3s;
    }

    .pagination a:hover {
      background-color: #d1d5db;
    }

    .pagination a.active {
      background-color: #2563eb;
      color: #fff;
    }

    @media (max-width: 768px) {
      .container {
        margin-left: 0;
        margin: 20px;
        padding: 20px;
      }

      table th, table td {
        font-size: 13px;
        padding: 10px;
      }

      .sidebar {
        display: none;
      }
    }
  </style>
</head>
<body>
<header>
  <div class="logo-container">
       <img src="../image/Dollario-logo .svg" alt="" style="height: auto; width: 150px;">
  </div>
  <div class="menu-container">
    <button class="menu-btn" id="menuToggle">☰</button>
  </div>
</header>
<script>
  document.addEventListener('DOMContentLoaded', function () {
    const menuBtn = document.getElementById('menuToggle');
    const sidebar = document.querySelector('.sidebar');
    menuBtn.addEventListener('click', function () {
      sidebar.classList.toggle('active');
    });
  });
</script>
<style>
header {
  display: none;
}
@media (max-width: 768px) {
  header {
    display: flex;
    flex-direction: row;
    justify-content: space-between;
    align-items: center;
    padding: 10px 20px;
    background-color:#0e1a2b;
    color: white;
  }

  .logo-container {
    flex: 1;
    text-align: left;
  }

  .menu-container {
    display: flex;
    justify-content: flex-end;
  }

  .menu-btn {
    background: none;
    border: none;
    color: white;
    font-size: 30px;
    cursor: pointer;
  }
}
</style>

<div class="container">
  <div class="page-header">📄 Transaction History</div>

  <div class="total-count">
    Total Transactions: <strong><?= $totalTransactions ?></strong>
  </div>

  <table>
    <thead>
      <tr>
        <th>ID</th>
        <th>Type</th>
        <th>Amount</th>
        <th>Currency</th>
        <th>Description</th>
        <th>Status</th>
        <th>Date</th>
      </tr>
    </thead>
    <tbody>
      <?php if ($transactions): ?>
        <?php foreach ($transactions as $row):
          $typeLabels = [
            'sell'         => '💱 Sell USDT',
            'deposit'      => '⬇️ Deposit INR',
            'withdraw_inr' => '⬆️ Withdraw INR',
            'withdraw'     => '🔼 Withdraw USDT',
          ];
          $typeLabel = $typeLabels[$row['type']] ?? ucfirst($row['type']);
          $statusColor = $row['status'] === 'completed' ? '#16a34a' : ($row['status'] === 'rejected' ? '#dc2626' : '#d97706');
          $amtPrefix = in_array($row['type'], ['deposit', 'sell']) ? '+' : '-';
          $amtColor  = in_array($row['type'], ['deposit', 'sell']) ? '#16a34a' : '#dc2626';
        ?>
          <tr>
            <td>#<?= htmlspecialchars($row['id']) ?></td>
            <td><?= $typeLabel ?></td>
            <td style="color:<?= $amtColor ?>; font-weight:600;">
              <?= $amtPrefix ?><?= $row['currency'] === 'INR' ? '₹' : '' ?><?= number_format($row['amount'], 2) ?><?= $row['currency'] === 'USDT' ? ' USDT' : '' ?>
            </td>
            <td><?= htmlspecialchars($row['currency']) ?></td>
            <td style="font-size:13px; color:#64748b;"><?= htmlspecialchars($row['description'] ?? '') ?></td>
            <td><span style="background:<?= $statusColor ?>20; color:<?= $statusColor ?>; padding:3px 10px; border-radius:20px; font-size:12px; font-weight:600;"><?= ucfirst($row['status'] ?? 'pending') ?></span></td>
            <td><?= date('d M Y, h:i A', strtotime($row['created_at'])) ?></td>
          </tr>
        <?php endforeach; ?>
      <?php else: ?>
        <tr><td colspan="7" style="text-align:center;">No transactions found.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>

  <div class="pagination">
    <?php
      $total_pages = ceil($totalTransactions / $records_per_page);
      for ($i = 1; $i <= $total_pages; $i++):
    ?>
      <a href="?page=<?= $i ?>" class="<?= ($i == $page) ? 'active' : '' ?>"><?= $i ?></a>
    <?php endfor; ?>
  </div>
</div>

</body>
</html>
