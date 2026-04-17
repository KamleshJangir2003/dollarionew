<?php
include '../includes/db.php';
include '../templates/sidebar.php';
include '../templates/header.php';

// Filters
$type   = $_GET['type']   ?? '';
$status = $_GET['status'] ?? '';
$from   = $_GET['from']   ?? '';
$to     = $_GET['to']     ?? '';

$where = "WHERE 1=1";
if ($type)   $where .= " AND t.type = '" . $conn->real_escape_string($type) . "'";
if ($status) $where .= " AND t.status = '" . $conn->real_escape_string($status) . "'";
if ($from)   $where .= " AND DATE(t.created_at) >= '" . $conn->real_escape_string($from) . "'";
if ($to)     $where .= " AND DATE(t.created_at) <= '" . $conn->real_escape_string($to) . "'";

$page    = max(1, intval($_GET['p'] ?? 1));
$perPage = 15;
$offset  = ($page - 1) * $perPage;

$totalRes = $conn->query("SELECT COUNT(*) as c FROM user_transactions t $where");
$total    = $totalRes ? $totalRes->fetch_assoc()['c'] : 0;
$pages    = ceil($total / $perPage);

$sql = "SELECT t.*, u.username, u.email
        FROM user_transactions t
        LEFT JOIN users u ON t.user_id = u.id
        $where
        ORDER BY t.created_at DESC
        LIMIT $offset, $perPage";
$result = $conn->query($sql);

