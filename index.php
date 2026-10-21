<?php
// index.php - landing page
require_once __DIR__.'/includes/session.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>FirstVault Bank</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<?php include __DIR__.'/includes/header.php'; ?>
    <h1>Welcome to FirstVault Bank</h1>
    <?php if (isset($_SESSION['user_id'])): ?>
        <p>Hello, <?=htmlspecialchars($_SESSION['username'])?>! <a href="dashboard.php">Go to Dashboard</a> | <a href="logout.php">Logout</a></p>
    <?php else: ?>
        <p><a href="login.php">Login</a> | <a href="register.php">Register</a></p>
    <?php endif; ?>
<?php include __DIR__.'/includes/footer.php'; ?>
</body>
</html>
