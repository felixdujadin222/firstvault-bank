<?php
// dashboard.php - main customer dashboard
require_once __DIR__ . '/includes/session.php';
require_once __DIR__ . '/includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$userId = $_SESSION['user_id'];

// Retrieve latest user details
$stmt = $mysqli->prepare('SELECT id, username, email, account_number, balance, role FROM users WHERE id = ?');
$stmt->bind_param('i', $userId);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if (!$user) {
    session_destroy();
    header('Location: login.php');
    exit;
}

// Fetch recent transactions (up to 5)
$stmt = $mysqli->prepare('SELECT * FROM transactions WHERE from_account = ? OR to_account = ? ORDER BY created_at DESC LIMIT 5');
$stmt->bind_param('ss', $userId, $userId);
$stmt->execute();
$recentTransactions = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard — FirstVault Bank</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<?php include __DIR__ . '/includes/header.php'; ?>

<main class="main-content">
    <div style="margin-bottom: 2rem; display: flex; justify-content: space-between; align-items: flex-end; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h1 style="font-size: 2rem; font-weight: 800; letter-spacing: -0.02em;">Account Overview</h1>
            <p style="color: var(--text-secondary);">Welcome back, <strong><?= htmlspecialchars($user['username']) ?></strong> &bull; Role: <span class="badge badge-role"><?= htmlspecialchars($user['role']) ?></span></p>
        </div>
        <div>
            <a href="transfer.php" class="btn btn-primary">+ Send Transfer</a>
        </div>
    </div>

    <div class="dashboard-grid">
        <!-- Virtual Debit Card -->
        <div class="virtual-card">
            <div class="card-top">
                <div class="card-chip"></div>
                <div class="card-brand">FIRSTVAULT</div>
            </div>
            <div>
                <span class="card-label">Account Number</span>
                <div class="card-number">
                    <?= htmlspecialchars(chunk_split($user['account_number'] ?? '0000000000', 4, ' ')) ?>
                </div>
            </div>
            <div class="card-bottom">
                <div>
                    <span class="card-label">Card Holder</span>
                    <span class="card-holder"><?= htmlspecialchars($user['username']) ?></span>
                </div>
                <div style="text-align: right;">
                    <span class="card-label">Available Balance</span>
                    <span class="card-balance-val" id="liveBalance">$<?= number_format(floatval($user['balance']), 2) ?></span>
                </div>
            </div>
        </div>

        <!-- Quick Actions & API Insight -->
        <div class="quick-actions-card">
            <div>
                <h3>Quick Actions</h3>
                <p style="color: var(--text-secondary); font-size: 0.9rem;">Manage funds, review statements, or query the developer API.</p>
            </div>
            <div class="actions-btn-group">
                <a href="transfer.php" class="action-tile">
                    <span class="action-tile-icon">💸</span>
                    <span>Send Funds</span>
                </a>
                <a href="statement.php" class="action-tile">
                    <span class="action-tile-icon">📜</span>
                    <span>Statements</span>
                </a>
                <a href="profile.php" class="action-tile">
                    <span class="action-tile-icon">👤</span>
                    <span>Edit Profile</span>
                </a>
                <a href="api/account.php?id=<?= urlencode($user['id']) ?>" target="_blank" class="action-tile">
                    <span class="action-tile-icon">🔌</span>
                    <span>Inspect API</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Recent Transactions Table -->
    <div class="table-card">
        <div class="table-header-bar">
            <div>
                <h3 style="font-size: 1.15rem; font-weight: 700;">Recent Activity</h3>
                <span style="color: var(--text-muted); font-size: 0.825rem;">Latest transactions for this account</span>
            </div>
            <a href="statement.php" class="btn btn-secondary" style="padding: 0.4rem 0.85rem; font-size: 0.85rem;">View Full History →</a>
        </div>
        <div class="table-responsive">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th>Tx ID</th>
                        <th>Type</th>
                        <th>Counterparty</th>
                        <th>Amount</th>
                        <th>Timestamp</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($recentTransactions && $recentTransactions->num_rows > 0): ?>
                        <?php while ($tx = $recentTransactions->fetch_assoc()): ?>
                            <?php $isCredit = strval($tx['to_account']) === strval($userId); ?>
                            <tr>
                                <td style="font-family: var(--font-mono); color: var(--text-muted);">#<?= htmlspecialchars($tx['id']) ?></td>
                                <td>
                                    <?php if ($isCredit): ?>
                                        <span class="badge badge-credit">↓ Received</span>
                                    <?php else: ?>
                                        <span class="badge badge-debit">↑ Sent</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?= $isCredit ? 'From User #' . htmlspecialchars($tx['from_account']) : 'To Account ' . htmlspecialchars($tx['to_account']) ?>
                                </td>
                                <td style="font-family: var(--font-mono); font-weight: 600; color: <?= $isCredit ? 'var(--accent-green)' : '#f87171' ?>;">
                                    <?= $isCredit ? '+' : '-' ?>$<?= number_format(abs(floatval($tx['amount'])), 2) ?>
                                </td>
                                <td style="color: var(--text-muted); font-size: 0.85rem;"><?= htmlspecialchars($tx['created_at']) ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" style="text-align: center; color: var(--text-muted); padding: 2rem;">
                                No transactions recorded yet. <a href="transfer.php">Send your first transfer!</a>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>

<script>
// Dynamic balance refresh via internal API
fetch('api/account.php?id=<?= json_encode($userId) ?>')
    .then(r => r.json())
    .then(data => {
        if (data && typeof data.balance !== 'undefined') {
            document.getElementById('liveBalance').innerText = '$' + Number(data.balance).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }
    })
    .catch(() => {});
</script>
</body>
</html>
