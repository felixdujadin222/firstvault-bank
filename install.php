<?php
// install.php - automated web installer & database migration wizard
$configFile = __DIR__ . '/includes/config.local.php';

// Check if already installed
$isInstalled = file_exists(__DIR__ . '/.installed');
$error = '';
$success = '';

// Default values
$defaultHost = getenv('DB_HOST') ?: 'localhost';
$defaultDb   = getenv('DB_NAME') ?: 'firstvault_bank';
$defaultUser = getenv('DB_USER') ?: 'root';
$defaultPass = getenv('DB_PASS') ?: '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $host = trim($_POST['db_host'] ?? $defaultHost);
    $name = trim($_POST['db_name'] ?? $defaultDb);
    $user = trim($_POST['db_user'] ?? $defaultUser);
    $pass = $_POST['db_pass'] ?? $defaultPass;

    // 1. Test MySQL server connection
    $conn = @new mysqli($host, $user, $pass);
    if ($conn->connect_error) {
        $error = 'Connection failed: ' . $conn->connect_error . '. Please verify your host, user, and password.';
    } else {
        // 2. Create database if needed
        $createDbSql = "CREATE DATABASE IF NOT EXISTS `$name` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
        if (!$conn->query($createDbSql)) {
            $error = 'Failed to create database: ' . $conn->error;
        } else {
            $conn->select_db($name);

            // 3. Create users table
            $usersSql = "CREATE TABLE IF NOT EXISTS `users` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `username` VARCHAR(50) NOT NULL UNIQUE,
                `password` CHAR(32) NOT NULL,
                `email` VARCHAR(100),
                `phone` VARCHAR(20),
                `address` VARCHAR(255) DEFAULT '',
                `account_number` VARCHAR(20) NOT NULL UNIQUE,
                `balance` DECIMAL(10,2) NOT NULL DEFAULT 1000.00,
                `role` VARCHAR(20) NOT NULL DEFAULT 'customer'
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
            $conn->query($usersSql);

            // 4. Create transactions table
            $transSql = "CREATE TABLE IF NOT EXISTS `transactions` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `from_account` VARCHAR(50) NOT NULL,
                `to_account` VARCHAR(50) NOT NULL,
                `amount` DECIMAL(10,2) NOT NULL,
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
            $conn->query($transSql);

            // 5. Seed default demo accounts
            $seedSql = "INSERT INTO `users` (`username`, `password`, `email`, `phone`, `address`, `account_number`, `balance`, `role`) VALUES
                ('alice', MD5('alice123'), 'alice@example.com', '123-456-7890', '100 Financial Way, New York, NY', '0000000001', 1000.00, 'customer'),
                ('bob',   MD5('bob123'),   'bob@example.com',   '987-654-3210', '250 Bank Avenue, Chicago, IL',   '0000000002', 500.00,  'customer'),
                ('admin', MD5('admin123'), 'admin@example.com', '555-019-2834', '1 FirstVault Plaza, Zurich, CH', '0000000003', 9999.00, 'admin')
                ON DUPLICATE KEY UPDATE `id` = `id`";
            $conn->query($seedSql);

            // 6. Write local config file if credentials differ from environment
            $configContent = "<?php\n// Auto-generated database configuration\n\$db_host = " . var_export($host, true) . ";\n\$db_name = " . var_export($name, true) . ";\n\$db_user = " . var_export($user, true) . ";\n\$db_pass = " . var_export($pass, true) . ";\n?>";
            @file_put_contents($configFile, $configContent);

            // 7. Write installation lock file
            @file_put_contents(__DIR__ . '/.installed', date('Y-m-d H:i:s'));

            $success = "Database '$name' initialized and seeded successfully! You can now log in with the test accounts.";
            $isInstalled = true;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Setup & Installer — FirstVault Bank</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="auth-page">
<header class="main-header">
    <div class="nav-container">
        <a href="index.php" class="brand-logo">
            <span class="brand-icon">🏦</span>
            <span>FirstVault Installer</span>
        </a>
        <a href="index.php" class="btn btn-secondary" style="padding: 0.35rem 0.85rem; font-size: 0.825rem;">Return Home</a>
    </div>
</header>

<main class="auth-container" style="max-width: 600px; margin: 0 auto;">
    <div class="auth-card" style="max-width: 100%;">
        <div class="auth-header">
            <div class="auth-icon-badge">⚙️</div>
            <h2>Database Setup Wizard</h2>
            <p class="auth-subtitle">Initialize your tables and seed demo accounts with 1 click</p>
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
            <div style="margin: 1.5rem 0; text-align: center;">
                <a href="login.php" class="btn btn-primary btn-block">Launch FirstVault Bank →</a>
            </div>
        <?php else: ?>
            <?php if ($isInstalled): ?>
                <div class="alert" style="background: rgba(56, 189, 248, 0.15); border: 1px solid rgba(56, 189, 248, 0.3); color: #7dd3fc;">
                    <span>ℹ️ Database has already been initialized. You can re-run to reset or update tables below.</span>
                </div>
            <?php endif; ?>

            <form method="post" action="install.php" class="form-stacked">
                <div class="form-group">
                    <label for="db_host">MySQL Host</label>
                    <div class="input-wrapper">
                        <input type="text" id="db_host" name="db_host" value="<?= htmlspecialchars($defaultHost) ?>" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="db_name">Database Name</label>
                    <div class="input-wrapper">
                        <input type="text" id="db_name" name="db_name" value="<?= htmlspecialchars($defaultDb) ?>" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="db_user">Database Username</label>
                    <div class="input-wrapper">
                        <input type="text" id="db_user" name="db_user" value="<?= htmlspecialchars($defaultUser) ?>" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="db_pass">Database Password</label>
                    <div class="input-wrapper">
                        <input type="password" id="db_pass" name="db_pass" value="<?= htmlspecialchars($defaultPass) ?>" placeholder="(Empty if default XAMPP/WAMP)">
                    </div>
                </div>

                <button type="submit" class="btn btn-primary btn-block" style="margin-top: 0.5rem;">
                    🚀 Initialize & Seed Database
                </button>
            </form>
        <?php endif; ?>

        <div class="demo-helpers">
            <span class="demo-title">Default Seeded Accounts Included:</span>
            <ul style="list-style: none; padding: 0; font-size: 0.85rem; color: var(--text-secondary); line-height: 1.8;">
                <li>&bull; <strong>alice</strong> / <code>alice123</code> — Balance: $1,000.00 (Customer)</li>
                <li>&bull; <strong>bob</strong> / <code>bob123</code> — Balance: $500.00 (Customer)</li>
                <li>&bull; <strong>admin</strong> / <code>admin123</code> — Balance: $9,999.00 (Administrator)</li>
            </ul>
        </div>
    </div>
</main>

<footer class="main-footer">
    <p>&copy; <?= date('Y') ?> FirstVault Bank Setup Wizard</p>
</footer>
</body>
</html>
