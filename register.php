<?php
// register.php - user registration
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/session.php';

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $email    = trim($_POST['email'] ?? '');
    $phone    = trim($_POST['phone'] ?? '');

    if ($username === '' || $password === '' || $email === '') {
        $error = 'Username, password, and email are required.';
    } else {
        // Check if username exists
        $stmt = $mysqli->prepare('SELECT id FROM users WHERE username = ?');
        $stmt->bind_param('s', $username);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) {
            $error = 'This username is already taken. Please choose another.';
        } else {
            $hash = md5($password);

            // Generate sequential 10-digit account number
            $stmt = $mysqli->prepare('SELECT COALESCE(MAX(id), 0) AS max_id FROM users');
            $stmt->execute();
            $maxId = $stmt->get_result()->fetch_assoc()['max_id'];
            $newId = $maxId + 1;
            $account_number = str_pad($newId, 10, '0', STR_PAD_LEFT);
            $starter_balance = 1000.00;

            $stmt = $mysqli->prepare('INSERT INTO users (username, password, email, phone, role, account_number, balance) VALUES (?, ?, ?, ?, "customer", ?, ?)');
            $stmt->bind_param('sssssd', $username, $hash, $email, $phone, $account_number, $starter_balance);

            if ($stmt->execute()) {
                header('Location: login.php?registered=1');
                exit;
            } else {
                $error = 'Registration could not be completed at this time.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Open Account — FirstVault Bank</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="auth-page">
<?php include __DIR__ . '/includes/header.php'; ?>

<main class="auth-container">
    <div class="auth-card">
        <div class="auth-header">
            <div class="auth-icon-badge">✨</div>
            <h2>Create Your Account</h2>
            <p class="auth-subtitle">Get instant access to your vault with a $1,000.00 demo starter balance</p>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-error">
                <span class="alert-icon">⚠️</span>
                <span><?= htmlspecialchars($error) ?></span>
            </div>
        <?php endif; ?>

        <form method="post" action="register.php" class="form-stacked">
            <div class="form-group">
                <label for="username">Desired Username</label>
                <div class="input-wrapper">
                    <input type="text" id="username" name="username" placeholder="Choose a unique username" required autofocus>
                </div>
            </div>

            <div class="form-group">
                <label for="email">Email Address</label>
                <div class="input-wrapper">
                    <input type="email" id="email" name="email" placeholder="you@example.com" required>
                </div>
            </div>

            <div class="form-group">
                <label for="phone">Phone Number (Optional)</label>
                <div class="input-wrapper">
                    <input type="text" id="phone" name="phone" placeholder="+1 (555) 000-0000">
                </div>
            </div>

            <div class="form-group">
                <label for="password">Create Password</label>
                <div class="input-wrapper">
                    <input type="password" id="password" name="password" placeholder="••••••••" required>
                </div>
            </div>

            <button type="submit" class="btn btn-primary btn-block" style="margin-top: 0.5rem;">
                Open Vault Account →
            </button>
        </form>

        <div class="auth-footer">
            Already have an account? <a href="login.php">Sign In</a>
        </div>
    </div>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>
</body>
</html>
