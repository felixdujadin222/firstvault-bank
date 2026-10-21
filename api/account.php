<?php
// api/account.php - public JSON endpoint returning balance (no auth)
require_once __DIR__ . '/../includes/db.php';
header('Content-Type: application/json');

$id = $_GET['id'] ?? null;
if (!$id || !ctype_digit(strval($id))) {
    echo json_encode(['error' => 'Invalid or missing user ID']);
    exit;
}

$stmt = $mysqli->prepare('SELECT id, username, account_number, balance FROM users WHERE id = ?');
$stmt->bind_param('i', $id);
$stmt->execute();
$res = $stmt->get_result();

if ($row = $res->fetch_assoc()) {
    echo json_encode([
        'id'             => $row['id'],
        'username'       => $row['username'],
        'account_number' => $row['account_number'],
        'balance'        => floatval($row['balance'])
    ]);
} else {
    echo json_encode(['error' => 'User not found']);
}
?>
