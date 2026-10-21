<?php
// account.php - view account details (IDOR vulnerable)
require_once __DIR__.'/includes/session.php';
require_once __DIR__.'/includes/db.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
$accountId = $_GET['id'] ?? $_SESSION['user_id']; // No ownership check
$stmt = $mysqli->prepare('SELECT * FROM users WHERE id = ?');
$stmt->bind_param('i', $accountId);
$stmt->execute();
$account = $stmt->get_result()->fetch_assoc();
if (!$account) {
    die('Account not found');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Account Details - FirstVault Bank</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<?php include __DIR__.'/includes/header.php'; ?>
<h2>Account Details</h2>
<p>Username: <?=htmlspecialchars($account['username'])?></p>
<p>Account Number: <?=htmlspecialchars($account['account_number'])?></p>
<p>Balance: $<?=htmlspecialchars($account['balance'])?></p>
<a href="statement.php?id=<?=urlencode($account['id'])?>">View Statement</a>
<?php include __DIR__.'/includes/footer.php'; ?>
</body>
</html>
