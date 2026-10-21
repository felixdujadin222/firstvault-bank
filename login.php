<?php
// login.php - user authentication
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/session.php';

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $error = 'Please enter both username and password.';
    } else {
        // Vulnerable query for educational/CTF banking simulation
        $sql = "SELECT * FROM users WHERE username = '$username' AND password = MD5('$password')";
        $result = $mysqli->query($sql);

        if ($result && $row = $result->fetch_assoc()) {
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['username'] = $row['username'];
            $_SESSION['role'] = $row['role'];

            // Remember-me token
            if (!empty($_POST['remember'])) {
                setcookie('remember', md5($row['username'] . $row['id']), time() + 30 * 24 * 60 * 60, '/');
            }

            header('Location: dashboard.php');
            exit;
        } else {
            $error = 'Invalid username or password.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In — FirstVault Bank</title>
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
            <div class="auth-icon-badge">🏦</div>
            <h2>Welcome Back</h2>
            <p class="auth-subtitle">Log in to manage your FirstVault accounts and transfers</p>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-error">
                <span class="alert-icon">⚠️</span>
                <span><?= htmlspecialchars($error) ?></span>
            </div>
        <?php endif; ?>

        <form method="post" action="login.php" class="form-stacked">
            <div class="form-group">
                <label for="username">Username</label>
                <div class="input-wrapper">
                    <input type="text" id="username" name="username" placeholder="e.g. alice" required autofocus>
                </div>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <div class="input-wrapper">
                    <input type="password" id="password" name="password" placeholder="••••••••" required>
                </div>
            </div>

            <div class="form-row-checkbox">
                <label class="checkbox-container">
                    <input type="checkbox" name="remember" value="1">
                    <span class="checkbox-label">Keep me signed in for 30 days</span>
                </label>
            </div>

            <button type="submit" class="btn btn-primary btn-block">Sign In to Vault</button>
        </form>

        <div class="demo-helpers">
            <span class="demo-title">⚡ Quick Fill Demo Accounts:</span>
            <div class="demo-buttons">
                <button type="button" class="btn-chip" onclick="fillDemo('alice', 'alice123')">Alice (Customer)</button>
                <button type="button" class="btn-chip" onclick="fillDemo('bob', 'bob123')">Bob (Customer)</button>
                <button type="button" class="btn-chip" onclick="fillDemo('admin', 'admin123')">Admin</button>
            </div>
        </div>

        <div class="auth-footer">
            Don't have an account yet? <a href="register.php">Create Account</a>
        </div>
    </div>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>

<script>
function fillDemo(username, password) {
    document.getElementById('username').value = username;
    document.getElementById('password').value = password;
}
</script>
</body>
</html>
