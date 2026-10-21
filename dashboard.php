<?php
// dashboard.php - user dashboard with live balance widget
require_once __DIR__.'/includes/session.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - FirstVault Bank</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<?php include __DIR__.'/includes/header.php'; ?>
<h2>Dashboard</h2>
<p>Welcome, <?=htmlspecialchars($_SESSION['username'])?>!</p>
<div id="balance">Loading balance...</div>
<script>
  // Fetch live balance via API (no auth check)
  fetch('api/account.php?id=' + <?=json_encode($_SESSION['user_id'])?>)
    .then(r => r.json())
    .then(data => {
        document.getElementById('balance').innerText = 'Balance: $' + data.balance;
    })
    .catch(err => {
        document.getElementById('balance').innerText = 'Error loading balance';
    });
</script>
<?php include __DIR__.'/includes/footer.php'; ?>
</body>
</html>
