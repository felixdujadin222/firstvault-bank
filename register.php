<?php
// register.php - user registration (normal flow)
require_once __DIR__.'/includes/db.php';
require_once __DIR__.'/includes/session.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $email    = $_POST['email'];
    // Simple validation (omitted for brevity)
    $hash = md5($password);
    // Generate sequential account number (vulnerable: predictable)
    $stmt = $mysqli->prepare('SELECT MAX(id) AS max_id FROM users');
    $stmt->execute();
    $maxId = $stmt->get_result()->fetch_assoc()['max_id'];
    $newId = $maxId + 1;
    $account_number = str_pad($newId, 10, '0', STR_PAD_LEFT);
    $balance = 1000.00; // starter balance
    $stmt = $mysqli->prepare('INSERT INTO users (username, password, email, role, account_number, balance) VALUES (?, ?, ?, "customer", ?, ?)');
    $stmt->bind_param('ssssd', $username, $hash, $email, $account_number, $balance);
    if ($stmt->execute()) {
        header('Location: login.php');
        exit;
    } else {
        $error = 'Registration failed';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register - FirstVault Bank</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<?php include __DIR__.'/includes/header.php'; ?>
<h2>Register</h2>
<?php if ($error): ?><p class="error"><?=htmlspecialchars($error)?></p><?php endif; ?>
<form method="post" action="register.php">
    <label>Username: <input type="text" name="username" required></label><br>
    <label>Password: <input type="password" name="password" required></label><br>
    <label>Email: <input type="email" name="email" required></label><br>
    <button type="submit">Register</button>
</form>
<?php include __DIR__.'/includes/footer.php'; ?>
</body>
</html>