// Summary stats
$stats = $conn->query("SELECT
    SUM(CASE WHEN type='deposit' AND status='completed' THEN amount ELSE 0 END) as total_deposits,
    SUM(CASE WHEN type='withdraw_inr' AND status='completed' THEN amount ELSE 0 END) as total_withdrawals,
    SUM(CASE WHEN type='sell' AND status='completed' THEN amount ELSE 0 END) as total_sells,
    COUNT(*) as total_txns
    FROM user_transactions")->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Transaction Reports - Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    .wrap { margin-left: 260px; padding: 30px; }
    @media (max-width: 767px) { .wrap { margin-left: 0; padding: 15px; } .header { margin-left: 0; } }
    .stat-card { border-radius: 12px; padding: 20px; color: #fff; }
    .badge-completed { background: #d1e7dd; color: #0f5132; }
    .badge-pending   { background: #fff3cd; color: #856404; }
    .badge-rejected  { background: #f8d7da; color: #842029; }
  </style>
</head>
<body>
<div class="wrap">
  <h2 class="mb-4">📊 Transaction Reports</h2>

  <!-- Summary Cards -->
  <div class="row mb-4">
    <div class="col-md-3">
      <div class="stat-card" style="background:#0d6efd;">
        <div style="font-size:0.85rem; opacity:0.85;">Total Transactions</div>
        <div style="font-size:1.8rem; font-weight:700;"><?= number_format($stats['total_txns']) ?></div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="stat-card" style="background:#198754;">
        <div style="font-size:0.85rem; opacity:0.85;">Total Deposits (INR)</div>
        <div style="font-size:1.8rem; font-weight:700;">₹<?= number_format($stats['total_deposits'], 2) ?></div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="stat-card" style="background:#dc3545;">
        <div style="font-size:0.85rem; opacity:0.85;">Total Withdrawals (INR)</div>
        <div style="font-size:1.8rem; font-weight:700;">₹<?= number_format($stats['total_withdrawals'], 2) ?></div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="stat-card" style="background:#fd7e14;">
        <div style="font-size:0.85rem; opacity:0.85;">Total USDT Sold</div>
        <div style="font-size:1.8rem; font-weight:700;"><?= number_format($stats['total_sells'], 2) ?> USDT</div>
      </div>
    </div>
  </div>

  <!-- Filters -->
  <form method="GET" class="row g-2 mb-4">
    <div class="col-md-2">
      <select name="type" class="form-select form-select-sm">
        <option value="">All Types</option>
        <option value="deposit"      <?= $type==='deposit'      ?'selected':'' ?>>Deposit INR</option>
        <option value="withdraw_inr" <?= $type==='withdraw_inr' ?'selected':'' ?>>Withdraw INR</option>
        <option value="sell"         <?= $type==='sell'         ?'selected':'' ?>>Sell USDT</option>
        <option value="withdraw"     <?= $type==='withdraw'     ?'selected':'' ?>>Withdraw USDT</option>
      </select>
    </div>
    <div class="col-md-2">
      <select name="status" class="form-select form-select-sm">
        <option value="">All Status</option>
        <option value="pending"   <?= $status==='pending'   ?'selected':'' ?>>Pending</option>
        <option value="completed" <?= $status==='completed' ?'selected':'' ?>>Completed</option>
        <option value="rejected"  <?= $status==='rejected'  ?'selected':'' ?>>Rejected</option>
      </select>
    </div>
    <div class="col-md-2">
      <input type="date" name="from" value="<?= htmlspecialchars($from) ?>" class="form-control form-control-sm" placeholder="From">
    </div>
    <div class="col-md-2">
      <input type="date" name="to" value="<?= htmlspecialchars($to) ?>" class="form-control form-control-sm" placeholder="To">
    </div>
    <div class="col-md-2">
      <button type="submit" class="btn btn-primary btn-sm w-100">🔍 Filter</button>
    </div>
    <div class="col-md-2">
      <a href="transaction_reports.php" class="btn btn-outline-secondary btn-sm w-100">Reset</a>
    </div>
  </form>

  <p class="text-muted mb-2">Showing <?= $total ?> records</p>

  <div class="table-responsive">
    <table class="table table-bordered table-striped table-hover">
      <thead class="table-dark">
        <tr>
          <th>#</th>
          <th>User</th>
          <th>Type</th>
          <th>Amount</th>
          <th>Currency</th>
          <th>Description</th>
          <th>Status</th>
          <th>Date</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $i = $offset + 1;
        if ($result && $result->num_rows > 0):
          while ($row = $result->fetch_assoc()):
            $badgeClass = $row['status'] === 'completed' ? 'badge-completed' : ($row['status'] === 'pending' ? 'badge-pending' : 'badge-rejected');
            $typeLabel = [
              'deposit'      => '⬇️ Deposit INR',
              'withdraw_inr' => '⬆️ Withdraw INR',
              'sell'         => '💱 Sell USDT',
              'withdraw'     => '🔼 Withdraw USDT',
            ][$row['type']] ?? $row['type'];
        ?>
        <tr>
          <td><?= $i++ ?></td>
          <td>
            <strong><?= htmlspecialchars($row['username'] ?? 'User #'.$row['user_id']) ?></strong><br>
            <small class="text-muted"><?= htmlspecialchars($row['email'] ?? '') ?></small>
          </td>
          <td><?= $typeLabel ?></td>
          <td><strong><?= $row['currency'] === 'INR' ? '₹' : '' ?><?= number_format($row['amount'], 2) ?><?= $row['currency'] === 'USDT' ? ' USDT' : '' ?></strong></td>
          <td><?= htmlspecialchars($row['currency']) ?></td>
          <td style="max-width:220px; font-size:0.82rem;"><?= htmlspecialchars($row['description'] ?? '') ?></td>
          <td><span class="badge <?= $badgeClass ?> px-2 py-1"><?= ucfirst($row['status'] ?? 'pending') ?></span></td>
          <td><?= date('d M Y, h:i A', strtotime($row['created_at'])) ?></td>
        </tr>
        <?php endwhile; else: ?>
        <tr><td colspan="8" class="text-center text-muted">No transactions found.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  <!-- Pagination -->
  <?php if ($pages > 1): ?>
  <nav>
    <ul class="pagination pagination-sm">
      <?php for ($pg = 1; $pg <= $pages; $pg++): ?>
        <li class="page-item <?= $pg == $page ? 'active' : '' ?>">
          <a class="page-link" href="?p=<?= $pg ?>&type=<?= urlencode($type) ?>&status=<?= urlencode($status) ?>&from=<?= urlencode($from) ?>&to=<?= urlencode($to) ?>"><?= $pg ?></a>
        </li>
      <?php endfor; ?>
    </ul>
  </nav>
  <?php endif; ?>
</div>
</body>
</html>
