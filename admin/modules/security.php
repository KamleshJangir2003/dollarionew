<?php
if (session_status() === PHP_SESSION_NONE) session_start();
include '../includes/db.php';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $enc  = $conn->real_escape_string($_POST['encryption_status']);
    $ssl  = $conn->real_escape_string($_POST['ssl_status']);
    $ip   = $conn->real_escape_string($_POST['ip_whitelist_status']);
    $rate = $conn->real_escape_string($_POST['rate_limiting_status']);
    $conn->query("UPDATE security_settings SET encryption_status='$enc', ssl_status='$ssl', ip_whitelist_status='$ip', rate_limiting_status='$rate' WHERE id=1");
    $success = 'Security settings updated successfully!';
}

// Fetch settings
$result = $conn->query("SELECT * FROM security_settings WHERE id = 1");
$settings = ($result && $result->num_rows > 0) ? $result->fetch_assoc() : [
    'encryption_status' => 'AES-256', 'ssl_status' => 'Active',
    'ip_whitelist_status' => 'Enabled', 'rate_limiting_status' => 'Active (Limit: 5 attempts/min)'
];
?>
<?php include '../templates/sidebar.php'; ?>
<?php include '../templates/header.php'; ?>


<!-- HTML Form here -->


<!DOCTYPE html>
<html>
<head>
    <title>Security Settings</title>
</head>
<style>
    body {
        font-family: 'Segoe UI', sans-serif;
        background-color: #f8f9fa;
        margin: 0;
        padding: 0;
        overflow-x: hidden;
    }

    .content {
        margin-top: 20px;
        margin-left: 260px;
        max-width: calc(100vw - 260px);
        padding: 30px;
        background-color: #fff;
        border-radius: 10px;
        box-shadow: 0 0 15px rgba(0,0,0,0.1);
    }
    .content h2 {
        text-align: center;
        color: #333;
        margin-bottom: 30px;
    }

    label {
        display: block;
        margin-bottom: 6px;
        color: #555;
        font-weight: 500;
    }

    select, input[type="submit"] {
        width: 100%;
        padding: 10px;
        margin-bottom: 20px;
        border-radius: 5px;
        border: 1px solid #ccc;
        box-sizing: border-box;
        font-size: 16px;
    }

    input[type="submit"] {
        background-color: #007bff;
        color: #fff;
        border: none;
        transition: background-color 0.3s ease;
        cursor: pointer;
    }

    input[type="submit"]:hover {
        background-color: #0056b3;
    }
    @media (max-width: 768px) {
        .content { margin-left: 0; max-width: 100%; padding: 16px; }
    }
</style>

<body>
    <?php if (!empty($success)): ?>
        <div style="margin-left:260px;padding:12px 30px;"><div style="background:#f0fdf4;color:#166534;border:1px solid #bbf7d0;padding:12px 16px;border-radius:8px;"><?= $success ?></div></div>
    <?php endif; ?>
    <section class="content">
        <h2>Update Security Settings</h2>
        <form action="security.php" method="POST">
            <label for="encryption_status">Encryption Status:</label>
            <select name="encryption_status" id="encryption_status">
                <option value="AES-256" <?= (isset($settings['encryption_status']) && $settings['encryption_status'] == 'AES-256') ? 'selected' : '' ?>>AES-256</option>
                <option value="None" <?= (isset($settings['encryption_status']) && $settings['encryption_status'] == 'None') ? 'selected' : '' ?>>None</option>
            </select>
            <br><br>

            <label for="ssl_status">SSL/TLS Status:</label>
            <select name="ssl_status" id="ssl_status">
                <option value="Active" <?= (isset($settings['ssl_status']) && $settings['ssl_status'] == 'Active') ? 'selected' : '' ?>>Active</option>
                <option value="Inactive" <?= (isset($settings['ssl_status']) && $settings['ssl_status'] == 'Inactive') ? 'selected' : '' ?>>Inactive</option>
            </select>
            <br><br>

            <label for="ip_whitelist_status">IP Whitelisting:</label>
            <select name="ip_whitelist_status" id="ip_whitelist_status">
                <option value="Enabled" <?= (isset($settings['ip_whitelist_status']) && $settings['ip_whitelist_status'] == 'Enabled') ? 'selected' : '' ?>>Enabled</option>
                <option value="Disabled" <?= (isset($settings['ip_whitelist_status']) && $settings['ip_whitelist_status'] == 'Disabled') ? 'selected' : '' ?>>Disabled</option>
            </select>
            <br><br>

            <label for="rate_limiting_status">Rate Limiting:</label>
            <select name="rate_limiting_status" id="rate_limiting_status">
                <option value="Active (Limit: 5 attempts/min)" <?= (isset($settings['rate_limiting_status']) && $settings['rate_limiting_status'] == 'Active (Limit: 5 attempts/min)') ? 'selected' : '' ?>>Active (Limit: 5 attempts/min)</option>
                <option value="Inactive" <?= (isset($settings['rate_limiting_status']) && $settings['rate_limiting_status'] == 'Inactive') ? 'selected' : '' ?>>Inactive</option>
            </select>
            <br><br>

            <input type="submit" value="Update Security Settings">
        </form>
    </section>
</body>
</html>
