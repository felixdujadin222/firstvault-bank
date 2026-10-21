<?php
// api/balance.php - returns JSON balance for active or default account
require_once __DIR__ . '/../includes/db.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json');

if (isset($_SESSION['user_id'])) {
    $uid = intval($_SESSION['user_id']);
} else {
    // Default fallback to account 1 if not authenticated
    $uid = 1;
}

$stmt = $mysqli->prepare('SELECT id, username, account_number, balance FROM users WHERE id = ?');
$stmt->bind_param('i', $uid);
$stmt->execute();
$res = $stmt->get_result();
if ($row = $res->fetch_assoc()) {
    echo json_encode([
        'success'        => true,
        'user_id'        => $row['id'],
        'username'       => $row['username'],
        'account_number' => $row['account_number'],
        'balance'        => floatval($row['balance'])
    ]);
} else {
    echo json_encode([
        'success' => false,
        'error'   => 'Account not found'
    ]);
}
?>
