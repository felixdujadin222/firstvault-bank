<?php
// transfer.php - money transfer functionality
require_once __DIR__ . '/includes/session.php';
require_once __DIR__ . '/includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$userId = $_SESSION['user_id'];
$error = '';
$success = '';

// Fetch sender current balance & account number
$stmt = $mysqli->prepare('SELECT id, username, account_number, balance FROM users WHERE id = ?');
$stmt->bind_param('i', $userId);
$stmt->execute();
$sender = $stmt->get_result()->fetch_assoc();

if (!$sender) {
    header('Location: logout.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $to_account = trim($_POST['to_account'] ?? '');
    $amount     = floatval($_POST['amount'] ?? 0);

    if ($to_account === '') {
        $error = 'Please enter a valid recipient account number.';
    } elseif ($to_account === $sender['account_number']) {
        $error = 'Cannot transfer funds to your own account.';
    } elseif ($sender['balance'] < $amount) {
        $error = 'Insufficient funds for this transfer.';
    } else {
        // Fetch recipient by account number
        $stmt = $mysqli->prepare('SELECT id, username, account_number, balance FROM users WHERE account_number = ?');
        $stmt->bind_param('s', $to_account);
        $stmt->execute();
        $recipient = $stmt->get_result()->fetch_assoc();

        if (!$recipient) {
            $error = 'Recipient account number was not found.';
        } else {
            // Debit sender
            $stmt = $mysqli->prepare('UPDATE users SET balance = balance - ? WHERE id = ?');
            $stmt->bind_param('di', $amount, $userId);
            $stmt->execute();

            // Credit recipient
            $stmt = $mysqli->prepare('UPDATE users SET balance = balance + ? WHERE id = ?');
            $stmt->bind_param('di', $amount, $recipient['id']);
            $stmt->execute();

            // Record transaction
            $stmt = $mysqli->prepare('INSERT INTO transactions (from_account, to_account, amount) VALUES (?, ?, ?)');
            $stmt->bind_param('ssd', $userId, $recipient['id'], $amount);
            $stmt->execute();

            $success = "Successfully transferred $" . number_format($amount, 2) . " to account {$to_account} ({$recipient['username']}).";

            // Refresh sender's updated balance
            $stmt = $mysqli->prepare('SELECT balance FROM users WHERE id = ?');
            $stmt->bind_param('i', $userId);
            $stmt->execute();
            $sender['balance'] = $stmt->get_result()->fetch_assoc()['balance'];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transfer Funds — FirstVault Bank</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<?php include __DIR__ . '/includes/header.php'; ?>

<main class="main-content">
    <div class="content-card">
        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1.25rem;">
            <div>
                <h1 class="card-title">Transfer Funds</h1>
                <p class="card-subtitle">Wire money directly to any FirstVault account number</p>
            </div>
            <div style="background: rgba(255,255,255,0.04); border: 1px solid var(--border-subtle); padding: 0.5rem 0.85rem; border-radius: var(--radius-md); text-align: right;">
                <span style="font-size: 0.75rem; color: var(--text-muted); display: block; text-transform: uppercase;">Available</span>
                <span style="font-family: var(--font-mono); font-weight: 700; color: var(--accent-teal); font-size: 1.1rem;">
                    $<?= number_format(floatval($sender['balance']), 2) ?>
                </span>
            </div>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-error">
                <span class="alert-icon">⚠️</span>
                <span><?= htmlspecialchars($error) ?></span>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success">
                <span class="alert-icon">✅</span>
                <span><?= htmlspecialchars($success) ?></span>
            </div>
        <?php endif; ?>

        <form method="post" action="transfer.php" class="form-stacked">
            <div class="form-group">
                <label for="to_account">Recipient Account Number</label>
                <div class="input-wrapper">
                    <input type="text" id="to_account" name="to_account" placeholder="e.g. 0000000002" required>
                </div>
                <div style="display: flex; gap: 0.5rem; margin-top: 0.35rem;">
                    <span style="font-size: 0.75rem; color: var(--text-muted);">Quick Recipients:</span>
                    <button type="button" class="btn-chip" onclick="setRecipient('0000000001')">Alice (0000000001)</button>
                    <button type="button" class="btn-chip" onclick="setRecipient('0000000002')">Bob (0000000002)</button>
                </div>
            </div>

            <div class="form-group">
                <label for="amount">Transfer Amount ($ USD)</label>
                <div class="input-wrapper">
                    <input type="number" id="amount" name="amount" step="0.01" min="0.01" placeholder="0.00" required>
                </div>
                <div class="preset-amounts">
                    <button type="button" class="btn-chip" onclick="setAmount(25)">+$25</button>
                    <button type="button" class="btn-chip" onclick="setAmount(50)">+$50</button>
                    <button type="button" class="btn-chip" onclick="setAmount(100)">+$100</button>
                    <button type="button" class="btn-chip" onclick="setAmount(250)">+$250</button>
                    <button type="button" class="btn-chip" onclick="setAmount(<?= floatval($sender['balance']) ?>)">Max Available</button>
                </div>
            </div>

            <button type="submit" class="btn btn-primary btn-block" style="margin-top: 1rem;">
                Authorize Transfer →
            </button>
        </form>

        <div style="margin-top: 1.5rem; text-align: center;">
            <a href="dashboard.php" style="font-size: 0.875rem; color: var(--text-secondary);">← Back to Dashboard</a>
        </div>
    </div>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>

<script>
function setRecipient(acc) {
    document.getElementById('to_account').value = acc;
}
function setAmount(val) {
    document.getElementById('amount').value = parseFloat(val).toFixed(2);
}
</script>
</body>
</html>
