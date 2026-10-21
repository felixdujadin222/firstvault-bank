<?php
// account.php - view account details
require_once __DIR__ . '/includes/session.php';
require_once __DIR__ . '/includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$accountId = $_GET['id'] ?? $_SESSION['user_id'];
$stmt = $mysqli->prepare('SELECT id, username, email, phone, address, account_number, balance, role FROM users WHERE id = ?');
$stmt->bind_param('i', $accountId);
$stmt->execute();
$account = $stmt->get_result()->fetch_assoc();

if (!$account) {
    die('Account record not found.');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Details — FirstVault Bank</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<?php include __DIR__ . '/includes/header.php'; ?>

<main class="main-content">
    <div class="content-card">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
            <div>
                <h1 class="card-title">Account Details</h1>
                <p class="card-subtitle" style="margin-bottom: 0;">Verified FirstVault Account Holder</p>
            </div>
            <span class="badge badge-role"><?= htmlspecialchars($account['role']) ?></span>
        </div>

        <div style="background: rgba(15, 23, 42, 0.6); border: 1px solid var(--border-subtle); border-radius: var(--radius-md); padding: 1.5rem; margin-bottom: 1.5rem; display: flex; flex-direction: column; gap: 0.85rem;">
            <div>
                <span style="font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase;">Username</span>
                <div style="font-weight: 600; font-size: 1.05rem;"><?= htmlspecialchars($account['username']) ?></div>
            </div>
            <div>
                <span style="font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase;">Account Number</span>
                <div style="font-family: var(--font-mono); font-size: 1.15rem; color: var(--accent-teal);"><?= htmlspecialchars($account['account_number']) ?></div>
            </div>
            <div>
                <span style="font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase;">Available Balance</span>
                <div style="font-family: var(--font-mono); font-size: 1.4rem; font-weight: 700; color: var(--accent-green);">
                    $<?= number_format(floatval($account['balance']), 2) ?>
                </div>
            </div>
            <?php if (!empty($account['email'])): ?>
            <div>
                <span style="font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase;">Email</span>
                <div><?= htmlspecialchars($account['email']) ?></div>
            </div>
            <?php endif; ?>
        </div>

        <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
            <a href="statement.php?id=<?= urlencode($account['id']) ?>" class="btn btn-primary" style="flex: 1;">View Statements →</a>
            <a href="transfer.php" class="btn btn-secondary" style="flex: 1;">Transfer Funds</a>
        </div>
    </div>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>
</body>
</html>
