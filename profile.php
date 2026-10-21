<?php
// profile.php - view and edit user profile (no CSRF, insecure file upload)
require_once __DIR__.'/includes/session.php';
require_once __DIR__.'/includes/db.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
$userId = $_SESSION['user_id'];
$error = '';
$success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    // Directly update without validation
    $stmt = $mysqli->prepare('UPDATE users SET email = ?, phone = ?, address = ? WHERE id = ?');
    $stmt->bind_param('sssi', $email, $phone, $address, $userId);
    $stmt->execute();
    $success = 'Profile updated';
}
// Fetch current profile
$stmt = $mysqli->prepare('SELECT username, email, phone, address FROM users WHERE id = ?');
$stmt->bind_param('i', $userId);
$stmt->execute();
$profile = $stmt->get_result()->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Profile - FirstVault Bank</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<?php include __DIR__.'/includes/header.php'; ?>
<h2>Profile</h2>
<?php if ($error): ?><p class="error"><?=htmlspecialchars($error)?></p><?php endif; ?>
<?php if ($success): ?><p class="success"><?=htmlspecialchars($success)?></p><?php endif; ?>
<form method="post" action="profile.php">
    <label>Username: <?=htmlspecialchars($profile['username'])?></label><br>
    <label>Email: <input type="email" name="email" value="<?=htmlspecialchars($profile['email'])?>"></label><br>
    <label>Phone: <input type="text" name="phone" value="<?=htmlspecialchars($profile['phone'])?>"></label><br>
    <label>Address: <input type="text" name="address" value="<?=htmlspecialchars($profile['address'])?>"></label><br>
    <button type="submit">Save</button>
</form>
<?php include __DIR__.'/includes/footer.php'; ?>
</body>
</html>
