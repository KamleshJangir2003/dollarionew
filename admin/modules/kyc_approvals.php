<?php
if (session_status() === PHP_SESSION_NONE) session_start();
include '../includes/db.php';

// Handle approve / reject
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'], $_POST['kyc_id'])) {
    $kycId  = intval($_POST['kyc_id']);
    $userId = intval($_POST['user_id']);
    $action = $_POST['action'];

    if ($action === 'approve') {
        $conn->query("UPDATE user_kyc SET status='approved', rejection_reason=NULL WHERE id=$kycId");
        $conn->query("UPDATE kyc_verifications SET status='approved' WHERE user_id=$userId");
        $success = "KYC approved successfully.";
    } elseif ($action === 'reject') {
        $reason = $conn->real_escape_string($_POST['reason'] ?? 'Documents not clear');
        $conn->query("UPDATE user_kyc SET status='rejected', rejection_reason='$reason' WHERE id=$kycId");
        $conn->query("UPDATE kyc_verifications SET status='rejected' WHERE user_id=$userId");
        $success = "KYC rejected.";
    }
}

include '../templates/sidebar.php';
include '../templates/header.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>KYC Approvals - Admin</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <style>
    * { box-sizing:border-box; }
    body { background:#f3f4f6; }
    .container { margin-left:260px; max-width:calc(100vw - 260px); }
    @media(max-width:767px) { .container { margin-left:0; max-width:100%; padding:12px; } }
    .doc-thumb { width:80px; height:60px; object-fit:cover; border-radius:6px; border:1px solid #e2e8f0; cursor:pointer; }
    .doc-link { font-size:0.78rem; color:#6366f1; text-decoration:none; display:block; margin-top:4px; }
    .badge-pending  { background:#fef9c3; color:#92400e; padding:4px 10px; border-radius:20px; font-size:0.75rem; font-weight:700; }
    .badge-approved { background:#dcfce7; color:#166534; padding:4px 10px; border-radius:20px; font-size:0.75rem; font-weight:700; }
    .badge-rejected { background:#fee2e2; color:#991b1b; padding:4px 10px; border-radius:20px; font-size:0.75rem; font-weight:700; }
    /* Modal */
    .modal-overlay { display:none; position:fixed; inset:0; background:rgba(0,0,0,0.7); z-index:9999; align-items:center; justify-content:center; }
    .modal-overlay.open { display:flex; }
    .modal-box { background:#fff; border-radius:14px; padding:24px; max-width:500px; width:90%; }
    .modal-box img { width:100%; border-radius:8px; margin-bottom:10px; }
  </style>
</head>
<body>
<div class="container mt-4">
  <h4 class="mb-4 fw-bold">KYC Approvals</h4>

  <?php if (!empty($success)): ?>
    <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
  <?php endif; ?>

  <div class="card shadow-sm">
    <div class="card-body p-0">
      <table class="table table-hover align-middle mb-0">
        <thead class="table-dark">
          <tr>
            <th>#</th>
            <th>User</th>
            <th>PAN Card</th>
            <th>Aadhaar</th>
            <th>Bank Statement</th>
            <th>Status</th>
            <th>Submitted</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $result = $conn->query("SELECT k.*, u.username, u.email FROM user_kyc k LEFT JOIN users u ON u.id = k.user_id ORDER BY k.updated_at DESC");
          $i = 1;
          while ($row = $result->fetch_assoc()):
            $base = 'http://localhost/dollario-new/User_dashboard/';
            // Fix any remaining double slashes in path
            $row['pan_card']       = preg_replace('#kyc_documents//+#', 'kyc_documents/' . $row['user_id'] . '/', $row['pan_card'] ?? '');
            $row['aadhaar_card']   = preg_replace('#kyc_documents//+#', 'kyc_documents/' . $row['user_id'] . '/', $row['aadhaar_card'] ?? '');
            $row['bank_statement'] = preg_replace('#kyc_documents//+#', 'kyc_documents/' . $row['user_id'] . '/', $row['bank_statement'] ?? '');
          ?>
          <tr>
            <td><?= $i++ ?></td>
            <td>
              <strong><?= htmlspecialchars($row['username'] ?? 'N/A') ?></strong><br>
              <small class="text-muted"><?= htmlspecialchars($row['email'] ?? '') ?></small>
            </td>
            <td>
              <?php if ($row['pan_card']): ?>
                <?php $ext = strtolower(pathinfo($row['pan_card'], PATHINFO_EXTENSION)); ?>
                <?php if (in_array($ext, ['jpg','jpeg','png'])): ?>
                  <img src="<?= $base . $row['pan_card'] ?>" class="doc-thumb" onclick="viewDoc('<?= $base . $row['pan_card'] ?>')">
                <?php else: ?>
                  <a href="<?= $base . $row['pan_card'] ?>" target="_blank" class="doc-link">📄 View PDF</a>
                <?php endif; ?>
              <?php else: echo '<span class="text-muted">—</span>'; endif; ?>
            </td>
            <td>
              <?php if ($row['aadhaar_card']): ?>
                <?php $ext = strtolower(pathinfo($row['aadhaar_card'], PATHINFO_EXTENSION)); ?>
                <?php if (in_array($ext, ['jpg','jpeg','png'])): ?>
                  <img src="<?= $base . $row['aadhaar_card'] ?>" class="doc-thumb" onclick="viewDoc('<?= $base . $row['aadhaar_card'] ?>')">
                <?php else: ?>
                  <a href="<?= $base . $row['aadhaar_card'] ?>" target="_blank" class="doc-link">📄 View PDF</a>
                <?php endif; ?>
              <?php else: echo '<span class="text-muted">—</span>'; endif; ?>
            </td>
            <td>
              <?php if ($row['bank_statement']): ?>
                <?php $ext = strtolower(pathinfo($row['bank_statement'], PATHINFO_EXTENSION)); ?>
                <?php if (in_array($ext, ['jpg','jpeg','png'])): ?>
                  <img src="<?= $base . $row['bank_statement'] ?>" class="doc-thumb" onclick="viewDoc('<?= $base . $row['bank_statement'] ?>')">
                <?php else: ?>
                  <a href="<?= $base . $row['bank_statement'] ?>" target="_blank" class="doc-link">📄 View PDF</a>
                <?php endif; ?>
              <?php else: echo '<span class="text-muted">—</span>'; endif; ?>
            </td>
            <td><span class="badge-<?= $row['status'] ?>"><?= ucfirst($row['status']) ?></span></td>
            <td><?= date('d M Y', strtotime($row['updated_at'])) ?></td>
            <td>
              <?php if ($row['status'] === 'pending'): ?>
                <form method="POST" style="display:inline" onsubmit="return confirm('Approve this KYC?')">
                  <input type="hidden" name="kyc_id" value="<?= $row['id'] ?>">
                  <input type="hidden" name="user_id" value="<?= $row['user_id'] ?>">
                  <input type="hidden" name="action" value="approve">
                  <button class="btn btn-success btn-sm">✔ Approve</button>
                </form>
                <button class="btn btn-danger btn-sm ms-1" onclick="openReject(<?= $row['id'] ?>, <?= $row['user_id'] ?>)">✘ Reject</button>
              <?php else: ?>
                <span class="text-muted">—</span>
              <?php endif; ?>
            </td>
          </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- Image Preview Modal -->
<div class="modal-overlay" id="imgModal">
  <div class="modal-box">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px">
      <strong>Document Preview</strong>
      <button onclick="closeModal()" style="border:none;background:none;font-size:1.4rem;cursor:pointer">×</button>
    </div>
    <img id="modalImg" src="" alt="Document">
  </div>
</div>

<!-- Reject Modal -->
<div class="modal-overlay" id="rejectModal">
  <div class="modal-box">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px">
      <strong>Reject KYC</strong>
      <button onclick="closeReject()" style="border:none;background:none;font-size:1.4rem;cursor:pointer">×</button>
    </div>
    <form method="POST">
      <input type="hidden" name="action" value="reject">
      <input type="hidden" name="kyc_id" id="r_kyc_id">
      <input type="hidden" name="user_id" id="r_user_id">
      <label style="font-size:0.85rem;font-weight:600;display:block;margin-bottom:6px">Rejection Reason</label>
      <textarea name="reason" rows="3" style="width:100%;padding:10px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:0.9rem;margin-bottom:14px" placeholder="e.g. Documents are blurry, PAN not visible..." required></textarea>
      <button type="submit" class="btn btn-danger w-100">Reject KYC</button>
    </form>
  </div>
</div>

<script>
function viewDoc(src) {
  document.getElementById('modalImg').src = src;
  document.getElementById('imgModal').classList.add('open');
}
function closeModal() { document.getElementById('imgModal').classList.remove('open'); }

function openReject(kycId, userId) {
  document.getElementById('r_kyc_id').value = kycId;
  document.getElementById('r_user_id').value = userId;
  document.getElementById('rejectModal').classList.add('open');
}
function closeReject() { document.getElementById('rejectModal').classList.remove('open'); }
</script>
</body>
</html>
