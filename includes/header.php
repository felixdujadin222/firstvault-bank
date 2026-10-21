<?php
// includes/header.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$currentPage = basename($_SERVER['PHP_SELF'] ?? '');
?>
<header class="main-header">
    <div class="nav-container">
        <a href="index.php" class="brand-logo">
            <span class="brand-icon">🏦</span>
            <span>FirstVault</span>
        </a>
        <nav class="nav-links">
            <a href="index.php" class="nav-link <?= $currentPage === 'index.php' ? 'active' : '' ?>">Home</a>
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="dashboard.php" class="nav-link <?= $currentPage === 'dashboard.php' ? 'active' : '' ?>">Dashboard</a>
                <a href="transfer.php" class="nav-link <?= $currentPage === 'transfer.php' ? 'active' : '' ?>">Transfer</a>
                <a href="statement.php" class="nav-link <?= $currentPage === 'statement.php' ? 'active' : '' ?>">Statement</a>
                <a href="profile.php" class="nav-link <?= $currentPage === 'profile.php' ? 'active' : '' ?>">Profile</a>
                <?php if (!empty($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                    <a href="admin/index.php?pwd=admin123" class="nav-link" style="color: #f59e0b;">Admin Panel</a>
                <?php endif; ?>
                <div class="user-badge-nav">
                    <span class="avatar-dot"></span>
                    <span><?= htmlspecialchars($_SESSION['username'] ?? 'User') ?></span>
                </div>
                <a href="logout.php" class="btn btn-secondary" style="padding: 0.35rem 0.85rem; font-size: 0.825rem;">Logout</a>
            <?php else: ?>
                <a href="login.php" class="nav-link <?= $currentPage === 'login.php' ? 'active' : '' ?>">Sign In</a>
                <a href="register.php" class="btn btn-primary" style="padding: 0.45rem 1rem; font-size: 0.85rem;">Open Account</a>
            <?php endif; ?>
        </nav>
    </div>
</header>
