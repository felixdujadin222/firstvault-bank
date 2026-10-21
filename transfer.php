<?php
// transfer.php - money transfer (multiple vulnerabilities)
require_once __DIR__.'/includes/session.php';
require_once __DIR__.'/includes/db.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
$error = '';
$success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $to_account = $_POST['to_account'];
    $amount = floatval($_POST['amount']); // No check for > 0
    // No CSRF token validation
    // Fetch sender balance
    $stmt = $mysqli->prepare('SELECT balance FROM users WHERE id = ?');
    $stmt->bind_param('i', $_SESSION['user_id']);
    $stmt->execute();
    $sender = $stmt->get_result()->fetch_assoc();
    if (!$sender) {
        $error = 'Sender not found';
    } elseif ($sender['balance'] >= $amount) { // negative amount passes trivially
        // Fetch recipient id by account number
        $stmt = $mysqli->prepare('SELECT id FROM users WHERE account_number = ?');
        $stmt->bind_param('s', $to_account);
        $stmt->execute();
        $recipient = $stmt->get_result()->fetch_assoc();
        if (!$recipient) {
            $error = 'Recipient not found';
        } else {
            // Race condition prone: separate update queries
            $stmt = $mysqli->prepare('UPDATE users SET balance = balance - ? WHERE id = ?');
            $stmt->bind_param('di', $amount, $_SESSION['user_id']);
            $stmt->execute();
            $stmt = $mysqli->prepare('UPDATE users SET balance = balance + ? WHERE id = ?');
            $stmt->bind_param('di', $amount, $recipient['id']);
            $stmt->execute();
            // Log transaction
            $stmt = $mysqli->prepare('INSERT INTO transactions (from_account, to_account, amount) VALUES (?, ?, ?)');
            $stmt->bind_param('ssd', $_SESSION['user_id'], $recipient['id'], $amount);
            $stmt->execute();
            $success = 'Transfer completed';
        }
    } else {
        $error = 'Insufficient funds';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Transfer - FirstVault Bank</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<?php include __DIR__.'/includes/header.php'; ?>
<h2>Transfer Funds</h2>
<?php if ($error): ?><p class="error"><?=htmlspecialchars($error)?></p><?php endif; ?>
<?php if ($success): ?><p class="success"><?=htmlspecialchars($success)?></p><?php endif; ?>
<form method="post" action="transfer.php">
    <label>Recipient Account Number: <input type="text" name="to_account" required></label><br>
    <label>Amount: <input type="number" step="0.01" name="amount" required></label><br>
    <button type="submit">Transfer</button>
</form>
<?php include __DIR__.'/includes/footer.php'; ?>
</body>
</html>
