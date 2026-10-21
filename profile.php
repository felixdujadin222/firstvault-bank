<?php
// profile.php - view and edit user profile
require_once __DIR__ . '/includes/session.php';
require_once __DIR__ . '/includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$userId = $_SESSION['user_id'];
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email   = trim($_POST['email'] ?? '');
    $phone   = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');

    $stmt = $mysqli->prepare('UPDATE users SET email = ?, phone = ?, address = ? WHERE id = ?');
    $stmt->bind_param('sssi', $email, $phone, $address, $userId);
    if ($stmt->execute()) {
        $success = 'Profile details updated successfully.';
    } else {
        $error = 'Failed to update profile details.';
    }
}

// Fetch current user details
$stmt = $mysqli->prepare('SELECT id, username, email, phone, address, account_number, balance, role FROM users WHERE id = ?');
$stmt->bind_param('i', $userId);
$stmt->execute();
$profile = $stmt->get_result()->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile — FirstVault Bank</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<?php include __DIR__ . '/includes/header.php'; ?>

<main class="main-content">
    <div class="content-card">
        <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1.5rem;">
            <div style="width: 52px; height: 52px; border-radius: 50%; background: linear-gradient(135deg, #0284c7, #00d2b4); display: flex; align-items: center; justify-content: center; font-size: 1.5rem; font-weight: 700;">
                <?= strtoupper(substr($profile['username'] ?? 'U', 0, 1)) ?>
            </div>
            <div>
                <h1 class="card-title" style="margin-bottom: 0.15rem;"><?= htmlspecialchars($profile['username']) ?></h1>
                <span class="badge badge-role"><?= htmlspecialchars($profile['role']) ?></span>
                <span style="color: var(--text-muted); font-size: 0.85rem; margin-left: 0.5rem; font-family: var(--font-mono);">
                    Acc #<?= htmlspecialchars($profile['account_number']) ?>
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

        <form method="post" action="profile.php" class="form-stacked">
            <div class="form-group">
                <label for="username_ro">Account Username</label>
                <div class="input-wrapper">
                    <input type="text" id="username_ro" value="<?= htmlspecialchars($profile['username']) ?>" disabled style="opacity: 0.6; cursor: not-allowed;">
                </div>
            </div>

            <div class="form-group">
                <label for="email">Email Address</label>
                <div class="input-wrapper">
                    <input type="email" id="email" name="email" value="<?= htmlspecialchars($profile['email'] ?? '') ?>" required>
                </div>
            </div>

            <div class="form-group">
                <label for="phone">Phone Number</label>
                <div class="input-wrapper">
                    <input type="text" id="phone" name="phone" value="<?= htmlspecialchars($profile['phone'] ?? '') ?>">
                </div>
            </div>

            <div class="form-group">
                <label for="address">Mailing Address</label>
                <div class="input-wrapper">
                    <input type="text" id="address" name="address" value="<?= htmlspecialchars($profile['address'] ?? '') ?>">
                </div>
            </div>

            <button type="submit" class="btn btn-primary btn-block" style="margin-top: 1rem;">
                Save Profile Changes
            </button>
        </form>

        <div style="margin-top: 1.5rem; text-align: center;">
            <a href="dashboard.php" style="font-size: 0.875rem; color: var(--text-secondary);">← Back to Dashboard</a>
        </div>
    </div>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>
</body>
</html>
