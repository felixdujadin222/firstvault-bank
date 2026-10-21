<?php
// statement.php - view transaction history (IDOR vulnerable)
require_once __DIR__.'/includes/session.php';
require_once __DIR__.'/includes/db.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
$accountId = $_GET['id'] ?? $_SESSION['user_id']; // No ownership check
$stmt = $mysqli->prepare('SELECT * FROM transactions WHERE from_account = ? OR to_account = ? ORDER BY created_at DESC');
$stmt->bind_param('ss', $accountId, $accountId);
$stmt->execute();
$res = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Statement - FirstVault Bank</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<?php include __DIR__.'/includes/header.php'; ?>
<h2>Transaction Statement</h2>
<table class="statement-table">
    <tr><th>ID</th><th>From</th><th>To</th><th>Amount</th><th>Date</th></tr>
    <?php while ($row = $res->fetch_assoc()): ?>
    <tr>
        <td><?=htmlspecialchars($row['id'])?></td>
        <td><?=htmlspecialchars($row['from_account'])?></td>
        <td><?=htmlspecialchars($row['to_account'])?></td>
        <td>$<?=htmlspecialchars($row['amount'])?></td>
        <td><?=htmlspecialchars($row['created_at'])?></td>
    </tr>
    <?php endwhile; ?>
</table>
<?php include __DIR__.'/includes/footer.php'; ?>
</body>
</html>
