<?php
// statement.php - view transaction history
require_once __DIR__ . '/includes/session.php';
require_once __DIR__ . '/includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$accountId = $_GET['id'] ?? $_SESSION['user_id'];

// Fetch account user info
$stmt = $mysqli->prepare('SELECT id, username, account_number, balance FROM users WHERE id = ?');
$stmt->bind_param('i', $accountId);
$stmt->execute();
$account = $stmt->get_result()->fetch_assoc();

if (!$account) {
    die('Account not found.');
}

// Fetch all transactions
$stmt = $mysqli->prepare('SELECT * FROM transactions WHERE from_account = ? OR to_account = ? ORDER BY created_at DESC');
$stmt->bind_param('ss', $accountId, $accountId);
$stmt->execute();
$transactions = $stmt->get_result();

$totalIn = 0;
$totalOut = 0;
$txList = [];
while ($t = $transactions->fetch_assoc()) {
    $txList[] = $t;
    if (strval($t['to_account']) === strval($accountId)) {
        $totalIn += floatval($t['amount']);
    } else {
        $totalOut += floatval($t['amount']);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statement — FirstVault Bank</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<?php include __DIR__ . '/includes/header.php'; ?>

<main class="main-content">
    <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h1 style="font-size: 2rem; font-weight: 800; letter-spacing: -0.02em;">Account Statement</h1>
            <p style="color: var(--text-secondary);">
                Transaction ledger for <strong><?= htmlspecialchars($account['username']) ?></strong> &bull; Acc #<?= htmlspecialchars($account['account_number']) ?>
            </p>
        </div>
        <div style="display: flex; gap: 0.75rem;">
            <a href="transfer.php" class="btn btn-primary">+ New Transfer</a>
            <button type="button" class="btn btn-secondary" onclick="window.print()">🖨️ Print / Export</button>
        </div>
    </div>

    <!-- Stats Summary Row -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 2rem;">
        <div style="background: var(--bg-card); border: 1px solid var(--border-subtle); border-radius: var(--radius-md); padding: 1.25rem;">
            <span style="font-size: 0.78rem; color: var(--text-muted); text-transform: uppercase;">Current Balance</span>
            <div style="font-size: 1.5rem; font-weight: 700; font-family: var(--font-mono); color: var(--accent-teal); margin-top: 0.25rem;">
                $<?= number_format(floatval($account['balance']), 2) ?>
            </div>
        </div>
        <div style="background: var(--bg-card); border: 1px solid var(--border-subtle); border-radius: var(--radius-md); padding: 1.25rem;">
            <span style="font-size: 0.78rem; color: var(--text-muted); text-transform: uppercase;">Total Inflows (Credits)</span>
            <div style="font-size: 1.5rem; font-weight: 700; font-family: var(--font-mono); color: var(--accent-green); margin-top: 0.25rem;">
                +$<?= number_format($totalIn, 2) ?>
            </div>
        </div>
        <div style="background: var(--bg-card); border: 1px solid var(--border-subtle); border-radius: var(--radius-md); padding: 1.25rem;">
            <span style="font-size: 0.78rem; color: var(--text-muted); text-transform: uppercase;">Total Outflows (Debits)</span>
            <div style="font-size: 1.5rem; font-weight: 700; font-family: var(--font-mono); color: #f87171; margin-top: 0.25rem;">
                -$<?= number_format($totalOut, 2) ?>
            </div>
        </div>
    </div>

    <!-- Transactions Table -->
    <div class="table-card">
        <div class="table-header-bar">
            <div>
                <h3 style="font-size: 1.1rem; font-weight: 700;">All Transactions</h3>
                <span style="color: var(--text-muted); font-size: 0.825rem;"><?= count($txList) ?> records found</span>
            </div>
        </div>
        <div class="table-responsive">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th>Transaction ID</th>
                        <th>Type</th>
                        <th>Sender</th>
                        <th>Recipient</th>
                        <th>Amount</th>
                        <th>Date & Time</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($txList)): ?>
                        <?php foreach ($txList as $row): ?>
                            <?php $isCredit = strval($row['to_account']) === strval($accountId); ?>
                            <tr>
                                <td style="font-family: var(--font-mono); color: var(--text-muted);">#<?= htmlspecialchars($row['id']) ?></td>
                                <td>
                                    <?php if ($isCredit): ?>
                                        <span class="badge badge-credit">↓ Inflow</span>
                                    <?php else: ?>
                                        <span class="badge badge-debit">↑ Outflow</span>
                                    <?php endif; ?>
                                </td>
                                <td>User #<?= htmlspecialchars($row['from_account']) ?></td>
                                <td>User #<?= htmlspecialchars($row['to_account']) ?></td>
                                <td style="font-family: var(--font-mono); font-weight: 700; color: <?= $isCredit ? 'var(--accent-green)' : '#f87171' ?>;">
                                    <?= $isCredit ? '+' : '-' ?>$<?= number_format(abs(floatval($row['amount'])), 2) ?>
                                </td>
                                <td style="color: var(--text-muted); font-size: 0.85rem;"><?= htmlspecialchars($row['created_at']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" style="text-align: center; color: var(--text-muted); padding: 2.5rem;">
                                No transactions logged for this account yet.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>
</body>
</html>
