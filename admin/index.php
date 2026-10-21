<?php
// admin/index.php - organizer only answer key (hidden)
require_once __DIR__.'/../includes/db.php';
// Simple password protection (hardcoded, not linked anywhere)
$pwd = $_GET['pwd'] ?? '';
if ($pwd !== 'admin123') {
    echo '<h2>Access Denied</h2>';
    exit;
}
// Fetch all users (including passwords, tokens)
$res = $mysqli->query('SELECT id, username, password, email, role, account_number, balance FROM users');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Answer Key</title>
    <style>
        body {font-family: Arial, sans-serif; background:#222; color:#eee; padding:20px;}
        table {border-collapse:collapse; width:100%;}
        th, td {border:1px solid #555; padding:8px; text-align:left;}
        th {background:#444;}
    </style>
</head>
<body>
<h1>FirstVault Bank – Organizer Answer Key</h1>
<table>
    <tr><th>ID</th><th>Username</th><th>Password (MD5)</th><th>Email</th><th>Role</th><th>Account #</th><th>Balance</th></tr>
    <?php while($row = $res->fetch_assoc()): ?>
    <tr>
        <td><?=htmlspecialchars($row['id'])?></td>
        <td><?=htmlspecialchars($row['username'])?></td>
        <td><?=htmlspecialchars($row['password'])?></td>
        <td><?=htmlspecialchars($row['email'])?></td>
        <td><?=htmlspecialchars($row['role'])?></td>
        <td><?=htmlspecialchars($row['account_number'])?></td>
        <td><?=htmlspecialchars($row['balance'])?></td>
    </tr>
    <?php endwhile; ?>
</table>
<p>Access this page with <code>?pwd=admin123</code> – not linked from any UI.</p>
</body>
</html>
