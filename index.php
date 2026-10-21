<?php
// index.php - landing page
require_once __DIR__ . '/includes/session.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FirstVault Bank — Next-Gen Banking</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<?php include __DIR__ . '/includes/header.php'; ?>

<main class="main-content">
    <section class="hero-wrapper">
        <div class="hero-pill">
            <span>🛡️ Next-Gen Financial Portal & Security Lab</span>
        </div>
        <h1 class="hero-title">
            Banking built for the <span class="gradient-text">digital speed</span> of tomorrow
        </h1>
        <p class="hero-description">
            FirstVault provides instant transfers, encrypted account vaults, and live transaction statements with developer-ready REST APIs.
        </p>

        <div class="hero-cta">
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="dashboard.php" class="btn btn-primary">Go to Dashboard →</a>
                <a href="transfer.php" class="btn btn-secondary">Transfer Funds</a>
            <?php else: ?>
                <a href="login.php" class="btn btn-primary">Sign In to Vault</a>
                <a href="register.php" class="btn btn-secondary">Create Account</a>
            <?php endif; ?>
        </div>

        <div class="features-grid">
            <div class="feature-box">
                <span class="feature-icon">⚡</span>
                <h3>Instant Transfers</h3>
                <p>Transfer funds between account numbers seamlessly with automated ledger tracking.</p>
            </div>
            <div class="feature-box">
                <span class="feature-icon">📊</span>
                <h3>Live Statements</h3>
                <p>Review comprehensive historical debits and credits with instantaneous status badges.</p>
            </div>
            <div class="feature-box">
                <span class="feature-icon">🔌</span>
                <h3>Developer API</h3>
                <p>Integrate directly via endpoints like <code>/api/account.php</code> and <code>/api/balance.php</code>.</p>
            </div>
        </div>

        <div class="demo-helpers" style="max-width: 600px; margin-top: 3.5rem; text-align: center;">
            <span class="demo-title">🔑 Test Accounts Available</span>
            <p style="color: var(--text-secondary); font-size: 0.85rem; margin-bottom: 0.75rem;">
                Explore pre-seeded accounts: <strong>alice</strong> (pass: <code>alice123</code>), <strong>bob</strong> (pass: <code>bob123</code>), or <strong>admin</strong> (pass: <code>admin123</code>).
            </p>
            <a href="login.php" class="btn-chip" style="display: inline-block;">Launch Demo Login →</a>
        </div>
    </section>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>
</body>
</html>
