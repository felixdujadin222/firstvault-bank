<?php
// admin/index.php - organizer administrative console
require_once __DIR__ . '/../includes/db.php';

$pwd = $_GET['pwd'] ?? '';
if ($pwd !== 'admin123') {
    http_response_code(403);
    echo '<!DOCTYPE html><html><head><title>403 Forbidden</title><link rel="stylesheet" href="../css/style.css"></head><body style="display:flex;align-items:center;justify-content:center;height:100vh;text-align:center;"><div><h1 style="font-size:3rem;margin-bottom:0.5rem;color:#f87171;">403 Access Denied</h1><p style="color:var(--text-secondary);margin-bottom:1.5rem;">Valid organizer passkey parameter required (?pwd=...).</p><a href="../index.php" class="btn btn-secondary">Return Home</a></div></body></html>';
    exit;
}

$res = $mysqli->query('SELECT id, username, password, email, role, account_number, balance FROM users ORDER BY id ASC');

$totalDeposits = 0;
$userList = [];
while ($row = $res->fetch_assoc()) {
    $userList[] = $row;
    $totalDeposits += floatval($row['balance']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Organizer Console — FirstVault Bank</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body style="background:#070b14;">
<header class="main-header" style="border-bottom: 1px solid rgba(245, 158, 11, 0.3);">
    <div class="nav-container">
        <div class="brand-logo">
            <span class="brand-icon" style="background: linear-gradient(135deg, #f59e0b, #ef4444);">⚙️</span>
            <span>FirstVault <span style="color:#f59e0b;font-weight:600;font-size:0.85rem;margin-left:0.25rem;">ADMIN CONSOLE</span></span>
        </div>
        <div style="display:flex; gap:0.75rem;">
            <a href="../dashboard.php" class="btn btn-secondary" style="padding:0.4rem 0.85rem;font-size:0.85rem;">← Bank Dashboard</a>
            <a href="../logout.php" class="btn btn-secondary" style="padding:0.4rem 0.85rem;font-size:0.85rem;">Logout</a>
        </div>
    </div>
</header>

<main class="main-content">
    <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h1 style="font-size: 2rem; font-weight: 800; letter-spacing: -0.02em;">System Accounts Overview</h1>
            <p style="color: var(--text-secondary);">Direct database viewer for lab organizers and administrative security audits.</p>
        </div>
        <span class="badge" style="background: rgba(245, 158, 11, 0.2); color: #fbbf24; border: 1px solid rgba(245, 158, 11, 0.4); padding: 0.4rem 0.85rem;">
            🔒 Authenticated via Passkey
        </span>
    </div>

    <!-- Metrics Cards -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1rem; margin-bottom: 2rem;">
        <div style="background: var(--bg-card); border: 1px solid var(--border-subtle); border-radius: var(--radius-md); padding: 1.25rem;">
            <span style="font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase;">Total Active Users</span>
            <div style="font-size: 1.6rem; font-weight: 700; font-family: var(--font-mono); margin-top: 0.25rem;">
                <?= count($userList) ?> Accounts
            </div>
        </div>
        <div style="background: var(--bg-card); border: 1px solid var(--border-subtle); border-radius: var(--radius-md); padding: 1.25rem;">
            <span style="font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase;">Total Ledger Custody</span>
            <div style="font-size: 1.6rem; font-weight: 700; font-family: var(--font-mono); color: var(--accent-teal); margin-top: 0.25rem;">
                $<?= number_format($totalDeposits, 2) ?>
            </div>
        </div>
    </div>

    <!-- Users Table -->
    <div class="table-card" style="border-color: rgba(245, 158, 11, 0.2);">
        <div class="table-header-bar" style="background: rgba(245, 158, 11, 0.05);">
            <div>
                <h3 style="font-size: 1.1rem; font-weight: 700;">Registered Accounts & Password Hashes</h3>
                <span style="color: var(--text-muted); font-size: 0.825rem;">Inspect user records directly</span>
            </div>
        </div>
        <div class="table-responsive">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Password Hash (MD5)</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Account #</th>
                        <th>Balance</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($userList as $u): ?>
                    <tr>
                        <td style="font-family: var(--font-mono); color: var(--text-muted);">#<?= htmlspecialchars($u['id']) ?></td>
                        <td style="font-weight: 600; color: #f8fafc;"><?= htmlspecialchars($u['username']) ?></td>
                        <td style="font-family: var(--font-mono); font-size: 0.8rem; color: #a5b4fc;"><?= htmlspecialchars($u['password']) ?></td>
                        <td><?= htmlspecialchars($u['email'] ?? '—') ?></td>
                        <td>
                            <span class="badge <?= $u['role'] === 'admin' ? 'badge-role' : 'badge-credit' ?>">
                                <?= htmlspecialchars($u['role']) ?>
                            </span>
                        </td>
                        <td style="font-family: var(--font-mono); color: var(--accent-teal);"><?= htmlspecialchars($u['account_number']) ?></td>
                        <td style="font-family: var(--font-mono); font-weight: 700;">$<?= number_format(floatval($u['balance']), 2) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<footer class="main-footer">
    <p>Organizer Secret URL: <code>/admin/index.php?pwd=admin123</code> &bull; FirstVault Banking Platform</p>
</footer>
</body>
</html>
