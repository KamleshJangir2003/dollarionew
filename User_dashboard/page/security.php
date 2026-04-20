<?php
if (session_status() === PHP_SESSION_NONE) session_start();
include('submit_help.php');
require_once 'PHPGangsta/GoogleAuthenticator.php';

// ---------------------- Session Timeout -----------------------
$timeout = 900; // 15 minutes

if (isset($_SESSION['last_activity']) && time() - $_SESSION['last_activity'] > $timeout) {
    session_unset();
    session_destroy();
    echo "<script>alert('Session expired due to inactivity.'); window.location.href='security.php';</script>";
    exit();
}
$_SESSION['last_activity'] = time();

// ---------------------- Setup -----------------------
$email = "user@example.com"; // Replace with dynamic email if needed
$ga = new PHPGangsta_GoogleAuthenticator();
$_SESSION['2fa_secret'] = $_SESSION['2fa_secret'] ?? $ga->createSecret();
$secret = $_SESSION['2fa_secret'];
$qrCodeUrl = $ga->getQRCodeGoogleUrl('MySecureApp', $secret);

// ---------------------- OTP Form Submission -----------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['otp'])) {
    $otp = $_POST['otp'];
    $checkResult = $ga->verifyCode($secret, $otp, 2); // 2 = 2*30sec clock tolerance

    if ($checkResult) {
        $_SESSION['2fa_enabled'] = true;
        echo "<script>alert('✅ 2FA Enabled Successfully!'); window.location.href='security.php';</script>";
        exit();
    } else {
        echo "<script>alert('❌ Invalid OTP!'); window.location.href='security.php';</script>";
        exit();
    }
}

// ---------------------- Logout All -----------------------
if (isset($_GET['logout_all'])) {
    session_unset();
    session_destroy();
    echo "<script>alert('Logged out from all devices.'); window.location.href='security.php';</script>";
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Security Settings</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../responsive.css">
<style>
    .container { width: auto; margin-left: 250px; }
    @media (max-width: 768px) {
      .container { margin-left: 0 !important; }
    }
</style>
</head>


<body class="bg-light">
<?php include('../sidebar.php'); ?>
<?php include('../mobile_header.php'); ?>
<div class="container">
    
    <h2>🔐 Security Settings</h2>

    <div class="card my-4">
        <div class="card-header">Session Management</div>
        <div class="card-body">
            <p>💤 Auto Logout after 15 minutes of inactivity is enabled.</p>
            <p>🧾 JWT Token Expiry: <strong>30 minutes (example only)</strong></p>
            <a href="?logout_all=true" class="btn btn-warning">Logout from All Devices</a>
        </div>
    </div>

    <div class="card my-4">
        <div class="card-header">Two-Factor Authentication (2FA)</div>
        <div class="card-body">
            <form method="post">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="toggle2FA" <?= isset($_SESSION['2fa_enabled']) && $_SESSION['2fa_enabled'] ? 'checked disabled' : '' ?>>
                    <label class="form-check-label" for="toggle2FA">
                        <?= isset($_SESSION['2fa_enabled']) && $_SESSION['2fa_enabled'] ? '2FA is Enabled' : 'Enable Google Authenticator' ?>
                    </label>
                </div>

                <div id="2faSetup" class="mt-4" style="display: <?= isset($_SESSION['2fa_enabled']) && $_SESSION['2fa_enabled'] ? 'none' : 'block' ?>;">
                    <p>📱 Scan this QR code in your Google Authenticator app:</p>
                    <img src="<?= $qrCodeUrl ?>" alt="QR Code" style="max-width:200px;">
                    <div class="mt-3">
                        <label>Enter OTP:</label>
                        <input type="text" name="otp" class="form-control" placeholder="123456" required>
                        <button type="submit" class="btn btn-success mt-2">Verify & Enable</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    const toggle = document.getElementById('toggle2FA');
    const setupDiv = document.getElementById('2faSetup');
    if (toggle && !toggle.disabled) {
        toggle.addEventListener('change', () => {
            setupDiv.style.display = toggle.checked ? 'block' : 'none';
        });
    }
</script>

</body>
</html>
